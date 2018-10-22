<?php

/**
 *	MedicamentoPrecificado
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class MedicamentoPrecificado {

	private $id;
    private $titulo;

	function __construct(int $id, String $titulo) {
		$this->id = $id;
		$this->titulo = $titulo;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId(int $id){
        $this->id = $id;
    }
}
?>