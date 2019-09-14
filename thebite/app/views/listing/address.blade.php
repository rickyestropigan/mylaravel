@section('content')

<style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
      }

      #infowindow-content .title {
        font-weight: bold;
      }

      #infowindow-content {
        display: none;
      }

      #map #infowindow-content {
        display: inline;
      }

      .pac-card {
        margin: 10px 10px 0 0;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        background-color: #fff;
        font-family: Roboto;
      }

      #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
      }

      .pac-controls {
        display: inline-block;
        padding: 5px 11px;
      }

      .pac-controls label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }

      #title {
        color: #fff;
        background-color: #4d90fe;
        font-size: 25px;
        font-weight: 500;
        padding: 6px 12px;
      }
    </style>
  </head>
  <body>
    <div class="pac-card" id="pac-card">
      <div>
        <div id="title">
          Autocomplete search
        </div>
        <div id="type-selector" class="pac-controls">
          <input type="radio" name="type" id="changetype-all" checked="checked">
          <label for="changetype-all">All</label>

          <input type="radio" name="type" id="changetype-establishment">
          <label for="changetype-establishment">Establishments</label>

          <input type="radio" name="type" id="changetype-address">
          <label for="changetype-address">Addresses</label>

          <input type="radio" name="type" id="changetype-geocode">
          <label for="changetype-geocode">Geocodes</label>
        </div>
        <div id="strict-bounds-selector" class="pac-controls">
          <input type="checkbox" id="use-strict-bounds" value="">
          <label for="use-strict-bounds">Strict Bounds</label>
        </div>
      </div>
      <div id="pac-container">
        <input id="pac-input" type="text" placeholder="Enter a location">
        <button type="button" id="s_location" name="s_location">Save location</button>
	<button type="button" onclick="showPosition();">Locate Me</button>
      </div>
    </div>
    <div id="map"></div>
    <div id="infowindow-content">
      <img src="" width="16" height="16" id="place-icon">
      <span id="place-name"  class="title"></span><br>
      <span id="place-address"></span>
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
       <div class="form-group">
              <input type="text" placeholder="Name" id="custname" placeholder="Name" name="cust_name" value="{{$profile->cust_name or ''}}" class="form-control" >  
          </div>
           <div class="error" id="err_custname" style="color:red;"></div>
          <div class="form-group">
              <input type="text" placeholder="Email" id="email" placeholder="Email" name="cust_email" value="{{$profile->cust_email or ''}}" class="form-control" readonly>  
          </div>
           <div class="error" id="err_email" style="color:red;"></div>
          <div class="form-group">
              <input type="text" placeholder="Phone" placeholder="Phone" id="phone" name="cust_phone" value="{{$profile->cust_phone or ''}}" class="form-control">  
          </div>
           <div class="error" id="err_phone" style="color:red;"></div>
          <div class="form-group password">
              <input type="password" placeholder="Password" placeholder="Password" id="pwd" name="cust_password" value="{{$profile->plain_pwd or ''}}" class="form-control">  
          </div>
           <div class="error" id="err_pwd" style="color:red;"></div>
           <div class="form-group password">
               <textarea id="address" class="form-control">{{$profile->address or ''}}</textarea>
          </div>
            <div class="form-group show-map">
                       <a href="{{URL::to('updateLocation')}}" class="center">Update location</a>
                   </div>
           <input type="hidden" id="lat" value="{{$profile->latitude}}" />
           <input type="hidden" id="lng" value="{{$profile->longitude}}" />
      </div>
       
      <div class="modal-footer text-center">
        <input type="submit" class="btn btn-default m-auto profile"  value="Change Profile">
        
      </div>
       {{ Form::close() }}
        </div>
    </div>

  </div>
</div> 
    

@stop
