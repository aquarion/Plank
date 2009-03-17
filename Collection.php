<?PHP

// This represents a collection of objects of any kind

class Plank_Collection {

	private $collection = array();
	
	function __construct($array = false){
		if($array){
			$this->collection = $array;
		}
	}
	
	static function getLastTen($type, $field){
		
		$obj = new $type();
				
		return new Plank_Collection($obj->_collection_list($field, 10 ) ) ;
	}
	
	function asArray(){
		return $this->collection;
	}
	

}

?>
