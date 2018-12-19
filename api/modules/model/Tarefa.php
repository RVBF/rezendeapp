<?php

/**
 *	Tarefa
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Tarefa {

	private $id;
    private $titulo;
    private $descricao;
    private $dataLimite;
    private $dataCadastro;
    private $setor;
    private $loja;
    private $questionador;
    private $perguntas;
    private $encerrada;

    const TAM_TITULO_MIM = 2;
    const TAM_TITULO_MAX = 100;

    function __construct($id = 0, $titulo = '', $descricao = '', $dataLimite = '', $dataCadastro = '', $setor = null, $loja = null, $questionador = null, $perguntas = [], $encerrada = false) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->dataCadastro = $dataCadastro;
        $this->setor = $setor;
        $this->loja = $loja;
        $this->questionador = $questionador;
        $this->perguntas = $perguntas;
        $this->encerrada = $encerrada;
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
 
    public function getEncerrada(){
        return $this->encerrada; 
    }
    public function setEncerrada($encerrada){
        $this->encerrada = $encerrada;
    }

    public function getPerguntas(){
        return $this->perguntas; 
    }
 
    public function setPerguntas($perguntas){
        $this->perguntas = $perguntas;
    }

    public function addPergunta($pergunta){
        $this->perguntas[] = $pergunta;
    }
}
?>