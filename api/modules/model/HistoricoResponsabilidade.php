<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	HistoricoResponsabildiade
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class HistoricoResponsabilidade {
	use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;
    private $id;
    private $data;
    private $planoAcao;
    private $responsavelAtual;
    private $responsavelAnterior;

    function __construct(
        $id,
        $data,
        $planoAcao,
        $responsavelAtual,
        $responsavelAnterior
    ) {
    
        $this->id = $id;
        $this->data = $data;
        $this->planoAcao = $planoAcao;
        $this->responsavelAtual = $responsavelAtual;
        $this->responsavelAnterior = $responsavelAnterior;
    }
}
?>