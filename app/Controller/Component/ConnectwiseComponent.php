<?php    
    App::uses('Component', 'Controller');
    
    class ConnectwiseComponent extends Component {
        var $url = "";
        var $user = "";
        var $pass = "";
        var $sessionid = '';
        var $error = '';
        var $raw = '';
        var $response = '';
        var $log = '';
        var $data;
        
        public function configure($url = '', $username = '', $password = '') {
        	if (isset($url)) {
        		$this->url = $url;
        	}
        	if (isset($username)) {
        		$this->user = $username;
        	}
        	if (isset($password)) {
        		$this->pass = $password;
        	}
        	
        }
        
        
        public function create() {
            
            $this->log .= "Creating new record\r\n";
            if (!$this->login()) {
                return false;
            }
            
            $parameters = array(
                "session" => $this->sessionid,
                "module_name" => "Leads",
                "name_value_list" => array(
                    "description" => "Trent testing openanswer integration",
                    "first_name" => "Trent",
                    "last_name" => "Chastain",
                    "title" => "The Awesome",
                ),
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
            
            return true;
        }
        
        
        private function curl_execute($parameters = array()) {
            
            
            
            $url = $this->url.$parameters['module'];
            
            
            
            
            //Init our curl object
            $this->log .= "Initializing curl\r\n";
            $curl = curl_init();
            
            //set curl options
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
        public function get_methods() {
            
            $this->log .= "Getting field list\r\n";
            if (!$this->login()) {
                return false;
            }
            
            $parameters = array(
                "session" => $this->sessionid,
                "module_name" => "Leads",
                "fields" => "",
            );
            
            $post = array(
                "method" => "get_module_fields",
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => json_encode($parameters),
            );
            
            $this->log .= "Executing curl\r\n";
            if (!$this->curl_execute($post)) {
                return false;
            }
            
            return true;
        }
        
        
        
        public function get_entries($module = '',$fields = array()) {
            
            
            
            if $module = "";
            
            
            $this->log .= "Getting entry list\r\n";
			
			
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
                    $querystring .= strtolower($module).".".$field." Contains '".$value."'";
                    if ($mysize < sizeof($name_value_list)) {
                        $querystring .= " AND ";
                    }
                    $mysize++;
                }
            }
            $this->log("QueryString:".$querystring);
            
            
            $parameters = array(
                "session" => $this->sessionid,
                "module_name" => $module,
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
                "page" => 1,
                "pageSize" => 25,
                "conditions" => $querystring,
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
            
            //$this->log($data);
            
            
            
            
            return true;
        }
        public function set_entry($module = '',$fields = array()) {
            
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
                "module_name" => $module,
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