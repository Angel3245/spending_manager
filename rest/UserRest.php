<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");
require_once(__DIR__."/BaseRest.php");

/**
* Class UserRest
*
* It contains operations for adding and check users credentials.
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
* @author Jose Ángel Pérez Garrido
*/
class UserRest extends BaseRest {
	private $userMapper;

	public function __construct() {
		parent::__construct();

		$this->userMapper = new UserMapper();
	}

	public function postUser($data) {
		$salt = random_bytes(4);
		$user = new User($data->username, $data->email, $data->password, NULL, base64_encode($salt));
		try {
			$user->checkIsValidForRegister();

			if(!$this->userMapper->aliasExists($user->getAlias())){
				$password = hash('sha256', ($data->password).$salt);
				$user->setPassword($password);

				$this->userMapper->save($user);

				header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
				header("Location: ".$_SERVER['REQUEST_URI']."/".$data->username);
			} else {
				header($_SERVER['SERVER_PROTOCOL'].' 400 Already Exists');
				header('Content-Type: application/json');
				echo(json_encode(array("alias"=>"Alias already exists")));
			}
		}catch(ValidationException $e) {
			http_response_code(400);
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function login($username) {
		$currentLogged = parent::authenticateUser();
		if ($currentLogged->getAlias() != $username) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not authorized to login as anyone but you");
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
			//Send user role
			echo($currentLogged->getRole());
		}
	}

	/* Update user profile */
	public function putUser($username,$data) {
		$currentLogged = parent::authenticateUser();
		if ($currentLogged->getAlias() != $username) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not authorized to update another user");
		} else {	
			$salt = random_bytes(4);
			$user = new User($username, $data->email, $data->password, NULL, base64_encode($salt));

			if($this->userMapper->aliasExists($username)){
				try {
					$user->checkIsValidForUpdate();

					$password = hash('sha256', ($data->password).$salt);
					$user->setPassword($password);

					$this->userMapper->update($user);
		
					header($_SERVER['SERVER_PROTOCOL'].' 204 Updated');
					//header("Location: ".$_SERVER['REQUEST_URI']."/".$alias);
				}catch(ValidationException $e) {
					http_response_code(400);
					header('Content-Type: application/json');
					echo(json_encode($e->getErrors()));
				}
			} else {
				header($_SERVER['SERVER_PROTOCOL'].' 400 Not Found');
				echo("The user you want to update does not exist");
			}
		}
	}

	/* Delete user */
	public function deleteUser($username) {
		$currentLogged = parent::authenticateUser();
		if ($currentLogged->getAlias() != $username) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not authorized to delete another user");
		} else {	
			if($this->userMapper->aliasExists($username)){
				try {
					$this->userMapper->deleteUser($username);
		
					header($_SERVER['SERVER_PROTOCOL'].' 204 Deleted');
					//header("Location: ".$_SERVER['REQUEST_URI']."/".$alias);
				} catch(PDOException $e) {
					header($_SERVER['SERVER_PROTOCOL'].' 500 Internal server error');
					echo("Database error");
				}
			} else {
				header($_SERVER['SERVER_PROTOCOL'].' 400 Not Found');
				echo("The user you want to delete does not exist");
			}
		}
	}

	/* Get info user */
	public function getUser($username) {
		$currentLogged = parent::authenticateUser();
		if ($currentLogged->getAlias() != $username) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not authorized to get info from anyone but you");
		} else {
			if($this->userMapper->aliasExists($username)){
				try {
					$user = $this->userMapper->findByUsername($username);
		
					// json_encode Spending objects.
					// since Spending objects have private fields, the PHP json_encode will not
					// encode them, so we will create an intermediate array using getters and
					// encode it finally
					$user_array = array();
					array_push($user_array, array(
						"alias" => $user->getAlias(),
						"email" => $user->getEmail()
						// "passwd" => $user->getPassword() // password cannot be sent by network for security reasons
					));

					header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
					header('Content-Type: application/json');
					echo(json_encode($user_array));

				} catch(PDOException $e) {
					header($_SERVER['SERVER_PROTOCOL'].' 500 Internal server error');
					echo("Database error");
				}
			} else {
				header($_SERVER['SERVER_PROTOCOL'].' 400 Not Found');
				echo("The user you want to get does not exist");
			}
		}
	}
}

// URI-MAPPING for this Rest endpoint
$userRest = new UserRest();
URIDispatcher::getInstance()
->map("GET",	"/user/$1/info", array($userRest,"getUser"))
->map("GET",	"/user/$1", array($userRest,"login"))
->map("POST", "/user", array($userRest,"postUser"))
->map("PUT", "/user/$1", array($userRest,"putUser"))
->map("DELETE", "/user/$1", array($userRest,"deleteUser"));