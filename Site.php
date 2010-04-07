<?PHP
/***

	Plank Site
	
	Site is the main execution model of the system for a website.
	
	In to it comes a request & response object, and we modify the response
	object until we get to the end and throw it back up for display.
	
	Notable are:
	
		Plank_Site_Initialise - a class for seting up an environment and doing
				 what needs to be done afterwards. Sessions is a good example.
		
		Plank_Routing         - work out what controller to execute.

*/


class Plank_Site {
	

	function __construct($request, $response){
		
		$config = Plank_Config::getInstance();
		
		if (!$app_prefix = $config->get('system', 'application_prefix')){
			$app_prefix = 'Plank';
		}

		$init = false;
		if (Plank_Autoload::findClass($app_prefix.'_Site_Initialise')) {
			Plank_Logger::log('Initialise', 'Loading '.$app_prefix.'_Site_Initialise', L_DEBUG);
			$class = $app_prefix.'_Site_Initialise';	
			$init = new $class($request, $response);
		} else {
			$init = new Plank_Site_Initialise($request, $response);
		}
		
		if ($init){
			$init->preroute();
		}
		
		if (Plank_Autoload::findClass($app_prefix.'_Site_Routing')) {
			Plank_Logger::log('Router', 'Loading '.$app_prefix.'_Routing as a routing file', L_DEBUG);
			$class_name = $app_prefix.'_Site_Routing';
		} else {
			Plank_Logger::log('Router', 'Using Plank default routing table', L_DEBUG);
			$class_name = 'Plank_Routing';
		}
			
		$routing = new $class_name($request);
	
		if (!$routing->foundRoute){
			Plank_Logger::log('Router', 'Routing failed', L_FATAL);
			$response->setError('I couldn\'t find a route for that URL');
			$response->setStatus(404);
			return;
		}
		
		$init->postroute($routing->controller, $routing->action);
		
		$controllername = $app_prefix.'_Controller_'.$routing->controller;
		
		if (Plank_Autoload::findClass($controllername)){
			# Yay
		} elseif (Plank_Autoload::findClass('Plank_Controller_'.$routing->controller)) {
			$controllername = 'Plank_Controller_'.$routing->controller;
		} else {
			Plank_Logger::log('Router', 'There is no such thing as '.$controllername, L_WARN);
			$response->setError('I couldn\'t load the controller \''.$routing->controller.'\'');
			$response->setStatus(404);
			return;
		}
		
		$controller = new $controllername($request, $response);
		
		
		if (! is_a($controller, "Plank_Controller")){
			throw new Plank_Exception("$controllername must be a subclass of Plank_Controller");
		}
		
		$init->gotcontroller($controller, $routing->action);
		
		$method = $routing->action.'Action';
		
		$controller->$method();
		
		
		$init->shutdown($routing->controller, $routing->action);
		
	}
	
}