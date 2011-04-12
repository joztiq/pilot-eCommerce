<?php 
/**
 * Joztiq database class
 * 
 * Class uses PDO(make sure your pdo driver is active!), config using main.ini.
 * @author Daniel Maison
 * @author Markus Gerdau
 *
 */
class joz_db {
	/**
	 * Holding the active link
	 * @var PDO
	 */
    private static $link = null ;

    /**
     * Call hook.
     * @return PDO
     */
    public static function getLink ( ) {
        if ( self :: $link ) {
            return self :: $link ;
        }
        $config = Joztiq::app()->config;

        $driver = $config->db_driver ;
        $dsn = "${driver}:" ;
        $user = $config->db_username;
        $password = $config->db_password;
        $options = $config->db_options;
        $attributes = $config->db_attributes;

        foreach ( $config->db_dsn as $k => $v ) {
            $dsn .= "${k}=${v};" ;
        }
		
        try
		{
			self :: $link = new PDO ( $dsn, $user, $password, $options ) ;

       		foreach ( $attributes as $k => $v ) {
            	self :: $link -> setAttribute ( constant ( "PDO::{$k}" )
                	, constant ( "PDO::{$v}" ) ) ;
        	}
	
    	    
			}catch(PDOException $e)
    		{
    			Logger::getLogger('joztiq.framework.joztiq.joz_db')->error("Could not connect to DB:".$e);
    			throw $e;
    		}
		return self :: $link ;
    	}
    /**
     * allows for static calling.
     * 
     * Will use existing link or make a new.
     * @param string $name
     * @param array $args
     */
    public static function __callStatic ( $name, $args ) {
        $callback = array ( self :: getLink ( ), $name ) ;
        return call_user_func_array ( $callback , $args ) ;
    }
}