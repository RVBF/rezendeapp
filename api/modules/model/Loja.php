<?php

/**
 *	Loja
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Loja {

	private $id;
    private $nome;
    private $nomeFantasia;
    
    function __construct($id = 0, $nome = '', $nomeFantasia = '') {
        $this->id = $id;
        $this->nome = $nome;
        $this->nomeFantasia = $nomeFantasia;
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

    public function getNomeFantasia(){
        return $this->nomeFantasia; 
    }
 
    public function setNomeFantasia($nomeFantasia){
        $this->nomeFantasia = $nomeFantasia;
    }
}
?>