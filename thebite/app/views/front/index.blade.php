@section('content')
<?php $c_time = date('H:i');
      $day = date('D');
      $d_index = 0;
      if($day == 'Mon'){
        $d_index = 0;
      } else if($day == 'Tue' ){
          $d_index = 1;
      } else if($day == 'Wed' ){
          $d_index = 2;
      } else if($day == 'Thu' ){
          $d_index = 3;
      } else if($day == 'Fri' ){
          $d_index = 4;
      } else if($day == 'Sat' ){
          $d_index = 5;
      } else if($day == 'Sun' ){
          $d_index = 6;
      } else {
          $d_index = 0;
      } 
      
      ?>
 <section class="middle_section_slider">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="center_text text-center">
                            <h3>Best Restaurants. Best Deals.</h3>
                        <div class="form-group center_field m-auto">
                            <i class="fa fa-map-marker"></i>
                            <input type="text" placeholder="Enter Your Location" id="location"> 
                        <input type="hidden" placeholder="Enter Your Location" id="location_city"> 
                        <button type="button" style="right: 95px" onclick="showPosition();">Locate Me</button>
                        <button type="button" style="right: 10px" onclick="search();">Search</button>
                        </div>
                            <div class="nav-center tabs_section">
                               <ul class="nav nav-tabs" id="myTab" role="tablist">
  
  <li class="nav-item">
    <a class="nav-link delivery" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Delivery</a>
  </li>
  <li class="nav-item">
    <a class="nav-link restaurant active" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Pickup</a>
  </li>
<li class="nav-item">
    <a class="nav-link reservation" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Reservations</a>
  </li>
</ul>
<!--<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab" style="max-width:100%">
	<div class="row">
      <?php 
foreach ($data as $res) {

    $service_offered = explode(',',$res->service_offered);
    $start_time = explode(',',$res->start_time);
    $end_time = explode(',',$res->end_time);
$open_days = explode(',',$res->open_days);

                        	$d_index = array_search($day, $open_days);
    
    if((in_array("Table reservations",$service_offered)) && ($c_time >= $start_time[$d_index] && $c_time <= $end_time[$d_index])){

    $uid = $res->userid;

    $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();

    $datahr = DB::table('opening_hours')->where("opening_hours.open_close", "=", '1')->where('user_id', '=', $uid)->get();
    $r_slot_s_time = explode(',',$datahr[0]->start_time);
    $r_slot_e_time = explode(',',$datahr[0]->end_time);
    $crusines = str_replace(',', ' | ', $res->cuisines);


    $current_time = date('h:i A');
    $current_time = strtotime($current_time);
    $frac = 1800;
    
    $r = $current_time % $frac;
    $f_time = $current_time + ($frac - $r);
    $f_slot_time = date('h:i A', $f_time);
       
    $c_slot_time = strtotime($f_slot_time) - (30 * 60);
    $c_slot_time = date('h:i A', $c_slot_time);
    
    $l_time = $current_time - ($frac + $r);
    $p_slot_time = date('h:i A', $l_time);

    ?>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="card br-0 custom_card border-0 mb-5">
                                <div class="card_img position-relative">
                                    <div class="tag position-absolute">
                                        {{$res->discount}}% off on all menu
                                    </div>
                                    <a href="{{ url('/restaurantdetail') }}/{{$res->userslug}}"><?php if ($res->profile_image) { ?>
                                            {{ HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH.$res->profile_image,'',array('width' => '350px','height' => '250px')) }}
                        <?php } else { ?>
                                            {{ HTML::image("public/listingimg/food_a.png") }}
                        <?php } ?>
                                    </a></div>
                                <div class="card-body px-0">
                                    <h4 class="card-title"> <div class="product_title">{{ $res->first_name }}</div> <button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i> {{ $res->average_price }}</button> <span class="float-right">N/A KM</span></h4> 
                                    <ul class="list-unstyled big_size">
                                        <li class="d-inline-block"><a href="{{ url('/restaurantdetail') }}/{{$res->userslug}}">{{$crusines}}</a></li>

                                    </ul>
                                    
                                    <ul class="list-unstyled radio-toolbar ">
                                       
                                        <li class="d-inline-block">

                                            <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                            <label for="discount"><span>{{$p_slot_time}}</span>
                                                <b>{{$res->discount}}% off</b>
                                            </label>
                                        </li>
                                        <li class="d-inline-block">
                                            <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                            <label for="radioBanana"><span>{{$c_slot_time}}</span>
                                                <b>{{$res->discount}}% off</b></label>
                                        </li>
                                        <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                            <label for="radioOrange"><span>{{$f_slot_time}}</span>
                                                <b>{{$res->discount}}% off</b></label>
                                        </li>
                                    </ul>

                                </div>
                            </div>  

                        </div>
<?php } } ?> 
 </div>
