<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	PlanoAcao
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class PlanoAcao {

    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;
    
    private $id;
    private $status;
    private $descricao;
    private $dataLimite;
    private $solucao;
    private $resposta;
    private $responsavel;
    private $dataCadastro;
    private $dataExecucao;
    private $historico;

    function __construct(
        $id,
        $status,
        $descricao,
        $dataLimite,
        $solucao,
        $resposta = '',
        $responsavel,
        $dataCadastro = '',
        $dataExecucao = '',
        $historico = []
    
     ) {
        $this->id = $id;
        $this->status = $status;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->solucao = $solucao;
        $this->responsavel  = $responsavel;
        $this->resposta = $resposta;
        $this->dataCadastro = $dataCadastro;
        $this->dataExecucao = $dataExecucao;
        $this->historico = $historico;

    }
}
?>