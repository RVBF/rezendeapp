<?php

/**
 *	Pendencia
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Pendencia {

	private $id;
    private $descricao;
    private $dataLimite;
    private $solucao;
    private $responsavel;
    private $dataCadastro;
    private $dataExecucao;

    function __construct(
        $id,
        $descricao,
        $dataLimite,
        $solucao,
        $resposta = '',
        $responsavel,
        $dataCadastro = '',
        $dataExecucao = ''
     ) {
        $this->id = $id;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->solucao = $solucao;
        $this->responsavel  = $responsavel;
        $this->dataCadastro = $dataCadastro;
        $this->dataExecucao = $dataExecucao;

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

    public function getSolucao(){
        return $this->solucao; 
    }

    public function setSolucao($solucao){
        $this->solucao = $solucao;
    }

    public function setResponsavel($responsavel){
        $this->responsavel = $responsavel;
    }

    public function getResponsavel(){
        return $this->responsavel;
    }

    public function getDataCadastro(){
        return $this->dataCadastro; 
    }
 
    public function setDataCadastro($dataCadastro){
        $this->dataCadastro = $dataCadastro;
    }
 
    public function getDataExecucao(){
        return $this->dataExecucao; 
    }

    public function setdataExecucao($dataExecucao){
        $this->dataExecucao = $dataExecucao;
    }
}
?>