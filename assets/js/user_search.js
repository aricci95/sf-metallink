$(document).ready(function() {
    refresh();

    $("#search_form").on('change', 'select', function(e) {
        e.preventDefault();
        refresh();
    });

    $("#search_form").on('click', '#submit_button', function(e) {
        e.preventDefault();
        refresh();
    });

    function refresh() {
        var params = {};

        if ($('#user_search_username').val()) {
          params['username'] = $('#user_search_username').val();
        }

        if ($('#user_search_gender').val()) {
          params['gender'] = $('#user_search_gender').val();
        }

        $.get('user/search', params,
            function(data) {
               tmp = $(data);
               loading = $(".loading");
               tmp.hide();
               $(".results").html(tmp);
               loading.data('offset', 0);
               loading.data('end', 'false');
               loading.data('show', 'false');
               tmp.fadeIn();
            },
            'html'
        );
    }
});
