<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Loja
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Loja {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;


	private $id;
    private $razaoSocial;
    private $nomeFantasia;
    private $endereco;

    const TAM_TEXT_MIM = 2;
    const TAM_TEXT_MAX = 85;

    function __construct($id = 0, $razaoSocial = '', $nomeFantasia = '', $endereco = null) {
        $this->id = $id;
        $this->razaoSocial = $razaoSocial;
        $this->nomeFantasia = $nomeFantasia;
        $this->endereco = $endereco;
    }
}
?>