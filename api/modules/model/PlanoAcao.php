<?php

/**
 *	PlanoAcao
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class PlanoAcao {

	private $id;
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
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->solucao = $solucao;
        $this->resposta = $resposta;
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

    public function getResposta(){
        return $this->resposta; 
    }
    
    public function setResposta($resposta){
        $this->resposta = $resposta;
    }

    public function getDataCadastro(){
        return $this->dataCadastro; 
    }
 
    public function setDataCadastro($dataCadastro){
        $this->dataCadastro = $dataCadastro;
    }
 
    public function getdataExecucao(){
        return $this->dataExecucao; 
    }
    public function setdataExecucao($dataExecucao){
        $this->dataExecucao = $dataExecucao;
    }
}
?>