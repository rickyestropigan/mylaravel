<!DOCTYPE HTML>
<html>
    <!--[if lt IE 7 ]><html class="no-js ie6" dir="ltr" lang="en-US"><![endif]-->
    <!--[if IE 7 ]><html class="no-js ie7" dir="ltr" lang="en-US"><![endif]-->
    <!--[if IE 8 ]><html class="no-js ie8" dir="ltr" lang="en-US"><![endif]-->
    <!--[if IE 9 ]><html class="no-js ie9" dir="ltr" lang="en-US"><![endif]-->
    <!--[if (gte IE 9)|!(IE)]><!-->
    <!--[if !IE]><!-->
    <!--<![endif]-->
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>@yield('title')</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" type="image/png" href="{{ asset('public/img/front/favicon-16x16.png') }}"/>
        <?php if (file_exists(UPLOAD_LOGO_IMAGE_PATH . SITE_FAVICON)) {
            ?>
            <link rel="icon" type="image/png" href="<?php echo HTTP_PATH . DISPLAY_LOGO_IMAGE_PATH . SITE_FAVICON; ?>"/>
        <?php } else { ?>
            <link rel="icon" type="image/png" href="{{ asset('public/img/front/favicon.ico') }}"/>
        <?php } ?>

        <link href="{{ URL::asset('public/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('public/css/bootstrap-reset.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('public/assets/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('public/css/style.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('public/css/table-responsive.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('public/css/owl.carousel.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('public/css/font-awesome.css') }}" rel="stylesheet">


        <!--right slidebar-->
        <link href="{{ URL::asset('public/css/slidebars.css') }}" rel="stylesheet">
        <script src="{{ URL::asset('public/js/jquery.js') }}"></script>
        <script src="{{ URL::asset('public/js/listing.js') }}"></script>
        <script src="{{ URL::asset('public/js/bootstrap.min.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBMUm-KXWAU0iQoirUzhdLQxszuH0s8eNE&libraries=places&callback=initMap" type="text/javascript" async defer></script>
<!--<script src="http://maps.google.com/maps/api/js?key=AIzaSyDhZvPgmmRa1Wj_VAfjFJMyFGOsP2qNu00&libraries=places&callback=initMap" async defer type="text/javascript"></script>-->
    <body>
        <section id="container" >
            <!--header start-->
            <header class="header white-bg">
                <div class="sidebar-toggle-box">
                    <div class="fa fa-bars toggle-left-menu tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
                </div>
                <?php
                if (file_exists(UPLOAD_LOGO_IMAGE_PATH . SITE_LOGO)) {
                    ?>

                    {{ html_entity_decode(link_to('/admin/admindashboard', HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, 'Logo'), array('escape' => false,'class'=>"logo"))) }}

                    <?php
                } else {
                    ?>
                    <img src="{{ URL::asset('public/img/front') }}/logo.png" alt="<?php echo SITE_TITLE; ?>" title="<?php echo SITE_TITLE; ?>" />{{ html_entity_decode(link_to('/admin/admindashboard', HTML::image("public/img/front/logo.png", 'Logo', ['width'=>110]), array('escape' => false,'class'=>"logo"))) }}

                    <?php
                }
                ?>
                <!--logo start-->

                <!--logo end-->
                <div class="top-nav ">
                    <!--search & user info start-->
                    <ul class="nav pull-right top-menu">
                        <!-- user login dropdown start-->
                        <li class="dropdown">
                            <a data-toggle="dropdown" class="dropdown-toggle " href="#">
                                <span class="username">Admin</span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu extended logout dropss">
                                <div class="log-arrow-up"></div>
                                <li class="dropss">
                                    {{ link_to('/admin/editprofile', "Edit Profile", array('escape' => false,'class'=>"")) }}
                                <li class="dropss">
                                    {{ link_to('/admin/changepassword', "Change Password", array('escape' => false,'class'=>"")) }}
                                </li>
                                <li>
                                    {{ html_entity_decode(link_to('/admin/logout', '<i class="fa fa-key"></i> Log Out', array('escape' => false,'class'=>""))) }}
                                </li>
                            </ul>
                        </li>
                        <!-- user login dropdown end -->
                    </ul>
                    <!--search & user info end-->
                </div>
            </header>
            <!--header end-->
            <!--sidebar start-->
            @include('elements/admin_left_menu')
            @yield('content')

            <!--footer start-->
            <footer class="site-footer">
                <div class="text-center">
                    <?php echo date('Y'); ?> &copy; <?php echo SITE_TITLE; ?>
                    <a href="#" class="go-top">
                        <i class="fa fa-angle-up"></i>
                    </a>
                </div>
            </footer>
            <!--footer end-->
        </section>
    </body>
    <!-- js placed at the end of the document so the pages load faster -->
    <script class="include" type="text/javascript" src="{{ URL::asset('public/js/jquery.dcjqaccordion.2.7.js') }}"></script>
    <script src="{{ URL::asset('public/js/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ URL::asset('public/js/jquery.sparkline.js') }}"></script>
    <script src="{{ URL::asset('public/js/owl.carousel.js') }}"></script>
    <script src="{{ URL::asset('public/js/jquery.customSelect.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/respond.min.js') }}"></script>
    <script src="{{ URL::asset('public/js/cssua.min.js') }}"></script>
    <!--right slidebar-->
    <script src="{{ URL::asset('public/js/slidebars.min.js') }}"></script>
    <!--common script for all pages-->
    <script src="{{ URL::asset('public/js/common-scripts.js') }}"></script>
    <!--script for this page-->
    <script src="{{ URL::asset('public/js/sparkline-chart.js') }}"></script>

    <script>
    //owl carousel
    $(document).ready(function () {
        $("#owl-demo").owlCarousel({
            navigation: true,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: true
        });
    });
    //custom select box
    $(function () {
        $('select.styled').customSelect();
    });
    </script>
