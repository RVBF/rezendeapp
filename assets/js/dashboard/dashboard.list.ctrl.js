/**
 *  index.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function (window, app, $, toastr) {
   'use strict';

   function ControladoraListagemDashboard(servicoDashboard) {
      var _this = this;

      _this.renderizarContadores = function renderizarContadores() {
         var sucesso = function (resposta) {
            var html = '';
            html += '<div class="contadores col col-md-12 col-sm-12 col-lg-12 col-12">'
            html += '<div class="col col-md-4 col-sm-3 col-lg-3 col-12">';
            html += '<div class="card-panel center-align text-white blue darken-2">'
            html += '<p class="font-weight-bold">Total de Checklists</p>'
            html += '<p class="font-weight-bold">'+ resposta.resposta['contagemChecklist'] +'</p>'
            html += '</div>';
            html += '</div>';

            html += '<div class="col col-md-4 col-sm-3 col-lg-3 col-12">';
            html += '<div class="card-panel center-align text-white teal darken-4">'
            html += '<p class="font-weight-bold">Total de Pendências</p>'
            html += '<p class="font-weight-bold">'+ resposta.resposta['contagemPendencia'] +'</p>'
            html += '</div>';
            html += '</div>';

            html += '<div class="col col-md-4 col-sm-3 col-lg-3 col-12">';
            html += '<div class="card-panel center-align text-white yellow darken-2">'
            html += '<p class="font-weight-bold">Total de Planos de Ação</p>'
            html += '<p class="font-weight-bold">'+ resposta.resposta['contagemPlanoAcao'] +'</p>'
            html += '</div>';
            html += '</div>';

            html += '</div>';
            $('.dashboard').prepend(html);

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
               console.log(data);

               var options = {
                title: 'Checklist por loja',
                is3D: true,
              };
            //   $('<div></div>').attr("class","grafico_loja").attr("id","grafico_loja").append(".dashboard").promise().done(function () {

            //   });

              var chart = new google.visualization.PieChart(document.getElementById('grafico_loja'));
      
              chart.draw(data, options);
           
            }
         };

         var jqXHR = servicoDashboard.contadores();
         jqXHR.done(sucesso);
      };

      _this.renderizarGraficoChecklistsPorLoja = function renderizarGraficoChecklistsPorLoja() {
         var sucesso = function (resposta) {
              var html = '<div class="col col-md-5 col-12 col-sm-12 col-lg-5">';
              html += '<div class="grafico_loja" id="grafico_loja"></div>';
              html += '</div>';

              $(".dashboard").append(html).promise().done(function () {
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
            $(".dashboard").append(html).promise().done(function () {
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
            $(".dashboard").append(html).promise().done(function () {
               google.charts.load("current", {packages:["corechart"]});
               google.charts.setOnLoadCallback(carregarGrafico); 
               function carregarGrafico() {
                  // Some raw data (not necessarily accurate)
                  var data = google.visualization.arrayToDataTable([
                     ['Month', 'Bolivia', 'Ecuador', 'Madagascar', 'Papua New Guinea', 'Rwanda', 'Average'],
                     ['2004/05',  165,      938,         522,             998,           450,      614.6],
                     ['2005/06',  135,      1120,        599,             1268,          288,      682],
                     ['2006/07',  157,      1167,        587,             807,           397,      623],
                     ['2007/08',  139,      1110,        615,             968,           215,      609.4],
                     ['2008/09',  136,      691,         629,             1026,          366,      569.6]
                  ]);

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
         _this.renderizarContadores();
         _this.renderizarGraficoChecklistsPorLoja();
         _this.renderizarGraficoChecklistsPorStatus();
         _this.redenrizarGraficoQtdPAePE();
      };
   } // ControladoraListagemDashboard

   // Registrando
   app.ControladoraListagemDashboard = ControladoraListagemDashboard;
})(window, app, jQuery, toastr);