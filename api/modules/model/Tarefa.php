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
    private $tarefa;

    function __construct($id = 0, $titulo = '', $descricao = '', $tarefa = '') {
		$this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->tarefa = $tarefa;
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

    public function getTarefa(){
        return $this->tarefa; 
    }
 
    public function setTarefa($tarefa){
        $this->tarefa = $tarefa;
    }
}
?>