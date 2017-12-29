// loads the jquery package from node_modules
const $ = require('jquery');

$(document).ready(function() {
    // LINK
    $(".site").on('click', '.link', function(e) {
        e.preventDefault();

        $.post("link/create", {
            target_id : $(e.target).data('target-id')
        });
    });
});
