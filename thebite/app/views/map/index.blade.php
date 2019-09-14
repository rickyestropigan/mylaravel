        
<div id="map" style="width: 100%; height: 500px;"></div>
        <div id="marker-tooltip"></div>
        
        <script type="text/javascript">
            
            var title = JSON.parse('<?php echo json_encode($title)?>');
            var title_slug = JSON.parse('<?php echo json_encode($slug)?>');
            var lat = JSON.parse('<?php echo json_encode($lat)?>');
            var lng = JSON.parse('<?php echo json_encode($lng)?>');
            var user_lat = Number('<?php echo $user_lat;?>');
            var user_lng = Number('<?php echo $user_lng;?>');
            var address = JSON.parse('<?php echo json_encode($address);?>');
            var profile_image = JSON.parse('<?php echo json_encode($profile_image);?>');
            var cuisines = JSON.parse('<?php echo json_encode($cuisines);?>');
            var distance = JSON.parse('<?php echo json_encode($distance);?>');
            var p_image = '';
            var url = '<?php echo HTTP_PATH?>';
            
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 5,
                center: new google.maps.LatLng(lat[0],lng[0]),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
		disableDefaultUI: false

            });
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            directionsDisplay.setMap(map);
            var infowindow = new google.maps.InfoWindow();
                var pos = {
                    lat: user_lat,
                    lng: user_lng
                };
          
            var marker, i,contentString;
            for (i = 0; i < title.length; i++) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat[i],lng[i]),
                    map: map
                });

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        p_image  = (profile_image[i] != '') ? url+"uploads/users/"+profile_image[i] : url+"public/listingimg/food_a.png";
                        if(i==0){
                            contentString = "<div class='info-window'>" +
                                                "<h6><a target='_blank' href='javascript:void(0)'>"+title[i]+"</a></h6>" +
                                            "</div>";
                        } else {
                            contentString = "<div class='info-window'>" +
                                                "<h6>Name: <a target='_blank' href='restaurantdetail/"+title_slug[i]+"'>"+title[i]+"</a></h6>" +
                                                "<div class='info-content'>" +
                                                    "<p>Address: "+address[i]+"</p>" +
                                                    "<p>Cuisines: "+cuisines[i]+"&nbsp ,Distance:"+distance[i]+" KM</p>" +
                                                    "<p><a href='javascript:void(0);' onclick='getDirection(this)' id='"+i+"'>Get Direction</a></p>"+
                                                    "<p><img height='100px' width='100px' src='"+p_image+"' /></p>"+
                                                "</div>" +
                                            "</div>";
                        }
                        infowindow.setContent(contentString);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
                
                 google.maps.event.addListener(marker, 'mouseover', (function (maker,i) {
                     return function () {
                         p_image  = (profile_image[i] != '') ? url+"uploads/users/"+profile_image[i] : url+"public/listingimg/food_a.png";
                        if(i==0){
                            contentString = "<div class='info-window'>" +
                                                "<h6><a target='_blank' href='javascript:void(0)'>"+title[i]+"</a></h6>" +
                                            "</div>";
                        } else {
                            contentString = "<div class='info-window'>" +
                                                "<h6>Name: <a target='_blank' href='restaurantdetail/"+title_slug[i]+"'>"+title[i]+"</a></h6>" +
                                                "<div class='info-content'>" +
                                                    "<p>Address: "+address[i]+"</p>" +
                                                    "<p>Cuisines: "+cuisines[i]+"&nbsp ,Distance:"+distance[i]+" KM</p>" +
                                                    "<p><a href='javascript:void(0);' onclick='getDirection(this)' id='"+i+"'>Get Direction</a></p>"+
                                                    "<p><img height='100px' width='100px' src='"+p_image+"' /></p>"+
                                                "</div>" +
                                            "</div>";
                        }
                        infowindow.setContent(contentString);
                        infowindow.open(map,this);
                     } 
                })(marker,i));

                google.maps.event.addListener(marker, 'mouseout', (function (maker,i) {
                    return function(){
                        //infowindow.close();
                    }    
                })(marker,i));
            
            };
            function getDirection(obj){
                var origin = address[0];
                var destination = address[obj.id];
                calculateAndDisplayRoute(directionsService, directionsDisplay,origin,destination);
                
            };
            function calculateAndDisplayRoute(directionsService, directionsDisplay,origin,destination) {
                directionsService.route({
                  origin: origin,
                  destination: destination,
                  travelMode: 'DRIVING'
                }, function(response, status) {
                  if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                    infowindow.close();
                  } else {
                    window.alert('Directions request failed due to ' + status);
                  }
                });
              }
             
        </script>
   

