{{-- Js --}}
<script src="user/js/plugins/jquery-3.3.1.min.js"></script>
<script src="user/js/plugins/bootstrap.min.js"></script>
<script src="user/js/plugins/imagesloaded.pkgd.min.js"></script>
<script src="user/js/plugins/jquery.isotope.v3.0.2.js"></script>
<script src="user/js/plugins/modernizr-2.6.2.min.js"></script>
<script src="user/js/plugins/jquery.waypoints.min.js"></script>
<script src="user/js/plugins/wow.min.js"></script>
<script src="user/modules/owl-carousel/owl.carousel.min.js"></script>
<script src="user/modules/slick/slick.js"></script>
<script src="user/modules/magnific-popup/jquery.magnific-popup.js"></script>
<script src="user/modules/masonry/masonry.pkgd.min.js"></script>
<script src="user/js/script.js"></script>
<script>
    (function () {
        function initWow() {
            if (typeof WOW === 'undefined') {
                return;
            }
            new WOW({
                boxClass: 'wow',
                animateClass: 'animated',
                offset: 80,
                mobile: true,
                live: false
            }).init();
        }

        /* Chờ preloader (script.js: delay 500ms + fadeOut) rồi mới init WOW để tránh giật layout */
        if (window.jQuery) {
            jQuery(window).on('load', function () {
                window.setTimeout(initWow, 1100);
            });
        } else {
            window.addEventListener('load', function () {
                window.setTimeout(initWow, 1100);
            });
        }
    })();
</script>
