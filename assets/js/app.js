// loads the jquery package from node_modules
const $ = require('jquery');

$(document).ready(function() {
    $("#search_form").on('change', 'select', function(e) {
        e.preventDefault();
        refresh();
    });

    $("#search_form").on('click', '#submit_button', function(e) {
        e.preventDefault();
        refresh();
    });

    function refresh() {
        $.post('user/search', {
                search_login : $('#user_search_username').val(),
                search_distance : $('#user_search_distance').val(),
                search_gender : $('#user_search_gender').val(),
                search_age : $('#user_search_age').val(),
                search_keyword : $('#user_search_keyword').val(),
                search_style : $('#user_search_style').val()
            },
            function(data) {
               tmp = $(data);
               loading = $(".loading");
               tmp.hide();
               $(".results").html(tmp);
               loading.attr('data-offset', 0);
               loading.attr('data-end', 'false');
               loading.attr('data-show', 'false');
               tmp.fadeIn();
            },
            'html'
        );
    }
});
