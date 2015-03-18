<?php
use Phalcon\Mvc\Controller;
class UsersController extends Controller {
	private $token;

	public function initialize(){
		$this->response->setContentType('application/json', 'utf-8');
	}
	private function _auth($mail,$password){
		$result=false;
		$user=User::findFirst(array(
			"conditions" => "mail = ?1",
			"bind"       => array(1 => $mail)
		));
		if($user){
			if (hash_equals($user->getPassword(), crypt($password, $user->getSalt()))) {
				$this->token=bin2hex(openssl_random_pseudo_bytes(16));
				$this->session->set("token",$this->token);
				$result=true;
			}
		}
		return $result;
	}

	public function userFormAction(){
	}

	public function userAddAction(){
		$user=new User();
		$user->setMail($this->request->getPost("mail"));
		$user->setPassword($this->request->getPost("password"));
		if($user->save()){
			$token=bin2hex(openssl_random_pseudo_bytes(16));
			$this->persistent->token = $token;
			echo '{"token": "'.$token.'","inserted": true}';
		}else{
			echo '{"inserted":false}';
		}
	}
	public function checkUserExistsAction($mail){
		$user=User::findFirst(array(
				"conditions" => "mail = ?1",
				"bind"       => array(1 => $mail)
		));
		if($user){
			echo '{"exists": true,"mail": "'.$mail.'"}';
		}else{
			echo '{"exists": false,"mail": "'.$mail.'"}';
		}
	}
	public function checkConnectionAction($mail,$password){
		if($this->_auth($mail, $password)){
			echo '{"token": "'.$this->token.'","connected": true}';
		}else{
			echo '{"connected": false}';
		}
	}

	public function checkConnectedAction(){
		if($this->session->has("token")){
			echo '{"token" : "'.$this->session->get("token").'",connected: true}';
		}else{
			echo '{"connected": false}';
		}
	}

	public function connectAction(){
		$mail=$this->request->getPost("mail");
		$password=$this->request->getPost("password");
		if($this->_auth($mail, $password)){
			echo '{"token": "'.$this->token.'","connected": true}';
		}else{
			echo '{"connected": false}';
		}
	}

	public function disconnectAction(){
		$this->session->destroy();
		echo '{"connected": false}';
	}
}