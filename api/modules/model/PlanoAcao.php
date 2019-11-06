<?php

/**
 *	PlanoAcao
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class PlanoAcao {

    private $id;
    private $status;
    private $descricao;
    private $dataLimite;
    private $solucao;
    private $resposta;
    private $responsavel;
    private $dataCadastro;
    private $dataExecucao;
    private $historico;

    function __construct(
        $id,
        $status,
        $descricao,
        $dataLimite,
        $solucao,
        $resposta = '',
        $responsavel,
        $dataCadastro = '',
        $dataExecucao = '',
        $historico = []
    
     ) {
        $this->id = $id;
        $this->status = $status;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->solucao = $solucao;
        $this->responsavel  = $responsavel;
        $this->resposta = $resposta;
        $this->dataCadastro = $dataCadastro;
        $this->dataExecucao = $dataExecucao;
        $this->historico = $historico;

    } 

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
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

    public function getResposta(){
        return $this->resposta; 
    }
    
    public function setResposta($resposta){
        $this->resposta = $resposta;
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

    public function setHistorico($historico = []){
        $this->historico = $historico;
    }

    public function getHistorico(){
        return $this->historico;
    }
}
?>