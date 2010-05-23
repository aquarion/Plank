<?PHP

if (!include_once 'MDB2.php'){
	Plank_Logger::log('DB', 'Couldn\'t load MDB2, search path:'.ini_get("include_path"), L_FATAL);
	throw new Plank_Exception("MDB2 doesn't appear to be installed");
}

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
    
    function checkerror($result){
    
		if (PEAR::isError($result)) {
			Plank_Logger::log('DB', 'DB Error! '.$result->getMessage().' '.$result->getUserInfo(), L_FATAL);
		   throw new Plank_Exception_Database('Database failed: '.$result->getMessage());
		}
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

				if(PEAR::isError($this->connections[$connection])){
					throw new Plank_Exception_Database('DB Error! '.$this->connections[$connection]->getMessage().' '.$this->connections[$connection]->getUserInfo());
				}    			
	
    			if (SHOWDEBUG){
    				$this->connections[$connection]->setOption('debug', 1);
    				$this->connections[$connection]->setOption('log_line_break', "<br/>");
    			}
    			
    			$this->connections[$connection]->setOption('portability', MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE);
    			
    			$this->connections[$connection]->setFetchMode(MDB2_FETCHMODE_ASSOC);
	    		
	    		if(PEAR::isError($this->connections[$connection])){
	    			throw new Plank_Exception_Database_Connection('Error connecting '.$connection.': '.$this->connections[$connection]->getMessage().' '.$this->connections[$connection]->getUserInfo());
	    		}
			} else {
				throw new Plank_Exception_Database_Config('No DSN for DB Connection '.$connection);
			}
    	}
        
        
    }
	# End Singleton Zen
	
	function query($sql = null, $data = null, $cxn= null){
		if(!$sql){
			throw new Plank_Exception_Database("Query must be set");
		}
		if(!is_null($data) && !is_array($data)){
			throw new Plank_Exception_Database("Data must be an array if it is set (Null otherwise)");
		}
		if(!$cxn){
			throw new Plank_Exception_Database("Must select a connection");
		}
		
		
		$cxn = $this->connection($cxn);
		$query = $cxn->prepare($sql, MDB2_PREPARE_MANIP);
		if (PEAR::isError($query)) {
		   throw new Plank_Exception_Database('Couldn\'t prepare statement: '. $query->getMessage());
		}
		$result = $query->execute($data);
		
		if (PEAR::isError($result)) {
			Plank_Logger::log('DB', 'DB Error! '.$result->getMessage().' '.$result->getUserInfo(), L_FATAL);
		   throw new Plank_Exception_Database('Database failed: '.$result->getMessage().'\n\n'.$result->getUserInfo());
		}
		
		return $result;
		
	}
	
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
