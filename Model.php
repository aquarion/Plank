<?PHP
abstract class Plank_Model{
	
	
	protected $_dbTable       = false;   // Database Table ID
	protected $_dbNumId       = '';   // Indexed Numeric Identiier
	protected $_dbStrId       = '';   // Index String Identifier
	protected $_dbSecondaryId = '';   // Secondary ID (maybe combined with above for a unique result)
	
	//Fields to save back to the DB
	protected $_dbSaveFields  = ''; // eg 'name,age,bloodtype'
	protected $_dbSaveTypes   = ''; // eg 'text,integer,text'
	
	// Current working set of data.
	protected $data           = null;
	protected $_dbOrigData    = false;// Original dataset from the database
	
	protected $changed        = false;
	
	function __construct($Id = null, $gameId = null){
		if(!$this->_dbTable){
			throw new Plank_Exception_Model('Model DB Table Undefined');
		}
		
		if (is_array($Id)){
		Plank_Logger::log('MDL'.$this->_dbTable, 'Populate from Array '.print_r($Id,1), L_INFO);
			$this->_populateFromArray($Id);
			return;
		}
		
		if (!is_null($Id)){
			$this->_load($Id);
		}
	}
	
	function __destruct(){
		Plank_Logger::log('MDL'.$this->_dbTable, 'Doing destruct save', L_TRACE);
		$this->_save();
	}

	// Load a unique data object from the database
	function _load($id, $secondaryId = null){
		
		Plank_Logger::log('MDL'.$this->_dbTable, 'Loading data from ID '.$id, L_TRACE);
		
		// If the ID is numeric it's probably the primary id.
		if (is_numeric($id)){
			$uniqueColumn = $this->_dbNumId;
		} elseif (!is_null($id)){
			$uniqueColumn = $this->_dbStrId;
		} else {
			throw new Plank_Exception('Cannot load thing with ID of '.$id);
		}
		
		// Initialise database connection		
		$db = Plank_DB::getInstance();
		$cxn = $db->connection($this->_masterOrSlave());
		
		
		// Build the query. If we've supplied a secondary ID, apply that.
		// SecondaryID allows us to have global primary keys (ie, CompanyID = 4)
		// whilst also having locally unique IDs like Company Name (unique within a GameID)
		$sql = sprintf('SELECT * FROM %s where %s = :id', $this->_dbTable, $uniqueColumn); 
			
		if (!is_null($secondaryId)){
			$sql .= sprintf(' and %s = :secondaryId');
		} 
		
		$sql .= ' limit 2';
		
		
		// Prepare the statement for MDB2
		$query = $cxn->prepare($sql, MDB2_PREPARE_MANIP);
		
		if (PEAR::isError($query)) {
		   throw Plank_Excepion_Database('Couldn\'t prepare statement');
		}
		
		// Fill in the data, and add SecondaryID if we're using it.
		$data = array('id' => $id);
		
		if (!is_null($secondaryId)){
			$data['secondaryId'] = $secondaryId;
		} 		
		$result = $query->execute($data);
		
		if (PEAR::isError($result)) {
			Plank_Logger::log('MDL'.$this->_dbTable, 'DB Error! '.$result->getMessage().' '.$result->getUserInfo(), L_FATAL);
		   throw new Plank_Exception_Database('Database failed: '.$result->getMessage().'\n\n'.$result->getUserInfo());
		}
		
		$row = $result->fetchRow();
		$this->_dbOrigData = $this->data = $row;
		
		
		// If more than one row came back, we've fucked up.
		if ($result->fetchRow()){
			throw new Plank_Exception_Database('Load From ID was insufficently unique');
		}
		
	}
	
