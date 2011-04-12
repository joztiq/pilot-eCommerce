<?php
/**
 * Base class that all other pageControllers are to extend.
 * 
 * It makes sense to create a appController with predifened values such as layout etc and extend your pageControllers from that.
 * @author Daniel Maison
 * @author Markus Gerdau
 */
abstract class joz_pageController{
	/**
	 * Holding passed params
	 * @var Array passed params
	 */
	public $params = array();
	/**
	 * Var holding the data to be passed to the view file
	 * @var Array mixed
	 */
	protected $viewData = array();
	/**
	 * Var holding the data to be passed to the layout file
	 * @var Array mixed
	 */
	protected $layoutData = array();
	
	/**
	 * Holding a logger for this class
	 * @var Logger
	 */
	protected $logger;
	
	public function __construct()
	{
		$this->logger = Logger::getLogger('joztiq.application.controllers.'.get_class($this));
	}
	
	/**
	 * Function runs before any actions
	 */
	public function before()
	{
		
	}
	
	/**
	 * Function runs after any actions
	 */
	public function after()
	{
		
	}
	
	protected function render($file = null)
	{
		$view = $this->loadView($file);
		$viewOutput = $view->render();
		if(!$this->layout)
		{
			echo $viewOutput;
			return;
		}
		if(count($view->layoutData) > 0)
			$this->layoutData = array_merge($this->layoutData, $view->layoutData);
		$this->layoutData['content'] = $viewOutput;
		echo $this->loadLayoutView()->render();
	}
	/**
	 * Load a view instanse
	 * @param String OPTIONAL para, view file to load. Defaults to controller/action
	 * @return joz_view
	 */
	protected function loadView($file = null)
	{
		if($file === null)
		{
			$file = Joztiq::app()->fc->getControllerName() . DS . Joztiq::app()->fc->getActionName() . '.php';
		}
		return new joz_view($file , $this->viewData);
	}
	
	/**
	 * Load a view instanse for the layout
	 * @param String Optional para, view file to load. Defaults to controller/action
	 * @return joz_view
	 */
	protected function loadLayoutView()
	{
		$file = 'Layouts'.DS.$this->layout.'.php';
		return new joz_view($file , $this->layoutData);
	}
	
	/**
	 * Add a var to be available in the view
	 * @param String $varName
	 * @param mixed $varData
	 */
	public function setViewVar($varName , $varData)
	{
		$this->viewData[$varName] = $varData;
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
	 * Redirect to the specified path (controller-view). NOTE that this function does not stop
	 * execution.
	 * 
	 * @param string $url path to redirect to.
	 * @return void
	 **/
	protected function redirect($url) {
		header('Location: ' . joz_config::getInstance()->rootPath . $url);
	}
	
	/**
	 * Redirect to the specified URL. NOTE that this function does not stop
	 * execution.
	 * 
	 * @param string $url URL to redirect to.
	 * @return void
	 **/
	protected function redirectExternal($url) {
		header('Location: ' . $url);
	}
	
	/**
	 * Get the full URL for links
	 * @param String $path
	 * @return String URL
	 */
	protected function url($path)
	{
		return joz_config::getInstance()->rootPath . $path;
	}
}