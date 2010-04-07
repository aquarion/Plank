<?PHP


class Plank_Controller {
	
	function __construct(Plank_HTTP_Request $request, Plank_HTTP_Response $response){
		$this->request = $request;
		$this->response = $response;
	}
	
	function __call($method, $params){
		
		Plank_Logger::log('Controller', get_class($this).' doesn\'t have an action called '.$method, L_WARN);
		$this->response->setError('Controller '.get_class($this).' doesn\'t have an action called '.$method);
		$this->response->setStatus(404);
	}
}
