<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" context="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>Storage Performance Benchmarker [kvaes.be]</title>
    <meta name="description" content="">

	<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/superhero/bootstrap.min.css" rel="stylesheet" integrity="sha384-Xqcy5ttufkC3rBa8EdiAyA1VgOGrmel2Y+wxm4K3kI3fcjTWlDWrlnxyD6hOi3PF" crossorigin="anonymous">
    
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	
	<?php if ($script <> "") { echo $script; } ?>
	
	<!-- 
	To collect end-user usage analytics about your application, 
	insert the following script into each page you want to track.
	Place this code immediately before the closing </head> tag,
	and before any other scripts. Your first data will appear 
	automatically in just a few seconds.
	-->
	<script type="text/javascript">
	  var appInsights=window.appInsights||function(config){
		function i(config){t[config]=function(){var i=arguments;t.queue.push(function(){t[config].apply(t,i)})}}var t={config:config},u=document,e=window,o="script",s="AuthenticatedUserContext",h="start",c="stop",l="Track",a=l+"Event",v=l+"Page",y=u.createElement(o),r,f;y.src=config.url||"https://az416426.vo.msecnd.net/scripts/a/ai.0.js";u.getElementsByTagName(o)[0].parentNode.appendChild(y);try{t.cookie=u.cookie}catch(p){}for(t.queue=[],t.version="1.0",r=["Event","Exception","Metric","PageView","Trace","Dependency"];r.length;)i("track"+r.pop());return i("set"+s),i("clear"+s),i(h+a),i(c+a),i(h+v),i(c+v),i("flush"),config.disableExceptionTracking||(r="onerror",i("_"+r),f=e[r],e[r]=function(config,i,u,e,o){var s=f&&f(config,i,u,e,o);return s!==!0&&t["_"+r](config,i,u,e,o),s}),t
		}({
			instrumentationKey:"145bdfef-e5ce-44c7-864e-55c638540559"
		});
		   
		window.appInsights=appInsights;
		appInsights.trackPageView();
	</script>
	
</head>
<body>
<div class="container">
	<div class="navbar navbar-inverse">
	  <div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-inverse-collapse">
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		  <span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="/">Home</a>
	  </div>
	  <div class="navbar-collapse collapse navbar-inverse-collapse">
	    <ul class="nav navbar-nav">
		  <li class="dropdown">
			<a href="https://github.com/kvaes/storage-benchmarker-script" target="_blank">Storage Performance Benchmarker Script</a>
		  </li>
		</ul>
		<ul class="nav navbar-nav">
		  <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Data <b class="caret"></b></a>
			<ul class="dropdown-menu">
			  <li class="dropdown-header">Systems</li>
			  <li><a href="/system/">Public / Shared Systems</a></li>
			  <li><a href="/system/private">My Private Systems</a></li>
			</ul>
		  </li>
		</ul>
		<ul class="nav navbar-nav navbar-right">
		  <li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Information<b class="caret"></b></a>
			<ul class="dropdown-menu">
			  <li class="dropdown-header">Organization</li>
			  <li><a href="/info/about">About</a></li>
			  <li><a href="/info/privacy">Privacy</a></li>
			  <li><a href="/info/legal">Legal</a></li>
			  <li><a href="/info/contact">Contact</a></li>
			</ul>
		  </li>
		</ul>
	  </div>
	</div>
	
