/**
 *  tipoQuestionario.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function TipoQuestionario() {
		this.padrao = 'Padrão';
		this.PA = 'PA';
		this.QNP = 'QNP';
		this.GENERICO = 'Generico';


		this.getTipoQuestionario = function(){
            return { 
                1 : this.padrao,
                2 : this.PA,
                3 : this.QNP,
                4 : this.GENERICO 
            };
		}
	};

	// Registrando
	app.TipoQuestionario = TipoQuestionario;
})(app, $);