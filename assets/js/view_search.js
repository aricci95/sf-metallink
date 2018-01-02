$(document).ready(function() {
    refresh();

    function refresh() {
        var params = {};

        $.get('view/search', {},
            function(data) {
              tmp = $(data);
            //   loading = $(".loading");
               tmp.hide();
               $(".results").html(tmp);
        //       loading.attr('data-offset', 0);
            //   loading.attr('data-end', 'false');
            //   loading.attr('data-show', 'false');
               tmp.fadeIn();
            },
            'html'
        );
    }
});
