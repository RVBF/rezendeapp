<?php

/**
 *	Checklist
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Checklist {

	private $id;
    private $descricao;
    private $dataLimite;
    private $dataCadastro;
    private $categoria;
    private $lojas;

    const TAM_TITULO_MIM = 2;
    const TAM_TITULO_MAX = 85;

    
    function __construct($id = 0, $descricao = '', $dataLimite = '', $dataCadastro = '', $categoria = null, $lojas = null) {
		$this->id = $id;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->dataCadastro = $dataCadastro;
        $this->categoria = $categoria;
		$this->lojas = $lojas;
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

    public function getDataLimite(){
        return $this->dataLimite; 
    }
 
    public function setDataLimite($dataLimite){
        $this->dataLimite = $dataLimite;
    }

    public function getDataCadastro(){
        return $this->dataCadastro; 
    }
 
    public function setDataCadastro($dataCadastro){
        $this->dataCadastro = $dataCadastro;
    }

    public function getCategoria(){
        return $this->categoria; 
    }
 
    public function setCategoria($categoria){
        $this->categoria = $categoria;
    }

    public function getlojas(){
        return $this->categoria; 
    }
 
    public function setlojas($lojas){
        $this->lojas = $lojas;
    }
}
?>