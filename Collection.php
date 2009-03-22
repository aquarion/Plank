<?PHP

// This represents a collection of objects of any kind

class Plank_Collection  implements Iterator {

	private $collection = array();
	private $type = 'Objects';
	private $sort = '';
	
	function __construct($array = false){
		if($array){
			$this->collection = $array;
		}
	}
	
	function __asString(){
		return "A collection of ".count($this->collection)." $type things";
	}
	
	function setType($type){
		$this->type = $type;
	}
	
	static function getLastTen($type, $field){
		
		$obj = new $type();
		$this->type = $type;
		
		
		$collection =  new Plank_Collection($obj->_collection_list($field, 10 ) );
		$collection->setType($type);
		return $collection;
	}
	
	static function find($type, $field, $value, $op = '=', $sort = null, $limit= null){
	
		$obj = new $type();
		
	
		$array = $obj->_collection_find($field, $value, $op, $sort, $limit );
		
		$collection = array();
		foreach($array as $element){
			$collection[] = new $type($element);
		}
				
		$collection = new Plank_Collection($collection);
		$collection->setType($type);
		return $collection;
	}

	
	function asArray(){
		return $this->collection;
	}
	
	function sortBy($thing){
		$this->sort = $thing;
		usort($this->collection, array($this, 'compare'));
	}
	
	function compare($a, $b){
		if ($a->get($this->sort) < $b->get($this->sort)){
			return 1;
		} elseif ($a->get($this->sort) > $b->get($this->sort)){
			return -1;
		}
		return 0;
	}
	
	function count(){
		return count($this->collection);
	}
	
	
	// Iterators:

    public function rewind() {
        reset($this->collection);
    }

    public function current() {
        return current($this->collection);
        
    }

    public function key() {
        return key($this->collection);
    }

    public function next() {
        return next($this->collection);
    }

    public function valid() {
        $var = $this->current() !== false;
        return $var;
    }
    
    public function getid($id){
    	return $this->collection[$id];
    }
    
   	public function add($object){
   		if (get_class($object) != $this->type){
   			throw new Plank_Exception(sprintf('Cannot add %s to collection of %ss', get_class($object), $this->type));
   		}
   	}

}
