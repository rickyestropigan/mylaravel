<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="shortcut icon" type="image/x-icon" href="public/frontimg/favicon.ico"> 
        <title><?php
            if (isset($title)) {
                echo $title;
            } 
            ?></title>

        <!-- Bootstrap -->
       <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
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
         {{ HTML::style('public/frontcss/aos.css') }}
      <!--  <link href="css/style.scss" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link href="css/font-awesome.css" rel="stylesheet">
        <link href="css/aos.css" rel="stylesheet">-->


    </head>
    <body id="reload">
        
         
        <!--<div class="body_bg" >  {{ HTML::image("public/frontimg/body_bg.png") }}</div>-->
        
        <header>
         

            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-5 col-lg-3">
                        <div class="logo">
                            <a href="{{ url('/listing') }}"><!--<img src="public/frontimg/logo.png" alt="bitebargain">-->
                              {{ HTML::image("public/frontimg/logo.png") }}</a>   
                        </div>   
                    </div>   
                    <div class="ml-auto col-12 col-sm-12 col-md-7 col-lg-6">
                        <ul class="d-inline-block list-unstyled right_align self_user">
                       <?php 

                       if(Session::has('userdata'))
                           {?>  <li class="d-inline-block"><b>Hi, {{ Session::get('userdata')->first_name }}</b></li>
                            <!--<li class="d-inline-block"><a href="{{url('listing')}}">Listing</a></li> -->
                            <li class="d-inline-block"><a href="{{url('logout')}}">Logout</a></li> 
                            <li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal1">Profile</a></li> 

                          <?php }else {?>

                            <li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal">Sign up</a></li>  
                           <?php }?>
                            <li class="d-inline-block"><a href="#">Cart<span class="bag_cart">
                              {{ HTML::image("public/frontimg/bag.png") }}<!--<b class="cart_btn"></b>--></span></a></li>   
                        </ul>
                    </div>
                </div>  
            </div>  
        </header>
        
        @if ($errors->any())
        <div class="alert alert-danger" role="alert">
              {{ implode('', $errors->all('<div>:message</div>')) }}
               </div>
          @endif
       

          @if(Session::has('success_message'))
            <div class="alert alert-success" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            {{Session::get('success_message')}}
            </div>
        @endif
        @if(Session::has('forgotsuccess_message'))
            <div class="alert alert-success" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            {{Session::get('forgotsuccess_message')}}
            </div>
        @endif

         @if(Session::has('error_message'))
            <div class="alert alert-danger" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            {{Session::get('error_message')}}
            </div>
        @endif
      
          @yield('content')
        @include('front.footer')
        <!-- Modal -->
         
<div id="myModal" class="modal fade registration_pop" role="dialog">
   <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="pop_inner">
      <div class="modal-header">
          <h4 class="modal-title">Sign Up
              <span><b>or</b><a data-toggle="modal" id="log" data-target="#myModal_login" href="#">Login</a></span>
          </h4>
        <button type="button" class="close resets" class="stop" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">

        
        <!-- {{ Form::open(['url' => 'signup', 'method' => 'post']) }}-->
        <div id="error_message"></div>
         <div id="success_message"></div>
          <div class="form-group">
              <input type="text" name="cust_name" id="name" placeholder="First Name" class="form-control" required >  
          </div>
          <div class="error" id="err_name" style="color:red;"></div>
		<div class="form-group">
              <input type="text" name="cust_lastname" id="lastname" placeholder="Last Name" class="form-control" required >  
          </div>
          <div class="error" id="err_lname" style="color:red;"></div>
