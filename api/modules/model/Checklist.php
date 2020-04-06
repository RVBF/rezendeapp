<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Checklist
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Checklist {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;


    private $id;
    private $status;
    private $titulo;
    private $descricao;
    private $dataLimite;
    private $dataCadastro;
    private $tipoChecklist;
    private $setor;
    private $loja;
    private $questionador;
    private $responsavel;
    private $questionarios;
    private $questionamentos;
    private $repeteDiariamente;

    const TAM_TITULO_MIM = 2;
    const TAM_TITULO_MAX = 100;

    function __construct(
        $id = 0,
        $status = '',
        $titulo = '',
        $descricao = '',
        $dataLimite = '',
        $dataCadastro = '',
        $tipoChecklist = '',
        $setor = null,
        $loja = null,
        $questionador = null,
        $responsavel = null,
        $questionarios = [],
        $questionamentos = []
    ) {
        $this->id = $id;
        $this->status  = $status;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->dataLimite = $dataLimite;
        $this->dataCadastro = $dataCadastro;
        $this->tipoChecklist = $tipoChecklist;
        $this->setor = $setor;
        $this->loja = $loja;
        $this->questionador = $questionador;
        $this->responsavel = $responsavel;
        $this->questionarios = $questionarios;
        $this->questionamentos = $questionamentos;
    }
}
?>