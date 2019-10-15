<?php

/**
 *	Loja
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Loja {

	private $id;
    private $razaoSocial;
    private $nomeFantasia;

    const TAM_TEXT_MIM = 2;
    const TAM_TEXT_MAX = 85;

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