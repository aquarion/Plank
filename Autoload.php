<?PHP

class Plank_Autoload {

	static function loadClass($class_name){
		if (defined('INIT')){	
			Plank_Logger::log('Autoloader', 'Loading class '.$class_name, L_INFO);
		}
		
		error_log('Loading '.$class_name);
		
		$found = Plank_Autoload::findClass($class_name);
		
		if ($found){
			defined('INIT') ? Plank_Logger::log('Autoloader', 'Found '.$found, L_TRACE) : false;
			require($found);
			return true;
		} else {
			error_log('CRITICAL ERROR LOADING '.$class_name);
			global $response;
			Plank_Error::Error503('Code Error: Fatal Error Loading '.$class_name, $response);
			$response->respond();
		}
	}
	
	static function findClass($class_name){
		if (defined('INIT')){
			$config = Plank_Config::getInstance();	
			
						
			if ($pathconfig = $config->get('system', "librarypath")){
				$searchpath = PLANK_PATH.':'.CODE_PATH.':'.$pathconfig.':'.ini_get('include_path');
				$searchpath = explode(':',$searchpath);
			} else {
				$searchpath = array(explode(PLANK_PATH.':'.CODE_PATH.':'.ini_get('include_path')));
			}		
			
		} else {
			$searchpath = array(PLANK_PATH, CODE_PATH);
		}
		
		$found = 0;
		
		$filename = implode('/',explode('_',$class_name)).'.php';
		foreach($searchpath as $path){
			$path .= '/';
			defined('INIT') ? Plank_Logger::log('Autoloader', 'Looking for '.$path.$filename, L_TRACE) : false;
			if (file_exists($path.$filename)){
				return $path.$filename;
			}
		}
		
	}
}