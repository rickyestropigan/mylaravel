<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="shortcut icon" type="image/x-icon" href="{{{ asset('public/frontimg/favicon.ico') }}}"> 
        <title>Welcome :: Bitebargain </title>

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
                    <div class="col-12 col-sm-6 col-md-5 col-lg-3">
                        <div class="logo">
                            <a href="{{ url('/listing') }}">{{ HTML::image("public/listingimg/logo.png") }}</a>   
                        </div>   
                    </div> 
                    <div class="center_text text-center col-12 col-sm-6 col-md-7 col-lg-5">
                        <div class="form-group center_field ml-auto">
                            <i class="fa fa-map-marker"></i>
                            <input type="text" placeholder="Enter Your City"> 

                        </div></div>    
                    <div class="ml-auto col-12 col-sm-12 col-md-12 col-lg-4">
                        <ul class="d-inline-block list-unstyled right_align">
                            <?php
                            if (Session::has('userdata')) {
                                ?>   <li class="d-inline-block"><a>Hi, {{ Session::get('profile')->cust_name }}</a></li>
                                <li class="d-inline-block"><a href="{{url('logout')}}">Logout</a></li> 
<li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal1">Profile</a></li> 

                            <?php } else { ?>
                                <li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal_login">Login</a></li> 
                            <?php } ?>
                            
                            <li class="nav-item d-inline-block"><a href="#">Cart<span class="bag_cart">{{ HTML::image("public/frontimg/bag.png") }}<!--<b class="cart_btn"></b>--></span></a></li>   
                        </ul>
                    </div>
                </div>  
            </div>  
        </header>
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
              {{ Form::text('username', Session::get('email_address'), array('id'=>'logname', 'class' => 'required form-control','placeholder'=>"Email or Phone")) }}
             
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
       
        @yield('content')
        @include('front.footer')
        <!-------FOOTER-SECTION-END------->

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="{{ URL::asset('public/frontjs/jquery.min.js') }}"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="{{ URL::asset('public/frontjs/bootstrap.min.js') }}"></script>

        <script type="text/javascript" src="{{ URL::asset('public/frontjs/owl.carousel.js') }}"></script>  
       	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{API_KEY}}&libraries=places" type="text/javascript" async defer></script>
        <script>
              $(function () {
                var date = new Date();
                var hours = date.getHours() > 12 ? date.getHours() - 12 : date.getHours();
                var am_pm = date.getHours() >= 12 ? "PM" : "AM";
                hours = hours < 10 ? "0" + hours : hours;
                var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
                var seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
                var time = hours + ":" + minutes + " " + am_pm;
                $(".datepicker").datepicker({
                    dateFormat: 'MM dd, yy',
                    setdate: new Date()
                });
                var months=["January","February","March","April","May","June","July","August","September","October","November","December"];
                $(".datepicker").val((months[date.getMonth()])+' '+date.getDate()+', '+date.getFullYear());
                $(".custom-timepicker").val(time);
               
                  
              });
              $(document).ready(function () {
                  $('input.timepicker').timepicker();
              });
        </script>

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


            $('.categoryId').click(function () {
                var id = $(this).attr('categoryId');
                var catname = $(this).attr('catname');
                $.ajax({
                    type: 'POST',
                    url: 'getmenu',
                    data: {id: id},
                    success: function (data) {

                        $('#finalmenu').html(data);
                        $('#menucatname').text(catname);

                    }
                });

            });

            $('#fav').click(function () {

                var resid = $('#resid').val();
                var userid = $('#userid').val();
                var resname = $('#resname').val();

                $.ajax({
                    type: 'POST',
                    url: 'getfavourite',
                    data: {resid: resid, userid, userid, resname, resname},
                    success: function (data) {
                        location.reload();
                        $('#favitem').html(data)
                    }
                });

            });
            $('#restreviews').click(function () {
                var resid = $('#resid').val();
                $.ajax({
                    type: 'POST',
                    url: 'getreviews',
                    data: {resid: resid},
                    success: function (data) {
                        $('#finalreview').html(data)
                    }
                });


            });

            function slot_data(id,name){
                var id = id;
                if($('#discount_'+name+'_'+id).prop('checked') == true){
                    $('#discount_'+name+'_'+id).removeAttr('checked');
                } else {
                    $('#discount_'+name+'_'+id).attr('checked','checked');
                }
            }
            
            
        </script>
        <script>
        $(function (){
            setTimeout(function(){
                //initMap();
            },1000);
        });
        // Initialize and add the map
        function initMap() {
          // The location of Uluru
          var uluru = {lat: parseFloat($('#lat').val()), lng: parseFloat($('#lng').val())};
          var title = "http://maps.google.com/?q="+parseFloat($('#lat').val())+','+parseFloat($('#lng').val());
          var r_name = $('#resname').val();
          // The map, centered at Uluru
          var map = new google.maps.Map(
              document.getElementById('map'), {zoom: 15, center: uluru});
          // The marker, positioned at Uluru
          var marker = new google.maps.Marker({position: uluru, map: map});
          var contentString = '<div id="content">'+
            '<div id="siteNotice">'+
            '</div>'+
            '<a href="'+title+'" target="_blank"><h3 id="firstHeading" class="firstHeading">'+r_name+'</h3></a>'+
            '<div id="bodyContent">'+
            '<p></p>'+
            '</div>'+
            '</div>';

        var infowindow = new google.maps.InfoWindow({
          content: contentString
        });

        var marker = new google.maps.Marker({
          position: uluru,
          map: map,
          title: r_name
        });
        marker.addListener('click', function() {
          infowindow.open(map, marker);
        });
        }
    </script>
        


    </body>
</html>
