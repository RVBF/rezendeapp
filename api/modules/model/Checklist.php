<?php

/**
 *	Checklist
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Checklist {

    private $id;
    private $status;
    private $titulo;
    private $descricao;
    private $dataLimite;
    private $dataCadastro;
    private $tipoChecklist;
    private $setor;
    private $loja;
    private $questionador;
    private $responsavel;

    const TAM_TITULO_MIM = 2;
    const TAM_TITULO_MAX = 100;

    function __construct(
        $id = 0,
        $status = '',
        $titulo = '',
        $descricao = '',
        $dataLimite = '',
        $dataCadastro = '',
        $tipoChecklist = '',
        $setor = null,
        $loja = null,
        $questionador = null,
        $responsavel = null
    ) {
        $this->id = $id;
        $this->status  = $status;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->dataCadastro = $dataCadastro;
        $this->tipoChecklist = $tipoChecklist;
        $this->setor = $setor;
        $this->loja = $loja;
        $this->questionador = $questionador;
        $this->responsavel = $responsavel;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setStatus($status){

        $status  = $this->status;
    }

    public function getStatus(){
        return $this->status;
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
    
    public function getTipoChecklist(){
        return $this->tipoChecklist; 
    }
 
    public function setTipoChecklist($tipoChecklist){
        $this->tipoChecklist = $tipoChecklist;
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