
$(document).ready(function() {
    // Only run if user is logged in (cart button exists)
    const $cartIcon = $('#cartIcon');
    if ($cartIcon.length) {
        $cartIcon.on('click', function(e) {
            e.preventDefault();
            $('#cartSidebar').addClass('open');
            $('#cartOverlay').addClass('active');
            $('body').css('overflow', 'hidden'); // prevent scroll
        });
    }

    // Close cart sidebar
    $('.close-cart, #cartOverlay').on('click', function() {
        $('#cartSidebar').removeClass('open');
        $('#cartOverlay').removeClass('active');
        $('body').css('overflow', 'auto');
    });

    // Optional: Close with Escape key
    $(document).on('keydown', function(e) {
        if (e.key === "Escape") {
            $('#cartSidebar').removeClass('open');
            $('#cartOverlay').removeClass('active');
            $('body').css('overflow', 'auto');
        }
    });
});
