<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class PermissionsHelper extends AppHelper {

    public function isAuthorized($shortname = '',$permissions) {
        $check = array_search($shortname,$permissions);
        if ($check === false) {
            return false;
        }
        else {
            return true;
        }
    }
}
?>