<?php

namespace HskyZhou\Workflow\Controllers;

use Illuminate\Http\Request;
use HskyZhou\Workflow\Models\Workflow;
use HskyZhou\Workflow\Models\Place;
use HskyZhou\Workflow\Models\Transition;
use HskyZhou\Workflow\Models\Arc;

class WorkflowController extends Controller
{
	public function index()
	{
		return app('view')->make('workflow::workflow.index');
	}

	public function datatables()
	{

		$count = Workflow::count();
		$data = Workflow::get()->map(function($item, $key){
			return [
				'id' => $item->id,
				'name' => $item->name,
				'created_at' => $item->created_at->toDateString(),
				'button' => $this->button($item->id),
			];
		});

		$data = [
			'draw' => request('draw', 1),
			'recordsTotal' => $count,
			'recordsFiltered' => $count,
			'data' => $data,
		];

		return response()->json($data);
	}

	public function create()
	{
		return app('view')->make('workflow::workflow.create');
	}

	public function store()
	{
		Workflow::create(request()->all());

		return redirect()->route('workflow.index');
	}

	private function button($id)
	{
		$str = '';

		$str .= '<a href="'.route('workflow.place', [$id]).'">设置places</a> | ';
		$str .= '<a href="'.route('workflow.transition', [$id]).'">设置transitions</a>';

		return $str;
	}

	public function place($id)
	{
		$workflowId = $id;

		$places = Workflow::find($workflowId)->places;

		return app('view')->make('workflow::workflow.place')->with(compact('workflowId', 'places'));
	}

	public function placeSet($workflowId)
	{
		if($places = request('place', [])) {
			$placeIds = [];
			foreach($places as $key => $place) {
				if($place) {
					if(starts_with($key, 'update')){
						$id = str_replace('update', '', $key);
						$info = Place::where('id', $id)->update([
							'workflow_id' => $workflowId,
							'name' => $place
						]);
						$placeIds[] = $id;
					}else{
						$info = Place::create([
							'workflow_id' => $workflowId,
							'name' => $place
						]);
						$placeIds[] = $info->id;
					}
				}
			}

			Place::whereNotIn('id', $placeIds)->where('workflow_id', $workflowId)->delete();
		}

		return redirect()->route('workflow.index');
	}

	public function transition($workflowId)
	{
		$places = Place::where('workflow_id', $workflowId)->get();

		$arcs = Arc::where('workflow_id', $workflowId)->with(['placeFrom', 'placeTo', 'transition'])->get();

		return app('view')->make('workflow::workflow.transition')->with(compact('workflowId', 'places', 'arcs'));

	}

	public function transitionSet($workflowId)
	{
		// dd(request()->all());
		if($transitions = request('transition', [])) {
			$transitionIds = [];
			$arcIds = [];
			$froms = request('placeFrom', []);
			$tos = request('placeTo', []);
			foreach($transitions as $key => $transition) {
				if($transition) {
					if(starts_with($key, 'update')){
						$id = str_replace('update', '', $key);
						$info = Transition::where('id', $id)->update([
							'desc' => isset($transition['desc']) ? $transition['desc'] : '',
							'name' => $transition['name'],
							'slug' => $transition['slug'],
						]);
						$transitionIds[] = $id;

						$arcInfo = Arc::where('workflow_id', $workflowId)->where('transition_id', $id)->first();
						$arcInfo->from = isset($froms[$key]) ? $froms[$key] : 0;
						$arcInfo->to = isset($tos[$key]) ? $tos[$key] : 0;
						$arcInfo->save();

						$arcIds[] = $arcInfo->id;
					}else{
						$info = Transition::create([
							'workflow_id' => $workflowId,
							'desc' => isset($transition['desc']) ? $transition['desc'] : '',
							'name' => $transition['name'],
							'slug' => $transition['slug'],
						]);
						$transitionIds[] = $info->id;

						$arcInfo = Arc::create([
							'workflow_id' => $workflowId,
							'transition_id' => $info->id,
							'from' => isset($froms[$key]) ? $froms[$key] : 0,
							'to' => isset($tos[$key]) ? $tos[$key] : 0,
						]);

						$arcIds[] = $arcInfo->id;
					}
				}
			}

			Transition::whereNotIn('id', $transitionIds)->where('workflow_id', $workflowId)->delete();
			Arc::whereNotIn('id', $arcIds)->where('workflow_id', $workflowId)->delete();
		}

		return redirect()->route('workflow.index');

	}
}