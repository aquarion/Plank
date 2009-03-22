<?PHP

class Plank_HTTP_Response {
	
	private $content = '<hr><i>No content provided to response</i>';
	private $content_type = 'text/xml';
	private $status_code  = '200';
	private $status_message = 'All arite, Captain';
	
	private $cookies= array();
	
	private $etag = '';
	private $http_version = '1.1';
	
	
	public function respond(){
		header('HTTP '.$this->http_version.' '.$this->status_code.' '.$this->status_message);
		
		foreach($this->cookies as $cookie){
			list($name, $value, $expire, $path, $domain, $secure, $httponly) = $cookie;
			setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
		}
		
		echo $this->content;
	}
	
	public function setcontent($content){
		$this->content = $content;
		
	}
	
	
	public function seterror($content){
		$this->errortext = $content;
		
	}
	
	public function setcookie($name, $value, $expire = 0, $path = '/' , $domain = false , $secure=false, $httponly=false){
		$expire = strtotime($expire);
		$this->cookies[$name] = array($name, $value, $expire, $path, $domain, $secure, $httponly);
		Plank_Logger::log('Response', 'Setting cookie '.$name.' to '.$value, L_TRACE);
	}
	
	public function setstatus($code){
		$this->status_code = $code;
		
		switch ($code){
			case '404':
				$this->status_message = 'Does not exist';
				try {
					$view = new Plank_View('Errors', 'Error404');
					
					$view->error = $this->errortext; 
					
					$this->content = $view->render();
				} catch ( Plank_Exception_View_NotFound $e ) {
					$this->content = '
					<h1>File Not Found</h1>
					
					<p>Sorry folks, that doesn\'t exist here. The message would be prettier, but that\'s missing too. </p>			
					';			
				}
				break;
				
			case '503':
				$this->status_message = 'Something\'s screwed';
				try {
					$view = new Plank_View('Errors', 'Error503');
					$this->content = $view->render();
				} catch ( Plank_Exception_View_NotFound $e ) {
					$this->content = '
					<h1>System Error</h1>
					
					<p>Something\'s gone wrong. Try again later? Maybe</p>			
					';			
				}
				break;
		}
		

		
	}
}
