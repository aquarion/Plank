<?PHP




class Plank_Error {
	
	
	static function __call($function, $message){
		
		Plank_Error::defaultError('503', $message, $response);
	}
	
	static function Error503($message, $response){
		
		Plank_Error::defaultError('503', $message, $response);
	}
	
	
	function defaultError($status, $message, $response){
		$trace = debug_backtrace();
		
		if(is_object($message) && is_subclass_of($message, 'Exception')){
			$trace = $message->getTrace();
			$message = $message->getMessage();
		}
		
		if(defined('DESTRUCT')){
			die($message);
		}
	
		
		$view = new Plank_View('Errors', 'Error503');
		
				
		$view->error = '		
		<p>Something\'s drastically wrong. Sorry about this, this is a last-ditch fail message, caused by terror upon terror to have been inflicted upon my poor innocent paradiems. Nothing\'s worked. The thing you asked for? Failed. The recovery from error system? Broken. The error display system itself? Fucked. I have no idea what to try next, so I\'m going to throw data at you until you go away.</p>
				
		<h2>'.$message.'</h2>
		
		';
		
		$view->error .= Plank_Error::getBacktrace($trace);
		
		$response->setContent($view->render());
		
		$response->respond();
		
		echo Plank_Logger_Display::display();
		die('');
		
	}
	
	function getBacktrace($trace = null){
		
		if (is_null($trace)){
			$trace = debug_backtrace();
		}
		#$trace = array_slice($trace, 1);
		
		$output = '
		<table>
			<tr><th>Function/Method</th><th>File</th><th>Line</th><th>Args</th></tr>';
		
		$sprintf = '<tr><td>%s</td><td>%s</td><td>%s</td><td><a href="#" onClick="document.getElementById(\'%s\').style.display = \'block\'; this.style.display = \'none\'">Args</a><div id="%s" style="display: none;">%s</div></td></tr>';
		foreach($trace as $t){
			$function = isset($t['class']) ?  $t['class']. $t['type']. $t['function'] : $t['function'];
			
			$id = uniqid();
			
			
			//$file = str_replace(getcwd(), 'CWD', $t['file']);
			$file = str_replace(realpath(CODE_PATH), '[APP]', $t['file']);
			
			$output .= sprintf($sprintf, $function, $file, $t['line'], $id, $id, print_r($t['args'],1));
			
		}
		
		$output .= '</table>';

		return $output;
		
	}
}
