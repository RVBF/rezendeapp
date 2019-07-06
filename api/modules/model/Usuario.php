<?php

/**
 *	Usuario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Usuario {
	private $id;
	private $login;
    private $senha;
    private $colaborador;
	private $gruposUsuario;
	private $administrador;

	const TABELA = 'usuario';
    
	const TAMANHO_MINIMO_LOGIN = 4;
	const TAMANHO_MAXIMO_LOGIN = 30;

	const TAMANHO_MINIMO_SENHA = 3;
	const TAMANHO_MAXIMO_SENHA = 20;

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
    
    public function getAdministrador(){
        return $this->administrador; 
    }
 
    public function setAdministrador($administrador){
        $this->administrador = $administrador;
    }

    public function getGruposUsuario(){
        return $this->gruposUsuario; 
    }
 
    public function setGruposUsuario($gruposUsuario){
        $this->gruposUsuario = $gruposUsuario;
    }
    public function getColaborador(){
        return $this->colaborador; 
    }
 
    public function setColaborador($colaborador){
        $this->colaborador = $colaborador;
    }
}
?>