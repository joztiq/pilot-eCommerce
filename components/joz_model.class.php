<?php
/**
 * 
 * Base class for models in Joztiq.
 * @author Daniel Maison
 * @author Markus Gerdau
 *
 */
class joz_model{
	/**
	 * Holding a logger for this class
	 * @var Logger
	 */
	protected $logger;
	
	/**
	 * Var holding validator instance
	 * @var joz_validator
	 */
	protected $validator;
	
	/**
	 * Var holding validation and other errors
	 * @var array errors
	 */
	protected $_errors = array();
	
	/**
	 * Var holding success notices
	 * @var array success
	 */
	protected $_success = array();
	
	public function __construct()
	{
		$this->validator = new joz_validator;
		$this->logger = Logger::getLogger('joztiq.application.models.'.get_class($this));
	}
	/**
	 * Validate current instance against validation rules
	 * 
	 * @throws InvalidArgumentException
	 * @throws BadMethodCallException
	 * @return bool validation results
	 */
	public function validate()
	{
		if(!isset($this->rules) && !$this instanceof joz_Ivalidation)
		{
			throw new BadMethodCallException('No rules found! Class needs to implement joz_Ivalidation or the depricated $this->rules array');
		}
		
		if($this instanceof joz_Ivalidation)
		{
			foreach ($this->getRules() as $rule)
			{
				if(!$rule instanceof joz_validationRule)
				{
					throw new InvalidArgumentException("getRules must only return instances of joz_validationrule. found ".gettype($rule));
				}
				$rule->validate($this);
			}
			if(count($this->_errors) > 0)
			{
				return false;
			}else{
				return true;
			}
		}
		
		
		/**
		 * Depricated, use joz_Ivalidation
		 * alidation rule syntax:
	 	 * array('variable' => 'rule,error text');
		 */
		if(isset($this->rules))
		{
			foreach($this->rules as $var => $text)
			{
				
				$ruleArr = explode(',' , $text, 2);
				$rule = "validate".ucfirst($ruleArr[0]);
				$errorText = $ruleArr[1];
				$this->validator->$rule($this->$var , $errorText);
			}
		}
		$this->_errors = array_merge($this->_errors, $this->validator->getErrors());
		
		return !$this->validator->foundErrors();
	}
	
	/**
	 * Get errors after validation
	 * @return array error texts
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * Add an error to the list of errors
	 * @param string|array $error
	 * @return bool
	 */
	public function setError($error)
	{
		if(is_array($error))
		{
			$this->_errors = array_merge($this->_errors, $error);
			return true;
		}
		$this->_errors[] = $error;
		return true;
	}
	
	/**
	 * Get success notices
	 * @return array $success texts
	 */
	public function getSuccess()
	{
		return $this->_success;
	}
	
	/**
	 * Add a success notification to the list of successes
	 * @param string|array $success
	 * @return bool
	 */
	public function setSuccess($success)
	{
		if(is_array($success))
		{
			$this->_success = array_merge($this->_success, $success);
			return true;
		}
		$this->_success[] = $success;
		return true;
	}
	
	
	/**
	 * function used to draw a form from model vars
	 * @return void
	 */
	public function drawForm()
	{
		if(!isset($this->labels))
			$this->logger->warn("Trying to draw a form, but no labels are defined!");
		$formName = str_replace('Model', 'Form', get_class($this));
		echo <<<EOT
		<form name="$formName" action="" method="POST">
EOT;
		foreach($this->labels as $var => $label)
		{
			if(is_array($label))
			{
				echo '<label for="'.$formName.'['.$var.']">'.$label['label'].'</label><br/><select name="'.$formName.'['.$var.']">';
				$relationModel = new $label['class'];
				$relationList = $relationModel->findAll();
				foreach($relationList as $row)
				{
					if(isset($label['default']) && $row->id == $label['default'])
					{
						echo '<option selected value="'.$row->id.'">'.$row->$label['field'].'</option>';
					}else{
						echo '<option value="'.$row->id.'">'.$row->$label['field'].'</option>';
					}
					
				}
				echo '</select><br/>';
			}else{
			echo '<label for="'.$formName.'['.$var.']">'.$label.'</label><br/><input type="text" placeholder="'.$label.'" class="text" name="'.$formName.'['.$var.']" value="'.$this->$var.'" /><br/>';
			}
		}
		echo "<input type='submit' class='submit_small' value='Spara'/></form>";
		
	}
}