<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="shortcut icon" type="image/x-icon" href="public/frontimg/favicon.ico"> 
        <title>Bitebargain :: Listing </title>

        <!-- Bootstrap -->
        <!--<link href="css/bootstrap.min.css" rel="stylesheet"> -->
        {{ HTML::style('public/frontcss/bootstrap.min.css') }}
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        {{ HTML::style('public/frontcss/style.scss') }}
        {{ HTML::style('public/frontcss/style.css') }}
        <!-- <link href="css/style.scss" rel="stylesheet">
         <link href="css/style.css" rel="stylesheet">-->
        <!--  <link href="css/font-awesome.css" rel="stylesheet">
          <link href="css/aos.css" rel="stylesheet">-->
        {{ HTML::style('public/frontcss/font-awesome.css') }}
        {{ HTML::style('public/frontcss/aos.css') }}
        {{ HTML::style('public/frontcss/vdrop.min.css') }}
        <!--<link href="css/vdrop.min.css" rel="stylesheet" type="text/css"/>-->
        {{ HTML::style('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css') }}
        <!--<link rel='stylesheet' href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>-->
        <script src="https://maps.googleapis.com/maps/api/js?key={{API_KEY}}&libraries=places&callback=initMap" type="text/javascript" async defer></script>



    </head>
    <body>




        <header class="header_inner">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-5 col-lg-3">
                        <div class="logo">
                            <a href="{{ url('/listing') }}">{{ HTML::image("public/listingimg/logo.png") }}</a>   
                        </div>   
                    </div> 
                    <div class="center_text text-center col-12 col-sm-6 col-md-7 col-lg-5">
                        <div class="form-group center_field ml-auto mr-auto">
                            <i class="fa fa-map-marker"></i>
                            <input type="text" id="locate" placeholder="Enter Your City"> 

                        </div></div>    
                    <div class="ml-auto col-12 col-sm-12 col-md-12 col-lg-4">
                        <ul class="d-inline-block list-unstyled right_align">
                            <?php
                            if (Session::has('userdata')) {
                                ?>   <li class="d-inline-block"><a>Hi, {{ Session::get('profile')->cust_name }}</a></li>
                                <li class="d-inline-block"><a href="{{url('logout')}}">Logout</a></li> 
                                <li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal1">Profile</a></li> 
                            <?php } else { ?>
                                <li class="d-inline-block"><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li> 
                            <?php } ?>
                            <li class="d-inline-block"><a href="#">Cart<span class="bag_cart">   {{ HTML::image("public/frontimg/bag.png") }}<!--<b class="cart_btn"></b>--></span></a></li>   

                        </ul>
                    </div>
                </div>  
                <div class="row">
                    <div class="nav-center inner_tabs_section">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link delivery  active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Delivery</a>
                            </li>
