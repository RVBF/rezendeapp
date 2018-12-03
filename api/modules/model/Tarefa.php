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
    private $formularioRespondido;

    function __construct($id = 0, $titulo = '', $descricao = '', $checklist = null, $questionador = null, $formularioRespondido = null) {
		$this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->checklist = $checklist;
        $this->questionador = $questionador;
        $this->formularioRespondido = $formularioRespondido;
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

    public function getformularioRespondido(){
        return $this->formularioRespondido; 
    }
 
    public function setFormularioRespondido($formularioRespondido){
        $this->formularioRespondido = $formularioRespondido;
    }
}
?>