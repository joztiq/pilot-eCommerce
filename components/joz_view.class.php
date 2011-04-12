<?php
/**
 * Class used to render views and elements.
 * @author Daniel Maison
 * @author Markus Gerdau
 *
 */
class joz_view{
	/**
	 * Var holding the filename inkluding it's path (relative to the Views folder)
	 * @var String file name with path
	 */
	protected $file;
	/**
	 * Var holding the data to be passed to the view file
	 * @var Array data
	 */
	protected $data = array();
	
	/**
	 * Var holding the data to be passed from a view to the layout file
	 * @var Array mixed
	 */
	public $layoutData = array();
	
	/**
	 * Holding the root path from config
	 * @var string
	 */
	protected $rootPath;
	
	/**
	 * Holding a logger for this class
	 * @var Logger
	 */
	protected $logger;
	
	/**
	 * Construct
	 * @param String $file
	 * @param Array $data
	 */
	public function __construct($file , $data)
	{
		$this->file = $file;
		$this->data = $data;
		$this->rootPath = Joztiq::app()->config->rootPath;
		$this->logger = Logger::getLogger('joztiq.application.views.'.str_ireplace(DS,".",$file));
	}
	
	/**
	 * Render an element
	 * 
	 * Render a "piece" or "mini view" once or many times in your view.
	 * Function intendet to be used from View
	 * @param String $file
	 * @param Array $data
	 */
	public function renderElement($file , $data)
	{
		$this->logger = Logger::getLogger('joztiq.application.views.'.str_ireplace(DS,".",$file));
		extract($data , EXTR_SKIP);
		include(APP_PATH.DS."Views".DS.$file);
		$this->logger = Logger::getLogger('joztiq.application.views.'.str_ireplace(DS,".",$this->file));
	}
	
	/**
	 * Render the view and return output.
	 * 
	 * Function intended to be used from the controller
	 * @param void
	 * @return String output
	 */
	public function render()
	{
		ob_start();
		extract($this->data , EXTR_SKIP);
		include(APP_PATH.DS."Views".DS.$this->file);
		return ob_get_clean();
	}
	
	/**
	 * Add a var to be available in the layout
	 * @param String $varName
	 * @param mixed $varData
	 */
	public function setLayoutVar($varName , $varData)
	{
		$this->layoutData[$varName] = $varData;
	}
	
	/**
	 * Get the full URL for links
	 * @param String $path
	 * @return String URL
	 */
	protected function url($path)
	{
		return $this->rootPath . $path;
	}
}