/**
 *  tipoChecklist.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(app, $)
{
   'use strict';

   function TipoChecklist() {
        this.CHECKLIST = 'Checklist Padrão';
        this.PA = 'PA';
        this.VENDA_ASSISTIDA = 'Venda Assistida';
        this.QNP = 'QNP';

       this.getTipoChecklist = function(){
           return { 
                1 : this.CHECKLIST,
                2 : this.PA,
                3 : this.VENDA_ASSISTIDA,
                4 : this.QNP
           };
       }
   };

   // Registrando
   app.TipoChecklist = TipoChecklist;
})(app, $);