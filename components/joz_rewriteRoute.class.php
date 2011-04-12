<?php
class joz_rewriteRoute implements joz_Iroute
{
	/**
	 * 
	 * Holding a instance of logger for this class
	 * @var Logger
	 */
	protected $logger;
	
	
	public function __construct()
	{
		$this->logger = Logger::getLogger('joztiq.framework.'.get_class($this));
	}
	
	/**
	 * Parse the request URI and set action and controller vaules for the front controller
	 * @param joz_frontController $fc
	 * @return bool
	 */
	public function parse(joz_frontController &$fc)
	{
		//Get app
		$app = Joztiq::app();
		//Get defaults
		$defaultController = $app->config->defaultController;
		$defaultAction = $app->config->defaultAction;
		//Get request URI
		$requestURI = strtolower($app->httpRequest->requestURI);
		//Trim it and remove gets
		//$requestURI = explode($requestURI , '?');
		$rootPath = strtolower($app->config->rootPath);
		$requestURI = str_replace($rootPath , "" , $requestURI);
		$requestURI = trim($requestURI, '/');
		
		//Start parsing
		$url = explode('/' , $requestURI);
		$count = count($url);
			// Set controller, action, and some action params from the segmented URL.
			if ($count > 0 && $url[0] != "") {
				$fc->setController(strtolower($url[0]).'Controller');
				$this->logger->debug('Setting controller: '.$url[0]);
				$actionParams = array();
				if ($count > 1) {
					$fc->setAction(strtolower($url[1]).'Action');
					$this->logger->debug('Setting action: '.$url[1]);
					if ($count == 3) 
					{
						$fc->setActionParams(array('id' => $url[2]));
						$this->logger->debug('Three values found, mapping third to id. Value: '.$url[2]);
					}
					if($count > 3){
						$this->logger->debug('Parsing additional params:');
						for ($i = 2; $i < $count; $i = $i + 2) {
							if (empty($url[$i+1])) {
								$this->logger->debug('Odd param, setting param '.$url[i].' with value: null');
								$url[$i+1] = null;
							}
							$this->logger->debug('setting param:'.$url[$i].' to value: '.$url[$i+1]);
							$actionParams[$url[$i]] = $url[$i+1];
						}
						$fc->setActionParams($actionParams);
					}
					}
					return true;
				}else{
				return false;
			}
	}
}