<?php
/**
 *	Questionario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Questionario {

    private $id;
    private $titulo;
    private $descricao;
    private $tipoQuestionario;
    private $perguntas;

    const TAM_MIN_TITUlO = 2;
    const TAM_MAX_TITUlO = 100;

    function __construct($id = 0, $titulo = '', $descricao = '', $tipoQuestionario = '', $perguntas = []) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->tipoQuestionario = $tipoQuestionario;
        $this->perguntas = $perguntas;
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
 
    public function setTitulo(titulo $titulo) {
        $this->titulo = $titulo;
    }

    public function getDescricao(){
        return $this->descricao; 
    } 

    public function setDescricao($descricao){
        $this->descricao = $descricao;
    }

    public function getTipoQuestionario(){
        return $this->tipoQuestionario;
    }

    public function setTipoQuestionario($tipoQuestionario){
        $this->tipoQuestionario = $tipoQuestionario;
    }

    public function getPerguntas(){
        return $this->perguntas;
    }

    public function setPerguntas($perguntas){
        $this->perguntas = $perguntas;
    }
}
?>