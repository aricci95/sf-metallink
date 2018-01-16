$(document).ready(function() {
    window.search(getParams());

    $("#search_form").on('change', 'select', function(e) {
        e.preventDefault();
        window.search(getParams());
    });

    $("#search_form").on('click', '#submit_button', function(e) {
        e.preventDefault();
        window.search(getParams());
    });

    function getParams() {
        var params = {};

        if ($('#user_search_username').val()) {
          params['username'] = $('#user_search_username').val();
        }

        if ($('#user_search_gender').val()) {
          params['gender'] = $('#user_search_gender').val();
        }

        return params;
    }

    $(document).scroll(function() {
        $(window).scroll(function () {
            window.search(getParams());
        });
    });
});