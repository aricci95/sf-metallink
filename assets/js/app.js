// loads the jquery package from node_modules
const $ = require('jquery');

var nextPage = 1;

window.searchable = false;

window.search = function search(params) {
    if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 900) && !$('.results').data('processing') && nextPage) {
        $('.results').data('processing', true);

        $.get('search/' + nextPage, params, function(response) {
            data = JSON.parse(response);

            nextPage = data.nextPage

            if (data.html) {
                html = $(data.html);

                html.hide();

                $('.results').append(html);

                html.fadeIn();
            }

            $('.results').data('processing', false);
        }, 'html');
    }
}

$(document).ready(function() {
    function refreshIndicator() {
        $('#indicator').html('<img src="/images/icone/loading.gif">');

        $.get($('#indicator').data('href'), {}, function (data) {
            $('#indicator').html(data)
        });
    }

    refreshIndicator();

    // Link
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

    // Search
    if (window.searchable) {
        window.search(getParams());

        $(document).scroll(function() {
            $(window).scroll(function () {
                window.search(window.getParams());
            });
        });
    }

    // Chat
    $(".site").on('click', '.chatLink', function(e) {
        e.preventDefault();

        $('.dialogBox[data-id="' + $(e.target).closest('.chatLink').data('id') + '"]').show();

        if (!$('.dialogBox[data-id="' + $(e.target).closest('.chatLink').data('id') + '"]').length) {
            $.get($(e.target).closest('.chatLink').data('href'), {}, function (data) {
                $('.chatDock').append(data);
            });
        }
    });

    $(".site").on('click', '.dialogClose a', function(e) {
        e.preventDefault();
        $(e.target).closest('.dialogBox').remove();
    });
});