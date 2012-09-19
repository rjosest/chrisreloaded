/**
 * Define the _PACS_ namespace
 */
var _PACS_ = _PACS_ || {};
_PACS_.pull_focus = function() {
  jQuery('#pacs_pull_mrns').focus(function() {
    jQuery(this).animate({
      height : '80px'
    }, 200);
    jQuery('#pacs_pull_ui').show();
  });
}
_PACS_.pull_blur = function() {
  jQuery('#pacs_pull_mrns').blur(function() {
    if (jQuery('#pacs_pull_mrns').val() == '') {
      jQuery('#pacs_pull_mrns').animate({
        height : '19px'
      }, 200);
      jQuery('#pacs_pull_ui').hide();
    }
  });
}
_PACS_.pull_click = function() {
  jQuery("#pacs_pull").click(function(event) {
    // if not already querying the pacs
    if (!jQuery("#pacs_pull_mrns").prop("readonly")) {
      var mrn_list = jQuery('#pacs_pull_mrns').val();
      jQuery("#pacs_pull_mrns").prop("readonly", "readonly");
      jQuery("#pacs_pull_advanced").prop("readonly", "readonly");
      jQuery(this).text('Pulling...');
      blinking(jQuery(this));
      jQuery.ajax({
        type : "POST",
        url : "controller/pacs_move.php",
        dataType : "json",
        data : {
          USER_AET : 'FNNDSC-CHRISDEV',
          SERVER_IP : '134.174.12.21',
          SERVER_POR : '104',
          PACS_LEV : 'STUDY',
          PACS_STU_UID : '',
          PACS_MRN : mrn_list,
          PACS_NAM : '',
          PACS_MOD : '',
          PACS_DAT : '',
          PACS_STU_DES : '',
          PACS_ACC_NUM : ''
        },
        success : function(data) {
          clearInterval(timer);
          jQuery("#pacs_pull").text('Pull');
          jQuery("#pacs_pull_mrns").removeProp("readonly");
          jQuery("#pacs_pull_advanced").removeProp("readonly");
          jQuery('#pacs_pull_mrns').val('');
          jQuery('#pacs_pull_mrns').animate({
            height : '19px'
          }, 200);
          jQuery('#pacs_pull_ui').hide();
        }
      });
    }
  });
}
/**
 * Setup the javascript when document is ready (finshed loading)
 */
jQuery(document).ready(function() {
  _PACS_.pull_click();
  _PACS_.pull_focus();
  _PACS_.pull_blur();
});