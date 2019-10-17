<?php

/**
 *	Pergunta
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Pergunta {

	private $id;
    private $pergunta;
    private $questionario; 


    function __construct($id = 0, $pergunta = '', $questionario = null) {
		$this->id = $id;
        $this->pergunta = $pergunta;
        $this->questionario = $questionario;
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

    public function getQuestionario(){
        return $this->questionario;
    }

    public function setQuestionario($questionario){
        $this->questionario = $questionario;
    }
}
?>