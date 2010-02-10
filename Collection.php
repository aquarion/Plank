<?PHP

// This represents a collection of objects of any kind

class Plank_Collection  implements Iterator {

	private $collection = array();
	private $type = 'Objects';
	private $sort = '';
	
	
	private $position = 0;
	
	function __construct($array = false){
        $this->position = 0;
		if($array){
			$this->collection = $array;
		} else {
			$this->collection = array();
		}
	}
	
	function __toString(){
		return "A collection of ".count($this->collection)." ".$this->type." things";
	}
	
	function setType($type){
		$this->type = $type;
	}
	
	static function getLastTen($type, $field){
		
		$obj = new $type();
		
		
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
    function rewind() {
        #var_dump(__METHOD__);
        $this->position = 0;
    }

    function current() {
        #var_dump(__METHOD__);
        return $this->collection[$this->position];
    }

    function key() {
       # var_dump(__METHOD__);
        return $this->position;
    }

    function next() {
        #var_dump(__METHOD__);
        ++$this->position;
    }

    function valid() {
       # var_dump(__METHOD__);
        return isset($this->collection[$this->position]);
    }
    
    function add($that){
    	$this->collection[] = $that;
    }
    
    function remove($id){
    	unset($this->collection[$id]);
    }

}
