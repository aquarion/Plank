<?PHP

class Plank_View {

	private $template = null;

	function __construct($templateDir, $template){
		$this->template = $this->findFile($templateDir, $template);
		
	}
	
	function findFile($templateDir, $template){
		
		$config = Plank_Config::getInstance();
		
		$this->config = $config->getAll();
		
		if (! $dir = $config->get('locations', 'view_directory') ){
			$dir = CODE_PATH.'/Templates';
		}
		
		
		$file = '/'.$templateDir.'/'.$template.'.template.php';
		if (file_exists($dir.$file)){
			return $dir.$file;
		} elseif (file_exists(PLANK_PATH.'/Plank/templates'.$file)){
			
			if (!is_readable(PLANK_PATH.'/Plank/templates'.$file)){
				throw new Plank_Exception_View('View '.$templateDir.'/'.$template.' is not readable by me.');
			} else {
				return PLANK_PATH.'/Plank/templates'.$file;
			}
		} else {
			Plank_Logger::log('View', "View not found ".$dir.$file, L_FATAL);
			throw new Plank_Exception_View_NotFound('View '.$templateDir.'/'.$template.' not found');
		}
	}
	
	function render(){

		if ($this->template === null){
			throw new Plank_Exception_View('Template Not Set');			
		}
		
		else {
			ob_start();
			include($this->template);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
			
		}		
		
	}
	
	function partial($templateDir, $template, $data = array(), $as_string = false){
		
		foreach($data as $index=>$value){
			$this->$index = $value;
		}
		
		$file = $this->findFile($templateDir, $template);
		if ($as_string){
			ob_start();
		}		
		include($file);
		if ($as_string){
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
	
	function replace($tag, $default){
		
		if (isset($this->$tag)){
			Plank_Logger::log('View', 'Replacing tag '.$tag, L_DEBUG);
			return $this->$tag;
			
		}
		
		Plank_Logger::log('View', 'Leaving tag '.$tag, L_DEBUG);
		return $default;
		
	}

}
