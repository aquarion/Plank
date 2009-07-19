<?PHP

class Plank_HTTP_Request {
	
	public $uri;
	public $get;
	public $post;
	public $cookies;
	public $client_ip;
	public $user_agent;
	public $keep_alive;
	public $connection;
	public $accept_content;
	public $accept_charset;
	public $accept_languages;

	
	function __construct(){
		
		
      Plank_Logger::log('Request', 'Init Request with '.count($_COOKIE).' cookies', L_TRACE);
		
		$this->post             = (object)$_POST;
		$this->get              = (object)$_GET;
		$this->cookies          = (object)$_COOKIE;
		$this->user_agent       = @$_SERVER['HTTP_USER_AGENT'];
		$this->accept_content   = explode(',', $_SERVER['HTTP_ACCEPT']);
		$this->accept_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$this->accept_charset   = explode(',', $_SERVER['HTTP_ACCEPT_CHARSET']);
		
		$this->keep_alive       = @$_SERVER['HTTP_KEEP_ALIVE'];
		$this->connection       = @$_SERVER['HTTP_CONNECTION'];
		
		$this->client_ip        = @$_SERVER['REMOTE_ADDR'];
		
		$this->uri              = @$_SERVER['REQUEST_URI'];
		
		foreach((array) $this->post as $index => $value){
			$this->post->$index = stripslashes($value);
		}
		foreach((array) $this->get as $index => $value){
			$this->get->$index = stripslashes($value);
		}

		
	}	
	
	
	
}


?>
