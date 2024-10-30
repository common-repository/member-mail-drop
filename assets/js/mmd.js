jQuery(function($){
    $('[mmd-tooltip]').each(function() {
        var tooltipText = $(this).attr('mmd-tooltip');
        var tooltip = $('<div class="mmd-tp">' + tooltipText + '</div>');
        
        $(this).data('tooltip', tooltip);
        
        $(this).hover(
          function() {
            var tooltip = $(this).data('tooltip');
            tooltip.appendTo('body');
            tooltip.css('visibility', 'visible').animate({ opacity: 1 }, 200);
            positionTooltip($(this), tooltip);
          },
          function() {
            var tooltip = $(this).data('tooltip');
            tooltip.animate({ opacity: 0 }, 200, function() {
              tooltip.remove();
            });
          }
        ).mousemove(function(e) {
          var tooltip = $(this).data('tooltip');
          positionTooltip($(this), tooltip);
        });
      });
      
      function positionTooltip(element, tooltip) {
        var elementOffset = element.offset();
        var elementWidth = element.outerWidth();
        var elementHeight = element.outerHeight();
        
        var tooltipHeight = tooltip.outerHeight();
        
        var tooltipTop = elementOffset.top - tooltipHeight - 10;
        var tooltipLeft = elementOffset.left + (elementWidth / 2) - (tooltip.outerWidth() / 2);
        
        tooltip.css({
          top: tooltipTop,
          left: tooltipLeft
        });
      }
      
      
      
      

});

function mmd_sucess_handler(message = '', is_refresh = false, redirect_url = ''){
  alert(message);
  if(is_refresh){
      setTimeout(function(){
        if(redirect_url !==''){
          window.location.href = redirect_url;
        } else {
          location.reload();
        }
    }, 500);
  }
}
function mmd_error_handler(message = ''){
  alert(message);
}