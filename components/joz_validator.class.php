<?php
/**
 * 
 * Class used to validate user input
 * @author Daniel Maison
 * @author Markus Gerdau
 *
 */
class joz_validator {
	
	
	/**
	 * 
	 * Var holding errors
	 * @var array
	 */
    protected  $errors = array();

    /**
     * Validate if anything is entered.
     * @param string $input input string
     * @param string $errorText [OPTIONAL] error text
     * @return bool result
     */
    public function validateRequired($input,$errorText = 'Missed a required field')
    {
        if (trim($input) != "") {
            return true;
        }else{
            $this->errors[] = $errorText;
            return false;
        }
    }
    /**
     * Validate if the input is a valid email
     * @param string $input input string
     * @param string $errorText [OPTIONAL] error text
     * @return bool result
     */
    public function validateEmail($input , $errorText='Email is not valid')
    {
    	if(filter_var($input, FILTER_VALIDATE_EMAIL))
    		return true;
    	$this->errors[] = $errorText;
    	return false;
    }
    
    /**
     * Validate if input is boolean
     * @param mixed $input input
     * @param string $errorText [OPTIONAL] error text
     * @return bool result
     */
    public function validateBoolean($input, $errorText='not boolean')
    {
    	if(filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) != null)
    		return true;
    	$this->errors[] = $errorText;
    	return false;
    }
    
    /**
     * Validate if input is a float
     * @param mixed $input input
     * @param string $errorText [OPTIONAL] error text
     * @return bool result
     */
    public function validateFloat($input, $errorText='not a float')
    {
    	if(filter_var($input, FILTER_VALIDATE_FLOAT))
    		return true;
    	$this->errors[] = $errorText;
    	return false;
    }  

    /**
     * 
     * Validate that the string is only text
     * @param string $theinput input string
     * @param string $description Error text
     * @param bool $allowSpaces [OPTIONAL] if spaces are allowd. Defaults to true
     * @return bool
     */
    public function validateTextOnly($theinput,$description = '', $allowSpaces = true)
    {
    	if(allowSpaces)
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
            $this->errors[] = $description;
            return false;
    	}
   

    /**
     * Validate if input is a number
     * @param $theinput
     * @param $description
     */
    public function validateNumber($theinput,$description = '')
    {
    	if (is_numeric($theinput)) {
            return true; // The value is numeric, return true
        }else{
            $this->errors[] = $description; // Value not numeric! Add error description to list of errors
            return false; // Return false
        }
    }
    
    /**
     * validate if the input is a date
     * @param string|int $thedate date to validate
     * @param string $description error description
     */
    public function validateDate($thedate,$description = '')
    {
        if (strtotime($thedate) === -1 || $thedate == '') 
        {
            $this->errors[] = $description;
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
    
    /**
     * Manually add somthing to the list of errors
     * @param $description
     */
    public function addError($description)
    {
        $this->errors[] = $description;
    }
    
    /**
     * Get list of errors
     * @return array list of error descriptions
     */
    public function getErrors()
    {
    	return $this->errors;
    }
        
}