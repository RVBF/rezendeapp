<?php
/**
 *  Serviço de Arquivos.
 *  
 *  @author	Rafael Vinicius Barros Ferreira
 */

class ServicoArquivo {
	

	private function __construct()	{}
	private function __clone()	{}
	private function __wakeup()	{}
    private static $singleton = null;
	
	static function instance() {
		if (null == self::$singleton)
		{
            self::$singleton = new ServicoArquivo;
			return self::$singleton;
        }
        
		return self::$singleton;
    }
    
    public function validarESalvarImagem($arquivo, $nomePasta, $pastaEntidadeSalva) {

        $valorArquivo = base64_decode($arquivo['arquivo']);

		$dimensoes = getimagesize($arquivo['arquivo']);

        $extensao = $arquivo['nome'];

		$splited = explode(',', substr($arquivo['arquivo'] , 5) , 2);
        $mime=$splited[0];
		$data=$splited[1];

        $mime_split_without_base64 = explode(';', $mime,2);
        $mime_split = explode('/', $mime_split_without_base64[0],2);

        $output = realpath(dirname('..\\')) . Anexo::CAMINHO_ARQUIVOS. '/' . $nomePasta . '/' . $pastaEntidadeSalva . '/' . $extensao;
        if(is_dir(realpath(dirname('..\\')) . Anexo::CAMINHO_ARQUIVOS . '/' . $nomePasta)) {

            if(is_dir(realpath(dirname('..\\')) . Anexo::CAMINHO_ARQUIVOS . '/' . $nomePasta . '/' . $pastaEntidadeSalva)) file_put_contents($output, base64_decode($data));
            else{
                mkdir(realpath(dirname('..\\')) . Anexo::CAMINHO_ARQUIVOS . '/' . $nomePasta . '/' . $pastaEntidadeSalva, 0777);
                file_put_contents($output, base64_decode($data));
            }

        }
        else {
            mkdir(realpath(dirname('..\\')) . Anexo::CAMINHO_ARQUIVOS . '/' . $nomePasta, 0777);
            mkdir(realpath(dirname('..\\')) . Anexo::CAMINHO_ARQUIVOS . '/' . $nomePasta . '/' . $pastaEntidadeSalva, 0777);

            file_put_contents($output, base64_decode($data));
        }
   	    return $output;
	}

    function excluiPasta($pastaEntidade, $nomePasta, $caminhoArquivo){

        $output = realpath(dirname('..\\')) . Anexo::CAMINHO_ARQUIVOS. '/' . $pastaEntidade . '/' .$nomePasta;
        if(is_dir($output)) {
            unlink($caminhoArquivo);
            rmdir($output);
        }
        return true;
    }
    public function imagemParaBase64($caminho){
        $data = file_get_contents($caminho);

        $valorArquivo = base64_encode($data);

       return $valorArquivo;
    }
}
?>