<script>
    $(function(){
        initMap();
    });
    //Google map get location using current location and using search address
    function initMap() {


        var marker = '';
        var map = new google.maps.Map(document.getElementById('map-convas'), {
            center: {lat: -33.8688, lng: 151.2195},
            zoom: 13
        });





        var card = document.getElementById('pac-card');
        var input = document.getElementById('pac-input');
        var types = document.getElementById('type-selector');
        var strictBounds = document.getElementById('strict-bounds-selector');

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


        //get currnet location and set marker
        navigator.geolocation.getCurrentPosition(function (position, marker) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            infowindow.setPosition(pos);
             //geocodeLatLng(geocoder, map, infowindow);
            //infowindow.setContent('Location found.');
            //infowindow.open(map,marker);
            map.setCenter(pos);
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                map: map
            });
        });

        var componentForm = {
               // sublocality_level_1: 'short_name',
                //street_number: 'long_name',
                //route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'long_name',
                //country: 'long_name',
                postal_code: 'long_name'
              };
        autocomplete.addListener('place_changed', function (event) {
            infowindow.close();
            //marker.setMap(null);
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                  var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
              }
            
//            document.getElementById('pac-input').value = place.name;
            
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
//        function setupClickListener(id, types) {
//            var radioButton = document.getElementById(id);
//            radioButton.addEventListener('click', function () {
//                autocomplete.setTypes(types);
//            });
//        }

//        setupClickListener('changetype-all', []);
//        setupClickListener('changetype-address', ['address']);
//        setupClickListener('changetype-establishment', ['establishment']);
//        setupClickListener('changetype-geocode', ['geocode']);
//
//        document.getElementById('use-strict-bounds')
//                .addEventListener('click', function () {
//                    console.log('Checkbox clicked! New state=' + this.checked);
//                    autocomplete.setOptions({strictBounds: this.checked});
//                });
    }
    function geocodeLatLng(geocoder, map, infowindow) {
//        var input = document.getElementById('latlng').value;
//        var latlngStr = input.split(',', 2);
        var latlng = {lat: infowindow.position.lat(), lng: infowindow.position.lng()};
        geocoder.geocode({'location': latlng}, function(results, status) {
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

</html>
