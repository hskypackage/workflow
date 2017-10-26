<?php 

use HskyZhou\Workflow\WorkflowRegistry;
use HskyZhou\Workflow\Models\Workflow as WorkflowModel;
use HskyZhou\Workflow\Models\Transition as TransitionModel;
use HskyZhou\Workflow\Models\Item as ItemModel;

if(!function_exists('record_workflow')) {
	/**
	 * 
	 * @param $info Model实例
	 * @param $action string 执行的操作
	 * @param $bool boolean 是否记录日志
	 */
	function record_workflow($info, $action, $userId = 0, $bool = true)
	{
		$results = [
			'result' => true,
			'message' => '记录工作流成功'
		];

		try {
			/*工作流执行*/
			$workflow = (new WorkflowRegistry([]))->get($info);
			$workflow->apply($info, $action);
			$info->save(); // Don't forget to persist the state

			/*记录日志*/
			if($bool) {
				$model = get_class($info);

				if(!$userId) {
					if($curUser = auth()->user()) {
						$userId = $curUser->id;
					}
				}

				$transitionInfo = null;
				if($workflowInfo = WorkflowModel::where('name', $workflow->getName())->first()){
					$transitionInfo = TransitionModel::where('workflow_id', $workflowInfo->id)->where('slug', $action)->first();
				}

				$data = [
					'model' => $model,
					'model_id' => $info->id,
					'user_id' => $userId,
					'workflow_id' => $workflowInfo ? $workflowInfo->id : 0,
					'transition_id' => $transitionInfo ? $transitionInfo->id : 0,
				];
				// dd($data);
				ItemModel::create($data);
			}
		} catch (Exception $e) {
			dd($e);
			$results = array_merge($results, [
				'result' => false,
				'message' => '记录工作流失败'
			]);
		}

		return $results;
	}
}