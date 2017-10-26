<?php 

Route::group(['namespace' => 'HskyZhou\Workflow\Controllers'], function(){

	Route::group(['prefix' => 'workflow', 'as' => 'workflow.'], function(){
		Route::get('/', [
			'uses' => 'WorkflowController@index',
		]);

		Route::get('datatables', [
			'uses' => 'WorkflowController@datatables',
			'as' => 'datatables'
		]);

		/*设置库所*/
		Route::get('place/{id}', [
			'uses' => 'WorkflowController@place',
			'as' => 'place'
		]);

		Route::post('place/set/{id}', [
			'uses' => 'WorkflowController@placeSet',
			'as' => 'place.set'
		]);

		Route::get('transition/{id}', [
			'uses' => 'WorkflowController@transition',
			'as' => 'transition'
		]);

		Route::post('transition/set/{id}', [
			'uses' => 'WorkflowController@transitionSet',
			'as' => 'transition.set'
		]);
	});

	Route::resource('workflow', 'WorkflowController');
});