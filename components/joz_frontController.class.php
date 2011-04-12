<?php
/**
 * Class to mapp the request to controller and actions
 * 
 */
class joz_frontController
{
	/**
	 * string holding the mapped controller requested
	 * @var string controller
	 */
	protected $controller;
	/**
	 * string holding the mapped action requested
	 * @var string action
	 */
	protected $action;
	/**
	 * array holding the mapped actionParams requested
	 * @var array actionParams
	 */
	protected $actionParams = array();
	/**
	 * Private var holding an instance of the class
	 * @var joz_frontController
	 */
	private static $_instance;
	
	/**
	 * Holding a instance of logger for this class
	 * @var Logger
	 */
	protected $logger;
	
	/**
	 * Returns the 'clean' name of the controller. eg not errorController but just error
	 * @return string
	 */
	public function getControllerName()
	{
		return str_replace('Controller' , '' , $this->controller);
	}
	
	/**
	 * Returns the 'clean' name of the action. eg not error404Action but just error404
	 * @return string
	 */
	public function getActionName()
	{
		return str_replace('Action' , '' , $this->action);
	}
	
	/**
	 * @var array holds the routes to try.
	 */
	protected $routes = array();
	
	private function __construct()
	{
		$this->logger = Logger::getLogger('joztiq.framework.'.get_class($this));
	}
	/**
	 * add a route to parse. Routes are parsed in the order they are added.
	 */
	public function addRoute($routeObj)
	{
		if($routeObj instanceof joz_Iroute)
		{
			$this->routes[] = $routeObj;
		}
		else
		{
			throw new SBSException('Supplied route must implement the joz_Iroute interface');
		}
	}
	
	public function parseRoutes()
	{
		foreach($this->routes as $route)
		{
			$this->logger->debug('Parsing route '.get_class($route));
			if($route->parse($this))
				break;
		}
		if(!isset($this->action))
			$this->action = joz_config::getInstance()->defaultAction."Action";
		
		if(!isset($this->controller))
			$this->controller = joz_config::getInstance()->defaultController."Controller";
	}
	/**
	 * Call hook for this class
	 * $param void
	 * @return joz_frontController
	 */
	public static function getInstance()
	{
		    if (!isset(self::$_instance)) 
		    {
            	$c = __CLASS__;
            	self::$_instance = new $c;
        	}

        return self::$_instance;
	}
	/**
	 * getter function for the action var
	 * @return string mapped action
	 */
	public function getAction()
	{
		return $this->action;
	}
	/**
	 * getter function for the controller var
	 * @return string mapped controller
	 */
	public function getController()
	{
		return $this->controller;
	}
	/**
	 * setter function for the action var
	 * @var string mapped actionName
	 */
	public function setAction($action)
	{
		 $this->action = $action;
	}
	/**
	 * setter function for the actionParams var
	 * @var array mapped actionParams
	 */
	public function setActionParams($actionParams)
	{
		if(!is_array($actionParams))
			throw new SBSException("Invalid type supplied. Var must be array type. Supplied type was :".gettype($actionParams));
		 $this->actionParams = $actionParams;
	}
	/**
	 * setter function for the controller var
	 *@var string mapped controllerName
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	public function dispatch()
	{
		$error = false;
		$this->logger->debug("Running dispatch. Controller: {$this->controller}, Action: {$this->action}.");
		if(!class_exists($this->controller) || !method_exists($this->controller , $this->action))
		{
			$this->logger->debug("Could not find appropriate controller/action. Redirecting to 404.");
			$this->controller = 'errorController';
			$this->action = 'error404Action';
		}
		
			$controllerName = $this->controller;
			$actionName = $this->action;
			$controller = new $controllerName;
			$controller->params = $this->actionParams;
			$controller->before($actionName);
			$controller->$actionName();
			$controller->after();
			
	}
}