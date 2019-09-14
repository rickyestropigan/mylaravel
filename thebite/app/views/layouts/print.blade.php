<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="format-detection" content="telephone=no"> 
            <title> 
                <?php
                if (isset($title)) {
                    echo $title;
                }
                ?>
            </title>
            <?php if (file_exists(UPLOAD_LOGO_IMAGE_PATH . SITE_FAVICON)) {
                ?>
                <link rel="icon" type="image/png" href="<?php echo HTTP_PATH . DISPLAY_LOGO_IMAGE_PATH . SITE_FAVICON; ?>"/>
            <?php } else { ?>
                <link rel="icon" type="image/png" href="{{ asset('public/img/front/favicon.ico') }}"/>
            <?php } ?>

            {{ HTML::style('public/css/front/style.css') }}
            {{ HTML::style('public/css/front/media.css') }}
            {{ HTML::style('public/css/front/font-awesome.css'); }}
            {{ HTML::script('public/js/front/jquery.min.js'); }}

            <script type="text/javascript">
                $(document).ready(function () {
                    $('.showhide2').click(function () {
                        $(".slidediv2").slideToggle();
                    });
                    setTimeout(function () {
                        $('.alert-success').fadeOut('fast');
                    }, 5000);
                });
            </script> 

    </head>
    <body>
        <div id="wrapper" class="printwrapper">
            <?php
            // get admin details
            $adminuser = DB::table('admins')
                    ->first();
            if ($adminuser->maintenance) {
                ?>
                <div style="width: 100%; text-align: center; margin-top: 10%;">
                    <img  style="text-align: center;" src="{{HTTP_PATH."public/img/front/under-maintenance.jpg"}}"/>
                </div>
                <?php
                die;
            }
            ?>
            @yield('content')
        </div>

    </body>
</html>
