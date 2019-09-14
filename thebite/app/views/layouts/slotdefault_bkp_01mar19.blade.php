<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="shortcut icon" type="image/x-icon" href="{{{ asset('public/frontimg/favicon.ico') }}}"> 
        <title><?php
            if (isset($title)) {
                echo $title;
            }
            ?></title>

        <!-- Bootstrap -->
        {{ HTML::style('public/frontcss/bootstrap.min.css') }}

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        {{ HTML::style('public/frontcss/style.scss') }}
        {{ HTML::style('public/frontcss/style.css') }}
        {{ HTML::style('public/frontcss/font-awesome.css') }}
        {{ HTML::style('public/frontcss/owl.theme.default.min.css') }}
        {{ HTML::style('public/frontcss/owl.carousel.min.css') }}

    </head>
    <body>
        <div id="fb-root"></div>
        <script>(function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>

        <header class="header_inner">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                        <div class="logo">
                            <a href="{{ url('/') }}">{{ HTML::image("public/listingimg/logo.png") }}</a>   
                        </div>   
                    </div> 
                    <div class="center_text text-center col-12 col-sm-6 col-md-6 col-lg-7">
                        <div class="form-group center_field ml-auto">
                            <i class="fa fa-map-marker"></i>
                            <input type="text" id="locate" placeholder="7th Ave, New York, NY 10036, USA"> 

                        </div></div>    
                    <div class="ml-auto col-12 col-sm-12 col-md-3 col-lg-3">
                        <ul class="d-inline-block list-unstyled right_align">
                            <?php
                            if (Session::get('userdata')->id) {
                                ?>  
                                <li class="d-inline-block"><a href="{{url('logout')}}">Logout</a></li> 

                            <?php } else { ?>
                                <li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li> 
                            <?php } ?>   
                            <li class="nav-item d-inline-block"><a href="#">Cart<span class="bag_cart">{{ HTML::image("public/frontimg/bag.png") }}<!--<b class="cart_btn"></b>--></span></a></li>   
                        </ul>
                    </div>
                </div>  
            </div>  
        </header>
        @yield('content')
        @include('front.footer')
        <!-------FOOTER-SECTION-END------->

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="{{ URL::asset('public/frontjs/jquery.min.js') }}"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ URL::asset('public/frontjs/bootstrap.min.js') }}"></script>

        <script type="text/javascript" src="{{ URL::asset('public/frontjs/owl.carousel.js') }}"></script>  


        <script>

                $('#auto_width').owlCarousel({
                    margin: 10,
                    loop: true,
                    autoWidth: true,
                    items: 4
                })

        </script>
        <script>

            $("#time_drop").click(function () {
                $(".time-toolbar").slideToggle();
            });
            $("#time_dropp").click(function () {
                $(".time-toolbar").slideToggle();
            });
            $("#time_droppp").click(function () {
                $(".time-toolbar").slideToggle();
            });
        </script>

        <script>
            $(".contactt").click(function () {

                $('html, body').animate({
                    scrollTop: $(".contact").offset().top - 80

                }, 2000);
            });

        </script>

        <script>
            $('.add').click(function () {
                if ($(this).prev().val() < 50) {
                    $(this).prev().val(+$(this).prev().val() + 1);
                }
            });
            $('.sub').click(function () {
                if ($(this).next().val() > 1) {
                    if ($(this).next().val() > 1)
                        $(this).next().val(+$(this).next().val() - 1);
                }
            });


            $("#locate").keyup(function () { //alert('hh');
                var locate = $('#locate').val();
                $.ajax({
                    type: 'POST',
                    url: 'getlocation',
                    data: 'locate=' + locate,
                    success: function (data) {
                        // alert(data);
                        $('#final').html(data)
                    }
                });

            });


        </script>


    </body>
</html>
