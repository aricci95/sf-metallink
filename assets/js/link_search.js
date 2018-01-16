window.searchable = true;

window.getParams = function getParams() {
    return {
        status: $('.results').data('status')
    };
}