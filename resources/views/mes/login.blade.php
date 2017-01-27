<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="{{asset('resources/css/ch-ui.admin.css')}}">
	<link rel="stylesheet" href="{{asset('resources/font/css/font-awesome.min.css')}}">
</head>
<body style="background:#F3F3F4;">
	<div class="login_box">
		<h1>MES</h1>
		<h2>欢迎使用福联MES平台</h2>
		<div class="form">
			@if(session('msg'))
			<p style="color:red">{{session('msg')}}</p>
			@endif
			<form action="#" method="post">
				{{csrf_field()}}
				<ul>
					<li>
					<input type="text" name="user_name" class="text"/>
						<span><i class="fa fa-user"></i></span>
					</li>
					<li>
						<input type="password" name="pwd" class="text"/>
						<span><i class="fa fa-lock"></i></span>
					</li>
					<li>
						<input type="text" class="code" name="code" autocomplete="off"/>
						<span><i class="fa fa-check-square-o"></i></span>
						<img src="code" alt="" onclick="this.src='{{url('/code')}}?'+Math.random()">
					</li>
					<li>
						<input type="submit" value="立即登陆"/>
					</li>
				</ul>
			</form>
			<p><a href="#">返回首页</a> &copy; 2017 Powered by <a href="http://www.unicompound.com" target="_blank">http://www.unicompound.com</a></p>
		</div>
	</div>
</body>
</html>