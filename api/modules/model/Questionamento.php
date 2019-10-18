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
    private $anexos;

    function __construct(
        $id,
        $status,
        $formularioPergunta,
        $formularioResposta,
        $checklist,
        $planoAcao,
        $anexos
    ) {
    
        $this->id = $id;
        $this->status = $status;
        $this->formularioPergunta = $formularioPergunta;
        $this->formularioResposta = $formularioResposta;
        $this->checklist = $checklist;
        $this->planoAcao = $planoAcao;
        $this->anexos = $anexos;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setStatus($status){

        $status  = $this->status;
    }

    public function getStatus(){
        return $this->status;
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

    public function getAnexos(){
        return $this->anexos; 
    }
 
    public function setAnexos($anexos){
        $this->anexos = $anexos;
    }
}
?>