<?php

/**
 *	Pergunta
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Pergunta {

	private $id;
    private $pergunta;
    private $tarefa;    
    private $formularioRespondido;


    function __construct($id = 0, $pergunta = '', $tarefa = null, $formularioRespondido = null) {
		$this->id = $id;
        $this->pergunta = $pergunta;
        $this->tarefa = $tarefa;
        $this->formularioRespondido = $formularioRespondido;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getPergunta(){
        return $this->pergunta; 
    }
 
    public function setPergunta($pergunta){
        $this->pergunta = $pergunta;
    }

    public function getRespondedor(){
        return $this->respondedor; 
    }
 
    public function setRespondedor($respondedor){
        $this->respondedor = $respondedor;
    }

    public function getQuestionador(){
        return $this->questionador; 
    }
 
    public function setQuestionador($questionador){
        $this->questionador = $questionador;
    }

    public function getTarefa(){
        return $this->tarefa;
    }

    public function setTarefa($tarefa){
        $this->tarefa = $tarefa;
    }

    public function setFormularioRespondido($formularioRespondido){
        $this->formularioRespondido = $formularioRespondido;
    }
 
    public function getFormularioRespondido(){
        return $this->formularioRespondido; 
    }
}
?>