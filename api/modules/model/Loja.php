<?php

/**
 *	Loja
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Loja {

	private $id;
    private $razaoSocial;
    private $nomeFantasia;
    
    function __construct($id = 0, $razaoSocial = '', $nomeFantasia = '') {
        $this->id = $id;
        $this->razaoSocial = $razaoSocial;
        $this->nomeFantasia = $nomeFantasia;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getRazaoSocial(){
        return $this->razaoSocial; 
    }
 
    public function setRazaoSocial($razaoSocial){
        $this->razaoSocial = $razaoSocial;
    }

    public function getNomeFantasia(){
        return $this->nomeFantasia; 
    }
 
    public function setNomeFantasia($nomeFantasia){
        $this->nomeFantasia = $nomeFantasia;
    }
}
?>