<!--          <div class="form-group">
              <input type="text" name="cust_phone" id="custphone" placeholder="Phone" class="form-control" onkeydown="return numbersOnly(event);"  required>  
          </div>
            <div class="error" id="err_custphone" style="color:red;" ></div>-->
          <div class="form-group">
              <input type="text" name="cust_email" id="custemail" placeholder="Email" class="form-control" required>  
          </div>
           <div class="error" id="err_custemail" style="color:red;" ></div>
          <div class="form-group password">
              <input type="password" name="cust_password" id="pwd" placeholder="Password" class="form-control" required>  
              <span id="showpwd">SHOW</span>
          </div>
          <div class="error" id="err_pwd" style="color:red;" ></div>
<!--          <div class="form-group password">
              <input type="password" name="cust_conpassword" id="confirmpwd" placeholder="Confirm Password" class="form-control" required>  
              <span id="conshowpwd">SHOW</span>
          </div>
           <div class="error" id="err_conpwdwd" style="color:red;" ></div>-->
           <p>By creating an account, I accept the <a href="{{url('terms_conditions')}}">Terms & Conditions</a></p>
      </div>
       
      <div class="modal-footer text-center">
          
        <input type="submit" class="btn btn-default m-auto signup" id="signup" value="Sign Up">
      </div>
      <!-- {{ Form::close() }}-->
        </div>
    </div>

  </div>
</div>

<div id="myModal_forgot" class="modal fade registration_pop" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="pop_inner">
      <div class="modal-header">
          <h4 class="modal-title">Reset Password
          </h4>
        <button type="button" class="close mt-0 pt-0 resets" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">
          {{ Form::open(['url' => 'forgotpassword', 'method' => 'post']) }}
          <div class="form-group">
             <p>Enter your e-mail address below to reset your password.</p>
              <input type="text" name="username" id="foremail" placeholder="Email" class="form-control" required>  
          </div>
           <div class="error" id="err_foremail" style="color:red;" ></div>
          <!--<div class="form-group">
              <div class="form__remember">
                                <input type="hidden" name="Users[rememberme]" value="0"><input type="checkbox" name="Users[rememberme]" value="1" class="css-checkbox in-checkbox" id="checkboxG1">                                <label class="in-label" for="checkboxG1">Remember Me</label>
                            </div>
          </div>-->
      </div>
       
      <div class="modal-footer text-center">
        <input type="submit" class="btn btn-default m-auto forgot" value="Submit">
      </div>
       {{ Form::close() }}
        </div>
    </div>

  </div>
</div>

<div id="myModal_login" class="modal fade registration_pop" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="pop_inner">
      <div class="modal-header">
          <h4 class="modal-title">Login Up
              <span><b>or</b><a href="#" id="signuppopup" data-toggle="modal" data-target="#myModal_login">Sign up</a></span>
          </h4>
        <button type="button" class="close mt-0 pt-0 resets" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">
          {{ Form::open(['url' => '/userlogin', 'method' => 'post']) }}
          <div class="form-group">
             <!-- <input type="text" name="username" value="Session::get('email_address')" id="logname" placeholder="Name or phone" class="form-control" required>-->
              {{ Form::text('username', Session::get('email_address'), array('id'=>'logname', 'class' => 'required form-control','placeholder'=>"Email")) }}
             
          </div>
            <div class="error" id="err_logname" style="color:red;"></div>
          <div class="form-group">
            <input type="password" name="password" value="{{Session::get('planPass')}}" id="logpwd" placeholder="Password" class="form-control" required>
             
          </div>
            <div class="error" id="err_logpwd" style="color:red;" ></div>
          <!--<a href="#" class="text_forgot" data-target="#myModal_forgot">Forgot Password</a>-->
           <a data-toggle="modal" class="text_forgot" id="forgotpopup" href="#myModal_forgot"> Forgot Password</a>
          <div class="form-group">
              <div class="form__remember">
                                <input type="hidden" name="Users[rememberme]" value="0"><input type="checkbox" name="Users[rememberme]" value="1" <?php echo (Session::get('remember')=='1')?"checked":"" ;?> class="css-checkbox in-checkbox" id="checkboxG1">                                <label class="in-label" for="checkboxG1">Remember Me</label>

                            </div>
          </div>
      </div>
       
      <div class="modal-footer text-center">
        <input type="submit" class="btn btn-default m-auto login" value="Login">
      </div>
       {{ Form::close() }}
        </div>
    </div>

  </div>
