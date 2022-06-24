$(function(){
    $('.alert').fadeOut(10000, function () {
        $(this).remove();        
    });

    $('.timeago').timeago();
});