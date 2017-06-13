<?php    
    App::uses('Component', 'Controller');
    
    class SugarcrmComponent extends Component {
        var $url = "";
        var $user = "";
        var $pass = "";
        var $sessionid = '';
        var $error = '';
        var $raw = '';
        var $response = '';
        var $log = '';
        var $data;
        
        public function configure($options = array()) {
        	if (isset($options['url'])) {
        		$this->url = $options['url'];
        	}
        	if (isset($options['url'])) {
        		$this->user = $options['username'];
        	}
        	if (isset($options['url'])) {
        		$this->pass = $options['password'];
        	}
        	if (isset($options['module'])) {
        		$this->module = $options['module'];
        	}
        	
        }
        /*
        * function login.
        * Connects to remote API and submits authentication credentials
        * stores session id for additional requests if possible.
        *
        * returns true on success, false on failure.
        *
        */
        private function login() {
            $this->log .= "Checking to see if we are logged in\r\n";
            //check to see if we are already logged in.
            if ($this->sessionid != '') {
                $this->log .= "We are already logged in.\r\n";
                //session id is already set (we already logged in, so return true)
                return true;
            }
            $this->log .="Not logged in,attempting to do so.";
            
            //set the parameters required by the login call.
            $parameters = array(
                "user_auth" => array(
                    "user_name" => $this->user,
                    "password" => md5($this->pass), //password must be in the form of an md5 hash
                ),
                "application" => "VoiceNation", //not used according to the documentation, but sending anyway.
                "name_value_list" => array(
                    "language" => "english",
                    "notifyonsave" => false, //tells sugar/suite to enable/disable notifications when creates/updates are made.
                )
            );
            
            //set the parameters required by the http post, which includes the login call parameters from above.
            $post = array(
                "method" => "login",
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => json_encode($parameters),
            );
            
            //execute the http request to the API
            $this->log .= "Executing Curl, Attempting to log in.\r\n";
            if (!$this->curl_execute($post)) {
                $this->log .= "Login attempt failed\r\n";
                $this->log .= $this->error."\r\n";
                return false;
            }
            
            
            
            //verify that we have a session id returned, which indicated we successfully logged in.
            $this->log .= "Verifying login response\r\n";
            if (!isset($this->response->id)) {
                //No id is present, we did not log in. let's try to see why.
                $this->log .= "Login session id was not found in response\r\n";
                if (isset($this->response->description)) {
                    //the json response contained an error description, save it.
                    $this->error = $this->response->description;
                    $this->log .= $this->error."\r\n";
                }
                else {
                    //the response did not have an error description, so we don't know what happened. the raw response can be examined though.
                    $this->error = "Failed for unknown reason, unrecognized response";
                    $this->log .= $this->error."\r\n";
                }
                return false;
            }
            
            $this->log .= "Login was successful, session id: ".$this->response->id."\r\n";
            $this->sessionid = $this->response->id;
            
            return true;
        }
        
        
        
        private function curl_execute($parameters = array(),$skip_login = false) {
            
            
            
            //Init our curl object
            $this->log .= "Initializing curl\r\n";
            $curl = curl_init();
            
            //set curl options
            $this->log("Posting to".$this->url);
            $this->log("Params:".$parameters);
            curl_setopt($curl, CURLOPT_URL,$this->url); //set the url the of the api to connect to
            curl_setopt($curl, CURLOPT_POST, 1); //we will use http post for this
            curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($parameters));  //the data that will be posted to the API.
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //this tells curl to return the results of the post to an assigned variable
            curl_setopt($curl, CURLOPT_FAILONERROR,true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); //do not allow curl to follow redirects
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //force http version 1.0 for compatibility
            
            //execute the curl request, and store the results 
            $this->log .= "Executing curl request\r\n";
            $this->raw = curl_exec($curl);
            $this->log($this->raw);
            //close our curl handle
            curl_close($curl);
            
            //test the response to see if we successfully logged in.
            if (!$this->raw) { 
                //The response is null or false, the request failed.
                $this->error = "Failed retrieving a response from the API";
                $this->log .= $this->error."\r\n";
                return false;
            }
            
            //we got a response, attempt to decode it
            //$this->log(print_r($this->raw,true));
            $data = json_decode($this->raw);
            
            //verify the json response
            if ($data === null) {
                //we received a response but it was not JSON formatted, so we cannot process it further.
                $this->error = "Response was not correctly formatted, API error.";
                $this->log .= $this->error."\r\n";
                return false;
            }
            $this->log .= "Curl request successful\r\n";
            $this->response = $data;
            
            return true;
            
        }
        
        
        
        
        public function search($fields = array()) {
            
            $this->log .= "Getting entry list\r\n";
            if (!$this->login()) {
                return false;
            }
            $name_value_list = array();
            //remove any empty fields (we don't need them.)
            if (isset($fields) and sizeof($fields) > 0) {
                foreach ($fields as $field => $value) {
                    if ($value) {
                        $name_value_list[$field] = $value;
                    }
                }
            }
            
            $querystring = '';
            if (isset($name_value_list) and sizeof($name_value_list) > 0) {
                $mysize = 1;
                foreach ($name_value_list as $field => $value) {
                    $querystring .= strtolower($this->module).".".$field." LIKE '%".$value."%'";
                    if ($mysize < sizeof($name_value_list)) {
                        $querystring .= " AND ";
                    }
                    $mysize++;
                }
            }
            $this->log("QueryString:".$querystring);
            
            
            $parameters = array(
                "session" => $this->sessionid,
                "module_name" => $this->module,
                "query" => $querystring,
                "order_by" => "",
                "offset" => 0,
                "select_fields" => array(),
                "link_name_to_fields_array" => array(),
                "max_results" => 15,
                "deleted" => false,
                "favorites" => false
            );
            
            $post = array(
                "method" => "get_entry_list",
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => json_encode($parameters),
            );
            
            $this->log .= "Executing curl\r\n";
            if (!$this->curl_execute($post)) {
                return false;
            }
            
            $data = array();
            
            
            //foreach ($this->response['entry_list'] as $entry) {
            foreach ($this->response->entry_list as $entry) {
                $data[$entry->id] = array();
                foreach ($entry->name_value_list as $field) {
                    $data[$entry->id][$field->name] = $field->value;
                }
                
            }
            
            $this->data = $data;
            
            
            return true;
        }
        
        
        
        
        public function create($fields = array()) {
            
            $this->log .= "Setting entry\r\n";
            if (!$this->login()) {
                return false;
            }
            $name_value_list = array();
            //remove any empty fields (we don't need them.)
            if (isset($fields) and sizeof($fields) > 0) {
                foreach ($fields as $field => $value) {
                    $name_value_list[$field] = $value;
                }
            }
            
            $parameters = array(
                "session" => $this->sessionid,
                "module_name" => $this->module,
                "name_value_list" => $name_value_list,
                "track_view" => true
            );
            
            $post = array(
                "method" => "set_entry",
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => json_encode($parameters),
            );
            
            $this->log .= "Executing curl\r\n";
            if (!$this->curl_execute($post)) {
                return false;
            }
            
            $data = array();
            
            return true;
        }
        
        public function update($fields = array()) {
            
            $this->log .= "Setting entry\r\n";
            if (!$this->login()) {
                return false;
            }
            $name_value_list = array();
            //remove any empty fields (we don't need them.)
            if (isset($fields) and sizeof($fields) > 0) {
                foreach ($fields as $field => $value) {
                    $name_value_list[$field] = $value;
                }
            }
            
            $parameters = array(
                "session" => $this->sessionid,
                "module_name" => $this->module,
                "name_value_list" => $name_value_list,
                "track_view" => true
            );
            
            $post = array(
                "method" => "set_entry",
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => json_encode($parameters),
            );
            
            $this->log .= "Executing curl\r\n";
            if (!$this->curl_execute($post)) {
                return false;
            }
            
            $data = array();
            
            return true;
        }
        
    }
?>