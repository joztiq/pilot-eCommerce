<?php
/**
 * class joz_activeRecordModel
 * base class for all active record classes in joztiq
 * @author Daniel Maison
 * @author Markus Gerdau
 */
abstract class joz_activeRecordModel extends joz_model{
	/**
	 * public string holding the database table name for the model
	 * @var string database table name
	 */
	public $tableName;
	/**
	 * public string holding the primaryKey for the model
	 * @var string database column name
	 */
	public $primaryKey = 'id';
	/**
	 * protected array holding the state of the model at last database fetch
	 * @var array
	 */
	protected $lastFetch = array();
	/**
	 * protected array holding the relations for the model
	 * var array
	 */
	protected $relations = array();
	/**
	 * protected array holding the relational data for the model
	 * @var array
	 */
	protected $relData = array();
	
	/**
	 * Holding table overview
	 * @var array
	 */
	protected $table = array();


	/**
	 * Relation holds key
	 */
	const HAS_ONE = 1;
	/**
	 * Relation holds key
	 */
	const HAS_MANY = 2;
	/**
	 * This class holds key
	 */
	const BELONGS_TO = 3;
	const BELONGS_TO_MANY = 4;
	const MANY_TO_MANY = 5;

	/**
	 * function __get
	 *
	 * function used to get relational data
	 * @param string $name
	 * @throws InvalidArgumentException
	 * @return mixed instance of requested class or array of instances
	 */
	public function __get($name)
	{
		if(array_key_exists($name,$this->relations))
		{
			if(!array_key_exists($name,$this->relData))
			{
				if($this->relations[$name]['type'] === self::HAS_ONE)
				{
					$inst = new $this->relations[$name]['class']();
					$findbyStr = "findBy".$this->relations[$name]['key'];
					$inst->$findbyStr($this->{$this->primaryKey});
					$this->relData[$name] = $inst;
				}
				if($this->relations[$name]['type'] === self::BELONGS_TO)
				{
					$this->relData[$name] = new $this->relations[$name]['class']($this->{$this->relations[$name]['key']});
				}
				if($this->relations[$name]['type'] === self::HAS_MANY)
				{
					$inst = new $this->relations[$name]['class']();
					$key = $this->relations[$name]['key'];
					return $inst->findAll("$key = {$this->{$this->primaryKey}}");
					/*;
					$findbyStr = "searchFor".$key;
					$this->relData[$name] = $inst->$findbyStr($this->{$this->primaryKey});*/
					//$this->relData[$name] = $inst->__call($findbyStr,$this->{$this->primaryKey});
				}
			}
			return $this->relData[$name];
		}else {
			//TODO:Slänga en excception här istället? dock kräver det lite mer disciplin i kodningen.
			//throw new OutOfBoundsException("Trying to access property $name. It's not in this class and is not part of the relations array");
			$this->logger->warn('Försöker hämta property '.$name.' från '.get_class($this).'. Propertyn finns inte i klassen eller i relationsArrayen');
		}
	}

	function __construct($primaryKey = null)
	{
		parent::__construct();
	 if(!is_null($primaryKey)){
	 	/* =====
	 	 * Fetching from selected data
	 	 * =====
	 	 */
	 	try{
	 		$result = joz_db::getLink()->prepare("SELECT * FROM {$this->tableName} WHERE {$this->primaryKey}=?");
	 		$result->setFetchMode(PDO::FETCH_INTO,  $this);
	 		$result->execute(array($primaryKey));
	 		$result->fetch();
	 		if(joz_config::getInstance()->getVar("logSQLQueries"))
	 		{
	 			$this->logger->debug("SQL Query:\nSELECT * FROM {$this->tableName} WHERE {$this->primaryKey}=?\nValues:".print_r(array($primaryKey),true));
	 		}
	 		
			$this->setLastFetch();
	 	}catch(PDOException $e){
	 		$this->logger->error((string)$e);
	 		$this->_error = 'An unexpected error occured, please try again later';
	 	}
	 }
	}

