function blinking(elm) {
  timer = setInterval(blink, 2000);
  function blink() {
    // elm.show('highlight',{color: 'yellow'},'slow');
    /* elm.effect("pulsate", { times:1 }, 1000); */
    elm.fadeTo(1000, 0.5, function() {
      elm.fadeTo(1000, 1);
    });
  }
}
$(document).ready(function() {
  jQuery('.dropdown-toggle').dropdown();
  jQuery("[rel=bottom_tooltip]").tooltip({
    placement : 'bottom'
  });
  jQuery("[rel=right_tooltip]").tooltip({
    placement : 'right'
  });
  jQuery('#pacs_pull').focus(function() {
    $(this).animate({
      height : '80px'
    }, 200);
    jQuery('#pacs_pull_ui').show();
  });
  jQuery('#pacs_pull').blur(function() {
    $(this).animate({
      height : '19px'
    }, 200);
    jQuery('#pacs_pull_ui').hide();
  });
  jQuery("#cart").click(function(event) {
    if ($("#cartdiv").is(":visible")) {
      $("#cartdiv").hide('blind');
      blinking($("#cart"));
    } else {
      $("#cartdiv").show('blind');
      clearInterval(timer);
    }
  });

  jQuery("#cartdiv").hide();
  blinking($("#cart"));
  // blinking($("#submit"));
});