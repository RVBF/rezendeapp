<?php

/**
 *	Anexo
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Anexo {

	private $id;
    private $patch;
    private $tipo;
    private $resposta;
    private $arquivoBase64;

	const CAMINHO_ARQUIVOS = '/../assets/images/anexos/';

    function __construct($id = 0, $patch = '', $tipo = '', $resposta = null) {
        $this->id = $id;
        $this->patch = $patch;
        $this->tipo = $tipo;
        $this->resposta = $resposta;
    }

    public function getId(){
       return $this->id; 
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getPatch(){
        return $this->patch; 
    }
 
    public function setPatch($patch){
        $this->patch = $patch;
    }

    public function getTipo(){
        return $this->tipo; 
    }
 
    public function setTipo($tipo){
        $this->tipo = $tipo;
    }

    public function getResposta(){
        return $this->resposta; 
    }
 
    public function setResposta($resposta){
        $this->resposta = $resposta;
    }

    public function setArquivoBase64($arquivoBase64){
        $this->arquivoBase64 = $arquivoBase64;
    }

    public function getArquivoBase64() {
        return $this->arquivoBase64;
    }
}
?>