	function _save($force = false){
		
		if (!$this->changed && !$force){
			Plank_Logger::log('MDL'.$this->_dbTable, 'Not saving data, No change ', L_DEBUG);
			return;
		} elseif (!$this->data && !$force){
			Plank_Logger::log('MDL'.$this->_dbTable, 'Not saving data, No data here ', L_DEBUG);
			return;
		}


		// Check everything's valid
		foreach($this->data as $property => $value){
			$validationFunction = 'validate'.ucwords($property);
			if(method_exists($this, $validationFunction)){
				$this->$validationFunction($value);
			}
		}
	
		// Initialise database connection		
		$db = Plank_DB::getInstance();
		$cxn = $db->connection('master');
		
		
		// Once you have a valid MDB2 object named $mdb2...
		
		print_r( $this->data);
		
		$fields_values = array();
		$fields = explode(',', $this->_dbSaveFields);
		foreach($fields as $field){
			$fields_values[$field] = $this->data[$field];
		}
		$types = explode(',',$this->_dbSaveTypes);
		
		$cxn->loadModule('Extended');
		
		
		
		if ($this->data[$this->_dbNumId] == 0){
			Plank_Logger::log('MDL'.$this->_dbTable, 'Saving new record ', L_DEBUG);
			$result = $cxn->extended->autoExecute($this->_dbTable, $fields_values,
                        MDB2_AUTOQUERY_INSERT, null, $types);
			if(!PEAR::isError($result)){
				$this->data[$this->_dbNumId] = $cxn->lastInsertID();
			}
			Plank_Logger::log('MDL'.$this->_dbTable, 'Saved new record ID '.$this->data[$this->_dbNumId], L_DEBUG);
		} else {
			Plank_Logger::log('MDL'.$this->_dbTable, 'Updating record '.$this->data[$this->_dbNumId], L_DEBUG);
			$result = $cxn->extended->autoExecute($this->_dbTable, 
				$fields_values,
				MDB2_AUTOQUERY_UPDATE,
				$this->_dbNumId.' = '.$cxn->quote($this->data[$this->_dbNumId], 'integer'),
				$types);
		}
                        
		if (PEAR::isError($result)) {
			Plank_Logger::log('Session', 'DB Error! '
					.$result->getMessage().' '
					.$result->getUserInfo(), L_FATAL);
					
		   throw new Plank_Exception_Database('Database failed: '
		   		.$result->getMessage()."<br/>"
		   		.$result->getUserInfo());
		}
		
		$this->changed = false;
	}
	
	
	function _masterOrSlave(){
		$return = 'slave';
		if ( defined('USE_MASTER') ){
			$return = 'master';
		}
		
		return $return;
	}

	function get($value){
		if (!$this->data){
			$this->_init();
		}
		
		if(isset($this->data[$value])){
			return $this->data[$value];
		}
		
		
		throw new Plank_Exception($value.' is not a '.get_class($this).' attribute, and cannot be got');
	}
	
	
	function set($property, $value){
		if (!$this->data){
			$this->_init();
		}
		
		if(isset($this->data[$property])){
			$validationFunction = 'validate'.ucwords($property);
			if(method_exists($this, $validationFunction)){
				$this->$validationFunction($value);
			}
			$this->data[$property] = $value;
			$this->changed = true;
			return true;
		}
		
		throw new Plank_Exception($value.' is not a '.get_class($this).' attribute and cannot be set');
	}
	
	abstract function _init();
	
	
	function _populateFromArray($array){
		foreach($array as $index => $value){
			$this->data[$index] = $value;
		}
	}
	
	// This should provide a straight list of objects as a recordset for use
	// by the Collection object.
	function _collection_list($field, $limit, $start = 0, $direction = 'DESC'){
	
		$sql = sprintf('select * from %s order by %s %s limit %d, %d', $this->_dbTable, $field, $direction, $start, $limit);
		// Initialise database connection		
		$db = Plank_DB::getInstance();
		$cxn = $db->connection('slave');
		
		$query = $cxn->prepare($sql);
		
		$result = $query->execute();
		
		if (PEAR::isError($result)) {
			Plank_Logger::log('MDL'.$this->_dbTable, 'DB Error! '.$result->getMessage().' '.$result->getUserInfo(), L_FATAL);
		   	throw new Plank_Exception_Database('Query failed: '.$result->getMessage().'\n\n'.$result->getUserInfo());
		}
		
		return $result->fetchAll();
	}
	
	
	// This should provide a straight list of objects as a recordset for use
	// by the Collection object.
	function _collection_find($field, $value, $op = '=', $sort = null, $limit= null, $start = 0, $direction = 'DESC'){
		
		Plank_Logger::log('MDL'.$this->_dbTable, "Finding items with a $field $op $value", L_DEBUG);
	
		$sql = sprintf('select * from %s where %s %s :value', 
			$this->_dbTable, // select from pirate
			$field, // where name
			$op //  ==
			);
		
		if ( !is_null($sort) ){
			$sql .= sprintf(' order by %s %s', $sort, $direction); 
		}
		
		if ( !is_null($limit)){
			sprintf(' limit %d, %d', $start, $limit);
		}
		
		
		// Initialise database connection		
		$db = Plank_DB::getInstance();
		$cxn = $db->connection('slave');
		
		$query = $cxn->prepare($sql, MDB2_PREPARE_MANIP);
		$db->checkError($query);
		
		$result = $query->execute(array('value' => $value));
		$db->checkError($result);
		
		return $result->fetchAll();
		
	
	}
	
} 
