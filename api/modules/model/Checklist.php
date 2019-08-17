<?php

/**
 *	Checklist
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Checklist {

	private $id;
    private $titulo;
    private $descricao;
    private $dataLimite;
    private $dataCadastro;
    private $tipoTarefa;
    private $setor;
    private $loja;
    private $questionador;
    private $responsavel;
]
    const TAM_TITULO_MIM = 2;
    const TAM_TITULO_MAX = 100;

    function __construct($id = 0,
        $titulo = '',
        $descricao = '',
        $dataLimite = '',
        $dataCadastro = '',
        $tipoTarefa = '',
        $setor = null,
        $loja = null,
        $questionador = null,
        $responsavel = null,
    ) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->dataCadastro = $dataCadastro;
        $this->setor = $setor;
        $this->loja = $loja;
        $this->questionador = $questionador;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getTitulo(){
        return $this->titulo; 
    }
 
    public function setTitulo($titulo){
        $this->titulo = $titulo;
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
    
    public function getTipoTarefa(){
        return $this->tipoTarefa; 
    }
 
    public function setTipoTarefa($tipoTarefa){
        $this->tipoTarefa = $tipoTarefa;
    }

    public function getSetor(){
        return $this->setor; 
    }
 
    public function setSetor($setor){
        $this->setor = $setor;
    }

    public function getLoja(){
        return $this->loja; 
    }
 
    public function setLoja($loja){
        $this->loja = $loja;
    }

    public function getQuestionador(){
        return $this->questionador; 
    }
 
    public function setQuestionador($questionador){
        $this->questionador = $questionador;
    }

    public function getResponsavel(){
        return $this->responsavel; 
    }
 
    public function setResponsavel($responsavel){
        $this->responsavel = $responsavel;
    }
}
?>