<?PHP


class Plank_Config {
    
	# Begin Singleton Zen
    static private $_instance;
        
    static function getInstance()
    {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
 
    private function __construct ()
    {
        Plank_Logger::log('Singleton', get_class($this).' singleton created.', L_TRACE);
        $this->init();
    }
	# End Singleton Zen
    
	
	private $data = array();
	
	function init(){
		if (file_exists(CODE_PATH.'/config.ini')){
			if (is_readable(CODE_PATH.'/config.ini')){
		 		$this->data = parse_ini_file(CODE_PATH.'/config.ini', true);
	 		} else {
	 			throw new Plank_Exception_ConfigError('Config File isn\'t readable');
	 		}
		} else {
			header("HTTP/1.0 503 Service Unavailable");
			die('Could not find config file.');		
			throw new Plank_Exception_ConfigError('Where\'s my config file?');		
		}
		
	}
	
	function get($area, $value){
		if (isset($this->data[$area]) && isset($this->data[$area][$value])){
			return $this->data[$area][$value];
		}		
		Plank_Logger::log('Config', "Couldn't get $area/$value", L_DEBUG);
		return false;
	}
	
	function getArea($area){
		if (isset($this->data[$area])){
			return $this->data[$area];
		}		
		Plank_Logger::log('Config', "Couldn't get $area", L_TRACE);
		return false;
	}
	
	function getAll(){
		return $this->data;
	}
}
