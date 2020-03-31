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
    private $status;
    private $descricao;
    private $dataLimite;
    private $solucao;
    private $descricaoExecucao;
    private $responsavel;
    private $dataCadastro;
    private $dataExecucao;

    function __construct(
        $id = 0,
        $status = '',
        $descricao = '',
        $dataLimite = '',
        $solucao = '',
        $descricaoExecucao = '',
        $responsavel = null,
        $dataCadastro = '',
        $dataExecucao = ''
     ) {
        $this->id = $id;
        $this->status = $status;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->solucao = $solucao;
        $this->descricaoExecucao = $descricaoExecucao;
        $this->responsavel  = $responsavel;
        $this->dataCadastro = $dataCadastro;
        $this->dataExecucao = $dataExecucao;
    }
}
?>