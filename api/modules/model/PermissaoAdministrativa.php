<?php

/**
 *	PermissaoAdministrativa
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class PermissaoAdministrativa {

    private $grupos;
    private $usuarios;
    
    function __construct($grupos = [], $usuarios = []) {
        $this->grupos = $grupos;
        $this->usuarios = $usuarios;
    }

    public function getGrupos(){
       return $this->grupos; 
    }

    public function setGrupos($grupos){
        $this->grupos = $grupos;
    }

    public function getUsuarios(){
        return $this->usuarios; 
    }
 
    public function setUsuarios($usuarios){
        $this->usuarios = $usuarios;
    }
}
?>