<?php

/**
 *	Colaborador
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Colaborador {

    private $id;
    private $nome;
    private $sobrenome;
    private $email;
    private $usuario;
    private $lojas;

    const TAM_TEXT_MIM = 3;
    const TAM_TEXT_MAX = 50;

    function __construct($id = 0, $nome = '', $sobrenome = '', $email = '', $usuario = null,  $setor = null, $lojas = []) {
        $this->id = $id;
        $this->nome =  $nome;
        $this->sobrenome = $sobrenome;
        $this->email = $email;
        $this->usuario = $usuario;
        $this->lojas = $lojas;
        $this->setor = $setor;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getNome(){
        return $this->nome; 
    }
 
    public function setNome($nome){
        $this->nome = $nome;
    }

    public function getSobrenome(){
        return $this->sobrenome; 
    }
 
    public function setSobrenome($sobrenome){
        $this->sobrenome = $sobrenome;
    }

    public function getEmail(){
        return $this->email; 
    }
 
    public function setEmail($email){
        $this->email = $email;
    }

    public function getUsuario(){
        return $this->usuario; 
    }
 
    public function setUsuario($usuario){
        $this->usuario = $usuario;
    }


    public function getSetor(){
        return $this->setor; 
    }
 
    public function setSetor($Setor){
        $this->setor = $setor;
    }

    public function getLojas(){
        return $this->lojas; 
    }
 
    public function setLojas($lojas){
        $this->lojas = $lojas;
    }

    public function addLoja($loja){
        $this->lojas[] = $loja;
    }
}
?>