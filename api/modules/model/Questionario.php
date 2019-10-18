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
    private $formulario;

    const TAM_MIN_TITUlO = 2;
    const TAM_MAX_TITUlO = 100;

    function __construct($id = 0, $titulo = '', $descricao = '', $tipoQuestionario = '', $formulario = []) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->tipoQuestionario = $tipoQuestionario;
        $this->formulario = $formulario;
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

    public function getFormulario(){
        return $this->formulario;
    }

    public function setFormulario($formulario){
        $this->formulario = $formulario;
    }
}
?>