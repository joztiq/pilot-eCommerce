<?php
include(FW_PATH."nusoap.php");
abstract class joz_soapController extends soap_server
{

	public $soap_defencoding = 'UTF-8';
	protected $_use = 'literal';
	protected $_style = 'document';
	protected $_namespace;
	protected $_uri;
	protected $webMethods = array();
	/**
	 * Logger obj
	 * @var Logger
	 */
	protected $logger;
	
	
	public function __construct()
	{
		$this->logger = Logger::getLogger('joztiq.application.controller.'.get_class($this));
	}
	
	
	public function before($action)
	{
		if(!isset($this->_namespace) || !isset($this->_uri))
		{
			throw new SBSException('URI or NameSpace not set. set them in deriving class.');
		}
		
		$service = Joztiq::app()->fc->getActionName().'Service';
		$this->configureWSDL($service,$this->_namespace,$this->_uri,$this->_style);
		$this->wsdl->schemaTargetNamespace=$this->_namespace;
	}
	
	public function after()
	{
		$this->service(file_get_contents("php://input"));
	}
	
	public function registerWebMethod(joz_soapMethod $method)
	{
		$this->webMethods[] = $method;
		$inparams = array();
		foreach($method->getInputParams() as $param)
		{
			$inparams[$param->getName()] = $param->getType();
		}
		$outParams = array();
		foreach($method->getOutputParams() as $param)
		{
			$outParams[$param->getName()] = $param->getType();
		}
		$this->register($method->getMethod(),
		$inparams,
		$outParams,
		$this->_namespace,
		$this->_uri.'/'.$method->getMethod().'/',
		$this->_style,
		$this->_use,
		$method->getDocumentation()
		);
	}
	
	/**
	 * Add a new complex type to the wsdl
	 * @param string $obj name of the struct (also have to be a valid class)
	 */
	protected function addComplexStructType($obj)
	{
		$class = new ReflectionClass($obj);
		if(in_array('joz_IsoapComplexType', $class->getInterfaceNames()))
		{
			$this->logger->debug("$obj is an instance of joz_IsoapComplexType");
			$this->logger->debug("Props:".print_r($obj::getTypes(),true));
			$structProperties = array();
			foreach($obj::getTypes() as $property=>$type)
			{
				$structProperties[$property] = array('name' => $property , 'type' => $type);
			}
		}else{
			$this->logger->debug("$obj is not an instance of joz_IsoapComplexType");
			/*
			 * Trying to guess types based on doc comments.
			 */
			$class = new ReflectionClass($obj);
			$structProperties = array();
			//@var\s([a-zA-Z]*)
			
			foreach($class->getProperties(ReflectionProperty::IS_PUBLIC) as $prop)
			{
				$matches = array();
				if(preg_match('/@var\s([a-zA-Z]*)/', $prop->getDocComment(),$matches))
				{
					$type = $matches[1];
					switch (strtolower($type))
					{
						case 'string':
							$structProperties[$prop->getName()] = array('name' => $prop->getName() , 'type' => 'xsd:string');
							break;
						case 'int':
						case 'integer':
							$structProperties[$prop->getName()] = array('name' => $prop->getName() , 'type' => 'xsd:integer');
							break;
						case 'bool':
						case 'boolean':
							$structProperties[$prop->getName()] = array('name' => $prop->getName() , 'type' => 'xsd:boolean');
							break;
						case 'float':
							$structProperties[$prop->getName()] = array('name' => $prop->getName() , 'type' => 'xsd:float');
							break;
						default:
							if(class_exists($type))
							{
								$structProperties[$prop->getName()] = array('name' => $prop->getName() , 'type' => 'tns:'.$type);
							}else{
								$structProperties[$prop->getName()] = array('name' => $prop->getName() , 'type' => $type);
							}
							break;
					}
				}
			}
		}
		
		
		$this->logger->debug("Adding complex type $obj. struct props:".print_r($structProperties,true));
		$this->wsdl->addComplexType(
									    $obj,
									    'complexType',
									    'struct',
									    'all',
									    '',
		$structProperties);
	}
	
