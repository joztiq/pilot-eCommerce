<?php
class joz_validationRule{

	/**
	 * Rule type
	 * @var int 
	 */
	private $rule;
	/**
	 * Field name of field to validate
	 * @var string 
	 */
	private $field;
	/**
	 *  Any options to pass to the validation
	 * @var array
	 */
	private $options;
	
	/**
	 * Error text
	 * @var string
	 */
	private $errorText;
	
	const VALIDATE_REQUIRED = 1;
	const VALIDATE_EMAIL = 2;
	const VALIDATE_BOOLEAN = 3;
	const VALIDATE_FLOAT = 4;
	const VALIDATE_TEXT_ONLY = 5;
	const VALIDATE_NUMBER = 6;
	const VALIDATE_DATE = 7;
	const VALIDATE_MIN_LENGTH = 8;
	const VALIDATE_MAX_LENGTH = 9;
	
	/**
	 * Quickly create new rule
	 * @param string $field The field to validate 
	 * @param int $rule The rule to apply, use class constants
	 * @param string $errorText
	 * @param array $options any options for the rule.
	 */
	public function __construct($field , $rule , $errorText , $options = array())
	{
		$this->field = $field;
		$this->rule = $rule;
		$this->options = $options;
		$this->errorText = $errorText;
	}
	
	/**
	 * Setter for rule name
	 * @param string $ruleName
	 * @return void;
	 */
	public function setRule($ruleName)
	{
		$this->rule = $ruleName;
	}
	/**
	 * Setter for field name
	 * @param string $fieldName
	 * @return void
	 */
	public function setField($fieldName)
	{
		$this->field = $fieldName;
	}
	
	/**
	 * Setter for options
	 * @param array $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}
	
	public function validate(joz_model $model)
	{
		$input = $model->{$this->field};
		
		switch ($this->rule) {
			case self::VALIDATE_BOOLEAN:
				if(!self::validateBoolean($input))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_DATE:
				if(!self::validateDate($input))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_EMAIL:
				if(!self::validateEmail($input))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_FLOAT:
				if(!self::validateFloat($input))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_NUMBER:
				if(!self::validateNumber($input))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_REQUIRED:
				if(!self::validateRequired($input))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_TEXT_ONLY:
				if(!self::validateTextOnly($input, $this->options))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_MAX_LENGTH:
				if(!self::validateMaxLength($input, $this->options))
				{
					$model->setError($this->errorText);
				}
			break;
			case self::VALIDATE_MIN_LENGTH:
				if(!self::validateMinLength($input, $this->options))
				{
					$model->setError($this->errorText);
				}
			break;
			default:
				throw new InvalidArgumentException("Unknown rule flag $this->rule");
				;
			break;
		}
	}
	
    /**
     * Validate max length.
     * @param string $input input string
     * @return bool result
     */
    public static function validateMaxLength($input,$maxLength)
    {
        if(strlen($input) > $maxLength)
        	return false;
        return true;
    }
    
    
    /**
     * Validate min length.
     * @param string $input input string
     * @return bool result
     */
    public static function validateMinLength($input,$minLength)
    {
        if(strlen($input) < $minLength)
        	return false;
        return true;
    }
	

    /**
     * Validate if anything is entered.
     * @param string $input input string
     * @return bool result
     */
    public static function validateRequired($input)
    {
        if (trim($input) != "") {
            return true;
        }else{
            return false;
        }
    }
    /**
     * Validate if the input is a valid email
     * @param string $input input string
     * @return bool result
     */
    public static function validateEmail($input)
    {
    	if(filter_var($input, FILTER_VALIDATE_EMAIL))
    		return true;
    	return false;
    }
    
    /**
     * Validate if input is boolean
     * @param mixed $input input
     * @return bool result
     */
    public static function validateBoolean($input)
    {
    	if(filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) != null)
    		return true;
    	return false;
    }
    
    /**
     * Validate if input is a float
     * @param mixed $input input
     * @return bool result
     */
    public static function validateFloat($input)
    {
    	if(filter_var($input, FILTER_VALIDATE_FLOAT))
    		return true;
    	return false;
    }  

    /**
     * 
     * Validate that the string is only text
     * @param string $theinput input string
     * @param array $options Any options to pass
     * @return bool
     */
    public static function validateTextOnly($theinput,$options)
    {
    	if(isset($options['allowSpaces']))
    	{
    		$allowSpaces = $options['allowSpaces'];
    	}else{
    		$allowSpaces = true;
    	}

    	
    	if($allowSpaces)
    	{
    		$result = ereg ("^[A-Öa-ö0-9\ ]+$", $theinput );
        	if ($result)
        	    return true;
    	}else
    	{
    		$result = ereg ("^[A-Öa-ö0-9]+$", $theinput );
        	if ($result)
            	return true;
    	}
            return false;
    	}
   

    /**
     * Validate if input is a number
     * @param $theinput
     */
    public static function validateNumber($theinput)
    {
    	if (is_numeric($theinput)) {
            return true; // The value is numeric, return true
        }else{
            return false; // Return false
        }
    }
    
    /**
     * validate if the input is a date
     * @param string|int $thedate date to validate
     */
    public static function validateDate($thedate)
    {
        if (strtotime($thedate) === -1 || $thedate == '') 
        {
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * Check if there was any errors
     * @return bool result
     */
    public function foundErrors() {
        if (count($this->errors) > 0)
        {
            return true;
        }else
        {
            return false;
        }
    }
}