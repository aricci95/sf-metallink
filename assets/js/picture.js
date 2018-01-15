$(document).ready(function() {
    refresh();

    $('form[name="picture"]').on('change', '#picture_name', function(e) {
        $('form[name="picture"]').submit();
    });

    $("#collection").on('click', '.editPhoto', function(e) {
        e.preventDefault();

        $("#collection").css('opacity', 0.8);
        
        $.ajax({
            url: 'collection/' + $(e.target).closest('.editPhoto').data('id'),
            type: 'PUT',
            success: function(data) {
                $("#collection").html(data);
                $("#collection").css('opacity', 1);
            }
        });
    });

    function refresh() {
        $.get('collection', {},
            function(data) {
               tmp = $(data);
               loading = $(".loading");
               tmp.hide();
               $("#collection").html(tmp);
               tmp.fadeIn();
            },
            'html'
        );
    }
});
