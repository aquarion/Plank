<?PHP

class Plank_Singleton {
	
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
 
	# End Singleton Zen
	
	
	function __construct(){
        Plank_Logger::log('Singleton', get_class($this).' singleton created.', L_TRACE);
	}
	
}