<li class="nav-item">
                                <a class="nav-link restaurant" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Pickup</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link reservation" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Reservations</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
            </div>  
        </header>


        @if(Session::has('success_message'))
        <div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            {{Session::get('success_message')}}
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
        <script src="public/frontjs/jquery.vdrop.min.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
        <script>
       $(function () {
           $("#datepicker").datepicker({
               dateFormat: 'MM dd, yy',
               onSelect: function (dateText) {
                   //alert();
                   tmTotalHrsOnSite()
               }
           });
            var date = new Date();
            var hours = date.getHours() > 12 ? date.getHours() - 12 : date.getHours();
            var am_pm = date.getHours() >= 12 ? "PM" : "AM";
            hours = hours < 10 ? "0" + hours : hours;
            var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
            var seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
            var time = hours + ":" + minutes + " " + am_pm;

            var months=["Jan","Feb","Mar","Apr","May","June","July","Aug","Sep","Oct","Nov","Dec"];
            $(".datepicker").val((months[date.getMonth()])+' '+date.getDate()+', '+date.getFullYear());
            $(".custom-timepicker").attr('placeholder',time);
            $('input.timepicker').timepicker({

               change: tmTotalHrsOnSite,
                timeFormat: 'hh:mm a',
                interval: 30,
                dynamic: true,
                dropdown: true,
                scrollbar: true

           });
       });
       
      
       function tmTotalHrsOnSite() {
           var time = $(".timepicker").val();
           var date = $("#datepicker").val();
           //alert(date);
           $.ajax({
               type: 'POST',
               url: 'gettimedata',
               data: {time: time,date:date},
               success: function (data) {

                   $('#finalres').html(data)
               }
           });



       }
       ;
       function dateTotalHrsOnSite() {
           var date = $("#datepicker").val();
           //alert(date);
           $.ajax({
               type: 'POST',
               url: 'getdatedata',
               data: {date: date},
               success: function (data) {
                   //alert(data)
                   $('#finalres').html(data)
               }
           });

       }
        </script>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    $('[name="select-event"]').on('change update', function () {
                        $(this).parent().siblings().children().text($(this).find('option:selected').val());
                    });

                    $('#delivery-select').vDrop({allowMultiple: false});
                    $('#reservation-time').vDrop({allowMultiple: false});
                    $('#reservation-calendar').vDrop({allowMultiple: false});
                    $('#reservation-seat').vDrop({allowMultiple: false});
                    $('#pickup-time').vDrop({allowMultiple: false});

                    setTimeout(function () {
                        $('[name="delayed"]').append('<option>Now</option><option>We\'re</option><option selected="selected">loaded & i\'m selected</option>').vDrop('update');
                    }, 1000);

                    $('[name="heading-close"]').closest('.example').find('h2').on('mouseover', function () {
                        $('[name="heading-close"]').data('plugin_vDrop').close($('[name="heading-close"]'));
                    });
                });
            })(jQuery);
        </script>




        <script>
            $(document).ready(function () {
                $(".filter_pop").click(function () {
                    $(".filtter_option").slideToggle();

                });
            });

        </script>
        <script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>
        <script>
            $(document).ready(function () {
                $(".price-range").slider({range: true, min: 0, max: 100, values: [0, 100], slide: function (event, ui) {
                        $(".priceRange").val("" + ui.values[0] + " - " + ui.values[1]);
                    }
                });
                $(".priceRange").val("" + $(".price-range").slider("values", 0) + " - " + $(".price-range").slider("values", 1));

                $(".discount-range").slider({range: true, min: 0, max: 100, values: [0, 100], slide: function (event, ui) {
                        $(".discount").val(ui.values[0] + " - " + ui.values[1]);
                    }
                });
                $(".discount").val($(".discount-range").slider("values", 0) + " - " + $(".discount-range").slider("values", 1));

                $("#mileage-range").slider({range: true, min: 0, max: 200000, values: [0, 200000], slide: function (event, ui) {
                        $("#mileageRange").val(ui.values[0] + " - " + ui.values[1]);
                    }
                });
                $("#mileageRange").val($("#mileage-range").slider("values", 0) + " - " + $("#mileage-range").slider("values", 1));
            });

        </script>


        <script>
            $("#search").keyup(function () {
                var search = $('#search').val();
                var map_status = ($("#map_show1").prop("checked") == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                $.ajax({
                    type: 'POST',
                    url: 'getdata',
                    data: {serach: search, map_status: map_status,page_name:page_name},
                    success: function (data) {
                        $('#final').html(data)
                    }
                });
            });
            $("#locate").keyup(function () {
                var locate = $('#locate').val();
                var res_status = ($("#profile").hasClass("active") == true) ? 1 : 0;
                var map_status = 0;
                if ($("#home-tab").hasClass("active") == true) {
                    map_status = ($("#map_show1").prop("checked") == true) ? 1 : 0;
                }
                if ($("#profile").hasClass("active") == true) {
                    map_status = ($("#map_show2").prop("checked") == true) ? 1 : 0;
                }
                if ($("#contact-tab").hasClass("active") == true) {
                    map_status = ($("#map_show3").prop("checked") == true) ? 1 : 0;
                }
                $.ajax({
                    type: 'POST',
                    url: 'getlocation',
                    data: {locate: locate, map_status: map_status, res_status: res_status},
                    success: function (data) {
                        if (res_status == 1) {
                            $('#finalres').html(data);
                            console.log(res_status);
                        } else {
                            if ($("#home-tab").hasClass("active") == true) {
                                $('#final').html(data);
                                console.log(res_status);
                            } else {
                                $('#finalpick').html(data);
                                console.log(res_status);
                            }
                        }
                    }
                });

            });
            $("#reservation").keyup(function () {
                var locate = $('#locate').val();
                var map_status = ($("#map_show2").prop("checked") == true) ? 1 : 0;
                var search = $('#reservation').val();
                var page_name =  '<?php echo Request::segment(1); ?>';
                $.ajax({
                    type: 'POST',
                    url: 'getdatares',
                    data:{serach: search,map_status:map_status,page_name:page_name},
                    success: function (data) {
                        $('#finalres').html(data)
                    }
                });
            });
            $("#pickup").keyup(function () {
                var search = $('#pickup').val();
                var map_status = ($("#map_show3").prop("checked") == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                $.ajax({
                    type: 'POST',
                    url: 'getdatapickup',
                    data: {serach:search,map_status:map_status,page_name:page_name},
                    success: function (data) {

                        $('#finalpick').html(data)
                    }
                });



            });


            function filter() {
                var price = $('#price').val();
                var distance = $('#distance').val();
                var discount = $('#discount').val();
                var map_status = ($('#map_show1').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                $.ajax({
                    type: 'POST',
                    url: 'getfilterdata',
                    data: {price: price, discount: discount, distance: distance,map_status:map_status,page_name:page_name},
                    success: function (data) {

                        $('#final').html(data)
                    }
                });



            }
            function resfilter() {

                var price = $('#resfilterprice').val();
                var discount = $('#resfilterdiscount').val();
                var distance = $('#resfilterdistance').val();
                var map_status = ($('#map_show2').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                $.ajax({
                    type: 'POST',
                    url: 'getresfilterdata',
                    data: {price: price, discount: discount, distance: distance,map_status:map_status,page_name:page_name},
                    success: function (data) {

                        $('#finalres').html(data)
                    }
                });



            }
            function pickfilter() {

                var price = $('#pickfilterprice').val();
                var discount = $('#pickfilterdiscount').val();
                var distance = $('#pickfilterdistance').val();
                var map_status = ($('#map_show3').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                $.ajax({
                    type: 'POST',
                    url: 'getpickfilterdata',
                    data: {price: price, discount: discount, distance: distance,map_status:map_status,page_name:page_name},
                    success: function (data) {
                        $('#finalpick').html(data)
                    }
                });



            }


            $('#best').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#best').addClass(' active');
                event.preventDefault();
                $.ajax({
                    type: 'get',
                    url: 'getbest',
                    success: function (data) {
                        $('#final').html(data)
                    }
                });

            });
            
            $('#sortprice').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#sortprice').addClass(' active');
                var map_status = ($('#map_show1').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getprice',
                    success: function (data) {
                        $('#final').html(data)
                    }
                });

            });
            $('#sortdistance').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#sortdistance').addClass(' active');
                var map_status = ($('#map_show1').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getdistance',
                    success: function (data) {
                        $('#final').html(data)
                    }
                });



            });
            $('#sortdiscount').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#sortdiscount').addClass(' active');
                var map_status = ($('#map_show1').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getdiscount',
                    success: function (data) {
                        $('#final').html(data)
                    }
                });

            });

            $('#resprice').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#resprice').addClass(' active');
                var map_status = ($('#map_show2').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getresprice',
                    success: function (data) {
                        $('#finalres').html(data)
                    }
                });

            });
            $('#resbest').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#resbest').addClass(' active');
                var map_status = ($('#map_show2').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getresbest',
                    success: function (data) {
                        $('#finalres').html(data)
                    }
                });

            });
            $('#resdistance').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#resdistance').addClass(' active');
                var map_status = ($('#map_show2').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getresdistance',
                    success: function (data) {
                        $('#finalres').html(data)
                    }
                });

            });
            $('#resdiscount').click(function (event) {
                var map_status = ($('#map_show2').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getresdiscount',
                    success: function (data) {
                        $('#finalres').html(data)
                    }
                });

            });
            $('#pickprice').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#pcikprice').addClass(' active');
                var map_status = ($('#map_show3').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getpickprice',
                    success: function (data) {
                        $('#finalpick').html(data)
                    }
                });

            });
            $('#pickbest').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#pickbest').addClass(' active');
                var map_status = ($('#map_show3').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getpickbest',
                    success: function (data) {
                        $('#finalpick').html(data)
                    }
                });

            });

            $('#pickdistance').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#pickdistance').addClass(' active');
                var map_status = ($('#map_show3').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getpickdistance',
                    success: function (data) {
                        $('#finalpick').html(data)
                    }
                });

            });
            $('#pickdiscount').click(function (event) {
                $('li.d-inline-block > a').removeClass('active');
                $('#pickDiscount').addClass(' active');
                var map_status = ($('#map_show3').prop('checked') == true) ? 1 : 0;
                var page_name =  '<?php echo Request::segment(1); ?>';
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    data:{map_status : map_status,page_name:page_name},
                    url: 'getpickdiscount',
                    success: function (data) {
                        $('#finalpick').html(data)
                    }
                });

            });

        //get google with pins
            $('#map_show1').click(function () {
                if (document.getElementById('map_show2').checked == true) {
                    $('#map_show2').trigger('click');
                }
                if (document.getElementById('map_show3').checked == true) {
                    $('#map_show3').trigger('click');
                }

                if ($('#latitude').val() == '') {
                    window.location.href = "<?php echo HTTP_PATH ?>/updateLocation";
                }
                var locate = $('#locate').val();
                var search = locate;
                if (locate == '') {
                    search = $('#search').val();
                }
                if (document.getElementById('map_show1').checked == true) {
                    setTimeout(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'map_show',
                            data: {search: search},
                            success: function (data) {

                                $('#final').html(data);
                                $('#reservation').trigger('click');
                                $('#pickerup').trigger('keyup');
                            }
                        });
                    }, 500);
                } else if (document.getElementById('map_show1').checked == false) {
                    $(this).removeAttr('checked');
                    $('#search').trigger('keyup');
                } else {
                    //$(this).removeAttr('checked');
                    $('#search').trigger('keyup');
                }

            });
            $('#map_show2').click(function () {
                if (document.getElementById('map_show1').checked == true) {
                    $('#map_show1').trigger('click');
                }
                if (document.getElementById('map_show3').checked == true) {
                    $('#map_show3').trigger('click');
                }


                if ($('#latitude').val() == '') {
                    window.location.href = "<?php echo HTTP_PATH ?>/updateLocation";
                }
                var locate = $('#locate').val();
                var search = locate;
                if (locate == '') {
                    search = $('#reservation').val();
                }
                if (document.getElementById('map_show2').checked == true) {
                    setTimeout(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'map_show',
                            data: {search: search},
                            success: function (data) {
                                $('#finalres').html(data);
                            }
                        });
                    }, 500);
                } else if (document.getElementById('map_show1').checked == false) {
                    $(this).removeAttr('checked');
                    $('#reservation').trigger('keyup');
                } else {
                    //$(this).removeAttr('checked');
                    $('#reservation').trigger('keyup');
                }

            });
            $('#map_show3').click(function () {
                if (document.getElementById('map_show1').checked == true) {
                    $('#map_show1').trigger('click');
                }
                if (document.getElementById('map_show2').checked == true) {
                    $('#map_show2').trigger('click');
                }


                if ($('#latitude').val() == '') {
                    window.location.href = "<?php echo HTTP_PATH ?>/updateLocation";
                }
                var locate = $('#locate').val();
                var search = locate;
                if (locate == '') {
                    search = $('#pickup').val();
                }
                if (document.getElementById('map_show3').checked == true) {
                    setTimeout(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'map_show',
                            data: {search: search},
                            success: function (data) {
                                $('#finalpick').html(data);
                            }
                        });
                    }, 500);
                } else if (document.getElementById('map_show3').checked == false) {
                    $(this).removeAttr('checked');
                    $('#pickup').trigger('keyup');
                } else {
                    //$(this).removeAttr('checked');
                    $('#pickup').trigger('keyup');
                }

            });
            $('#s_location').click(function (event) {
                var address = document.getElementById('pac-input').value;
                var url = "<?php echo HTTP_PATH;?>/listing";
                if (address) {
                    event.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'saveLocation',
                        data: {address: address},
                        success: function (data) {
                            console.log('test');
                            if (data.status == 0) {
                                alert('Address save successfully!');
                                //location.reload();
                                window.location.href = url;
                            } else {
                                alert('Opps someething went wroung!');
                            }
                        }
                    });
                } else {
                    alert('Please enter location.');
                    return false;
                }

            });
        </script>

