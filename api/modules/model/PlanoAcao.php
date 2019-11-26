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
    private $unidade;
    private $dataCadastro;
    private $dataExecucao;
    private $responsabilidade;
    private $historicoAtual;
    private $historico;

    function __construct(
        $id = 0,
        $status = '',
        $descricao= '',
        $dataLimite= '',
        $solucao= '',
        $resposta = '',
        $responsavel = null,
        $unidade = null,
        $dataCadastro = '',
        $dataExecucao = '',
        $responsabilidade = false,
        $historicoAtual = null,
        $historico = []

     ) {
        $this->id = $id;
        $this->status = $status;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->solucao = $solucao;
        $this->responsavel  = $responsavel;
        $this->unidade = $unidade;
        $this->resposta = $resposta;
        $this->dataCadastro = $dataCadastro;
        $this->dataExecucao = $dataExecucao;
        $this->responsabilidade = $responsabilidade;
        $this->historico = $historico;

    }
}
?>