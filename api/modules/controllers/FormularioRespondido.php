<?php

/**
 *	FormularioRespodido
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class FormularioRespondido {

	private $id;
    private $dataHora;
    private $respondedor;
    private $respostas;

    function __construct($id = 0, $dataHora = '', $respondedor = null, $respostas = []) {
		$this->id = $id;
        $this->dataHora = $dataHora;
        $this->respondedor = $respondedor;
        $this->respostas = $respostas;
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

    public function getRespostas(){
        return $this->respostas; 
    }
 
    public function setRespostas($respostas){
        $this->respostas = $respostas;
    }

    public function addResposta($resposta){
        $this->respostas[] = $resposta;
    }
}
?>