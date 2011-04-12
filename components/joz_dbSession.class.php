<?php

class joz_dbSession
{
	
	private $session_lifetime;
	private $securityCode;
	private $table_name;
	private $gc_probability;
	private $gc_divisor;
	private $logger;
	private static $_instance;
	private static $static_logger;

	
	public static function getInstance()
	{
		    if (!isset(self::$_instance)) 
		    {
            	$c = __CLASS__;
            	self::$_instance = new $c;
        	}

        return self::$_instance;
	}
    
    public function __construct()
    {
		$this->logger = Logger::getLogger("joztiq.framework.joz_dbSession");
		self::$static_logger = $this->logger;
		$this->logger->debug("In construct, setting config values...");
    	$this->session_lifetime = joz_config::getInstance()->getVar("session.session_lifetime");
    	$this->securityCode = joz_config::getInstance()->getVar("session.salt");
    	$this->table_name = joz_config::getInstance()->getVar("session.table");
    	$this->gc_probability = joz_config::getInstance()->getVar("session.gc_probability");
    	$this->gc_divisor = joz_config::getInstance()->getVar("session.gc_divisor");
    	
    	if(!isset($this->table_name))
    	{
    		$this->logger->warn("No table name found. Throwing exception");
    		throw new SBSException("No table name set in config. Set session.table to your table name");
    	}
    	
    	
    	
            // if $session_lifetime is specified and is an integer number
            if ($this->session_lifetime != '' && is_numeric($this->session_lifetime)) {

               	$this->logger->info("Found value for session lifetime. Overriding php.ini value");
                ini_set('session.gc_maxlifetime', $this->session_lifetime);

            }else{
            	$this->session_lifetime = ini_get('session.gc_maxlifetime');
            }

            $this->logger->info("Checking gcp, {$this->gc_probability}");
            // if $gc_probability is specified and is an integer number
            if ($this->gc_probability != '' && is_numeric($this->gc_probability)) {
				
            	$this->logger->info("Found value for garbage probability, overriding php.ini value");
                // set the new value
                ini_set('session.gc_probability', $this->gc_probability);

            }
			$this->logger->info("Chiecking gcdiv, {$this->gc_probability}");
            // if $gc_divisor is specified and is an integer number
            if ($this->gc_divisor != '' && is_numeric($this->gc_divisor)) {

            	$this->logger->info("Found value for garbage divisor. Overriding php.ini value");
                // set the new value
                ini_set('session.gc_divisor', $this->gc_divisor);

            }

            // get session lifetime

            // register the new handler
            session_set_save_handler(
                array(&$this, 'open'),
                array(&$this, 'close'),
                array(&$this, 'read'),
                array(&$this, 'write'),
                array(&$this, 'destroy'),
                array(&$this, 'gc')
            );
            // start the session
            session_start();
            $this->logger->debug("Session started...");
        }

    /**
     *  Custom close() function
     *
     *  @access private
     */
    public function close()
    {
        return true;

    }

    /**
     *  Custom destroy() function
     *
     *  @access private
     */
    public function destroy($session_id)
    {
		$this->logger->debug("Search and destroy");
    	$db = joz_db::getLink();
    	try{
    	$db->prepare(
    			"DELETE FROM
                {$this->table_name}
           		 WHERE
                session_id = ?")->execute(array($session_id));

    	} catch (PDOException $exception){
    		$this->logger->error("".$exception);
			return false;
        }
    }

    /**
     *  Custom gc() function (garbage collector)
     *
     *  @access private
     */
    public  function gc($maxlifetime)
    {
		$this->logger->info("Running garbage collect...");
    	$db = joz_db::getLink();
    	try{
    			$db->prepare("
            DELETE FROM
                {$this->table_name}
            WHERE
                session_expire < ?")->execute(array(time()-$maxlifetime));
    		
    	}catch(PDOException $e)
    	{
    		$this->logger->error("".$e);
    	}
    }

    /**
     *  Custom open() function
     *
     *  @access private
     */
    public  function open($save_path, $session_name)
    {
		$this->logger->debug("in open. returning true");
        return true;

    }

    /**
     *  Custom read() function
     *
     *  @access private
     */
    public  function read($session_id)
    {
    	$this->logger->debug("in read. Reading for session_id=$session_id");
    	$db = joz_db::getLink();
    	try {
    		$statement = $db->prepare("SELECT session_data FROM {$this->table_name} WHERE session_id = ? AND session_expire > ? AND http_user_agent = ?");
    		$statement->execute(array($session_id,time(),md5((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . $this->securityCode)));
    		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
    		if(count($result) > 0)
    		{
    			return $result[0]['session_data'];
    		}else{
    			return '';
    		}
    	} catch (PDOException $e) {
    		$this->logger->error("".$e);
    		return '';
    	}
    	

    }
    public  function stop()
    {
    	$this->logger->debug("Stop... Hammer time!");
        session_unset();
        session_destroy();
    }

    /**
     *  Custom write() function
     *
     *  @access private
     */
    public function write($session_id, $session_data)
    {
    	$this->logger->debug("In write. Sesssion_id = $session_id, session_data = $session_data");
    	$db = joz_db::getLink();
    	try{
    	$statement = $db->prepare("
    	INSERT INTO
    	{$this->table_name}
    	(session_id,
    	http_user_agent,
    	session_data,
    	session_expire)
    	VALUES(
    	?,
    	?,
    	?,
    	?)
    	ON DUPLICATE KEY UPDATE
    		session_data = ?,
    		session_expire = ?"
    	);
    	
    	$statement->execute(array(
    	$session_id,
    	md5((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . $this->securityCode),
    	$session_data,
    	time()+$this->session_lifetime,
    	$session_data,
    	time()+$this->session_lifetime,
    	));
    	
    	if($statement->rowCount() > 1){
    		return true;
    	}else{
    		return '';
    	}
    	}catch(PDOException $e){
    		$this->logger->error("".$e);
    		return false;
    	}

    }

}
