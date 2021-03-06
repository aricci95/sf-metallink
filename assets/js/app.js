// loads the jquery package from node_modules
const $ = require('jquery');

var nextPage = 1;

window.searchable = false;

function chat(id)
{
    if (!$('.dialogBox[data-id="' + id + '"]').length) {
        $.get($('.chatDock').data('dialog-url'), {id: id}, function (data) {
            $('.chatDock').append(data);

            $('.dialogBox[data-id="' + id + '"]').find('.dialogInput').focus();

            chatRefresh(id);
        });
    }
}

function chatRefresh(id)
{
    var dialogBox = $('.dialogBox[data-id="' + id + '"]');

    $.get(dialogBox.data('refresh-url'), {
        lastChatId : dialogBox.data('last-chat-id'),            
    }, function(response) {
        if (response.html) {
            dialogBox.find('.results').append(response.html);
            dialogBox.find('.dialogContent').scrollTop(dialogBox.find('.dialogContent')[0].scrollHeight);

            if (response.isNew) {
                dialogBox.find('.dialogHeader').addClass('dialogHeaderNew');
            }

            dialogBox.data('last-chat-id', response.lastChatId)
        }
    });
}

function chatRefreshAll()
{
    console.log('refresh all');

    $.get($('.chatDock').data('has-new-url'), {}, function(response) {
        $.each(response.userIds, function(index, id) {
            if ($('.dialogBox[data-id="' + id + '"]').length) {
                chatRefresh(id);
            } else {
                chat(id);
            }
        });    
    });
}

// setInterval(chatRefreshAll, 10000); @TODO : ENABLE THIS FOR CHAT

window.search = function search(params, forceSearch = false) {
    if (forceSearch || ($(window).scrollTop() + $(window).height() >= ($(document).height() - 900) && !$('.results').data('processing') && nextPage)) {
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
    chatRefreshAll();

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

        chat($(e.target).closest('.chatLink').data('id'));
    });

    $(".site").on('click', '.dialogClose a', function(e) {
        e.preventDefault();
        $(e.target).closest('.dialogBox').remove();
    });

    $(".chatDock").keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();

            var form = $(e.target).closest('.dialogForm');

            $.post(form.data('href'), {
                content : $(e.target).val()
            }, function (data) {
                $(e.target).val('');
                chatRefresh($(e.target).closest('.dialogBox').data('id'));
            });
        }
    });
});