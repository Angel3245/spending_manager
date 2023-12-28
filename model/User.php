<?php
// file: model/User.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class User
*
* Represents a User in the blog
*
* @author Juan Yuri Díaz Sánchez
*/
class User {

	/**
	* The alias of the user
	* @var string
	*/
	private $alias;

	/**
	* The email of the user
	* @var string
	*/
	private $email;

	/**
	* The password of the user
	* @var string
	*/
	private $passwd;

	/**
	* The role of the user
	* @var string
	*/
	private $role;

	/**
	* The salt of the password
	* @var string
	*/
	private $salt;

	/**
	* The constructor
	*
	* @param string $alias The alias of the user
	* @param string $email The email of the user
	* @param string $passwd The password of the user
	* @param string $role The role of the user
	* @param string $salt The salt of the password
	*/
	public function __construct($alias=NULL, $email=NULL, $passwd=NULL, $role=NULL, $salt=NULL) {
		$this->alias = $alias;
		$this->email = $email;
		$this->passwd = $passwd;
		$this->role = $role;
		$this->salt = $salt;
	}

	/**
	* Gets the alias of this user
	*
	* @return string The alias of this user
	*/
	public function getAlias() {
		return $this->alias;
	}

	/**
	* Sets the alias of this user
	*
	* @param string $alias The alias of this user
	* @return void
	*/
	public function setAlias($alias) {
		$this->alias = $alias;
	}

	/**
	* Gets the email of this user
	*
	* @return string The email of this user
	*/
	public function getEmail() {
		return $this->email;
	}

	/**
	* Sets the email of this user
	*
	* @param string $email The email of this user
	* @return void
	*/
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	* Gets the password of this user
	*
	* @return string The password of this user
	*/
	public function getPassword() {
		return $this->passwd;
	}
	/**
	* Sets the password of this user
	*
	* @param string $passwd The password of this user
	* @return void
	*/
	public function setPassword($passwd) {
		$this->passwd = $passwd;
	}

	/**
	* Gets the role of this user
	*
	* @return string The role of this user
	*/
	public function getRole() {
		return $this->role;
	}
	/**
	* Sets the role of this user
	*
	* @param string $role The role of this user
	* @return void
	*/
	public function setRole($role) {
		$this->role = $role;
	}

	/**
	* Gets the salt of this user password
	*
	* @return string The salt of this user password
	*/
	public function getSalt() {
		return $this->salt;
	}
	/**
	* Sets the salt of this user password
	*
	* @param string $salt The salt of this user password
	* @return void
	*/
	public function setSalt($salt) {
		$this->salt = $salt;
	}

	/**
	* Checks if the current user instance is valid
	* for being registered in the database
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForRegister() {
		$errors = array();
		if (strlen($this->alias) < 3) {
			$errors["alias"] = "Alias must be at least 3 characters length";
		}
		if (strlen($this->alias) > 25) {
			$errors["alias"] = "Alias must be at most 25 characters length";
		}
		if (strlen($this->email) < 5) {
			$errors["email"] = "Email must be at least 5 characters length";
		}
		if (strlen($this->email) > 45) {
			$errors["email"] = "Email must be at most 45 characters length";
		}
		if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $this->email)) {
			$errors["email"] = "Email must follow the format name@domain";
		}
		if (strlen($this->passwd) < 5) {
			$errors["passwd"] = "Password must be at least 5 characters length";
		}
		if (strlen($this->passwd) > 64) {
			$errors["passwd"] = "Password must be at most 64 characters length";
		}
		
		if (sizeof($errors)>0){
			throw new ValidationException($errors, "user is not valid");
		}
	}

	/**
	* Checks if the current user instance is valid
	* for being updated in the database
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForUpdate() {
		$errors = array();

		//same restrictions as in register
		try{
			$this->checkIsValidForRegister();
		}catch(ValidationException $ex) {
			foreach ($ex->getErrors() as $key=>$error) {
				$errors[$key] = $error;
			}
		}

		if (sizeof($errors)>0){
			throw new ValidationException($errors, "user is not valid");
		}
	}
}
