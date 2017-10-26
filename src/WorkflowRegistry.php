<?php

namespace HskyZhou\Workflow;

use HskyZhou\Workflow\Events\WorkflowSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\MarkingStore\MultipleStateMarkingStore;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\SupportStrategy\ClassInstanceSupportStrategy;
use HskyZhou\Workflow\Models\Workflow as WorkflowModel;
use HskyZhou\Workflow\Models\Arc as ArcModel;

/**
 * @author Boris Koumondji <brexis@yahoo.fr>
 */
class WorkflowRegistry
{
    /**
     * @var Symfony\Component\Workflow\Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $config;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(array $config)
    {
        $this->registry     = new Registry();

        $this->config       = $config;

        $this->dispatcher   = new EventDispatcher();

        $subscriber         = new WorkflowSubscriber();
        $this->dispatcher->addSubscriber($subscriber);

        $arcGroups = ArcModel::with(['placeFrom', 'placeTo', 'transition'])->get()->groupBy('workflow_id');

        /*获取流程图*/
        $workflowConfigs = [];
        $workflows = WorkflowModel::with(['places', 'arcs'])->get();
        if($workflows->isNotEmpty()){
            foreach($workflows as $workflow) {
                $tempWorkflow = [
                    'type' => 'state_machine',
                    'marking_store' => [
                        'type' => 'single_state',
                    ],
                    'supports'      => [$workflow->model],
                    'places'        => $workflow->places->keyBy('name')->keys()->toArray(),
                ];

                $arcs = isset($arcGroups[$workflow->id]) ? $arcGroups[$workflow->id] : collect([]);
                if($arcs->isNotEmpty()){
                    $transitions = [];
                    foreach($arcs as $arc) {
                        $transitions[$arc->transition->slug] = [
                            'from' => $arc->placeFrom->name,
                            'to' => $arc->placeTo->name,
                        ];
                    }

                    $tempWorkflow['transitions'] = $transitions;
                }

                $workflowConfigs[$workflow->name] = $tempWorkflow;
            }
        }

        foreach ($workflowConfigs as $name => $workflowData) {
            $builder = new DefinitionBuilder($workflowData['places']);

            foreach ($workflowData['transitions'] as $transitionName => $transition) {
                $builder->addTransition(new Transition($transitionName, $transition['from'], $transition['to']));
            }

            $definition     = $builder->build();
            $markingStore   = $this->getMakingStoreInstance($workflowData);
            $workflow       = $this->getWorkflowInstance($name, $workflowData, $definition, $markingStore);

            foreach ($workflowData['supports'] as $supportedClass) {
                $this->registry->add($workflow, new ClassInstanceSupportStrategy($supportedClass));
            }
        }
    }

    /**
     * Return the $subject workflo
     * @param  object $subject
     * @param  string $workflowName
     * @return Workflow
     */
    public function get($subject, $workflowName = null)
    {
        return $this->registry->get($subject, $workflowName);
    }

    /**
     * Add a workflow to the subject
     * @param Workflow $workflow
     * @param Symfony\Component\Workflow\SupportStrategy\SupportStrategyInterface $supportStrategy
     */
    public function add(Workflow $workflow, $supportStrategy)
    {
        return $this->registry->add($workflow, $supportStrategy);
    }

    /**
     * Return the workflow instance
     *
     * @param  String                                                        $name
     * @param  array                                                         $workflowData
     * @param  Symfony\Component\Workflow\Definition                         $definition
     * @param  Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface $makingStore
     * @return Symfony\Component\Workflow\Workflow
     */
    private function getWorkflowInstance($name, $workflowData, Definition $definition, MarkingStoreInterface $markingStore)
    {
        $type  = isset($workflowData['type']) ? $workflowData['type'] : 'workflow';
        $className = Workflow::class;

        if ($type === 'state_machine') {
            $className = StateMachine::class;
        } else if (isset($workflowData['class'])) {
            $className = $workflowData['class'];
        }

        return new $className($definition, $markingStore, $this->dispatcher, $name);
    }

    /**
     * Return the making store instance
     *
     * @param  array $makingStoreData
     * @return Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface
     */
    private function getMakingStoreInstance($workflowData)
    {
        $makingStoreData    = isset($workflowData['marking_store']) ? $workflowData['marking_store'] : [];
        $type               = isset($makingStoreData['type']) ? $makingStoreData['type'] : 'single_state';
        $className          = SingleStateMarkingStore::class;
        $arguments          = [];

        if ($type === 'multiple_state') {
            $className = MultipleStateMarkingStore::class;
        } else if (isset($workflowData['class'])) {
            $className = $workflowData['class'];
        }

        if (isset($makingStoreData['arguments'])) {
            $arguments = $makingStoreData['arguments'];
        }

        $class = new \ReflectionClass($className);

        return $class->newInstanceArgs($arguments);
    }
}
