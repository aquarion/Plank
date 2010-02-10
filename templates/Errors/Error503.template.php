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
h2 {
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

acronym {
	cursor: help;
	border-bottom: 1px dotted #840000;
}

</style>

<div class="error">
	<h1>Plank</h1>
	
	<h2>System Error</h2>
	
	<?PHP if (isset($this->error)){?>
	<p><?PHP echo $this->error ?></p>
	
	<?PHP } ?>
	
	
	<p class="smallprint">This is Plank. Plank is the thing behind this website. It was written by Nicholas Avenell in 2009</p>
		
</div>

