<?php

/**
 *	Resposta
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Resposta {

	private $id;
    private $opcaoSelecionada;
    private $comentario;
    private $anexos;

    function __construct($id = 0, $opcaoSelecionada = 0, $comentario = '', $anexos = []) {
		$this->id = $id;
        $this->opcaoSelecionada = $opcaoSelecionada;
        $this->comentario = $comentario;
        $this->anexos = $anexos;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getOpcaoSelecionada(){
        return $this->opcaoSelecionada; 
    }
 
    public function setopcaoSelecionada($opcaoSelecionada){
        $this->opcaoSelecionada = $opcaoSelecionada;
    }

    public function getComentario(){
        return $this->comentario; 
    }
 
    public function setComentario($comentario){
        $this->comentario = $comentario;
    }

    public function getAnexos(){
        return $this->anexos; 
    }
 
    public function setAnexos($anexos){
        $this->anexos = $anexos;
    }
}
?>