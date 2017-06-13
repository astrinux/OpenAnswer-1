<?php
App::uses('FormAuthenticate', 'Controller/Component/Auth');
 
class OpenanswerAuthenticate extends FormAuthenticate
{
 
/**
 * Checks the fields to ensure they are supplied.
 *
 * @param CakeRequest $request The request that contains login information.
 * @param string $model The model used for login verification.
 * @param array $fields The fields to be checked.
 * @return boolean False if the fields have not been supplied. True if they exist.
 */
	protected function _checkFields(CakeRequest $request, $model, $fields) {
		if (empty($request->data[$model])) {
			return false;
		}
		foreach (array($fields['username'], $fields['password']) as $field) {
			$value = $request->data($model . '.' . $field);
			if (empty($value) || !is_string($value)) {
				return false;
			}
		}
		return true;
	}

/**
 * Authenticates the identity contained in a request. Will use the `settings.userModel`, and `settings.fields`
 * to find POST data that is used to find a matching record in the `settings.userModel`. Will return false if
 * there is no post data, either username or password is missing, or if the scope conditions have not been met.
 *
 * @param CakeRequest $request The request that contains login information.
 * @param CakeResponse $response Unused response object.
 * @return mixed False on login failure. An array of User data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		$userModel = $this->settings['userModel'];
		list(, $model) = pluginSplit($userModel);
    
		$fields = $this->settings['fields'];
		if (!$this->_checkFields($request, $model, $fields)) {
			return false;
		}
    $model = ClassRegistry::init($userModel);
    $conditions = array('username' => $request->data($userModel . '.' . $fields['username']), 'password' =>  $this->passwordHasher()->hash($request->data($userModel . '.' . $fields['password'])), 'deleted' => '0');
    $result = $model->find('first', array('conditions' => $conditions));
    if (!$result) return false;
    
		$user = $result[$userModel];
		unset($user[$fields['password']]);

		return $user;
	}
}