<!--        <script>
            $(".contactt").click(function () {

                $('html, body').animate({
                    scrollTop: $(".contact").offset().top - 80

                }, 2000);
            });

        </script>-->

        <script>
            $('.profile').click(function () {
                var name = $('#custname').val();
                var email = $('#email').val();
                var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                var phone = $('#phone').val();
                var phone = phone.replace(/[^0-9]/g, '');
                var pwd = $('#pwd').val();
                if ($.trim(name) == "") {
                    $('#err_custname').fadeIn();
                    $('#err_custname').html('Please enter name.');
                    $('#err_custname').fadeOut(4000);
                    $('#err_custname').focus();
                    return false;

                }

                if (!filter.test(email)) {
                    $('#err_email').fadeIn();
                    $('#err_email').html('Please enter valid email.');
                    $('#err_email').fadeOut(4000);
                    $('#err_email').focus();
                    return false;

                }

                if (phone.length != 10)
                {
                    $('#phone').val('');
                    $('#err_phone').fadeIn();
                    $('#err_phone').html('Please enter 10 digit phone number.');
                    $('#err_phone').fadeOut(4000);
                    $('#err_phone').focus();
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
                /*else if ( !pwd.match(/[A-Z]/) ) {
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
        </script>
        <script>

            $(function () {
                initMap();
            });
            // This example requires the Places library. Include the libraries=places
            // parameter when you first load the API. For example:

            function initMap() {

                var marker = '';
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {lat: -33.8688, lng: 151.2195},
                    zoom: 13
                });





                var card = document.getElementById('pac-card');
                var input = document.getElementById('pac-input');
                var types = document.getElementById('type-selector');
                var strictBounds = document.getElementById('strict-bounds-selector');
                var user_address = "";
                map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

                var autocomplete = new google.maps.places.Autocomplete(input);

                // Bind the map's bounds (viewport) property to the autocomplete object,
                // so that the autocomplete requests use the current map bounds for the
                // bounds option in the request.
                autocomplete.bindTo('bounds', map);

                // Set the data fields to return when the user selects a place.
                autocomplete.setFields(
                        ['address_components', 'geometry', 'icon', 'name']);

                var geocoder = new google.maps.Geocoder();
                var infowindow = new google.maps.InfoWindow();
                var infowindowContent = document.getElementById('infowindow-content');
                infowindow.setContent(infowindowContent);
                var marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });

                setTimeout(function () {
                    var user_address = $('#address').val();
                    // console.log(user_address);
                    //get currnet location and set marker
                    if (user_address == '') {
                        navigator.geolocation.getCurrentPosition(function (position, marker) {
                            var pos = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                            //console.log("Address => "+position.coords.latitude+"/"+position.coords.longitude);
                            infowindow.setPosition(pos);
                            //infowindow.setContent('Location found.');
                            //infowindow.open(map,marker);
                            map.setCenter(pos);
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                                map: map
                            });
                            geocodeLatLng(geocoder, map, infowindow);
                        });
                    } else {
                        var latlng = {lat: parseFloat($('#lat').val()), lng: parseFloat($('#lng').val())};
                        //console.log(latlng);
                        geocoder.geocode({'location': latlng}, function (results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    map.setZoom(11);
                                    var marker = new google.maps.Marker({
                                        position: latlng,
                                        center: latlng,
                                        map: map
                                    });
                                    infowindow.setContent(user_address);
                                    document.getElementById('pac-input').value = user_address;
                                    infowindow.open(map, marker);
                                } else {
                                    window.alert('No results found');
                                }
                            } else {
                                window.alert('Geocoder failed due to: ' + status);
                            }
                        });
                    }
                }, 1000);


                autocomplete.addListener('place_changed', function (event) {
                    infowindow.close();
                    //marker.setMap(null);
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    infowindow.setContent(place.name);
                    if (!place.geometry) {
                        // User entered the name of a Place that was not suggested and
                        // pressed the Enter key, or the Place Details request failed.
                        window.alert("No details available for input: '" + place.name + "'");
                        return;
                    }

                    // If the place has a geometry, then present it on a map.
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);  // Why 17? Because it looks good.
                    }
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);
                    console.log(marker.position.lat());
                    console.log(marker.position.lng());
                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }

                    infowindowContent.children['place-icon'].src = place.icon;
                    infowindowContent.children['place-name'].textContent = place.name;
                    infowindowContent.children['place-address'].textContent = address;
                    infowindow.open(map, marker);
                });

                // Sets a listener on a radio button to change the filter type on Places
                // Autocomplete.
                function setupClickListener(id, types) {
                    var radioButton = document.getElementById(id);
                    radioButton.addEventListener('click', function () {
                        autocomplete.setTypes(types);
                    });
                }

                setupClickListener('changetype-all', []);
                setupClickListener('changetype-address', ['address']);
                setupClickListener('changetype-establishment', ['establishment']);
                setupClickListener('changetype-geocode', ['geocode']);

                document.getElementById('use-strict-bounds')
                        .addEventListener('click', function () {
                            console.log('Checkbox clicked! New state=' + this.checked);
                            autocomplete.setOptions({strictBounds: this.checked});
                        });
            }

            function setLocation() {
                newLocation = new google.maps.LatLng(0, 0);
                marker.setPosition(newLocation);
            }
            function geocodeLatLng(geocoder, map, infowindow) {
        //        var input = document.getElementById('latlng').value;
        //        var latlngStr = input.split(',', 2);
                var latlng = {lat: infowindow.position.lat(), lng: infowindow.position.lng()};
                geocoder.geocode({'location': latlng}, function (results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            map.setZoom(11);
                            var marker = new google.maps.Marker({
                                position: latlng,
                                map: map
                            });
                            infowindow.setContent(results[0].formatted_address);
                            document.getElementById('pac-input').value = results[0].formatted_address;
                            infowindow.open(map, marker);
                        } else {
                            window.alert('No results found');
                        }
                    } else {
                        window.alert('Geocoder failed due to: ' + status);
                    }
                });
            }




        </script>

<script>
    function showSlot(obj) {
        var id = obj.id;
        id = id.split("_");
        var page_name =  '<?php echo Request::segment(1); ?>';
        $.ajax({
            type: 'POST',
            url: 'getmoreslot',
            data: {id: id,page_name:page_name},
            success: function (data) {
                if($('#home').hasClass('active') == true){
                    console.log('1'+id);
                    $('#timeslot_' + id[1]).html(data);
                    $('#home #defaul_height_' + id[1]).addClass("height_scroll");
                    $('#home .more#more_' + id[1] + '_' + id[2]).css('display', 'none');
                    $('#home .less#more_' + id[1] + '_' + id[2]).css('display', 'block');
                } 
                if($('#profile').hasClass('active') == true){
                    console.log('2'+id);
                    $('#timeresslot_' + id[1]).html(data);
                    $('#profile #defaul_height_' + id[1]).addClass("height_scroll");
                    $('#profile .more#more_' + id[1] + '_' + id[2]).css('display', 'none');
                    $('#profile .less#more_' + id[1] + '_' + id[2]).css('display', 'block');
                }
                if($('#contact').hasClass('active') == true){
                    console.log('3'+id);
                    $('#timepickslot_' + id[1]).html(data);
                    $('#contact #defaul_height_' + id[1]).addClass("height_scroll");
                    $('#contact .more#more_' + id[1] + '_' + id[2]).css('display', 'none');
                    $('#contact .less#more_' + id[1] + '_' + id[2]).css('display', 'block');
                }    
            }
        });
    }

    function hideSlot(obj) {
        var id = obj.id;
        id = id.split("_");
        $('.more#more_' + id[1] + '_' + id[2]).css('display', 'block');
        $('.less#more_' + id[1] + '_' + id[2]).css('display', 'none');
        
        if($('#home').hasClass('active') == true){
            $('#home #timeslot_' + id[1] + ' > ul:not(:first)').remove();
            $('#home #defaul_height_' + id[1]).removeClass("height_scroll");
        } 
        if($('#profile').hasClass('active') == true){
            $('#profile #timeresslot_' + id[1] + ' > ul:not(:first)').remove();
            $('#profile #defaul_height_' + id[1]).removeClass("height_scroll");
        }
        if($('#contact').hasClass('active') == true){
            $('#contact #timepickslot_' + id[1] + ' > ul:not(:first)').remove();
            $('#contact #defaul_height_' + id[1]).removeClass("height_scroll");
        }  
    }
    
    function slot_select(id,name){
        var id = id;
        if($('#discount_'+name+'_'+id).prop('checked') == true){
            $('#discount_'+name+'_'+id).removeAttr('checked');
        } else {
            $('#discount_'+name+'_'+id).attr('checked','checked');
        }
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
                        var api_key = '<?php echo API_KEY?>';
                        $.ajax({
                            url: 'https://maps.googleapis.com/maps/api/geocode/json?key='+api_key+'&latlng='+position.coords.latitude+','+position.coords.longitude+'&sensor=true',
                            dataType: 'json',
                            success: function(place){
                                var json_l = place.results.length;
                                var val = 'pune';
                                $('#location').val(place.results[3].formatted_address);
                                for (var i = 0; i < place.results[0].address_components.length; i++) {
                                    var addressType = place.results[0].address_components[i].types[0];
                                    if (componentForm[addressType]) {
                                      var val = place.results[0].address_components[i][componentForm[addressType]];
                                      if(addressType == 'locality'){
                                        console.log(place.results[0].address_components);
                                        
                                        var location = val;
                                        var section = 'delivery';
                                        if($('#home-tab').hasClass('active') == true){
                                            section = 'reservation';
                                        } else if($('#profile-tab').hasClass('active') == true){
                                            section = 'delivery';
                                        } else if($('#contact-tab').hasClass('active') == true){
                                             section = 'pickup';
                                         }
                                         var url = '<?php echo HTTP_PATH;?>'+ 'getrestaurants';
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
             setTimeout(function(){
                $('#locate').val($('#location_city').val());
                if($('#locate').val()){
                    $('#locate').trigger('keyup');
                }
            },500);
            
            $('#home-tab').click(function(){
                if($('#locate').val()){
                    setTimeout(function(){       
                        $('#locate').trigger('keyup');
                    },300);
                }    
            });
            $('#contact-tab').click(function(){
                if($('#locate').val()){
                    setTimeout(function(){       
                        $('#locate').trigger('keyup');
                    },300);    
                }    
            });
            $('#profile-tab').click(function(){
                if($('#locate').val()){
                    setTimeout(function(){       
                        $('#locate').trigger('keyup');
                    },300);    
                }    
            });
            
        </script>

    </body>
</html>
