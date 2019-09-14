@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {
    $("#password-recovery").validate();

});
</script>
<div class="login_bg">
    <div class="pop_up_div">
        <div class="pop_up_div_inner" id="login">
            <div class="login_logo"><a href="#"><img src="{{ asset('public/img/logo.png') }}" alt="logo"></a></div>
            <?php echo Form::open(array('url' => '/user/forgotpassword', 'method' => 'post', 'id' => 'password-recovery', 'class' => '')); ?>
            <div class="titleee">Forgot Password</div>
            {{ View::make('elements.actionMessage')->render() }}
            <div class="field">
                <div class="input_field">
                    <span class="iconn"><i class="fa fa-user" aria-hidden="true"></i></span>
                    <?php echo Form::text('email', '', array('id' => 'login', 'autofocus' => true, 'class' => "required  email", 'placeholder' => 'Enter Email Address')); ?>
                </div> 
                <div class="send_btn"> <?php
                echo Form::submit('Reset Password', array('class' => ''));
                ?></div>
                <div class="forgot_pass"><a href="{{HTTP_PATH}}">I Got My Password Login Now</a></div>
            </div>
        </div>
    </div>
</div>
@stop
