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
    private $loja;

    const TAM_MIN_DESCRICAO = 15;
    const TAM_MAX_DESCRICAO = 255;

    function __construct($id = 0, $descricao = '', $dataLimite = '', $dataCadastro = '', $categoria = null, $loja = null) {
		$this->id = $id;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->dataCadastro = $dataCadastro;
        $this->categoria = $categoria;
        $this->loja = $loja;
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
 
    public function setCategoria(Categoria $categoria){
        $this->categoria = $categoria;
    }

    public function getloja(){
        return $this->loja; 
    }
 
    public function setloja(Loja $loja) {
        $this->loja = $loja;
    }
}
?>