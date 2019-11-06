<?php

/**
 *	Questionamento
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Questionamento {

    private $id;
    private $status;
    private $formularioPergunta;
    private $formularioResposta;
    private $checklist;
    private $planoAcao;
    private $pendencia;
    private $anexos;

    function __construct(
        $id = 0,
        $status =  '',
        $formularioPergunta = '',
        $formularioResposta = '',
        $checklist = null,
        $planoAcao = null,
        $pendencia = null,
        $anexos = []
    ) {
    
        $this->id = $id;
        $this->status = $status;
        $this->formularioPergunta = $formularioPergunta;
        $this->formularioResposta = $formularioResposta;
        $this->checklist = $checklist;
        $this->planoAcao = $planoAcao;
        $this->pendencia = $pendencia;
        $this->anexos = $anexos;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus($status){
        $this->status = $status;
    }

    public function getFormularioPergunta(){
        return $this->formularioPergunta; 
    }
 
    public function setFormularioPergunta($formularioPergunta){
        $this->formularioPergunta = $formularioPergunta;
    }


    public function getFormularioResposta(){
        return $this->formularioResposta; 
    }
 
    public function setFormularioResposta($formularioResposta){
        $this->formularioResposta = $formularioResposta;
    }

    public function getChecklist(){
        return $this->checklist; 
    }
 
    public function setChecklist($checklist){
        $this->checklist = $checklist;
    }

    public function getPlanoAcao(){
        return $this->planoAcao; 
    }
 
    public function setPlanoAcao($planoAcao){
        $this->planoAcao = $planoAcao;
    }
    
    public function getPendencia(){
        return $this->pendencia;
    }

    public function setPendencia($pendencia){
        $this->pendencia  = $pendencia;
    }

    public function getAnexos(){
        return $this->anexos; 
    }
 
    public function setAnexos($anexos){
        $this->anexos = $anexos;
    }
}
?>