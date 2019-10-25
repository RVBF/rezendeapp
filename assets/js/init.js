(function($){
  $(function(){
    $('.sidenav').sidenav();
  }); // end of document ready
})(jQuery); // end of jQuery name space
document.addEventListener('DOMContentLoaded', function() {
  var elems = document.querySelectorAll('select');
  var instances = M.FormSelect.init(elems, options);
});

// Or with jQuery

// function mostrar(elemento){
//   $(elemento).parents('.ooo').next().show(100);
// }
// function sumir(elemento){
//   $(elemento).parents('.ooo').next().hide(100);
// }

$(document).ready(function(){
  // $('select').formSelect();
  
});
