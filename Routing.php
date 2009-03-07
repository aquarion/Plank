<?PHP

class Plank_Routing {

	public $controller;
	public $action;

	public $foundRoute = 0;	

	function __construct(Plank_HTTP_Request $request){
	
		$uri = trim($request->uri,'/');
		
		if(strpos($uri, '?') !== false){
			Plank_Logger::log('Routing', 'A Query String', L_INFO);
			list($uri, $query) = explode('?', $uri);
		}
		
		$path = explode('/',$uri);
		
		if (isset($path[0]) && empty($path[0])){
			array_pop($path);
		}
				
		switch (count($path)){
			case 0:
				Plank_Logger::log('Routing', 'Routing to front page', L_INFO);
				$config = Plank_Config::getInstance();
				if ($contoller = $config->get('routing', 'frontcontroller')){
					$this->controller = $controller;
				} else {
					$this->controller = 'Index';
				}
				$this->action = 'Index';
				break;
				
			case 1:
				Plank_Logger::log('Routing', 'Routing to controller '.$path[0].' index', L_INFO);
				$this->controller = $path[0];
				$this->action     = 'Index';
				break;
			
			default:
				Plank_Logger::log('Routing', 'Routing to controller '.$path[0].' method '.$path[1], L_INFO);
				$this->controller = $path[0];
				$this->action     = $path[1];
				break;
		
		}
		
		$this->foundRoute = 1;	
				
	}

}