<?PHP

class Plank_Routing {

	public $controller;
	public $action;

	public $foundRoute = 0;	

	function __construct(Plank_HTTP_Request $request){
	
		$path = $request->path;
				
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
				if (empty($path[1])){
					$path[1] = 'Index';
				}
				Plank_Logger::log('Routing', 'Routing to controller '.$path[0].' method '.$path[1], L_INFO);
				$this->controller = $path[0];
				$this->action     = $path[1];
				break;
		
		}
		
		$this->foundRoute = 1;	
				
	}

}