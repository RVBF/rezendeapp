<?php

use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Setor
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Setor {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;

    private $id;
    private $titulo;
    private $descricao;

    const TAM_MIN_TITUlO = 2;
    const TAM_MAX_TITUlO = 100;

    function __construct($id = 0, $titulo = '', $descricao = '') {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
    }
}
?>