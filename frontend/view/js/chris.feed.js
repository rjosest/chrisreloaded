/**
 * Define the FEED namespace
 */
var _FEED_ = _FEED_ || {};
_FEED_.onclick = function(more, details) {
  var hidden = details.is(':hidden');
  if (hidden) {
    more.html('<a>Hide details</a>');
    more.closest('.feed').css('margin-top', '10px');
    more.closest('.feed').css('margin-bottom', '11px');
    details.show('blind', 100);
  } else {
    more.html('<a>Show details</a>');
    more.closest('.feed').css('margin-top', '-1px');
    more.closest('.feed').css('margin-bottom', '0px');
    details.hide('blind', 100);
  }
}
_FEED_.more_onclick = function() {
  jQuery(".more").live('click', function(e) {
    // modify
    e.stopPropagation();
    var details = jQuery(this).closest('.preview').next();
    _FEED_.onclick(jQuery(this), details);
  });
}
_FEED_.feed_onclick = function() {
  jQuery(".feed").live(
      'click',
      function() {
        // modify
        // alert('Show details!');
        var details = jQuery(this).children('.details');
        var more = jQuery(this).children('.preview').children('.content')
            .children('.more');
        _FEED_.onclick(more, details);
      });
}
_FEED_.feed_mouseenter = function() {
  jQuery(".feed").live('mouseenter', function() {
    jQuery(this).css('background', '-moz-linear-gradient(top, #eee, #ddd)');
  });
}
_FEED_.feed_mouseleave = function() {
  jQuery(".feed").live('mouseleave', function() {
    jQuery(this).css('background', '-moz-linear-gradient(top, #fff, #eee)');
  });
}
_FEED_.updateFeedTimeout = function() {
  timer = setInterval(_FEED_.ajaxUpdate, 5000);
}
_FEED_.ajaxUpdate = function() {
  jQuery.ajax({
    type : "POST",
    url : "controller/feed_update.php",
    dataType : "text",
    data : {},
    success : function(data) {
      if (data) {
        // fill cache
        _FEED_.cachedFeeds += data;
        // update "Update" button
        jQuery('.feed_update').html('More feeds available');
      }
    }
  });
}
_FEED_.update_onclick = function() {
  jQuery(".feed_update").live('click', function() {
    // update the feeds
    jQuery('.feed_content').prepend(_FEED_.cachedFeeds);
    // empty buffer
    _FEED_.cachedFeeds = [];
    // update button
    jQuery(this).html('Up to date');
  });
}
/**
 * Setup the javascript when document is ready (finshed loading)
 */
jQuery(document).ready(function() {
  // feed functions
  _FEED_.cachedFeeds = '';
  _FEED_.feed_onclick();
  _FEED_.more_onclick();
  _FEED_.update_onclick();
  _FEED_.feed_mouseenter();
  _FEED_.feed_mouseleave();
  _FEED_.updateFeedTimeout();
});