	/**
	 * Add a complex array type. Such as "array of persons"
	 * @param string $name Name of your shiny new complex array
	 * @param string $arrayOf what's the name of the object you want to array
	 */
	protected function addComplexArrayType($name , $arrayOf)
	{
		$this->wsdl->addComplexType(
			$name,
			'complexType',
			'array',
			'sequence',
			'SOAP-ENC:Array',
		array(),
		array(
				$name => array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:'.$arrayOf.'[]')
		),
				'tns:'.$arrayOf
				);
	}
	
	
	
	
	
	
	/**
		* invokes a PHP function for the requested SOAP method
		*
		* The following fields are set by this function (when successful)
		*
		* methodreturn
		*
		* Note that the PHP function that is called may also set the following
		* fields to affect the response sent to the client
		*
		* responseHeaders
		* outgoing_headers
		*
		* This sets the fault field on error
		*
		* @access   private
		*/
	public function invoke_method() {
		$this->logger->debug('Incoming soap request, methodname=' . $this->methodname . ' methodURI=' . $this->methodURI . ' SOAPAction=' . $this->SOAPAction);

		if ($this->wsdl) {
			if ($this->opData = $this->wsdl->getOperationData($this->methodname)) {
			} elseif ($this->opData = $this->wsdl->getOperationDataForSoapAction($this->SOAPAction)) {
				// Note: hopefully this case will only be used for doc/lit, since rpc services should have wrapper element
				$this->methodname = $this->opData['name'];
			} else {
				$this->logger->debug('No WSDL for operation=' . $this->methodname);
				$this->fault('SOAP-ENV:Client', "Operation '" . $this->methodname . "' is not defined in the WSDL for this service");
				return;
			}
		} else {
			$this->logger->debug('No WSDL to validate method');
		}

		$class = '';
		$method = '';
		foreach($this->webMethods as $webmethod)
		{
			if($webmethod->getMethod() == $this->methodname)
			{
				$this->logger->debug("setting method to ".$this->methodname);
				$this->logger->debug("Setting class to ".$webmethod->getClass());
				$method = $this->methodname;
				$class = $webmethod->getClass();
			}
		}

		// does method exist?
		if ($class == '') {
			if (!function_exists($this->methodname)) {
				$this->logger->debug("Function '$this->methodname' not found!");
				$this->result = 'fault: method not found';
				$this->fault('SOAP-ENV:Client',"method '$this->methodname' not defined in service");
				return;
			}
		} else {
			$method_to_compare = $method;
			if (!in_array($method_to_compare, get_class_methods($class))) {
				$this->logger->debug("Method '$this->methodname' not found in class '$class'!");
				$this->result = 'fault: method not found';
				$this->fault('SOAP-ENV:Client',"method '$this->methodname' not defined in service");
				return;
			}
		}

		// evaluate message, getting back parameters
		// verify that request parameters match the method's signature
		if(! $this->verify_method($this->methodname,$this->methodparams)){
			// debug
			$this->logger->debug('ERROR: request not verified against method signature');
			$this->result = 'fault: request failed validation against method signature';
			// return fault
			$this->fault('SOAP-ENV:Client',"Operation '$this->methodname' not defined in service.");
			return;
		}

		// if there are parameters to pass
		$this->logger->debug('Parsed method params:'.print_r($this->methodparams,true));
		$this->logger->debug("Calling '$this->methodname'");

				$instance = new $class ();
				$call_arg = array(&$instance, $method);

			if (is_array($this->methodparams)) {
				$this->methodreturn = call_user_func_array($call_arg, array_values($this->methodparams));
			} else {
				$this->methodreturn = call_user_func_array($call_arg, array());
			}        
		$this->logger->debug("Called method $this->methodname, received data of type ".gettype($this->methodreturn));
		$this->logger->debug('Methodreturn:'.print_r($this->methodreturn,true));
	}
	
	
	
	
	
}