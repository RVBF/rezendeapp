<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Anexo
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Anexo {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;
    
	private $id;
    private $patch;
    private $tipo;
    private $questionamento;
    private $planoAcao;
    private $pendencia;
    private $arquivoBase64;

	const CAMINHO_ARQUIVOS = '/../assets/images/anexos';

    function __construct($id, $patch, $tipo) {
        $this->id = $id;
        $this->patch = $patch;
        $this->tipo = $tipo;
    }
}
?>