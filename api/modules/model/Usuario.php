<?php

/**
 *	Usuario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Usuario {

	private $id;
    private $login;

    function __construct($id = 0, $login = '') {
		$this->id = $id;
        $this->login = $login;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getLogin(){
        return $this->login; 
    }
 
    public function setLogin($login){
        $this->login = $login;
    }
}
?>