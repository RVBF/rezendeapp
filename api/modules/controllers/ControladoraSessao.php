<?php

/**
 * Controladora de sessão
 *
 * @author	Rafael Vinicicus Barros Ferreira
 */

class ControladoraSessao {
	private $params;
	private $servico;
	private $sessao;
	
	function __construct( $params, Sessao $sessao) {
		$this->params = $params;
		$this->sessao = $sessao;
		$this->servico = new ServicoLogin($this->sessao);
	}

	/**
	 *	Método que pega os parâmetros login e senha da requisição 
	 * e os utiliza no método logar do serviço do usuario. 
	 * 
	 * @return geradoraResposta->erro 			Caso o array de parâmetros esteja vazio.
	 * @return geradoraResposta->semConteudo 	Caso o login seja efetuado corretamente.
	 * @throws Exception
	 */
	
	function estaAtiva() {				
		try {
			if($this->servico->estaLogado()) {
				if(!$this->servico->sairPorInatividade()) {
					$this->servico->atualizaAtividadeUsuario();
                    $resposta = ['status' => true]; 

				}
				else {
                    $resposta = ['status' => false, 'mensagem'=> 'Erro ao acessar página.']; 
				}
			}
			else {
                $resposta = ['status' => false, 'mensagem'=> 'Erro ao acessar página.']; 
			}		
		}
		catch (\Exception $e)
		{
            $resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
        }
        
        return $resposta;
	}
}
