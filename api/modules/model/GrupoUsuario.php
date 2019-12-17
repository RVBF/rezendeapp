<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;

/**
 *	GrupoDeUsuario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class GrupoUsuario {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;

    private $id;
    private $nome;
    private $descricao;
    private $usuarios;
    
    function __construct($id = 0, $nome = '', $descricao = '', $usuarios = []) {
        $this->id = $id;
        $this->nome =  $nome;
        $this->descricao = $descricao;
        $this->usuarios = $usuarios;
    }

    public function addUsuario($usuario){
        $this->usuarios[] = $usuario;
    }

    public function removerUsuario($usuario){
        $key  = array_search($usuario, $this->usuarios);
    }    
}
?>