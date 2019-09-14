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
    <body>
        
         
        <div class="body_bg" ><img src="public/frontimg/body_bg.png"></div>
        
        <header>
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="logo">
                            <a href="#"><img src="public/frontimg/logo.png" alt="bitebargain"></a>   
                        </div>   
                    </div>   
                    <div class="ml-auto col-12 col-sm-6 col-md-4">
                        <ul class="d-inline-block list-unstyled right_align">
                            <li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>   
                            <li class="d-inline-block"><a href="#">Cart<span class="bag_cart"><img src="public/frontimg/bag.png"><b class="cart_btn">1</b></span></a></li>   
                        </ul>
                    </div>
                </div>  
            </div>  
        </header>
        
          @yield('content')
        @include('front.footer')
        <!-- Modal -->
<div id="myModal" class="modal fade registration_pop" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="pop_inner">
      <div class="modal-header">
          <h4 class="modal-title">Login Up
              <span><b>or</b><a href="#" data-toggle="modal" data-target="#myModal_login">Sign up</a></span>
          </h4>
        <button type="button" class="close mt-0 pt-0" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">
          <div class="form-group">
              <input type="text" placeholder="Name or phone" class="form-control">  
          </div>
          <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">  
          </div>
          <a href="#" class="text_forgot">Forgot Password</a>
          <div class="form-group">
              <div class="form__remember">
                                <input type="hidden" name="Users[rememberme]" value="0"><input type="checkbox" name="Users[rememberme]" value="1" class="css-checkbox in-checkbox" id="checkboxG1">                                <label class="in-label" for="checkboxG1">Remember Me</label>
                            </div>
          </div>
      </div>
       
      <div class="modal-footer text-center">
        <input type="submit" class="btn btn-default m-auto" data-dismiss="modal" value="Login">
      </div>
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
          <h4 class="modal-title">Sign Up
              <span><b>or</b><a href="#">Login</a></span>
          </h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body">
          <div class="form-group">
              <input type="text" placeholder="Name" class="form-control">  
          </div>
          <div class="form-group">
              <input type="text" placeholder="Phone" class="form-control">  
          </div>
          <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">  
          </div>
          <div class="form-group password">
              <input type="text" placeholder="Password" class="form-control">  
              <span>SHOW</span>
          </div>
           <p>By creating an account, I accept the <a href="#">Terms & Conditions</a></p>
      </div>
       
      <div class="modal-footer text-center">
          
        <input type="submit" class="btn btn-default m-auto" data-dismiss="modal" value="Sign Up">
      </div>
        </div>
    </div>

  </div>
</div>

        <!-------FOOTER-SECTION-END------->

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="public/frontjs/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="public/frontjs/bootstrap.min.js"></script>
       
        
       
        <script src="js/aos.js"></script>
        <script>
            AOS.init({
                duration: 1200, once: true
            });
        </script>




        <script>
            $(".contactt").click(function () {

                $('html, body').animate({
                    scrollTop: $(".contact").offset().top - 80

                }, 2000);
            });

        </script>


    </body>
</html>