<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Questionamento
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Questionamento {
	use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;
    private $id;
    private $status;
    private $formularioPergunta;
    private $formularioResposta;
    private $checklist;
    private $planoAcao;
    private $pendencia;
    private $anexos;

    function __construct(
        $id = 0,
        $status =  '',
        $formularioPergunta = '',
        $formularioResposta = '',
        $checklist = null,
        $planoAcao = null,
        $pendencia = null,
        $anexos = []
    ) {
    
        $this->id = $id;
        $this->status = $status;
        $this->formularioPergunta = $formularioPergunta;
        $this->formularioResposta = $formularioResposta;
        $this->checklist = $checklist;
        $this->planoAcao = $planoAcao;
        $this->pendencia = $pendencia;
        $this->anexos = $anexos;
    }
}
?>