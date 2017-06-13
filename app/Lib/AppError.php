<?php
//in app/Lib/AppError.php
class AppError {
    public static function handleError($code, $description, $file = null,
        $line = null, $context = null) {
        fb("Code: $code - $description, line $line of $file"); 
        fb($context);
    }
}
?>