@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Admin Login')
@extends('adminloginlayout')
@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
    $("#login-form").validate();
    $("#password-recovery").validate();
       $(".butnclose").click(function() {
    $("#password-recovery").validate().resetForm();
    $('.alert-danger').hide();
    
});
});
</script>

<div class="container">
    
    <?php echo Form::open(array('url' => 'admin/login', 'method' => 'post', 'id' => 'login-form', 'class' => 'form form-signin')); ?>
    <div class="website-logos">
        <div class="weblogo-in" >
         <?php
         if(file_exists(UPLOAD_LOGO_IMAGE_PATH.SITE_LOGO)){
              ?>
            {{ html_entity_decode(link_to(HTTP_PATH, HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, "", ['width'=>200]), array('escape' => false,'class'=>"logos"))) }}
            <?php 
         }else{
             ?>
            {{ html_entity_decode(link_to(HTTP_PATH, HTML::image("public/img/front/logo.png", "", ['width'=>110]), array('escape' => false,'class'=>"logos"))) }}
            <?php
         }
        ?>
        </div>
    </div>
    <h2 class="form-signin-heading">Administrator Login</h2>
      <title>@yield('title')</title>
    <div class="login-wrap">
        <div id="login-block"></div>
        {{ View::make('elements.actionMessage')->render() }}
        <?php echo Form::text('username', null, array('id' => 'login', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Username')); ?>
        <?php
        echo Form::password('password', array('id' => 'pass', 'class' => "required form-control", 'placeholder' => 'Password', 'type' => "password"));

        // check captcha code here
        if (Session::has('captcha')) {
            $class = "";
        } else {
            $class = "captcha_show";
        }
        ?>
        <div class="<?php echo $class; ?> captcha-section">
            <div class="sds">
            <label for="pass"><span class="big">Security code</span></label>
            <img src="<?php echo HTTP_PATH; ?>captcha?rand=<?php echo rand(); ?>" id='captchaimg' >
            <a href='javascript: refreshCaptcha();'>
                <img src="{{ URL::asset('public/img') }}/captcha_refresh.gif" width="35" height="35" alt="">
            </a></div>
            <?php
            echo Form::text('captcha', null, array('id' => 'login', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Type security code shown above'));
            ?>
        </div>
        <label class="checkbox">
            <input type="checkbox" name="remember" value="1"> Remember me
            <span class="pull-right">
                <a data-toggle="modal" href="#myModal"> Forgot Password?</a>
            </span>
        </label>
        <?php
        echo Form::submit('Login', array('class' => 'btn btn-lg btn-login btn-block'));
        ?>
    </div>
    <?php
    echo Form::close();
    ?>
    <!-- Modal -->
    <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <?php echo Form::open(array('url' => 'admin/forgotpassword', 'method' => 'post', 'id' => 'password-recovery', 'class' => 'form')); ?>

                <div class="modal-header">
                    <button type="button" class="close butnclose" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Forgot Password ?</h4>
                </div>
                <div class="modal-body">
                    <p>Enter your e-mail address below to reset your password.</p>
                    <div id="forgotpass-block"></div>
                    <input type="text" name="recovery-mail" id="recovery-mail"  placeholder="Email" autocomplete="off" class="required email form-control placeholder-no-fix">
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default butnclose" type="button">Cancel</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
                <?php
                echo Form::close();
                ?>
            </div>
        </div>
    </div>
</div>
<!-- modal -->

<!-- js placed at the end of the document so the pages load faster -->

{{ HTML::script('public/js/jquery.js') }}
{{ HTML::script('public/js/bootstrap.min.js') }}
{{ HTML::script('public/js/jquery.validate.min.js') }}
<!-- example login script -->
<script>

    $(document).ready(function()
    {
     
        function error(message) {
            return '<div class="alert alert-block alert-danger fade in"><button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button>' + message + '</div>'
        }
        function success(message) {
            return '<div class="alert alert-success alert-block fade in"><button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button><p>' + message + '</p></div>'
        }

        function loading(message) {
            return '<div class="alert alert-info fade in"><button data-dismiss="alert" class="close close-sm" type="button"><i class="fa fa-times"></i></button> <img src="{{ URL::asset("public/img/front") }}/input-spinner.gif"/> ' + message + ' </div>'
        }

        $('#password-recovery').submit(function(event)
        {
            // Stop full page load
            event.preventDefault();

            // Check fields
            var login = $('#recovery-mail').val();
            var emailRegex = new RegExp(/^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/i);
            var valid = emailRegex.test(login);
            if (!login || login.length == 0)
            {
                $('#forgotpass-block').html(error('Please enter your email address'));
            }
            else if (!valid) {
                $('#forgotpass-block').html(error('Please enter correct email address'));
            }
            else
            {

                // Target url
                var target = $(this).attr('action');
                if (!target || target == '')
                {
                    // Page url without hash
                    target = document.location.href.match(/^([^#]+)/)[1];
                }

                var captcha = $('#captcha_reset').val();
                // Request
                var data = {
                    a: $('#a').val(),
                    email: login,
                },
                        redirect = $('#redirect'),
                        sendTimer = new Date().getTime();

                if (redirect.length > 0)
                {
                    data.redirect = redirect.val();
                }

                // Send
                $.ajax({
                    url: target,
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function(data, textStatus, XMLHttpRequest)
                    {
                        if (data.valid)
                        {
                            // Small timer to allow the 'checking login' message to show when server is too fast
                            var receiveTimer = new Date().getTime();
                            if (receiveTimer - sendTimer < 500)
                            {
                                setTimeout(function()
                                {
                                    $('#forgotpass-block').html(success(data.message) || success('Please check your email account'));

                                }, 500 - (receiveTimer - sendTimer));
                            }
                            else
                            {
                                $('#forgotpass-block').html(success(data.message) || success('Please check your email account'));
                            }
                            $('#recovery-mail').val('');
                        }
                        else
                        {
                            // Message
                            $('#forgotpass-block').html(error(data.message) || success('An unexpected error occured, please try again'));

                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        // Message
                        $('#forgotpass-block').html(error('Error while contacting server, please try again'));

                    }
                });

                // Message
                $('#forgotpass-block').html(loading('Please wait, checking email...'));
            }
        });
    });
    function refreshCaptcha()
    {

        var img = document.images['captchaimg'];
        var img_reset = document.images['captchaimg_reset'];
        img_reset.src = img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;

    }
</script> 
@stop
