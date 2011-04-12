<?php
/**
 * Class to handle httpRequests in joztiq
 * @author Daniel Maison
 * @author Markus Gerdau
 *
 */
class joz_httpRequest
{
	/**
	 * public var holding the user agent of the user
	 * @var string user agent
	 */
	public $userAgent;
	/**
	 * public var holding the get params sent
	 * @var array get params
	 */
	public $get;
	/**
	 * public var holding the post params sent
	 * @var array post params
	 */
	public $post;
	/**
	 * public var holding the request uri sent
	 * @var string request uri
	 */
	public $requestURI;
	/**
	 * Params passed from route, ex, rewriteroute
	 * @var array route params
	 */
	public $params;
	/**
	 * public var holding the HTTP-referer
	 * @var string
	 */
	public $referer;
	
	/**
	 * private var holding the singleton instance of this class
	 * @var joz_httpRequest
	 */
	private static $_instance;
	
	
	
	private function __construct()
	{
		$this->get = $_GET;
		$this->post = $_POST;
		$this->userAgent = @$_SERVER['HTTP_USER_AGENT'];
		$this->requestURI = @$_SERVER['REQUEST_URI'];
		$this->referer = @$_SERVER['HTTP_REFERER'];
	}
	/**
	 * Call hook for this class
	 * $param void
	 * @return void
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
	
}