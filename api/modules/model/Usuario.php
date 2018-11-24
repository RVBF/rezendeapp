<?php

/**
 *	Usuario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Usuario {

    private $id;
    private $nome;
    private $login;
    private $senha;
    private $loja;
    private $grupoUsuario;

    const TABELA = 'usuario';
    
	const TAMANHO_MINIMO_LOGIN = 5;
	const TAMANHO_MAXIMO_LOGIN = 30;

	const TAMANHO_MINIMO_SENHA = 8;
	const TAMANHO_MAXIMO_SENHA = 50;

    function __construct($id = 0, $usuario = '', $login = '', $senha = '') {
        $this->id = $id;
		$this->usuario = $usuario;        
        $this->login = $login;
        $this->senha = $senha;
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

    public function getNome(){
        return $this->nome; 
    }
 
    public function setNome($nome){
        $this->nome = $nome;
    }

    public function getLoja(){
        return $this->loja; 
    }
 
    public function setLoja($loja){
        $this->loja = $loja;
    }

    public function getGrupoUsuario(){
        return $this->grupoUsuario; 
    }
 
    public function setGrupoUsuario($grupoUsuario){
        $this->grupoUsuario = $grupoUsuario;
    }
}
?>