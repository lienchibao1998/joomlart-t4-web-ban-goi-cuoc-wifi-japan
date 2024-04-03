jQuery(document).ready(function($) {
    $('a[href^="#"]').click(function () {
        $('html, body').animate({
            scrollTop: $('[name="' + $.attr(this, 'href').substr(1) + '"]').offset().top
        }, 500);

        return false;
    });
});