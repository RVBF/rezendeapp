<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;
/**
 *	Coleção de Anexo em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoAnexoEmBDR implements ColecaoAnexo
{

	const TABELA = 'anexos';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			$id = DB::table(self::TABELA)->insertGetId([ 
				'caminho' => $obj->getPatch(),
				'tipo' => $obj->getTipo(),
				'questionamento_id' => ($obj->getQuestionamento() instanceof Questionamento) ? $obj->getQuestionamento()->getId() : $obj->getQuestionamento()['id'],
				'planoacao_id' => ($obj->getPlanoAcao() instanceof PlanoAcao) ? $obj->getPlanoAcao()->getId() : $obj->getPlanoAcao()['id'],
				'pendencia_id' => ($obj->getPendencia() instanceof Pendencia) ? $obj->getPendencia()->getId() : $obj->getPendencia()['id']
			]);
			$obj->setId($id);
		}
		catch (\Exception $e) {
			Util::printr($e->getMessage());

			throw new ColecaoException('Erro ao adicionar Anexo!');
		}
	}

	function remover($id) {
		try {	
			DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao remover anexo!", $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		try {
			DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update([ 'caminho' => $obj->getPatch(), 'tipo' => $obj->getTipo(), 'questionamento_id' => $obj->geQuestionamento()->getId()]);
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao atualizar anexo no banco de dados!", $e->getCode(), $e);
		}
		
	}

	function comId($id){
		try {	
			return (DB::table(self::TABELA)->where('id', $id)->count() >  0) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar pendência  no banco de dados", $e->getCode(), $e);
		}
	}
	
	public function comQuestionamentoId($id){
		try {	

			$anexos = DB::table(self::TABELA)->where('deleted_at', NULL)->where('questionamento_id', $id)->get();
			$anexosObjects = [];
			foreach ($anexos as $anexo) {
				$anexosObjects[] =  $this->construirObjeto($anexo);
			}

			return $anexosObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao buscar anexo no banco de dados!", $e->getCode(), $e);
		}
	}

	public function comPendenciaId($id){
		try {	

			$anexos = DB::table(self::TABELA)->where('deleted_at', NULL)->where('pendencia_id', $id)->get();
			$anexosObjects = [];
			foreach ($anexos as $anexo) {
				$anexosObjects[] =  $this->construirObjeto($anexo);
			}

			return $anexosObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao buscar anexo no banco de dados!", $e->getCode(), $e);
		}
	}

	public function comPlanoAcaoId($id){
		try {	

			$anexos = DB::table(self::TABELA)->where('deleted_at', NULL)->where('planoacao_id', $id)->get();

			$anexosObjects = [];
			foreach ($anexos as $anexo) {
				$anexosObjects[] =  $this->construirObjeto($anexo);
			}

			return $anexosObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao buscar anexo no banco de dados!", $e->getCode(), $e);
		}
	}
	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0) {
		try {	
			$anexos = DB::table(self::TABELA)->where('deleted_at', NULL)->offset($limite)->limit($pulo)->get();

			$anexosObjects = [];
			foreach ($anexos as $pergunta) {
				$anexosObjects[] =  $this->construirObjeto($pergunta);
			}

			return $anexosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar anexos no banco de dados!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$arquivo = ServicoArquivo::instance();

		$base64 =  'data:'. $row['tipo'] . ';base64,'. $arquivo->imagemParaBase64($row['caminho']);
		$anexo = new Anexo($row['id'],
			$row['caminho'],
			$row['tipo'],
			$row['questionamento_id'],
			$row['planoacao_id'],
			$row['pendencia_id']
		);

		$anexo->setArquivoBase64($base64);
		return $anexo->toArray();
	}	

    function contagem() {
		return DB::table(self::TABELA)->where('deleted_at', NULL)->count();
	}
}

?>