</div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab" style="max-width:100%">
<div class="row"><?php 
                        foreach ($data as $res) {
                            $uid = $res->userid;
                            $service_offered = explode(',',$res->service_offered);
                        
                            $start_time = explode(',',$res->start_time);
                            $end_time = explode(',',$res->end_time);
$open_days = explode(',',$res->open_days);

                        	$d_index = array_search($day, $open_days);

                        if((in_array("Delivery",$service_offered)) && ($c_time >= $start_time[$d_index] && $c_time <= $end_time[$d_index])){
                            $datarev = DB::table('reviews')
                                    ->where('user_id', '=', $uid)
                                    ->get();

                            $datahr = DB::table('opening_hours')
                                    ->where('user_id', '=', $uid)
                                    ->get();



                            $crusines = str_replace(',', ' | ', $res->cuisines)
                            ?>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4" >
                                <div class="card br-0 custom_card border-0 mb-5">
                                    <div class="card_img position-relative">
                                        <div class="tag position-absolute">
                                            {{$res->discount}}% off on all menu
                                        </div>
                                        <a href="{{ url('/restaurantdetail') }}/{{$res->userslug}}"><?php if ($res->profile_image) { ?>
                                                {{ HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH.$res->profile_image,'',array('width' => '350px','height' => '250px')) }}
        <?php } else { ?>
                                                {{ HTML::image("public/listingimg/food_a.png") }}
        <?php } ?>
                                        </a>
                                    </div>
                                    <div class="card-body px-0">
                                        <h4 class="card-title">  <div class="product_title">{{ $res->first_name }}</div> <button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i> {{ $res->average_price }}</button> <span class="float-right">N/A KM</span></h4> 
                                        <ul class="list-unstyled big_size">
                                            <li class="d-inline-block"><a href="{{ url('/restaurantdetail') }}/{{$res->userslug}}">{{$crusines}}</a></li>

                                        </ul>
                                        
                                        <ul class="list-unstyled">
                                            <li class="d-inline-block"><a href="">Free Delivery Above <i class="fa fa-inr"></i> {{$res->delivery_cost}}</a></li>
                                            <li class="d-inline-block"><a href="">Min. Order <i class="fa fa-inr"></i> {{$res->minimum_order}}</a></li>
                                        </ul>

                                    </div>
                                </div>  

                            </div> 




                        <?php } }
                                 ?></div></div>
  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab" style="max-width:100%">
<div class="row">
      <?php
foreach ($data as $res) {
    $uid = $res->userid;
    
    $service_offered = explode(',',$res->service_offered);
    $start_time = explode(',',$res->start_time);
    $end_time = explode(',',$res->end_time);

    if((in_array("Pickup",$service_offered)) && ($c_time >= $start_time[$d_index] && $c_time <= $end_time[$d_index])){

    $datarev = DB::table('reviews')
            ->where('user_id', '=', $uid)
            ->get();

    $datahr = DB::table('opening_hours')
            ->where('user_id', '=', $uid)
            ->get();
    $crusines = str_replace(',', ' | ', $res->cuisines)
    ?>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="card br-0 custom_card border-0 mb-5">
                                <div class="card_img position-relative">
                                    <div class="tag position-absolute">
                                        {{$res->discount}}% off on all menu
                                    </div>
                                    <a href="{{ url('/restaurantdetail') }}/{{$res->userslug}}"><?php if ($res->profile_image) { ?>
                                            {{ HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH.$res->profile_image,'',array('width' => '350px','height' => '250px')) }}
    <?php } else { ?>
                                            {{ HTML::image("public/listingimg/food_a.png") }}
    <?php } ?>
                                    </a></div>
                                <div class="card-body px-0">
                                    <h4 class="card-title"> <div class="product_title">{{$res->first_name}}</div> <button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i> {{ $res->average_price }}</button> <span class="float-right">N/A KM</span></h4> 
                                    <ul class="list-unstyled big_size">
                                        <li class="d-inline-block"><a href="{{ url('/restaurantdetail') }}/{{$res->userslug}}">{{$crusines}}</a></li>

                                    </ul>
                                    
                                </div>
                            </div>  


                        </div> 
<?php } } ?>
  </div>
</div>--><div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel" style="max-width:100%" aria-labelledby="home-tab">Find your favourite restaurants around you at 
                                        great deals up to 50% off. Every day!</div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" style="max-width:100%" aria-labelledby="profile-tab">Find your favourite restaurants around you at 
                                        great deals up to 50% off. Every day!</div>
                                    <div class="tab-pane fade" id="contact" role="tabpanel" style="max-width:100%" aria-labelledby="contact-tab">Find your favourite restaurants around you at 
                                        great deals up to 50% off. Every day!</div>
                                </div></div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>   
        </section>
        <section class="middle_section_slider">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="center_text text-center">
                        <h3 class="pb-3">How Bitebargain Works?</h3>
                        <p>Connecting restaurants during off-peak <br>
time with Foodies.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-4">
                        <div class="first_icon restaurant_border">
                            <img src="public/frontimg/first_icon.png">
                        </div>   
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-8">
                        <div class="icon_text text-right">
                            <p>Find your favorite restaurant.</p>
                        </div>
                    </div>
                    
                </div>
                  <div class="row">
                        <div class="col-xs-12 col-sm-8 col-md-9 col-lg-8">
                        <div class="icon_text text-left">
                            <p>Pick your time and discount.</p>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-4">
                        <div class="first_icon clock_border">
                            <img src="public/frontimg/second_icon.png">
                        </div>   
                    </div>
                  
                    
                </div>
                  <div class="row">
                       
                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-4">
                        <div class="first_icon">
                            <img src="public/frontimg/third_icon.png">
                        </div>   
                    </div>
                       <div class="col-xs-12 col-sm-8 col-md-9 col-lg-8">
                        <div class="icon_text text-right">
                            <p>Enjoy your food and savings.</p>
                        </div>
                    </div>
                  
                    
                </div>
                </div>
        </section>
        <section class="bottom_img">
            <div class="bg_img_mobile">
                <img src="public/frontimg/bottom_img.png">
            </div>
        </section>

@stop
