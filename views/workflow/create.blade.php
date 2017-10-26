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
		    <form action="{{ route('workflow.store') }}" method="post">
		    	<div>
		    		<label>名称</label>
			    	<input type="text" name="name">
		    	</div>

		    	<div>
		    		<label>model</label>
			    	<input type="text" name="model">
		    	</div>

  	    	<div>
  	    		<label>desc</label>
  		    	<textarea name="desc"></textarea>
  	    	</div>

  	    	<input type="submit" name="提交">
		    </form>
		  </div>
		</div>
	</div>
@endsection

@section('js')

@endsection