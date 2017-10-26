<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="{{asset('workflows/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
	@yield('css')
</head>
<body>
	@yield('content')
	
	<script type="text/javascript" src="{{asset('workflows/jquery.min.js')}}"></script>
	@yield('js')
</body>
</html>