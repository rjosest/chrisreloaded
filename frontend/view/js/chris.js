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
    } else {
      $("#cartdiv").show('blind');
    }
  });
  //var mySessionVar='<%= Session["username"] %>';
  //alert(<?php echo $_SESSION['cart'] ?>);

  // var test = <?=$_SESSION["username"];?>;
  jQuery("#cartdiv").hide();
});