</div>
<div id="myModal1" class="modal fade registration_pop" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="pop_inner">
      <div class="modal-header">
          <h4 class="modal-title">My Profile
          </h4>
        <button type="button" class="close mt-0 pt-0" data-dismiss="modal">&times;</button>
        
      </div>
      {{ Form::open(['url' => 'profile', 'method' => 'post']) }}
      <div class="modal-body">
       <?php $profile = Session::get('profile');?>
         
	 <div class="form-group">
              <input type="text" placeholder="Name" id="proname" placeholder="Name" name="cust_name" value="{{$profile->cust_name or ''}}" class="form-control" >  
          </div>
          <div class="error" id="err_proname" style="color:red;"></div>
 <div class="form-group">
              <input type="text" placeholder="Email" id="proemail" placeholder="Email" name="cust_email" value="{{$profile->cust_email or ''}}" class="form-control" readonly>  
          </div>
           <div class="error" id="err_proemail" style="color:red;"></div>
          <div class="form-group">
              <input type="text" placeholder="Phone" placeholder="Phone" id="prophone" name="cust_phone" value="{{$profile->cust_phone or ''}}" class="form-control">  
          </div>
           <div class="error" id="err_prophone" style="color:red;"></div>
          <div class="form-group password">
              <input type="password" placeholder="Password" placeholder="Password" id="propwd" name="cust_password" value="{{$profile->plain_pwd or ''}}" class="form-control">  
          </div>
           <div class="error" id="err_propwd" style="color:red;"></div>
        <div class="form-group password">
                        <textarea id="address" class="form-control" readonly>{{$profile->address or ''}}</textarea>
                   </div>
      </div>
       <div class="form-group show-map">
                       <a href="{{URL::to('updateLocation')}}" class="center">Update location</a>
                   </div>
      <div class="modal-footer text-center">
        <input type="submit" class="btn btn-default m-auto profile"   value="Change Profile">
      </div>
       {{ Form::close() }}
        </div>
    </div>

  </div>