	/**
	 * function isChanged
	 * @param void
	 * @return bool
	 */

	function isChanged() {
		$now = array();
		
		foreach($this as $fieldName => $value)
		{
			if(in_array($fieldName,$this->getTable()))
			{
				$now[$fieldName] = $value;
			}
		}
		
		if($now == $this->lastFetch)
			return false;
		return true;
		
	}


	/*
	 * function save
	 * @param void
	 * @return boolean
	 */

	function save() {
		$this->logger->debug("Saving model...");
		try{
				

			$db = joz_db::getLink();
			$table = $this->getTable();
			$tableCount = count($table);
			/*=======
			 * Update
			 * ======
			 */
			if(isset($this->{$this->primaryKey})){
				if(!$this->isChanged()){
					return true;
				}
				$columnCount = 0;
				$data = '';
				$params = array();
				foreach($this as $fieldName=>$value){
					if(in_array($fieldName,$table)){
						if($fieldName == $this->primaryKey){
							continue;
						}
						$columnCount++;
						$data .="$fieldName = :$fieldName";
						$params[$fieldName] = $value;
						if($columnCount != $tableCount){
							$data .= " , ";
						}
					}
				}
				
				
				if(joz_config::getInstance()->getVar("logSQLQueries"))
				{
					$this->logger->debug("
                       UPDATE {$this->tableName}
                       SET
                       $data
                       WHERE
                       {$this->primaryKey} = ".$this->{$this->primaryKey}
                       . print_r($params,true));
				}
				$statement = $db->prepare("
                       UPDATE {$this->tableName}
                       SET
                       $data
                       WHERE
                       {$this->primaryKey} = ".$this->{$this->primaryKey}
                       );
                       $statement->execute($params);

			 
			if($statement->rowCount() > 0){
				$this->setLastFetch();
				return true;
			}else{
				$this->logger->warn("Kunde ej uppdatera ".get_class($this)." med primary key ".$this->{$this->primaryKey});
				return false;
			}

		}else{
			/* =======
			 * Insert
			 * =====
			 */
			$columnCount = 0;
			$columns = '';
			$values = '';
			$params = array();
			foreach($this as $fieldName=>$value){
				if(in_array($fieldName,$table)){
					if($fieldName == $this->primaryKey){
						continue;
					}
					$columnCount++;
					$columns .= $fieldName;
					$values .=":$fieldName";
					if($value === null)
						$value ='';
					$params[$fieldName] = $value;
					if($columnCount != $tableCount){
						$columns .= " , ";
						$values .= " , ";
					}
				}
			}
			if(joz_config::getInstance()->getVar("logSQLQueries"))
			{
							$this->logger->debug("INSERT INTO
                                      {$this->tableName}
                                      ($columns)
                                      VALUES
                                      ($values)"
                                      . print_r($params,true));
			}

			$statement = $db->prepare("
                                      INSERT INTO
                                      {$this->tableName}
                                      ($columns)
                                      VALUES
                                      ($values)
                                      ");
                                      $statement->execute($params);

                                      $this->{$this->primaryKey} = $db->lastInsertId();
                                      if(is_int((int)$this->{$this->primaryKey})){
                                      	$this->setLastFetch();
                                      	return true;
                                      }else{
                                      	$this->logger->error("Kunde inte göra ett nytt inlägg i ".$this->tableName." med primaryKey". $this->{$this->primaryKey});
                                      	return false;
                                      }
		}
		$this->logger->error("Något blev koko vis save i ".get_class($this).". Hamnade inte i nån sats. svarar false... Värden som försökte sparas var ".serialize($this));
		return false;
		}catch(PDOException $e){
				if($e->getCode() == 23000)
				{
					$this->logger->debug("Not saving, duplicate..");
					$this->_errors[] = 'Det finns redan ett inlägg med dessa uppgifter';
					return false;
				}
				$this->logger->error((string)$e);
				$this->_errors[] = 'An unexpected error occured, please try again later';
				return false;
		}
	}


	/**
	 * public function getTable
	 * @param void
	 * @return array table
	 */
	function getTable() {
		if(!empty($this->table))
			return $this->table;
		try{
		foreach(joz_db::getLink()->query("desc {$this->tableName}") as $desc){
			if($desc['Field'] == $this->primaryKey){
				continue;
			}
			$table[] = $desc['Field'];
		}
		$this->table = $table;
		return $table;
				}catch(PDOException $e){
				$this->logger->error((string)$e);
				$this->_error = 'An unexpected error occured, please try again later';
				return false;
			}
	}

	/**
	 * function assign
	 *
	 * function used to mass-assign values to an object
	 * @param array $vals
	 * @return void
	 */
	public function assign(array $vals)
	{
		foreach($vals as $fieldName => $value)
		{
			//Check if this val belongs to a relation
			if(is_array($value) && key_exists($fieldName,$this->relations))
			{
				foreach($value as $relK => $relV)
				{
					if(property_exists($this->relations[$fieldName]['class'] , $relK))
					{
						$this->$fieldName->$relK = $relV;
					}
				}
			}else //Not a relation, assigning as normal
			{
				if(property_exists($this , $fieldName))
				{
					$this->$fieldName = $value;
				}
			}
		}
	}

	/** Function __call
	 *
	 * function used to create a dynamic findBy
	 * @param string $name name of the overloaded method
	 * @param array $args the arguments passed to the overloaded method
	 * @throws BadMethodCallException if an unexpected method is called
	 * @return mixed
	 *
	 */
	function __call($name , $args)
	{
		/**
		 * Check if the call is a dynamic "findBy"
		 * Usage:
		 * ================================
		 * class foo extends joz_activeRecordModel
		 * {
		 * 	public $bar;
		 * 	public $baz;
		 * 	public $id;
		 * }
		 *
		 * $fooObj = new foo;
		 * $fooObj->finByBar('baz');
		 * ================================
		 *
		 *
		 */
		if(stristr($name,'findby'))
		{
			$property = substr($name,6);
			$searchTerm = $args[0];
			if(property_exists($this,$property))
			{
				try{
					
				
				$db = joz_db::getLink();
				$state = $db->query("SELECT
				{$this->primaryKey}
                        FROM
                        {$this->tableName}
                        WHERE
                        $property = '$searchTerm'
                           ");
                        if($state === false)
                        	return null;
						$result = $state->fetch();
						if($result == null)
						{
							return null;
						}
                        $this->__construct($result[0]);
                        return true;
				}catch(PDOException $e)
				{
					$this->logger->error((string)$e);
				}
			}
		}
			
		/**
		 * Check if the call is a dynamic "searchFor"
		 * Usage:
		 * ================================
		 * class foo extends joz_activeRecordModel
		 * {
		 * 	public $bar;
		 * 	public $baz;
		 * 	public $id;
		 * }
		 *
		 * $fooObj = new foo;
		 * $someArray = $fooObj->searchForbar('baz');
		 * ================================
		 *
		 *
		 */
		if(stristr($name,'searchfor'))
		{
			$property = substr($name,9);
			$searchTerm = $args[0];
			$class = get_class($this);
			if(property_exists($this,$property))
			{
				$db = joz_db::getLink();
				$sql = "
						SELECT
						{$this->primaryKey}
                        FROM
                        {$this->tableName}
                        WHERE
                        $property = '$searchTerm'
                           ";
                        	
                        if($results == null)
                        return null;
                        foreach($db->query($sql) as $result)
                        {
                        	$array[] = new $class($result[0]);
                        }
                        return $array;
			}
		}
		throw new BadMethodCallException("Unknown method $name in class ".get_class($this));
	}

	/**
	 * Function to get all objects of a model. Optional where statement can be sent as argument.
	 * @param String $where
	 * @return Array of instances of the requested object
	 */
	public function findAll($where = null)
	{
		$class = get_class($this);
		try{
			
		
		$db = joz_db::getLink();
		$sql = "SELECT * FROM {$this->tableName}";
		if($where)
		{
			$sql .= " WHERE $where";
		}
		$statement = $db->query($sql);
		$return = $statement->fetchAll(PDO::FETCH_CLASS, $class);
		
		return $return;
		}catch (PDOException $e)
		{
			$this->logger->error((string)$e);
			$this->_error = 'An unexpected error occured, please try again later';
			return false;
		}
	}
	/**
	 * Static version of findAll
	 * @param string $where
	 * @param bool [OPTIONAL] defaults to false. Return the first hit (convenient when you only expect one hit)
	 * @return array of instances of the requested object
	 */
	public static function find($where = null , $returnFirst = false)
	{
		$class = get_called_class();
		$model = new $class($where);
		$result = $model->findAll($where);
//		Logger::getLogger('joztiq.application.models.'.$class)->debug(print_r($result,true));
		if($returnFirst)
		{
			return $result[0];
		}
		return $result;
	}
	/**
	 * 
	 * Function used to delete a record
	 * @param void
	 * @return bool
	 */
	public function delete()
	{
		try{
			$id = (int)$this->{$this->primaryKey};
			$db = joz_db::getLink();
			if(joz_config::getInstance()->getVar("logSQLQueries"))
			{
				$this->logger->debug("DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :pk"." :pk = $id");
			}
			$query = $db->prepare("DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :pk");
			$query->bindParam(':pk',$id, PDO::PARAM_INT);
			$result = $query->execute();
			if($result)
				return true;
			$this->logger->error('Trying to delete record with id '.$this->{$this->primaryKey}.'. But something whent wrong, bailing out.');
			return false;
		}catch(PDOException $e)
		{
			$this->logger->error((string)$e);
			$this->_error = 'An unexpected error occured, please try again later';
			return false;
		}
		
	}
	
	
	protected function setLastFetch()
	{
		foreach($this as $fieldName => $value)
		{
			if(in_array($fieldName,$this->getTable()))
			{
				$this->lastFetch[$fieldName] = $value;
			}
		}
	}
	
	/**
	 * Will search a specific field for any value 'LIKE %$value%'
	 * @param string $field
	 * @param string $value
	 * @param string $criteria [OPTIONAL] SQL string appended to search eg: "AND (myField = 'myValue')" Note that this string is not properly escaped! Dont use user input here!
	 * @return array of instances
	 */
	public function searchField($field, $value, $criteria = null)
	{
		$value = "%$value%";
		$class = get_class($this);
		try{
			
			$db = joz_db::getLink();
			$state = $db->prepare(
			"SELECT DISTINCT *
			FROM {$this->tableName} 
			WHERE
			({$this->{$field}} LIKE ?) $criteria");
			
			$state->execute(array($value));
			
			return $state->fetchAll(PDO::FETCH_CLASS, $class);
		}catch(PDOException $e){
			$this->logger->error((string)$e);
		}
	}
	/**
	 * Will search the fields declared in the class's searchables array for any match 'LIKE %$value%'
	 * @param String $value
	 * @param String $criteria
	 * @return Array of instances
	 * @throws BadMethodCallException
	 */
	public function search($value, $criteria = null)
	{
		if(!isset($this->searchables))
			throw new BadMethodCallException("Trying to use function search(). But the searchables array is undefined!");
		$value = "%$value%";
		$class = get_class($this);
		foreach($this->searchables as $field)
		{
			if(is_array($field))
				continue;
			$new_searchables[] = $field;
		}
		$where = implode(" LIKE :value OR " , $new_searchables);
		$where .= " LIKE :value";
		
		try{
			
			$this->logger->debug("Söker, SQL: SELECT DISTINCT * FROM {$this->tableName} WHERE ($where) $criteria");
			
			$db = joz_db::getLink();
			$state = $db->prepare(
			"SELECT DISTINCT *
			FROM {$this->tableName} 
			WHERE
			($where) $criteria");
			
			$state->execute(array(':value' => $value));
			
			return $state->fetchAll(PDO::FETCH_CLASS, $class);
		}catch(PDOException $e){
			$this->logger->error((string)$e);
		}
	}
	
}
?>