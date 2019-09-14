<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('public/img/front') }}/favicon.ico"> 
        <title> <?php
            if (isset($title)) {
                echo $title;
            }
            ?>
        </title>
        <!-- Bootstrap -->
        {{ HTML::style('public/css/front2/bootstrap.min.css') }}
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        {{ HTML::style('public/css/front2/style.css') }}
        {{ HTML::style('public/css/front2/font-awesome.css') }}
        {{ HTML::style('public/css/front2/aos.css') }}
        {{ HTML::style('public/css/front2/owl.theme.default.min.css') }}
        {{ HTML::style('public/css/front2/owl.carousel.min.css') }}
        <!-- jQuery CDN -->
        <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    </head>
    <!---------------------Header------------------>
    <body>
        <div class="all_bg" id="loading-image" style="display:none">
<!--            <div class="all_bg_ldr"><img src="{{ HTTP_PATH.'public/img/front/loader.gif' }}" alt=""></div>-->
            <div class="all_bg_ldr">&nbsp;</div>
        </div>     
        @include('elements.left_menu')
        @yield('content')
        @include('elements.footer')

    </body>
</html>
