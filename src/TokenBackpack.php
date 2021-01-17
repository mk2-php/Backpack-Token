<?php

namespace mk2\backpack_token;

use Mk2\Libraries\Backpack;

use mk2\backpack_encrypt\EncryptBackpack;
use mk2\backpack_session\SessionBackpack;

class TokenBackpack extends Backpack{

	public $hashSalt=[
		"left"=>"mk2lefthashsalt123456789************************",
		"right"=>"mk2righthashsalt123456789************************",
	];

	/**
	 * __construct
	 */
	public function __construct(){
		parent::__construct();

		if(!empty($this->alternativeEncrypt)){
			$this->Encrypt=new $this->alternativeEncrypt();
		}
		else{
			$this->Encrypt=new EncryptBackpack();
		}

		if(!empty($this->alternativeSession)){
			$this->Session=new $this->alternativeSession();
		}
		else{
			$this->Session=new SessionBackpack();
		}

	}

	/**
	 * set
	 * @param string $tokenName
	 */
	public function set($tokenName){

		$tokenBase=date_format(date_create("now"),"YmdHis");
		
		$token=$this->Encrypt->hash($tokenBase.$this->hashSalt["left"]);

		$sessionToken=$this->Encrypt->hash($token.$this->hashSalt["right"]);

		$this->Session->write("tokenCenterHash_".$tokenName,$sessionToken);

		return $token;
	}

	/**
	 * berify
	 * @param string $tokenName
	 * @param string $token
	 */
	public function verify($tokenName,$token){

		$sessionToken=$this->Session->read("tokenCenterHash_".$tokenName);
		
		if(!$sessionToken){
			return false;
		}

		$targetToken=$this->Encrypt->hash($token.$this->hashSalt["right"]);

		if($targetToken!=$sessionToken){
			return false;
		}

		return true;
	}

}