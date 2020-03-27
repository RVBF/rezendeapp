/**
 *  index.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function (window, app, $, toastr) {
   'use strict';

   function ControladoraListagemDashboard(servicoDashboard) {
      var _this = this;

      _this.configurarDasboard = function configurarDasboard(){

         var html = '';
         html += '<div class="row contadores">';
         html += '</div>';

         html += '<div class="row graficos_padrao1">';
         html += '</div>';

         html += '<div class="row contadores_pe_pa">';
         html += '</div>';      

         $('#dashboard').append(html).promise().done(function() {
            function executaConfig(){
               return new Promise((resolve, reject) =>{
                  resolve();
               });
            }
   
            executaConfig().then(_this.renderizarContadores).then(_this.renderizarGraficoChecklistsPorLoja).then(_this.renderizarGraficoChecklistsPorStatus).then(_this.redenrizarGraficoQtdPAePE);
         });
      };


      _this.renderizarContadores = function renderizarContadores() {
         var sucesso = function (resposta) {
            var html = '';
            html += '<div class="col col-md-4 col-sm-4 col-lg-4 col-12">';
            html += '<div class="card-panel center-align text-white blue darken-2">'
            html += '<p class="font-weight-bold">Total de Checklists</p>'
            html += '<p class="font-weight-bold">'+ resposta.resposta['contagemChecklist'] +'</p>'
            html += '</div>';
            html += '</div>';

            html += '<div class="col col-md-4 col-sm-4 col-lg-4 col-12">';
            html += '<div class="card-panel center-align text-white teal darken-4">'
            html += '<p class="font-weight-bold">Total de Pendências</p>'
            html += '<p class="font-weight-bold">'+ resposta.resposta['contagemPendencia'] +'</p>'
            html += '</div>';
            html += '</div>';

            html += '<div class="col col-md-4 col-sm-4 col-lg-4 col-12">';
            html += '<div class="card-panel center-align text-white yellow darken-2">'
            html += '<p class="font-weight-bold">Total de Planos de Ação</p>'
            html += '<p class="font-weight-bold">'+ resposta.resposta['contagemPlanoAcao'] +'</p>'
            html += '</div>';
            html += '</div>';


            html += '</div>';
            $('#dashboard').find('.contadores:first').prepend(html).promise().done(function(){
               google.charts.load('current', {'packages':['corechart']});
               google.charts.setOnLoadCallback(drawChart);
         
               function drawChart() {
                  var contagemLoja = new Array();
                  contagemLoja.push(['Loja', 'Quantidade']);
                  for (const key in resposta.resposta.contagemPorLoja) {
                     const elemento = resposta.resposta.contagemPorLoja[key];
                     contagemLoja.push([ elemento.Loja, parseInt(elemento.Quantidade)]);
                  }

                  var data = google.visualization.arrayToDataTable(contagemLoja);
                  var options = {
							title: 'Checklist por loja',
							is3D: true,
              		};

						var chart = new google.visualization.PieChart(document.getElementById('grafico_loja'));
				
						chart.draw(data, options);
           	 	}
            });


     
         };

         var jqXHR = servicoDashboard.contadores();
         jqXHR.done(sucesso);
      };

      _this.renderizarGraficoChecklistsPorLoja = function renderizarGraficoChecklistsPorLoja() {
         var sucesso = function (resposta) {
              var html = '<div class="col col-md-5 col-12 col-sm-12 col-lg-5">';
              html += '<div class="grafico_loja" id="grafico_loja"></div>';
              html += '</div>';

              $('#dashboard').find('.graficos_padrao1:first').append(html).promise().done(function () {
               google.charts.load('current', {'packages':['corechart']});
               google.charts.setOnLoadCallback(drawChart);
         
               function drawChart() {
                  var contagemLoja = new Array();
                  contagemLoja.push(['Loja', 'Quantidade']);
                  for (const key in resposta.resposta.contagemPorLoja) {
                     const elemento = resposta.resposta.contagemPorLoja[key];
                     contagemLoja.push([ elemento.Loja, parseInt(elemento.Quantidade)]);
                  }
   
                  var data = google.visualization.arrayToDataTable(contagemLoja);
   
                  var options = {
                   title: 'Checklist por loja',
                   is3D: true,
                   height: 500,
                  };

                  var chart = new google.visualization.PieChart(document.getElementById('grafico_loja'));
                  chart.draw(data, options);
              }
            });
         };

         var jqXHR = servicoDashboard.contadores();
         jqXHR.done(sucesso);
      };

      _this.renderizarGraficoChecklistsPorStatus = function renderizarGraficoChecklistsPorStatus() {
         var sucesso = function (resposta) {
            var html = '<div class="col col-md-7 col-12 col-sm-12 col-lg-7">';
              html += '<div class="grafico_checklists_status" id="grafico_checklists_status"></div>';
              html += '</div>';
              $('#dashboard').find('.graficos_padrao1:first').append(html).promise().done(function () {
               google.charts.load('current', {'packages':['line']});
               google.charts.setOnLoadCallback(carregarGrafico); 
               function carregarGrafico() {
                  var linhas = new Array();
                  linhas.push(['Data', 'Aguardando Execução', 'Em Progresso', 'Executado']);

                  for(const key in resposta.resposta){
                     var objeto = resposta.resposta[key];

                     linhas.push([
                        moment(resposta.resposta[key].Data).format('DD/MM/YYYY'),
                        (objeto.qtdStatusAgaExecucao != null) ? objeto.qtdStatusAgaExecucao : 0, 
                        (objeto.qtdStatusEmProgresso != null) ? objeto.qtdStatusEmProgresso : 0,
                        (objeto.qtdStatusExecutado != null) ? objeto.qtdStatusExecutado : 0
                     ]);
                  }
                  var data = google.visualization.arrayToDataTable(linhas);
            
                  var options = {
                     title: 'Quantidade de Checklists Por Status e Data',
                     curveType: 'function',
                     height: 500,
                     legend: { position: 'bottom' },
                     colors : ['#2196f3', '#ef5350','#009688']
                  };
            
                  var chart = new google.visualization.LineChart(document.getElementById('grafico_checklists_status'));
            
                  chart.draw(data, options);
               };
            });
         };

         var jqXHR = servicoDashboard.checklistsPorStatusEData();
         jqXHR.done(sucesso);
      };

      _this.redenrizarGraficoQtdPAePE = function redenrizarGraficoQtdPAePE() {
         var sucesso = function (resposta) {
            var html = '<div class="col col-md-12 col-12 col-sm-12 col-lg-12">';
              html += '<div class="grafico_quantidade_pa_pe" id="grafico_quantidade_pa_pe"></div>';
              html += '</div>';
				  $('#dashboard').find('.contadores_pe_pa:first').append(html).promise().done(function () {
               google.charts.load("current", {packages:["corechart"]});
               google.charts.setOnLoadCallback(carregarGrafico); 
               function carregarGrafico() {

                  var resultado = [];
                  resultado.push(['Data', 'Pêndencias', 'Planos de ação']);

                  for(const key in resposta.resposta.pEsAbertasPorData){
                     var objeto = resposta.resposta.pEsAbertasPorData[key];
                     var qtdPAAberta = 0;
                     
                     for(const key in resposta.resposta.pAsAbertasPorData){
                        var objetoPA = resposta.resposta.pAsAbertasPorData[key];   
                        if(moment(objetoPA.Data).format('DD/MM/YYYY') ==  moment(objeto.Data).format('DD/MM/YYYY')) qtdPAAberta =  objetoPA.qtdStatusAgaExecucao;
                     }


                     var dados = [ moment(objeto.Data).format('DD/MM/YYYY'), (objeto.qtdStatusAgaExecucao != null) ? objeto.qtdStatusAgaExecucao : 0, qtdPAAberta ];
                     resultado.push(dados);
                  }

                  for(const key in resposta.resposta.pAsAbertasPorData){
                     var objeto = resposta.resposta.pAsAbertasPorData[key];
                     var qtdPEAberta = 0;
                     
                     for(const key in resposta.resposta.pEsAbertasPorData){
                        var objetoPE = resposta.resposta.pEsAbertasPorData[key];   
                        if(moment(objetoPE.Data).format('DD/MM/YYYY') ==  moment(objeto.Data).format('DD/MM/YYYY')) qtdPEAberta =  objetoPE.qtdStatusAgaExecucao;
                     }

                     if(resultado.find(element => moment(objeto.Data).format('DD/MM/YYYY')) != undefined){
                        
                        var dados = [ moment(objeto.Data).format('DD/MM/YYYY'), qtdPEAberta, (objeto.qtdStatusAgaExecucao != null) ? objeto.qtdStatusAgaExecucao : 0];
                        resultado.push(dados);
                     }
                  }

                  resultado.sort(function name(a,b) {

                     if(a == ['Data', 'Pêndencias', 'Planos de ação'] || b == ['Data', 'Pêndencias', 'Planos de ação']) return  0;

                     a = a[0].split('/');
                     b = b[0].split('/');

                     if(moment(a[2]+ '-' + a[1] + '-' + a[0]).isAfter(moment(b[2]+ '-' + b[1] + '-' + b[0]))) return 1;
                     if(moment(a[2]+ '-' + a[1] + '-' + a[0]).isBefore(moment(b[2]+ '-' + b[1] + '-' + b[0]))) return -1
                     
                     return 0;
                     console.log(a);
                     console.log(b);
                     // if (a é menor que b em algum critério de ordenação) {
                     //    return -1;
                     //  }
                     //  if (a é maior que b em algum critério de ordenação) {
                     //    return 1;
                     //  }
                     //  // a deve ser igual a b
                     //  return 0;  
                  })
                  
                  var data = google.visualization.arrayToDataTable(resultado);

                  var options = {
                     title: 'Totais de Planos de ação e Pendências Em Aberto',
                     vAxis: {title: 'Quantidade'},
                     hAxis: {title: 'Data'},
                     seriesType: 'bars',
                     series: {5: {type: 'line'}},
                     height : 500        
                  };

                  var chart = new google.visualization.ComboChart(document.getElementById('grafico_quantidade_pa_pe'));
                  chart.draw(data, options);
               };
            });
         };

         var jqXHR = servicoDashboard.qtdPAePe();
         jqXHR.done(sucesso);
      };

      _this.configurar = function configurar() {
         _this.configurarDasboard();
      };
   } // ControladoraListagemDashboard

   // Registrando
   app.ControladoraListagemDashboard = ControladoraListagemDashboard;
})(window, app, jQuery, toastr);