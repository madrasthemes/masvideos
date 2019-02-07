jQuery(document).ready(function($) {

  $('#widgets-right').on('click', '.vpw-tab-item', function(event) {
    event.preventDefault();
    var widget = $(this).parents('.widget');
    console.log(widget);
    widget.find('.vpw-tab-item').removeClass('active');
    $(this).addClass('active');
    widget.find('.vpw-tab').addClass('vpw-hide');
    widget.find('.' + $(this).data('toggle')).removeClass('vpw-hide');
  });

});