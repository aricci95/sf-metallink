$(document).ready(function() {
    $(".tasteDatas").on('focus', '.addTaste', function(e) {
        e.preventDefault();

        var taste     = $(e.target);
        var tasteList = taste.closest('ul.tasteDatas');
        var tasteType = tasteList.attr('data-taste-type');

        taste.closest('.addTaste').removeClass('addTaste');
        tasteList.append('<li><input class="addTaste taste" name="' + tasteType + 's[]" maxlength="30" value=""/></li>');
    });
});