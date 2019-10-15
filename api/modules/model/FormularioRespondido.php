<?php

/**
 *	FormularioRespodido
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class FormularioRespondido {

	private $id;
    private $dataHora;
    private $respondedor;
    private $perguntas;
    
    function __construct($id = 0, $dataHora = '', $respondedor = null) {
		$this->id = $id;
        $this->dataHora = $dataHora;
        $this->respondedor = $respondedor;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getDataHora(){
        return $this->dataHora; 
    }
 
    public function setDataHora($dataHora){
        $this->dataHora = $dataHora;
    }

    public function getRespondedor(){
        return $this->respondedor; 
    }
 
    public function setRespondedor($respondedor){
        $this->respondedor = $respondedor;
    }

    public function setPerguntas($perguntas){
        $this->perguntas = $perguntas;
    }

    public function getPerguntas(){
       return $this->perguntas;
    }

    public function addPergunta($pergunta){
        $this->perguntas[] = $pergunta;
    }
}
?>