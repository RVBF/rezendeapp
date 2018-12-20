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
    private $senha;
    private $gruposUsuario;

    const TABELA = 'usuario';
    
	const TAMANHO_MINIMO_LOGIN = 5;
	const TAMANHO_MAXIMO_LOGIN = 30;

	const TAMANHO_MINIMO_SENHA = 8;
	const TAMANHO_MAXIMO_SENHA = 50;

    function __construct($id = 0, $login = '', $senha = '', $gruposUsuario = []) {
        $this->id = $id;
        $this->login = $login;
        $this->senha = $senha;
        $this->gruposUsuarios = $gruposUsuario;
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

    public function getSenha(){
        return $this->senha; 
    }
 
    public function setSenha($senha){
        $this->senha = $senha;
    }

    public function getGruposUsuario(){
        return $this->gruposUsuario; 
    }
 
    public function setGruposUsuario($gruposUsuario){
        $this->gruposUsuario = $gruposUsuario;
    }
}
?>