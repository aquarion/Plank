<?PHP


class Plank_Controller_Index extends Plank_Controller{
	
	
	function IndexAction(){
		$view = new Plank_View('Errors', 'DefaultPage');
		
		$this->response->setContent($view->render());		
	}
}
