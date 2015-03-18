<?php
use Phalcon\Mvc\Model;
class User extends Model {
	protected $id;
	protected $mail;
	protected $password;
	protected $salt;

	/**
	 * Initialize method for model.
	 */
	public function initialize()
	{
		$this->setSource('user');
	}

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getMail() {
		return $this->mail;
	}
	public function setMail($mail) {
		$this->mail = $mail;
		return $this;
	}
	public function getPassword() {
		return $this->password;
	}
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}
	public function getSalt() {
		return $this->salt;
	}
	public function setSalt($salt) {
		$this->salt = $salt;
		return $this;
	}

	public function beforeCreate(){
		$this->salt = uniqid(mt_rand(), true);
		$this->password=crypt($this->password,$this->salt);
	}


}