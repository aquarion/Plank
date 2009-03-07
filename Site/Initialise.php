<?PHP

class Plank_Site_Initialise {
	
	Protected   $request;
	Protected   $response;
	
	function __construct(Plank_HTTP_Request $request,  Plank_HTTP_Response $response){
		//Plank_Logger::log('Initialise', 'Hi '.$request, L_DEBUG);
		
		$this->request  = $request;
		$this->response = $response;
	}
	
	
	function __call($method, $params){
		Plank_Logger::log('Initialise', 'Init Hook '.$method.' undefined', L_DEBUG);
	}
}