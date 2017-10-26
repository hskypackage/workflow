@extends('workflow::layout')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('workflows/datatables/datatables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('workflows/datatables/plugins/bootstrap/datatables.bootstrap.css')}}">
@endsection
@section('content')

	<div class="portlet light portlet-fit portlet-datatable bordered">
	  <div class="portlet-title">
	    <div class="actions">
	      <div class="btn-group">
	        <a href="{{ route('workflow.create') }}" class="btn btn-success btn-outline btn-circle">
	          <i class="fa fa-user-plus"></i>
	          <span class="hidden-xs">添加</span>
	        </a>
	      </div>
	    </div>
	  </div>
		<div class="portlet-body">
		  <div class="table-container" >
		    <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
		        <thead>
		            <tr role="row" class="heading">
		              <th style="min-width: 40px;">#</th>
		              <th width="15%"  style="min-width:114px;">名称</th>
		              <th width="15%"  style="min-width:114px;">创建时间</th>
		              <th width="15%"  style="min-width:114px;">操作</th>
		            </tr>
		            
		        </thead>
		        <tbody> </tbody>
		    </table>
		  </div>
		</div>
	</div>
@endsection

@section('js')
<script type="text/javascript" src="{{asset('workflows/datatables/datatables.all.min.js')}}"></script>

<script type="text/javascript">
	$(document).ready(function(){
		ajax_datatable = $("#datatable_ajax").DataTable({
		  "processing": true,
		  "serverSide": true,
		  "searching" : false,
		  "ajax": {
		    'url' : '{{route('workflow.datatables')}}',
		    "data": function ( d ) {
		      
		    }
		  },
		  "pagingType": "bootstrap_full_number",
		  "order" : [],
		  "orderCellsTop": true,
		  "dom" : "<'row'<'col-sm-3'l><'col-sm-6'<'customtoolbar'>><'col-sm-3'f>>" +"<'row'<'col-sm-12'tr>>" +"<'row'<'col-sm-5'i><'col-sm-7'p>>",
		  "columns": [
		    {
		      "data": "id",
		      "name" : "id",
		      'class' : 'text-center'
		    },
		    {
		      "data": "name",
		      "name" : "name",
		      "orderable" : false,
		    },
		    {
		      "data": "created_at",
		      "name": "created_at",
		      "orderable" : false,
		    },
		    { 
		      "data": "button",
		      "name": "button",
		      "type": "html",
		      "orderable" : false,
		    },
		  ],
		  "drawCallback": function( settings ) {
		    
		  }
		});
	});
</script>
@endsection