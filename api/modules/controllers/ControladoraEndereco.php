<?php

/**
 * Controladora de Farmácia
 *
 * @author	Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class ControladoraEndereco {

	private $geradoraResposta;
	private $params;
	private $servicoEndereco;
	private $servicoLogin;
	private $sessao;
	private $colecao;
	private $colecaoEnderecoEntidade;
	private $colecaoEndereco;
	private $colecaoCidade;
	private $colecaoBairro;
	private $colecaoEstado;
	private $colecaoPais;

	function __construct(GeradoraResposta $geradoraResposta,  $params, $sessao)
	{
		$this->geradoraResposta = $geradoraResposta;
		$this->params = $params;
		$this->sessao = $sessao;
		$this->servicoLogin = new ServicoLogin($this->sessao);
		$this->servicoEndereco = new ServicoEndereco();
		$this->colecao = DI::instance()->create('ColecaoEnderecoEmBDR');
		$this->colecaoEnderecoEntidade = DI::instance()->create('ColecaoEnderecoEntidade');
		$this->colecaoEndereco = DI::instance()->create('ColecaoEndereco');
		$this->colecaoCidade = DI::instance()->create('ColecaoCidade');
		$this->colecaoBairro = DI::instance()->create('ColecaoBairro');
		$this->colecaoEstado = DI::instance()->create('ColecaoEstado');
		$this->colecaoPais = DI::instance()->create('ColecaoPais');
	}

	function todosEstados()
	{
		// if($this->servicoLogin->verificarSeUsuarioEstaLogado()  == false)
		// {
		// 	return $this->geradoraResposta->naoAutorizado('Erro ao acessar página.', GeradoraResposta::TIPO_TEXTO);
		// }

		try
		{
			$estados = $this->colecaoEstado->todos();
		}
		catch (\Exception $e )
		{
			return $this->geradoraResposta->erro($e->getMessage(), GeradoraResposta::TIPO_TEXTO);
		}

		return $this->geradoraResposta->ok(JSON::encode($estados), GeradoraResposta::TIPO_JSON);
	}

	public function comCep()
	{
		// if($this->servicoLogin->verificarSeUsuarioEstaLogado()  == false)
		// {
		// 	return $this->geradoraResposta->naoAutorizado('Erro ao acessar página.', GeradoraResposta::TIPO_TEXTO);
		// }

		try
		{

			$inexistentes = \ArrayUtil::nonExistingKeys(['cep'], $this->params);

			if (count($inexistentes) > 0)
			{
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentes);
				return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}

			$cep = (int) \ParamUtil::value($this->params, 'cep');

			if (count($inexistentes) > 0)
			{
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentes);
				return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}

			$endereco = $this->servicoEndereco->consultarCepOnline($cep);
		}
		catch (\Exception $e)
		{
			return $this->geradoraResposta->erro($e->getMessage(), GeradoraResposta::TIPO_TEXTO);
		}

		return $this->geradoraResposta->resposta(JSON::enconde($endereco), GeradoraResposta::OK, GeradoraResposta::TIPO_JSON);
	}

	public function comUf()
	{
		// if($this->servicoLogin->verificarSeUsuarioEstaLogado()  == false)
		// {
		// 	return $this->geradoraResposta->naoAutorizado('Erro ao acessar página.', GeradoraResposta::TIPO_TEXTO);
		// }

		try
		{
			$inexistentes = \ArrayUtil::nonExistingKeys(['uf'], $this->params);

			if (count($inexistentes) > 0)
			{
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentes);
				return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}

			$uf = \ParamUtil::value($this->params, 'uf');

			$cidades = $this->servicoEndereco->consultarCidadesDoEstadoOnline($uf);
		}
		catch (\Exception $e)
		{
			return $this->geradoraResposta->erro($e->getMessage(), GeradoraResposta::TIPO_TEXTO);
		}

		return $this->geradoraResposta->resposta(JSON::encode($cidades), GeradoraResposta::OK, GeradoraResposta::TIPO_JSON);
	}

	public function comGeolocalizacao()
	{
		// if($this->servicoLogin->verificarSeUsuarioEstaLogado()  == false)
		// {
		// 	return $this->geradoraResposta->naoAutorizado('Erro ao acessar página.', GeradoraResposta::TIPO_TEXTO);
		// }

		try
		{
			$inexistentes = \ArrayUtil::nonExistingKeys(['latitude','longitude'], $this->params);

			if (count($inexistentes) > 0)
			{
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentes);
				return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}

			$latitude = (float) \ParamUtil::value($this->params, 'latitude');
			$longitude = (float) \ParamUtil::value($this->params, 'longitude');

			$endereco = $this->servicoEndereco->consultarGeolocalizacaoOnline($latitude, $longitude);
		}
		catch (\Exception $e)
		{
			return $this->geradoraResposta->erro($e->getMessage(), GeradoraResposta::TIPO_TEXTO);
		}

		return $this->geradoraResposta->resposta(JSON::encode($endereco), GeradoraResposta::OK, GeradoraResposta::TIPO_JSON);
	}
}
?>