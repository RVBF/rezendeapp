<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Questionario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Questionario {
	use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;
    
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
}
?>