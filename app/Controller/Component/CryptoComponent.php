<?php    
    App::uses('Component', 'Controller');
    
    class CryptoComponent extends Component {
    
        function decrypt($str, $key=ENCRYPTION_KEY){
            $str = base64_decode(urldecode($str));
            $result = '';
            for($i=0; $i<strlen($str); $i++) {
                $char = substr($str, $i, 1);
                $keychar = substr($key, ($i % strlen($key))-1, 1);
                $char = chr(ord($char)-ord($keychar));
                $result.=$char;
            }
            return $result;
        }  
        
        function encrypt($str, $key=ENCRYPTION_KEY) {
            $result = '';
            for($i=0; $i<strlen($str); $i++) {
                $char = substr($str, $i, 1);
                $keychar = substr($key, ($i % strlen($key))-1, 1);
                $char = chr(ord($char)+ord($keychar));
                $result.=$char;
            }
            return urlencode(base64_encode($result));
        }
    }
?>