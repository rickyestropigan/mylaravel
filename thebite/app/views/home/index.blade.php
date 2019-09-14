@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    $("#login-form").validate();
});
</script>
<div class="login_bg">
    <div class="pop_up_div">
        <div class="pop_up_div_inner" id="login">
            <div class="login_logo"><a href="{{HTTP_PATH}}"><img src="{{ asset('public/img/logo.jpg') }}" alt="logo"></a></div>
            {{ View::make('elements.actionMessage')->render() }}
            {{ Form::open(array('url' => '/login', 'method' => 'post', 'id' => 'login-form', 'files' => true,'class'=>"")) }}
            <div class="field">
                <div class="input_field">
                    <span class="iconn"><i class="fa fa-user" aria-hidden="true"></i></span>
                    {{ Form::text('username', Session::get('username'), array('id'=>'username', 'class' => 'required form-control','placeholder'=>"Username")) }}
                </div>  
                <div class="input_field">
                    <span class="iconn"><i class="fa fa-lock" aria-hidden="true"></i></span>
                    {{ Form::password('password' ,array( 'id'=>'password_login', 'type'=>'password','class' => 'required form-control','placeholder'=>"Password",))}} 

                    <?php
                    if (Session::has('captcha')) {
                        $class = "";
                    } else {
                        $class = "captcha_show";
                    }
                    ?>
                    <style>
                        .captcha_show{display: none !important;}
                        .captcha-section{display: block}

                    </style> 
                </div>
                
                <div class="inod rigi">
                    <div class="inpdf">
                        <?php echo Form::checkbox('remember_login', '1', (Session::get('email_address') ? TRUE : FALSE), ['class' => 'remem_checkbox', 'id' => 'remember_login']); ?> <label style="cursor: pointer;" for="remember_login">Remember Me</label>
                    </div>
                </div>
                <div class="send_btn">{{ Form::submit('Sign In', array('class' => "btn btn-primary")) }}</div>
                <div class="forgot_pass"><a href="{{HTTP_PATH}}user/forgotpassword">Forgot Password</a></div>
            </div>
            {{ Form::close() }}
        </div>   

    </div>  
</div>

<script>

    function refreshCaptcha()
    {

        var img = document.images['captchaimg'];
        img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;

    }
</script> 

<script>
    function showforgotpassword() {
        $("#forgot").show();
        $("#login").hide();
    }

    function showlogin() {
        $("#forgot").hide();
        $("#login").show();
    }

</script>
@stop


