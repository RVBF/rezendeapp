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
    private $checklist;
    private $questionador;
    private $encerrada;

    const TAM_TITULO_MIM = 2;
    const TAM_TITULO_MAX = 100;

    function __construct($id = 0, $titulo = '', $descricao = '', $checklist = null, $questionador = null, $perguntas = [], $encerrada = false) {
		$this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->checklist = $checklist;
        $this->questionador = $questionador;
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

    public function getChecklist(){
        return $this->checklist; 
    }
 
    public function setChecklist($checklist){
        $this->checklist = $checklist;
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
}
?>