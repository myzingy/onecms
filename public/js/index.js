/**
 * Created by Administrator on 2017/12/30 0030.
 */

$(function(){

    $('body').on('click', function(event) {
        var target = $(event.target); // One jQuery object instead of 3

        // Compare length with an integer rather than with
        if (!target.hasClass('popover')
                && target.attr('data-toggle') !== 'popover'
                && !target.hasClass('editable')
                && target.closest('.popover').lenght<1
            && target.parent('.popover-content').length === 0
            && target.parent('.myPopover').length === 0
            && target.parent('.popover-title').length === 0
            && target.parent('.popover').length === 0 && target.attr("id") !== "folder") {
            $('.popover').popover('hide');
        }
    });
})
