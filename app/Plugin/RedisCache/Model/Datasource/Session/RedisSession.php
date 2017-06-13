<?php
/**
 *	12/27/2012 - JRW  Modify read/write methods to store data in a JSON string format
 *                    instead of PHP session format.
 *
 */
App::uses('CakeSessionHandlerInterface', 'Model/Datasource/Session');

/**
 * Redis Session Store Class. Uses Predis as the PHP connection class
 */
class RedisSession implements CakeSessionHandlerInterface {


    /**
     * Placeholder for cached Redis resource
     *
     * @var object
     * @access public
     */
    public $_Predis;


    /**
     * Seconds until key should expire
     *
     * @var int
     * @access public
     */
    public $timeout;


    /**
     * Prefix to apply to all Redis session keys
     *
     * @var string
     * @access public
     */
    public $prefix;


    /**
      * OPEN
      * - Connect to Redis
      * - Calculate and set timeout for SETEX
      * - Set session_name as key prefix
      *
      * @access public
      * @return boolean true
      */
    public function open() {
    	
        $name = Configure::read('Session.cookie');
        $timeout = Configure::read('Session.timeout');
        $this->timeout = $timeout * Security::inactiveMins();
        $this->prefix = $name;

        $this->_Predis = new Predis\Client(
            RedisConfig::$default['session']
            , array('prefix' => $this->prefix)
        );
        return true;
    }


    /**
     * CLOSE
     * - Disconnect from Redis
     *
     * @access public
     * @return boolean true
     */
    public function close() {
        $this->_Predis->disconnect();
        return true;
    }


    /**
     * READ
     * - Return whatever is stored in session ID (as key)
     * - Session ID is autoprefixed by Predis to create the key
     *
     * @param string $session_id
     * @access public
     * @return boolean
     */
    public function read( $session_id = '' ) {
			//return $this->_Predis->get($session_id);    
			$sess = $this->_Predis->get($session_id);
			if ($sess) {
      	// do a session_encode to reformat the session data from JSON to php session's format
      	$tmp = $_SESSION;
      	$_SESSION = json_decode($this->_Predis->get($session_id), true);
      	if (isset($_SESSION) && !empty($_SESSION) && $_SESSION != null){
  	      $new_data = session_encode();
  	 			$_SESSION = $tmp;
     		 	return $new_data;
        }
        else {
  	      return $sess;
  	    } 				
			}
			else {
				return $sess;
			}
    }


    /**
     * WRITE
     * - SETEX data with timeout calculated in open()
     * - Session ID is autoprefixed by Predis to create the key
     *
     * @param string $session_id
     * @param mixed $data
     * @access public
     * @return boolean
     */
    public function write( $session_id = '', $data = null ) {
			// modify this session handler to save the session data in JSON format, so that it's easier and faster for Node.js to read and decipher
   		//file_put_contents('/var/www/html/cakeinstall/app/webroot/jw.log', date('g:i:s') . " write\r\n", FILE_APPEND);

    	if($session_id && is_string($session_id)) {
        	 //return $this->_Predis->setex($session_id, $this->timeout, $data);
        	$tmp = $_SESSION;
        	session_decode($data);
        	$new_data = $_SESSION;
        	$_SESSION = $tmp;
   				//file_put_contents('/var/www/html/cakeinstall/app/webroot/jw.log', "write: " . json_encode($_SESSION) . "\r\n", FILE_APPEND);
					return $this->_Predis->setex($session_id, $this->timeout, json_encode($_SESSION));
        }

        return false;
    }


    /**
     * DESTROY
     * - DEL the key from store
     * - Session ID is autoprefixed by Predis to create the key
     *
     * @param string $session_id
     * @access public
     * @return boolean
     */
    public function destroy( $session_id = '' ) {
        // Predis::del returns an integer 1 on delete, convert to boolean
        return $this->_Predis->del($session_id) ? true : false;
    }


    /**
     * GARBAGE COLLECTION
     * not needed as SETEX automatically removes itself after timeout
     * ie. works like a cookie
     *
     * @param int $expires defaults to null
     * @access public
     * @return boolean
     */
    public function gc( $expires = null ) {
        return true;
    }
}