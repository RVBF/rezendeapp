<?php
use phputil\FileBasedSession as Session;

/**
 *  Sessão 
 *  
 *  @author	Rafael Vinicius Barros Ferreira
 */
class Sessao
{
	const ID_USUARIO = 'idUsuario';
	const LOGIN_USUARIO = 'loginUsuario';
	const ULTIMA_REQUISICAO = 'ultimaRequisicao';

	private $session;
	
	function __construct(Session $session) {
		$this->session = $session;
	}
	
	function existe() {
		return $this->session->has( self::ID_USUARIO );
	}
	
	function criar( $id, $login, $ultimaRequisicao ) {
		$this->set( self::ID_USUARIO, $id );
		$this->set( self::LOGIN_USUARIO, $login );
		$this->set( self::ULTIMA_REQUISICAO, $ultimaRequisicao );
	}
	
	function destruir() {
		$this->session->destroy();
	}
	
	function idUsuario() {
		return (int) $this->get( self::ID_USUARIO );
	}
	
	function loginUsuario() {
		return $this->session->get( self::LOGIN_USUARIO );
	}	
	
	function ultimaRequisicao() {
		return $this->session->get( self::ULTIMA_REQUISICAO );
	}

	function atualizarUltimaRequisicao( $valor = null ) {
		$this->session->put( self::ULTIMA_REQUISICAO, null === $valor ? time() : $valor);
	}
	
	private function get( $chave ) {
		return $this->session->get( $chave );
	}
	
	private function set( $chave, $valor ) {
		return $this->session->put( $chave, $valor );
	}
}
?>