<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Pendencia
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Pendencia {
	use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;
    
	private $id;
    private $descricao;
    private $dataLimite;
    private $solucao;
    private $responsavel;
    private $dataCadastro;
    private $dataExecucao;

    function __construct(
        $id,
        $descricao,
        $dataLimite,
        $solucao,
        $resposta = '',
        $responsavel,
        $dataCadastro = '',
        $dataExecucao = ''
     ) {
        $this->id = $id;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->solucao = $solucao;
        $this->responsavel  = $responsavel;
        $this->dataCadastro = $dataCadastro;
        $this->dataExecucao = $dataExecucao;

    }
}
?>