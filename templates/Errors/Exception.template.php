<?php 
if(defined("TEXTMODE")){
	echo "\n\n";
	echo strip_tags($exception->getMessage())."\n\nStack Trace follows:";
	
	var_dump($exception->getTrace());
	die();
}
?><head>
	<title>Plank Exception</title>

<style type="text/css">
body {
	font-family: "Trebuchet MS", sans-serif;
}

.error h1 {
	color: #840000;
	font-size: 42pt;
	margin-top: 0;
	margin-bottom: 0;
	font-weight: normal;
	padding: 0 20px;
	border-bottom: 1px dotted #840000;	
}
h2, h3 {
	margin-top: 0;
	padding: 0 20px;
}

.error {
	border: 1px solid #840000;	
	padding: 0;
	width: 600px;
	margin-left: auto;
	margin-right: auto;
	font-size: small;
	-moz-border-radius: 30px 60px;
	-webkit-border-radius: 30px 60px;

	
}

.error > p {
	padding: 0 20px;

}

sample {
	font-family: fixed-width;
	background: #f7f7f7;
	padding: 2px;
}

q {
	font-weight: bold;
}

p.smallprint {
	font-size: smaller;
	border-top: 1px dotted #840000;	
	margin-top: 20px;
	padding-top: 15px;
	padding-bottom: 10px;
	text-align: center;
}

span.controller{
	color: blue;
}

span.prefix {
	color: green;
}

span.action {
	color: purple;
}
</style>
</head>
<body>
<div class="error">
	<h1>Plank - Exception</h1>
	
	<h2><?PHP echo $exception->getMessage(); ?></h2>
	
	<p>Stack trace follows:</p>
	<?PHP echo Plank_Error::getBacktrace($exception->getTrace())?>
		
	<p class="smallprint">This is Plank. Plank is the thing behind this website. It was written by Nicholas Avenell in 2009</p>
		
</div>

