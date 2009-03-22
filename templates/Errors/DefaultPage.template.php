<head>
	<title>Plank Error</title>

<style type="text/css">
body {
	font-family: "Trebuchet MS" sans-serif;
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
	<h1>Plank</h1>
	<h2>Hello World</h2>
	
	<p>This is the default page for a new Plank site. You probably want to do some development now.</p>
	
	<h3>How we chose what to display:</h3>
	
	<p>Plank autoloads classes. This means the naming scheme's a bit important. The format of the classes is that it will turn all the underscores into slashes and look for a file there. So a class called <sample>Plank_Controller_Index</sample> will be in <sample>$APPROOT/Plank/Controller/Index.php</sample>
	
	<p>In the config file (<sample>$APPROOT/config.ini</sample>), you set an Application Prefix. For the sake of this, we'll pretend you've set it to "<span class="prefix">APP</span>". This means that the first thing the Site object will do is see if there's a class called "<span class="prefix">APP</span>_Routing" (Which you would place in <sample>$APPROOT/<span class="prefix">APP</span>/Routing.php</sample>). If it doesn't find one, it will use "Plank_Routing", the default file (That's probably what it's using now) (Note that this preserves case. You can't make the prefix <span class="prefix">APP</span> and then refer to App, though that will work if your development stack is on Windows)</p>
	
	<p>Plank_Routing's theory of the universe is that URLs are broken up as <sample>http://Sitename.tld/<span class="controller">mycontroller</span>/<span class="action">myaction</span>/otherstuff</sample>. It ignores <sample>otherstuff</sample> entirely, and attempts to load a class called <sample><span class="prefix">APP</span>_Controller_<span class="controller">mycontroller</span></sample>. If it can't do this on the FrontPage controller (Called "Index" by default (so <sample><span class="prefix">APP</span>_Controller_<span class="controller">Index</span></sample>) or setable in config.ini), it'll attempt to load the Plank index file you're looking at now. If it can't do this, or it's not on the front page it will 404.</p>
	
	<p>If it succeeds, it will then attempt to call a method called <span class="action">myaction</span>Action on the controller. If you don't specify an action (so the URL is <sample>http://Sitename.tld/<span class="controller">mycontroller</span>/</sample>) that too will default to Index for the action, so it will look for <sample><span class="prefix">APP</span>_Controller_<span class="controller">mycontroller</span>-&gt;IndexAction().</sample></p>
		
	<p>When the controller is instantiated, it will be given a Request and a Response object. Very simply, the controller's job is to use what's in the Request object (which is everything sent to the server) to fill in the Response object. Mostly you'll do this by initialising a View and then rendering it into the <sample>$response-&gt;setContent()</sample> method. You're best off looking at how <sample>Plank_Controller_Index</sample> does that. It's not complicated, but I haven't finished writing the views system as I write this document, so anything said here is likely to be inaccuate :)</p>
		
		
	<p class="smallprint">This is Plank. Plank is the thing behind this website. It was written by Nicholas Avenell in 2009</p>
		
</div>

