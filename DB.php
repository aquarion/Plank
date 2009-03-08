<?PHP

require_once 'MDB2.php';

class Plank_DB {
	
	private $connections;
	
	# Begin Singleton Zen
    static private $_instance;
    static function getInstance()
    {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    
    static function hasInstance(){
        if(empty(self::$_instance)) {
            return false;
        }
        return true;
    }
 
    private function __construct ()
    {
        Plank_Logger::log('Singleton', get_class($this).' singleton created.', L_TRACE);
        $config = Plank_Config::getInstance();
        $config = $config->getArea('database');
        
        if(
        	!$config
        	|| !isset($config['connections'])
        	){
        	throw new Plank_Exception_Database_Config('Databases not configured');
        }
        
        $connections = explode(',',$config['connections']);
        
    	foreach($connections as $connection){
    		if (isset($config['dsn_'.$connection])){    			
    			$this->connections[$connection] = MDB2::factory($config['dsn_'.$connection]);
    			
    			if (SHOWDEBUG){
    				$this->connections[$connection]->setOption('debug', 1);
    				$this->connections[$connection]->setOption('log_line_break', "<br/>");
    			}
    			
    			$this->connections[$connection]->setFetchMode('MDB2_FETCHMODE_ASSOC');
	    		
	    		if(PEAR::isError($this->connections[$connection])){
	    			throw new Plank_Exception_Database_Connection('Error connecting '.$connection.': '.$this->connections[$connection]->getMessage().' '.$this->connections[$connection]->getUserInfo());
	    		}
			} else {
				throw new Plank_Exception_Database_Config('No DSN for DB Connection '.$connection);
			}
    	}
        
        
    }
	# End Singleton Zen
	
	
	static function connection($connection){
		$db = Plank_DB::getInstance();
		return $db->_getConnection($connection);
	}
	
	public function _getConnection($connection){
		if (isset($this->connections[$connection])){
			return $this->connections[$connection];
		}
		throw new Plank_Exception_Database('No such connection '.$connection.' Connections: '.implode(',', array_keys($this->connections)));
	}
	
	public function listConnections(){
		return array_keys($this->connections);
	}
		
}