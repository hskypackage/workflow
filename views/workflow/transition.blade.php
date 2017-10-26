@extends('workflow::layout')

@section('css')
@endsection
@section('content')

	<div class="portlet light portlet-fit portlet-datatable bordered">
	  <div class="portlet-title">
	    <div class="actions">
	      
	    </div>
	  </div>
		<div class="portlet-body">
		  <div class="table-container" >
		    <form action="{{ route('workflow.transition.set', [$workflowId]) }}" method="post">
		    	<div class="arcdiv">
		    		@if($arcs->isNotEmpty())
		    			@foreach($arcs as $arc)
								<div>
									<p>
										<label>动作名称<input type="text" name="transition[update{{$arc->transition->id}}][name]" value="{{$arc->transition->name}}"></label>
										<label>slug<input type="text" name="transition[update{{$arc->transition->id}}][slug]" value="{{$arc->transition->slug}}"></label>
										<a class="btn btn-danger delete_transition">删除</a>
									</p>
									<p>
										<label>
											from : 
											<select name="placeFrom[update{{$arc->transition->id}}]">
												@if($places->isNotEmpty())
													@foreach($places as $place)
														<option value="{{$place->id}}" @if($arc->placeFrom && $arc->placeFrom->id == $place->id) selected @endif>{{$place->name}}</option>
													@endforeach
												@endif
											</select>
										</label>
										<label>
											to : 
											<select name="placeTo[update{{$arc->transition->id}}]">
											@if($places->isNotEmpty())
													@foreach($places as $place)
														<option value="{{$place->id}}" @if($arc->placeTo && $arc->placeTo->id == $place->id) selected @endif>{{$place->name}}</option>
													@endforeach
												@endif
											</select>
										</label>
									</p>
								</div>
		    			@endforeach
		    		@endif
		    	</div>
  	    	<a class="btn btn-primary" onclick="add_transition()">添加</a>
  	    	<input class="btn btn-success" type="submit" name="提交">
		    </form>
		  </div>
		</div>
	</div>
@endsection

@section('js')
	<script type="text/javascript">
		var selectPlace = '';
		@if($places->isNotEmpty())
			@foreach($places as $place)
				selectPlace += '<option value="{{$place->id}}">{{$place->name}}</option>';
			@endforeach
		@endif

		var index = 1;
		function add_transition()
		{
			var html = '<div><p><label>动作名称<input type="text" name="transition['+index+'][name]" value=""></label><label>slug<input type="text" name="transition['+index+'][slug]" value=""></label><a class="btn btn-danger delete_transition">删除</a></p><p><label>from : <select name="placeFrom['+index+']">'+selectPlace+'</select></label><label>to : <select name="placeTo['+index+']">'+selectPlace+'</select></label></p></div>';

			$('.arcdiv').append(html);
			index += 1;
		}

		$(document).on('click', '.delete_transition', function(){
			$(this).parent().parent().remove();
		});
	</script>
@endsection