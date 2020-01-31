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
            html += '<div class="row contadores">'
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
              var html = '<div class="row">';
              html += '<div class="col col-md-12 col-12 col-sm-6 col-lg-12">';
              html += '<div class="grafico_loja" id="grafico_loja"></div>';
              html += '</div>';
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
                  console.log(data);
   
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

      _this.configurar = function configurar() {
         _this.renderizarContadores();
         _this.renderizarGraficoChecklistsPorLoja();
      };
   } // ControladoraListagemDashboard

   // Registrando
   app.ControladoraListagemDashboard = ControladoraListagemDashboard;
})(window, app, jQuery, toastr);