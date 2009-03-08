<?PHP


error_reporting(E_ALL);
//set_error_handler(array('Plank_Logger', 'logPHP'));

class Plank_Logger {
	
	static function log($area, $message, $level){
		if(!defined('SHOWDEBUG')){return;}	
		$log = Plank_Logger_Output::getInstance();
		
		$log->logLine($area, $message, $level);		
	}
	
	
	static function logQuery($area, $message, $query){
		if(!defined('SHOWDEBUG')){return;}
		
		
		$log = Plank_Logger_Output::getInstance();
		$log->logQuery($area, $message, $query);		
	}
	
	static function logStat($area, $thing){
		if(!defined('SHOWDEBUG')){return;}
		$log = Plank_Logger_Output::getInstance();
		$log->logStat($area, $thing);		
	}


	static function dumplog(){
		if(!defined('SHOWDEBUG')){return;}
		$log = Plank_Logger_Output::getInstance();
		$log->dumpLog();
	}
	
	
	static function logPHP($area, $thing){
		if(!defined('SHOWDEBUG')){return;}
		$log = Plank_Logger_Output::getInstance();
		$log->logPHP($area, $thing);		
	}
}


?>