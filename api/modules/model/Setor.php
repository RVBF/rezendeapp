<?php

/**
 *	Setor
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Setor {

    private $id;
    private $titulo;
    private $descricao;
    private $categoria;

    const TAM_MIN_TITUlO = 2;
    const TAM_MAX_TITUlO = 100;

    function __construct($id = 0, $titulo = '', $descricao = '', $categoria = null) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->categoria = $categoria;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getDescricao(){
        return $this->descricao; 
    }
 
    public function setDescricao($descricao){
        $this->descricao = $descricao;
    }

    public function getCategoria(){
        return $this->categoria; 
    }
 
    public function setCategoria(Categoria $categoria){
        $this->categoria = $categoria;
    }

    public function getTitulo(){
        return $this->titulo; 
    }
 
    public function setTitulo(titulo $titulo) {
        $this->titulo = $titulo;
    }
}
?>