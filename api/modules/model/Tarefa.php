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

    function __construct($id = 0, $titulo = '', $descricao = '', $checklist = '') {
		$this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->tarefa = $checklist;
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
}
?>