</div>


        <!-------FOOTER-SECTION-END------->

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="{{ URL::asset('public/frontjs/jquery.min.js') }}"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ URL::asset('public/frontjs/bootstrap.min.js') }}"></script>
        <!--<script src="public/frontjs/bootstrap.min.js"></script>-->
       
        
       
        <script src="js/aos.js"></script>

        <script>
            AOS.init({
                duration: 1200, once: true
            });
        </script>

        <script  type="text/javascript">
          function numbersOnly(e) { 
           // alert('hh');
                var key = e.which || e.keyCode;
                if (!e.shiftKey && !e.altKey && !e.ctrlKey &&
                        // numbers
                        key >= 48 && key <= 57 ||
                        // Numeric keypad
                        key >= 96 && key <= 105 ||
                        // comma, period and minus, . on keypad
                        key == 190 || key == 188 || key == 109 || key == 110 ||
                        // Backspace and Tab and Enter
                        key == 8 || key == 9 || key == 13 ||
                        // Home and End
                        key == 35 || key == 36 ||
                        // left and right arrows
                        key == 37 || key == 39 || key == 32 || key == 173 ||
                        // Del and Ins
                        key == 46 || key == 45)
                    return true;

                return false;
            }
            $(".contactt").click(function () {

                $('html, body').animate({
                    scrollTop: $(".contact").offset().top - 80

                }, 2000);
            });

            $("#forgotpopup").click(function(){
              //alert('h');
               $("#myModal_login").modal('hide');

             //$('#myModal').popup('hide');
               //location.reload();
            });
        $("#signuppopup").click(function(){
            // alert('h');
               $("#myModal").modal('show');
            });
            /*$(".resets").click(function(){
             //alert('h');
               location.reload();
            });*/
           $("#log").click(function(){ 
           
              $('#myModal').modal('hide');
            });

           
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



                });


            $('.signup').click(function(){ 
              
          
            var name                     = $('#name').val();
            var lastname                     = $('#lastname').val();
           // var phone                     = $('#custphone').val();
            var   intRegex = /[0-9 -()+]+$/;
            var email                     = $('#custemail').val();
            var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
           
            var pwd =  $('#pwd').val();
             //var confirmpwd =  $('#confirmpwd').val();
        // var tag                               = $('#category_tag').val();
       //  var category_name_ar                  = $('#category_name_ar').val();
         
         if($.trim(name)=="")
         { 
            $('#name').val('');
            $('#err_name').fadeIn();         
            $('#err_name').html('Please enter first name.');
            $('#err_name').fadeOut(4000);
            $('#err_name').focus();
            return false;
         }
	if($.trim(lastname)=="")
         { 
            $('#lastname').val('');
            $('#err_lname').fadeIn();         
            $('#err_lname').html('Please enter last name.');
            $('#err_lname').fadeOut(4000);
            $('#err_lname').focus();
            return false;
         }
         
//         if((phone.length < 10) || (!intRegex.test(phone)) || (phone.match(/\s/g)))   
//         {
//            $('#custphone').val('');
//            $('#err_custphone').fadeIn();         
//            $('#err_custphone').html('Please enter 10 digit phone number.');
//            $('#err_custphone').fadeOut(4000);
//            $('#err_custphone').focus();
//            return false;
//
//
//         }
         if (!filter.test(email)) {
            $('#custemail').val('');
            $('#err_custemail').fadeIn();         
            $('#err_custemail').html('Please enter valid email.');
            $('#err_custemail').fadeOut(4000);
            $('#err_custemail').focus();
            return false;

        }
        if (pwd.length < 8) {

         $('#pwd').val('');
            $('#err_pwd').fadeIn();         
            $('#err_pwd').html('Please enter Min 8 digit or alphabeth');
            $('#err_pwd').fadeOut(4000);
            $('#err_pwd').focus();
            return false;

        }
//        if($.trim(confirmpwd)=="")
//         { 
//            $('#confirmpwd').val('');
//            $('#err_conpwdwd').fadeIn();         
//            $('#err_conpwdwd').html('Please confirm password.');
//            $('#err_conpwdwd').fadeOut(4000);
//            $('#err_conpwdwd').focus();
//            return false;
//         }
//         if (pwd !== confirmpwd)
//                {
//                   //$('#password_em').val('');
//                     $('#err_conpwdwd').fadeIn();         
//                    $('#err_conpwdwd').html('Current password is not matching to confirm password please enter confirm password same as Current password');
//                    $('#err_conpwdwd').fadeOut(4000);
//                    $('#err_conpwdwd').focus();
//                    return false;
//                } 
        /*else if (!pwd.match(/[A-Z]/) ) {
          $('#pwd').val('');
             $('#err_pwd').fadeIn();         
            $('#err_pwd').html('Please enter Min 8 digit in which 1 should be number,one should be Capital alphabeth,one should be small alphabeth & one should be symbol');
            $('#err_pwd').fadeOut(4000);
            $('#err_pwd').focus();
            return false;
        } 
         else if ( !pwd.match(/\d/) ) {
          $('#pwd').val('');
             $('#err_pwd').fadeIn();         
            $('#err_pwd').html('Please enter Min 8 digit in which 1 should be number,one should be Capital alphabeth,one should be small alphabeth & one should be symbol');
            $('#err_pwd').fadeOut(4000);
            $('#err_pwd').focus();
            return false;
        } */
          $.ajax({
               type:'POST',
               url:'signup',
               data:{name: name, lastname: lastname,email:  email,pwd : pwd},
               success:function(data) {
             // alert(data);
               if(data == 'erremail'){ 
                     // $('#error_message').html('Sorry! Email id is already exist !.');
                      alert('Sorry ! Email id already exists. Please try another Email address');
                  }
                   if(data == 'errphone'){
                      //$('#error_message').html('Sorry! Phone number is already exist !.');
                       alert('Sorry! Phone number is already exists. Please try another Phone number');
                        
                  }
                   
                  else
                  {

                    //  $('#success_message').html('Your account is created successfully. Now you can login with your details.');
                          
                      //  alert('Your account is created successfully. Now you can login with your details');
                        $('#name').val("");
                       // $('#custphone').val("");
                        $('#custemail').val("");
                        $('#pwd').val("");
                        //$('#confirmpwd').val("");
                      //  $('#myModal_login').modal('show');
                         $('#myModal').modal('hide');
		          window.location.href = "listing";
                  }
               }
            });

       });
         $('.login').click(function(){ 

          var logname                     = $('#logname').val();
          var logpwd =  $('#logpwd').val();
          if($.trim(logname)=="")
           { 
              $('#logname').val('');
              $('#err_logname').fadeIn();         
              $('#err_logname').html('Please enter email or phone number.');
              $('#err_logname').fadeOut(4000);
              $('#err_logname').focus();
              return false;
           }
           if($.trim(logpwd)=="")
           {

              $('#logpwd').val('');
              $('#err_logpwd').fadeIn();         
              $('#err_logpwd').html('Please enter valid password.');
              $('#err_logpwd').fadeOut(4000);
              $('#err_logpwd').focus();
              return false;


           }


         });
         $('.forgot').click(function(){ 

         var foremail                     = $('#foremail').val();
            var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
           if (!filter.test(foremail)) {
            $('#foremail').val('');
            $('#err_foremail').fadeIn();         
            $('#err_foremail').html('Please enter valid email.');
            $('#err_foremail').fadeOut(4000);
            $('#err_foremail').focus();
            return false;

        }


         });

         $('#showpwd').click(function() { 
            
               var x = document.getElementById("pwd");
                if (x.type === "password") {
                  x.type = "text";
                  $('#showpwd').html('<span id="showpwd">Hide</span>')
                } else {
                  x.type = "password";
                   $('#showpwd').html('<span id="showpwd">Show</span>')
                }


         });
           $('#conshowpwd').click(function() { 
            
               var x = document.getElementById("confirmpwd");
                if (x.type === "password") {
                  x.type = "text";
                  $('#conshowpwd').html('<span id="showpwd">Hide</span>')
                } else {
                  x.type = "password";
                   $('#conshowpwd').html('<span id="showpwd">Show</span>')
                }


         });
         $('.contactus').click(function(){ 
                 
            var firstname                     = $('#contfirstname').val();
            var lastname                     = $('#contlastname').val();
            var phone                     = $('#contphone').val();
            var  phone = phone.replace(/[^0-9]/g,'');
            var email                     = $('#contemail').val();
            var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            var msg                     = $('#contmessage').val();
           if($.trim(firstname)=="")
           { 
              $('#contfirstname').val('');
              $('#err_firstname').fadeIn();         
              $('#err_firstname').html('Please enter First Name.');
              $('#err_firstname').fadeOut(4000);
              $('#err_firstname').focus();
              return false;
           }
           if($.trim(lastname)=="")
           { 
              $('#contlastname').val('');
              $('#err_lastname').fadeIn();         
              $('#err_lastname').html('Please enter Last Name.');
              $('#err_lastname').fadeOut(4000);
              $('#err_lastname').focus();
              return false;
           }
           if(phone.length != 10)
           {
              $('#contphone').val('');
              $('#err_phone').fadeIn();         
              $('#err_phone').html('Please enter 10 digit phone number.');
              $('#err_phone').fadeOut(4000);
              $('#err_phone').focus();
              return false;


           }
           if (!filter.test(email)) {
              $('#contemail').val('');
              $('#err_email').fadeIn();         
              $('#err_email').html('Please enter valid email.');
              $('#err_email').fadeOut(4000);
              $('#err_email').focus();
              return false;

            }
            if($.trim(msg)=="")
            { 
              $('#contmessage').val('');
              $('#err_msg').fadeIn();         
              $('#err_msg').html('Please enter Message.');
              $('#err_msg').fadeOut(4000);
              $('#err_msg').focus();
              return false;
             }



          });
         $('.profile').click(function(){ 
	     var name                     = $('#proname').val(); 
              
            var email                     = $('#proemail').val();
            var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            var phone                     = $('#prophone').val();
            var  phone = phone.replace(/[^0-9]/g,'');
            var pwd =  $('#propwd').val();
       
	   if ($.trim(name)=="") {
            $('#err_proname').fadeIn();         
            $('#err_proname').html('Please enter name.');
            $('#err_proname').fadeOut(4000);
            $('#err_proname').focus();
            return false;

           }
          if (!filter.test(email)) {
            $('#err_proemail').fadeIn();         
            $('#err_proemail').html('Please enter valid email.');
            $('#err_proemail').fadeOut(4000);
            $('#err_proemail').focus();
            return false;

        }
        
         if(phone.length != 10)
         {
            $('#prophone').val('');
            $('#err_prophone').fadeIn();         
            $('#err_prophone').html('Please enter 10 digit phone number.');
            $('#err_prophone').fadeOut(4000);
            $('#err_prophone').focus();
            return false;


         }
        
        if (pwd.length < 8) {

         $('#pwd').val('');
            $('#err_propwd').fadeIn();         
            $('#err_propwd').html('Please enter Min 8 digit or alphabeth');
            $('#err_propwd').fadeOut(4000);
            $('#err_propwd').focus();
            return false;

        }
       /* else if ( !pwd.match(/[A-Z]/) ) {
          $('#pwd').val('');
             $('#err_pwd').fadeIn();         
            $('#err_pwd').html('Please enter Min 8 digit in which 1 should be number,one should be Capital alphabeth,one should be small alphabeth & one should be symbol');
            $('#err_pwd').fadeOut(4000);
            $('#err_pwd').focus();
            return false;
        } 
         else if ( !pwd.match(/\d/) ) {
          $('#pwd').val('');
             $('#err_pwd').fadeIn();         
            $('#err_pwd').html('Please enter Min 8 digit in which 1 should be number,one should be Capital alphabeth,one should be small alphabeth & one should be symbol');
            $('#err_pwd').fadeOut(4000);
            $('#err_pwd').focus();
            return false;
        } */


       });
       
       $('#go').click(function(){
           var s_email = $('#s_email').val();
           var url = '<?php echo HTTP_PATH?>';
           $.ajax({
               url : url + 'subscribe',
               type: 'POST',
               data: {s_email:s_email},
               sucess: function(data){
                   console.log(data);
               },
               error: function(e){
                   console.log(e);
               }
               
               
           });
       });
       
       $('#location').keypress(function(){
           //getRes();
       });
       $('#home-tab').click(function(){
           //getRes();
       });
       $('#profile-tab').click(function(){
           //getRes();
       });
       $('#contact-tab').click(function(){
           //getRes();
       });
       function getRes(){
           var location = $('#location').val();
           var section = 'delivery';
           if($('#home-tab').hasClass('active') == true){
               section = 'reservation';
           } else if($('#profile-tab').hasClass('active') == true){
               section = 'delivery';
           } else if($('#contact-tab').hasClass('active') == true){
                section = 'pickup';
            }
            var url = '<?php echo HTTP_PATH ?>'+ 'getrestaurants';
            $.ajax({
                type: 'POST',
                url: url,
                data: {location:location,section:section},
                success: function (data) {
                    if($('#home-tab').hasClass('active') == true){
                        $('#home').html(data);
                    } else if($('#profile-tab').hasClass('active') == true){
                        $('#profile').html(data);
                    } else if($('#contact-tab').hasClass('active') == true){
                        $('#contact').html(data);
                    }
                }
            });
       }


        </script>
        <script type="text/javascript">
            function showPosition(){
                var componentForm = {
                 sublocality_level_1: 'short_name',
                 street_number: 'long_name',
                 route: 'long_name',
                 locality: 'long_name',
                 administrative_area_level_1: 'long_name',
                 country: 'long_name',
                 postal_code: 'long_name'
               };
                if(navigator.geolocation){
                    navigator.geolocation.getCurrentPosition(function(position){
                        var positionInfo = "Your current position is (" + "Latitude: " + position.coords.latitude + ", " + "Longitude: " + position.coords.longitude + ")";
                        //document.getElementById("location").value = positionInfo;
                         $('#location').after("<input type='hidden' id='lat' value='"+ position.coords.latitude+"' />");
                        $('#location').after("<input type='hidden' id='long' value='"+ position.coords.longitude+"' />");
                        var api_key = '<?php echo API_KEY?>';
                        $.ajax({
                            url: 'https://maps.googleapis.com/maps/api/geocode/json?key='+api_key+'&latlng='+position.coords.latitude+','+position.coords.longitude+'&sensor=true',
                            dataType: 'json',
                            success: function(place){
                                var json_l = place.results.length;
                                var val = 'pune';
                                console.log(place.results);
                                $('#location').val(place.results[3].formatted_address);
                                for (var i = 0; i < place.results[0].address_components.length; i++) {
                                    var addressType = place.results[0].address_components[i].types[0];
                                    if (componentForm[addressType]) {
                                      var val = place.results[0].address_components[i][componentForm[addressType]];
                                      if(addressType == 'locality'){
                                        console.log(place.results[0].address_components);
                                        
                                        var location = val;
                                        $('#location_city').val(val);
                                        var section = 'delivery';
                                        if($('#home-tab').hasClass('active') == true){
                                            section = 'reservation';
                                        } else if($('#profile-tab').hasClass('active') == true){
                                            section = 'delivery';
                                        } else if($('#contact-tab').hasClass('active') == true){
                                             section = 'pickup';
                                         }
                                         var url = '<?php echo HTTP_PATH;?>'+ 'getrestaurants';
                                         /*$.ajax({
                                             type: 'POST',
                                             url: url,
                                             data: {location:location,section:section},
                                             success: function (data) {
                                                 if($('#home-tab').hasClass('active') == true){
                                                     $('#home').html(data);
                                                 } else if($('#profile-tab').hasClass('active') == true){
                                                     $('#profile').html(data);
                                                 } else if($('#contact-tab').hasClass('active') == true){
                                                     $('#contact').html(data);
                                                 }
                                             }
                                         });*/
                                    }   
                                       // document.getElementById(addressType).val = val;
                                    }
                                  }
                            }
                        });
                    });
                } else{
                    alert("Sorry, your browser does not support HTML5 geolocation.");
                }
            }
            function search(){
                //let location_city = $('#location_city').val();
                //if(!location_city){
                  let  location_city = $('#location').val();
                //}
                var lat = $('#lat').val();
                var long = $('#long').val();
                let url = '<?php echo HTTP_PATH?>'+ 'listing/searchLocation';
                $.ajax({
                     type: 'POST',
                     url: url,
                     data: {location_city:location_city,lat:lat,long:long},
                     success: function (data) {
                         let result = JSON.parse(data);
                         if(result.status == 0){
                             alert("You need to login first");
                         } else {
                             window.location.href = "<?php echo HTTP_PATH?>listing";
                         }
                     }
                 });
            } 
        </script>
    </body>
</html>

