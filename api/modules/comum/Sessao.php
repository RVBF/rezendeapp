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
	const LOGIN_USUARIO = 'user_name';
	const ULTIMA_REQUISICAO = 'ultimaRequisicao';
	const ADMIN = 'e_adm';


	private $session;
	
	function __construct(Session $session) {
		$this->session = $session;
	}
	
	function existe() {
		return $this->session->get(self::ID_USUARIO) >  0;
	}
	
	function criar($id, $login, $ultimaRequisicao, $admin = false) {
		$this->session->put(self::LOGIN_USUARIO, $login); // Set a value in the session
		$this->session->put(self::ID_USUARIO, $id);
		$this->session->put(self::ULTIMA_REQUISICAO, $ultimaRequisicao);
		$this->session->put(self::ADMIN, $admin);
	}
	
	function destruir() {
		$this->session->destroy();
	}
	
	function idUsuario() {
		return (int) $this->get(self::ID_USUARIO);
	}
	
	function loginUsuario() {
		return $this->session->get(self::LOGIN_USUARIO);
	}	
	
	function ultimaRequisicao() {
		return $this->session->get(self::ULTIMA_REQUISICAO);
	}

	function eAdmin() {
		return $this->session->get(self::ADMIN);
	}

	function atualizarUltimaRequisicao($valor = null) {
		$this->session->put(self::ULTIMA_REQUISICAO, null === $valor ? time() : $valor);
	}
	
	private function get($chave) {
		return $this->session->get($chave);
	}
	
	private function set($chave, $valor) {
		return $this->session->put($chave, $valor);
	}
}
?>