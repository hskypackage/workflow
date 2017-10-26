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
		    <form action="{{ route('workflow.place.set', [$workflowId]) }}" method="post">
		    	<div class="placediv">
		    		@if($places->isNotEmpty())
		    			@foreach($places as $place)
								<p>
									<label>库所名称<input type="text" name="place[update{{$place->id}}]" value="{{$place->name}}"></label>
									<a class="btn btn-danger delete_palce">删除</a>
								</p>
		    			@endforeach
		    		@endif
		    	</div>
  	    	<a class="btn btn-primary" onclick="add_place()">添加</a>
  	    	<input class="btn btn-success" type="submit" name="提交">
		    </form>
		  </div>
		</div>
	</div>
@endsection

@section('js')
	<script type="text/javascript">
		function add_place()
		{
			var html = '<p><label>库所名称<input type="text" name="place[]"></label><a class="btn btn-danger delete_palce">删除</a></p>';

			$('.placediv').append(html);
		}

		$(document).on('click', '.delete_palce', function(){
			$(this).parent().remove();
		});
	</script>
@endsection