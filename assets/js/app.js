// loads the jquery package from node_modules
const $ = require('jquery');

$(document).ready(function() {

    function refreshIndicator() {
        $('#indicator').html('<img src="/images/icone/loading.gif">');

        $.get($('#indicator').data('href'), {}, function (data) {
            $('#indicator').html(data)
        });
    }

    refreshIndicator();

    // LINK
    $(".site").on('click', '.link', function(e) {
        e.preventDefault();

        $(e.target).attr('src', '/images/icone/loading.gif');

        if ($(e.target).data('action') == 'register') {
            window.location.replace($(e.target).data('href'));
        } else if ($(e.target).data('link-id')) {
            $.ajax({
                url: $(e.target).data('href'),
                type: 'PUT',
                success: function(data) {
                    if ($(e.target).data('action') == 'blacklist') {
                        $(e.target).closest('.divElement').fadeOut();
                    } else {
                        $(e.target).closest('.linkDestinataire').html(data);
                    }
                }
            });
        } else {
            $.post($(e.target).data('href'), {
                target_id : $(e.target).data('target-id')
            }, function (data) {
                $(e.target).closest('.linkDestinataire').html(data);
            });
        }

        refreshIndicator();
    });
});
