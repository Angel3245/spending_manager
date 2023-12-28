<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");

/**
* Class UserMapper
*
* Database interface for User entities
*
* @author Juan Yuri Díaz Sánchez
*/
class UserMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Saves a User into the database
	*
	* @param User $user The user to be saved
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function save($user) {
		$stmt = $this->db->prepare("INSERT INTO users(alias,email,passwd,salt) values (?,?,?,?)");
		$stmt->execute( array($user->getAlias(), $user->getEmail(), $user->getPassword(), $user->getSalt()) );
	}

	/**
	* Checks if a given alias is already in the database
	*
	* @param string $alias the alias to check
	* @return boolean true if the alias exists, false otherwise
	*/
	public function aliasExists($alias) {
		$stmt = $this->db->prepare("SELECT count(alias) FROM users where alias=?");
		$stmt->execute(array($alias));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	/**
	* Checks if a given tuple of alias/email/password exists in the database
	*
	* @param string $alias the alias
	* @param string $email the email
	* @param string $passwd the password
	* @return boolean true the alias/email/passwrod exists, false otherwise.
	*/
	public function isValidUser($alias, $email, $passwd) {
		$stmt = $this->db->prepare("SELECT count(alias) FROM users where alias=? and email=? and passwd=?");
		$stmt->execute( array($alias, $email, $passwd) );

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	/**
	* Checks if a given tuple of alias/password exists in the database
	*
	* @param string $alias the alias
	* @param string $passwd the password
	* @return boolean true the alias/passwrod exists, false otherwise.
	*/
	public function isValidUserWithoutEmail($alias, $passwd) {
		$saltstmt = $this->db->prepare("SELECT salt FROM users where alias=?");
		$saltstmt->execute( array($alias) );

		$salt = $saltstmt->fetch(PDO::FETCH_ASSOC);

		if ($salt != null) {
			$stmt = $this->db->prepare("SELECT count(alias) FROM users where alias=? and passwd=?");
			$stmt->execute( array($alias, hash('sha256', $passwd.base64_decode($salt["salt"])) ));
	
			if ($stmt->fetchColumn() > 0) {
				return true;
			}
		}
	}

	public function deleteUser($alias) {
		$stmt = $this->db->prepare("DELETE FROM users where alias=?");
		$stmt->execute(array($alias));

		return true;

	}

	/* EXTENSIONS */
	/**
	* Update a User from the database
	*
	* @param User $user The user to be updated
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function update($user) {
		$stmt = $this->db->prepare("UPDATE users SET email=?, passwd=?, salt=? WHERE alias=?");
		$stmt->execute( array($user->getEmail(), $user->getPassword(), $user->getSalt(), $user->getAlias()) );
	}

	/**
	 * Loads a User from the database given its username
	 *
	 * @param User $username The specific user to find
	 * @throws PDOException if a database error occurs
	 * @return User The User instances.
	 * NULL if the User is not found
	 */
	public function findByUsername($username)
	{
		$queryFindById = "SELECT * FROM users WHERE alias=?";
		$stmt = $this->db->prepare($queryFindById);
		$stmt->execute(array($username));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($user != null) {
			return new User(
				$user["alias"],
				$user["email"],
				$user["passwd"],
				$user["role"]
			);
		} else {
			return NULL;
		}
	}
}
