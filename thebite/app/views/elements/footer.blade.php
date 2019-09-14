
<!-- Bootstrap Js CDN -->

{{ HTML::script('public/js/front2/owl.carousel.js'); }}
<script type="text/javascript">
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });
});
</script>


<script>
    $(function () {
        $('#calendar_slider2').owlCarousel({
            rtl: false,
            loop: true,
            nav: true,
            autoplay: false,
            autoplayTimeout: 5000,
            smartSpeed: 500,
            slideSpeed: 3000,
            autoplayHoverPause: true,
            goToFirstSpeed: 100,
            responsive: {
                0: {items: 1},
                479: {items: 1},
                650: {items: 5},
                766: {items: 5},
                1100: {items: 5},
                1280: {items: 5}
            }

        })
    });
</script>


<script>
    $(document).ready(function () {
        $("#sidebarCollapse").click(function () {
            $(".navbar-btn").toggleClass("menuicon");

        });
    });
</script>

<script>
    $(window).scroll(function () {
        if ($(this).scrollTop() > 5) {
            $(".content_nav ").addClass("fixed-me");
        } else {
            $(".content_nav ").removeClass("fixed-me");
        }

        if ($(this).scrollTop() > 5) {
            $(".responsive_btn").addClass("fixed-icon");
        } else {
            $(".responsive_btn").removeClass("fixed-icon");
        }
    });
</script>

<script>

    $("#parking").on("click", ".init", function () {
        $(".init").toggleClass("arrow")

        $(this).closest("ul").children('li:not(.init)').toggle();
    });

    var allOptions1 = $("#parking").children('li:not(.init)');
    $("#parking").on("click", "li:not(.init)", function () {
        allOptions1.removeClass('selected');
        $(this).addClass('selected');
        $("#parking").children('.init').html($(this).html());
        var value = $(this).html();
        $(".parking").val(value);
        allOptions1.toggle();
    });
</script>
<script>
    $("#deliver").on("click", ".init", function () {
        $(".init").toggleClass("arrow")
        $(this).closest("ul").children('li:not(.init)').toggle();
    });

    var allOptions = $("#deliver").children('li:not(.init)');
    $("#deliver").on("click", "li:not(.init)", function () {
        allOptions.removeClass('selected');
        $(this).addClass('selected');
        $("#deliver").children('.init').html($(this).html());
        var value = $(this).html();
        if (value == 'Paid') {
            $('#delivery_type').show();
        } else {
            $('#delivery_type').hide();
        }
        $(".delivery_type").val(value);
        allOptions.toggle();
    });
</script>
