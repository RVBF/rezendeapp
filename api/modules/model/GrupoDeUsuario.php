<?php

/**
 *	GrupoDeUsuario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class GrupoDeUsuario {

    private $id;
    private $nome;
    private $descricao;

    function __construct($id = 0, $nome = '', $descricao = '') {
        $this->id = $id;
        $this->nome =  $nome;
        $this->descricao = $descricao;
        $this->senha = $senha;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getDescricao(){
        return $this->descricao; 
    }
 
    public function setDescricao($descricao){
        $this->descricao = $descricao;
    }
    public function getNome(){
        return $this->nome; 
    }
 
    public function setNome($nome){
        $this->nome = $nome;
    }
}
?>