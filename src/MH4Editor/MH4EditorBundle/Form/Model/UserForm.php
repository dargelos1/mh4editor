<?php

namespace MH4Editor\MH4EditorBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use MH4Editor\MH4EditorBundle\Entity\User;

class UserForm
{
	/**
	 * @Assert\Type(type="MH4Editor\MH4EditorBundle\Entity\User")
	 * @Assert\Valid
	 * @Assert\NotBlank
	 */
	protected $user;


	protected $password_confirm;


	public function getUser(){

		return $this->user;
	}
	public function setUser($user){

		$this->user = $user;
	}

	public function getPasswordConfirm(){
		return $this->password_confirm;
	}

	public function setPasswordConfirm($password_confirm){
		$this->password_confirm = $password_confirm;
	}

	//Funcion que funcion como método para errores globales de form
	/**
	 * @Assert\True(message = "The passwords doesn't match.")
	 */
	/*public function isPasswordEqual(){
		return $this->user->getPassword() === $this->password_confirm;
	}*/
}