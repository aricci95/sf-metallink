// loads the jquery package from node_modules
const $ = require('jquery');

$(document).ready(function() {
    // LINK
    $(".site").on('click', '.link', function(e) {
        e.preventDefault();

        $(e.target).attr('src', '/images/icone/loading.gif');

        if ($(e.target).data('action') == 'register') {
            window.location.replace('/register');
        } else if ($(e.target).data('action') == 'create') {
            $.post("link/create", {
                target_id : $(e.target).data('target-id')
            }, function (data) {
                $(e.target).closest('.linkDestinataire').html(data)
            });
        } else {
            $.ajax({
                url: "link/" + $(e.target).data('action') + '/' +  $(e.target).data('link-id'),
                type: 'PUT',
                success: function(data) {
                    $(e.target).closest('.linkDestinataire').html(data)
                }
            });
        }
    });
});
