<?php

/**
 *	PlanoAcao
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class PlanoAcao {

	private $id;
    private $descricao;
    private $dataLimite;
    private $solucao;
    private $resposta;
    private $dataCadastro;
    private $dataExecucao;

    function __construct($id, $descricao, $dataLimite, $solucao, $pergunta, $tarefa, $categoria, $dataCadastro, $dataExecucao) {
        $id = $id;
        $descricao = $descricao;
        $dataLimite = $dataLimite;
        $solucao = $solucao;
        $pergunta = $pergunta;
        $tarefa = $tarefa;
        $categoria = $categoria;
        $dataCadastro = $dataCadastro;
        $dataExecucao = $dataExecucao;
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