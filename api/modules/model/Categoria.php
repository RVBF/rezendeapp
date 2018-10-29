<?php

/**
 *	Categoria
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Categoria {

	private $id;
    private $titulo;

	function __construct($id = 0, $titulo = '') {
		$this->id = $id;
		$this->titulo = $titulo;
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
}
?>