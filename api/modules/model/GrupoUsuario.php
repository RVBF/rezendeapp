<?php

/**
 *	GrupoDeUsuario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class GrupoUsuario {

    private $id;
    private $nome;
    private $descricao;
    private $usuarios;
    private $administrador; 
    
    function __construct($id = 0, $nome = '', $descricao = '', $usuarios = []) {
        $this->id = $id;
        $this->nome =  $nome;
        $this->descricao = $descricao;
        $this->usuarios = $usuarios;
        $his->administrador = false;
    }

    public function getId() {
       return $this->id; 
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDescricao() {
        return $this->descricao; 
    }
 
    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
    
    public function getNome(){
        return $this->nome; 
    }
 
    public function setNome($nome){
        $this->nome = $nome;
    }

    public function getAdministrador(){
        return $this->administrador; 
    }
 
    public function setAdministrador($administrador){
        $this->administrador = $administrador;
    }

    public function getUsuarios(){
        return $this->usuarios; 
    }
 
    public function setUsuarios($usuarios){
        $this->usuarios = $usuarios;
    }

    public function addUsuario($usuario){
        $this->usuarios[] = $usuario;
    }

    public function removerUsuario($usuario){
        $key  = array_search($usuario, $this->usuarios);
    }    
}
?>