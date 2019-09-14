<?php

use Illuminate\Support\Facades\Crypt;

class ListingController extends BaseController {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    public function __construct() {

       /* if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }*/
        
    }

    protected $layout = 'layouts.listingdefault';

    public function showListing() {
        
        /*if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }*/
        $location_city = '';
        $location_city = Session::get('location_city');
        Session::forget('location_city');
        $day = date('D');
//        $data = DB::table('users')
//                ->select('users.slug as userslug', 'users.id as userid', 'offers.slug as offersslug', 'offers.id as offersid', 'offers.*', 'opening_hours.open_close as hrstatus', 'opening_hours.*','users.*')
//                ->join('offers', 'offers.user_id', '=', 'users.id')
//                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
//                //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
//                ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
//                ->where("users.user_type", "=", 'Restaurant')
//                ->where("users.status", "=", '1')
//                ->where("offers.type", "=", 'percentage')
//                ->where("offers.status", "=", '1')
//                ->where("offers.flagstatus", "=", '1')
//                ->where("opening_hours.open_close", "=", '1')
//                ->groupBy('offers.user_id')
//                //->orderBy('offers.created', 'DESC')
//                ->orderBy('offers.discount', 'DESC')
//                ->orderBy('offers.created', 'DESC')
//                ->get();
        
        $select_users = array('users.id as userid','users.first_name','users.username','users.profile_image','users.average_price','users.service_offered','users.payment_options','users.slug as userslug','users.cuisines','users.delivery_cost','users.minimum_order');
        $select_users = array_merge($select_users, array('opening_hours.open_days','opening_hours.open_close','opening_hours.start_time','opening_hours.end_time'));
        $cnd = array(
            'users.user_type' => 'Restaurant',
            'users.status' => '1',
            'opening_hours.open_close' => '1',
            'opening_hours.status' => '1'
        );
        $data = DB::table('users')
                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->select($select_users)
                ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                ->where($cnd)
                ->groupBy('userid')
                ->get();
        
        
        if (Session::has('userdata')) {
            
            $id = Session::get('userdata')->id;
            $profile = DB::table('users')->where("users.id", $id)->first();
            if ($profile) {
                Session::put('profile', $profile);

                $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                $this->layout->content = View::make('listing.index')->with('data', $data)->with('profile', $profile)->with('location_city',$location_city);
            } else {
                return Redirect::to('/logout');
            }
            /* if (($profile->address) && ($profile->latitude) && ($profile->longitude)) {
              $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
              $this->layout->content = View::make('listing.index')->with('data', $data)->with('profile', $profile);
              } else {
              $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
              $this->layout->content = View::make('listing.address')->with('data', $data)->with('profile', $profile);
              } */
        } else {
            $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
            $this->layout->content = View::make('listing.index')->with('data', $data)->with('location_city',$location_city);
        }
        
    }

      public function showSearch() {
        if (!Session::has(('userdata'))) {
            $user_id = '';
        } else {
            $user_id = Session::get('userdata')->id;
        }
        $offslot_result = array();
        $page_name = $slot_data = "";
        $day = date('D');
        $d_index = 0;
        
        $c_time = date('H:i');
        $input = Input::all();
        $search_keyword = trim($input['serach']);
        $map_status = trim($input['map_status']);
        $page_name = $input['page_name'];
        
        switch ($map_status) {
            case 0:
                if ($search_keyword) {
                    if (is_numeric($search_keyword)) {
                        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();
                        $lat = $user_Data[0]->latitude;
                        $lng = $user_Data[0]->longitude;
                        $search = DB::select("SELECT tbl_users.*,tbl_offers.*,tbl_offers.discount as offerdisc ,tbl_users.slug as userslug,tbl_users.id as userid,((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hour.use_id = tbl_users.id where tbl_opening_hour.open_close = '1' AND latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' GROUP BY tbl_users.id HAVING distance BETWEEN 0 AND $search_keyword");
                    } else {
                        //$search = DB::table("users")->join('offers', 'offers.user_id', '=', 'users.id')->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')->where("offers.type", "=", 'percentage')->where("users.user_type", "=", 'Restaurant')->where("users.status", "=", '1')->where("opening_hours.open_close", "=", '1')->where("offers.flagstatus", "=", '1')->where('users.first_name', 'LIKE', '%' . $search_keyword . '%')->orwhere('users.cuisines', 'LIKE', '%' . $search_keyword . '%')->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')->groupBy('users.id')->get();
                        $search = DB::table('users')
                                //->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.discount as offerdisc', 'offers.id as offersid', 'offers.*')
                                ->select('users.slug as userslug', 'users.id as userid', 'users.*')
                                //->join('offers', 'offers.user_id', '=', 'users.id')
                                //->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                ->join('menu_item','menu_item.user_id', '=', 'users.id')
                                ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                                ->whereRaw("FIND_IN_SET('Delivery',tbl_users.service_offered)")
                                ->where(function ($query) use ($search_keyword) {
                                    $query->where('users.first_name', 'LIKE', '%' . $search_keyword . '%');
                                    $query->orWhere('users.cuisines', 'LIKE', '%' . $search_keyword . '%');
                                    $query->orWhere('menu_item.item_name', 'LIKE', '%' . $search_keyword . '%');
                                })->Where(function($query) {
                                   // $query->where("offers.type", "=", 'percentage');
                                   // $query->where("offers.flagstatus", "=", '1');
                                    //$query->where("offers.status", "=", '1');
                                })
                                ->where("users.user_type", "=", 'Restaurant')
                                ->where("users.status", "=", '1')
                                ->where("opening_hours.open_close", "=", '1')
                                //->orderBy('offers.discount', 'DESC')
                                //->orderBy('offers.created', 'DESC')
                                ->groupBy('users.id')
                                ->get();
                        
                    }  
                } else {
//                    $search = DB::table('users')
//                                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.discount as offerdisc', 'offers.id as offersid', 'offers.*')
//                                //->join('offers', 'offers.user_id', '=', 'users.id')
//                                //->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
//                                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
//                                ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
//                                ->whereRaw("FIND_IN_SET('Delivery',tbl_users.service_offered)")
//                                ->where("offers.type", "=", 'percentage')
//                                ->where("offers.flagstatus", "=", '1')
//                                ->where("offers.status", "=", '1')
//                                ->where("users.user_type", "=", 'Restaurant')
//                                ->where("users.status", "=", '1')
//                                ->where("opening_hours.open_close", "=", '1')
//                                ->orderBy('offers.discount', 'DESC')
//                                ->orderBy('offers.created', 'DESC')
//                                ->groupBy('users.id')
//                                ->get();
                    
                    $search = DB::table('users')
                                //->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.discount as offerdisc', 'offers.id as offersid', 'offers.*')
                                ->select('users.slug as userslug', 'users.id as userid', 'users.*')
                                //->join('offers', 'offers.user_id', '=', 'users.id')
                                //->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                                ->whereRaw("FIND_IN_SET('Delivery',tbl_users.service_offered)")
                                ->where("users.user_type", "=", 'Restaurant')
                                ->where("users.status", "=", '1')
                                ->where("opening_hours.open_close", "=", '1')
                                //->orderBy('offers.discount', 'DESC')
                                //->orderBy('offers.created', 'DESC')
                                ->groupBy('users.id')
                                ->get();
                    
                    
                }
               
                //set data to html and create view then return it
                if (!empty($search)) {
                    $output = $discount = "";
                    $of_id = 0;
                    foreach ($search as $data) {    
                        $img = (isset($data->profile_image) && ($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();
                        $cnd = array(
                            'user_id' => $uid,
                            'offers.status' => '1',
                            'offers.flagstatus' => '1',
                            'offers.type' => 'percentage'
                        );
                        
                        $revsdays = $starttime = $endtime = $comment = "";
                        $parameter = Crypt::encrypt($data->id);
                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                            $validity = $t_discount = $discount = "";
                            if($user_id){
                                $distance = '<span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span>';
                            } else {
                                $distance = '';
                            }
                            $offslot_result = DB::table('offers')
                                    ->where($cnd)
                                    ->select('offers_slot.*')
                                    ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                    ->orderBY('offers_slot.id', 'ASC')
                                    ->limit(1)
                                    ->get();

                            if(isset($offslot_result[0])){
                                $t_discount = $offslot_result[0]->discount;
                            }
                           
                            $discount = ($t_discount != '') ? '<b>'.$t_discount.'% off</b>' : "";
                            if ($page_name == '') {
                                if ($offslot_result) {
                                    $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                    <li class="d-inline-block">
                                                        <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                        <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                            ' . $discount . '
                                                        </label>
                                                    </li>
                                                    <li class="d-inline-block">
                                                        <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                        <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                            ' . $discount . '</label></li>
                                                             <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                    </li>

                                                </ul>
                                                <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                                <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                                }
                            } else if ($page_name == 'discountdetails') {
                                if ($offslot_result) {

                                    $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                    <li class="d-inline-block">

                                                        <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                        <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                            ' . $discount . '
                                                        </label>
                                                    </li>
                                                    <li class="d-inline-block">
                                                        <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                        <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                            ' . $discount . '</label></li>
                                                </ul>
                                                <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                                <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                                }
                            } else {
                                if($offslot_result){
                                    $validity = "Validity: ".date('d-M-Y',strtotime($offslot_result[0]->start_date)).' To '.date('d-M-Y',strtotime($offslot_result[0]->end_date));
                                }
                            }
                            if($discount){
                                $discount = '<div class="tag position-absolute">' . $discount . ' on all menu</div>';
                            }
                            $output .= '
                                                             <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                             <div class="card br-0 custom_card border-0 mb-5">
                                                               <div class="card_img position-relative"> 
                                                              '.$discount . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                    '</div>' .
                                    '<div class="card-body px-0">
                                                                            <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> '.$distance.'</h4>
                                                                                 <ul class="list-unstyled big_size">
                                                                    <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                    '</a></li></ul>

                                                                             <ul class="list-unstyled">
                                                                    <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                    <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                                </ul>
                                                                <div class="validity">
                                                '.$validity.'
                                            </div>
                                                                <div class="" id="defaul_height_'.$uid.'">
                                            <div id="timeslot_'.$uid.'">'.$slot_data.'</div></div>
                                                                             </div>' .
                                    '</div></div>';
                        }
                        
                        
                    }
                    return $output;
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }


                break;

            case 1:
                $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
                $lat = $lng = $user_id = "";
                $user_id = Session::get('userdata')->id;
                $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

                $lat = $user_Data[0]->latitude;
                $lng = $user_Data[0]->longitude;

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.first_name LIKE '%$search_keyword%' OR tbl_users.last_name LIKE '%$search_keyword%' OR tbl_users.cuisines LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100 limit 0,100");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);

                break;

            default: $output = "Sorry! No match Found";
                return $output;
                break;
        }
    }

    public function showSearchreservation() {
        if (!Session::has(('userdata'))) {
            $user_id = '';
        } else {
            $user_id = Session::get('userdata')->id;
        }
        $input = Input::all();
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
        
        $c_time = date('H:i');
        $slot_data = $page_name = "";
        $data = trim($input['serach']);
        $map_status = trim($input['map_status']);
        $page_name = trim($input['page_name']);
        $search_keyword = trim($data);
        switch ($map_status) {
            case 0:
                if (!empty($data)) {

                    $search = DB::table("users")
                            //->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->join('menu_item','menu_item.user_id', '=', 'users.id')
                            ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                            ->whereRaw("FIND_IN_SET('Table reservations',tbl_users.service_offered)")
                            ->where(function ($query) use ($search_keyword) {
                                $query->where('users.first_name', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.cuisines', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('menu_item.item_name', 'LIKE', '%' . $search_keyword . '%');
                            })->Where(function($query) {
                               // $query->where("offers.type", "=", 'percentage');
                               // $query->where("offers.flagstatus", "=", '1');
                                //$query->where("offers.status", "=", '1');
                            })
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->where("opening_hours.open_close", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*','opening_hours.open_days')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                } else {

                     $search = DB::table("users")
                            //->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                             ->whereRaw("FIND_IN_SET('table reservations',tbl_users.service_offered)")
                            //->where("offers.type", "=", 'percentage')
                            //->where("offers.flagstatus", "=", '1')
                            //->where("offers.status", "=", '1')
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->where("opening_hours.open_close", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*','opening_hours.open_days')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                    
                    
                }
                   
                
                if (!empty($search)) {
                    $output = "";
                    foreach ($search as $data) {
                        
                        if (!empty($data->profile_image)) {
                            $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                        } else {
                            $img = HTML::image("public/listingimg/food_a.png");
                        }

                        $uid = $data->userid;

                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();

                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where(array("opening_hours.open_close" => '1','status' => '1'))->orderBy('id','DESC')->get();
                        $cnd = array(
                            'user_id' => $uid,
                            'offers.status' => '1',
                            'offers.flagstatus' => '1',
                            'offers.type' => 'percentage'
                        );
                        
                        $revsdays = $starttime = $endtime = $comment = "";
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
                        
                        $open_days = explode(',', $data->open_days);
                        $d_index = array_search($day, $open_days);
                        $start_time = explode(',', $datahr[0]->start_time);
                        $end_time = explode(',', $datahr[0]->end_time);
                        
                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";
                        
                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $start_time[$d_index]) && ($c_time <= $end_time[$d_index])){
                            
                       if($user_id){
                            $distance = '<span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span>';
                        } else {
                            $distance = '';
                        }
                        $validity = $t_discount = $discount = "";
                            $offslot_result = DB::table('offers')
                                    ->where($cnd)
                                    ->select('offers_slot.*')
                                    ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                    ->orderBY('offers_slot.id', 'ASC')
                                    ->limit(1)
                                    ->get();

                            if(isset($offslot_result[0])){
                                $t_discount = $offslot_result[0]->discount;
                            }
                        
                            $discount = ($t_discount != '') ? '<b>'.$t_discount.'% off</b>' : "";
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        '.$discount.'
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        '.$discount.'</label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                       '.$discount.'
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        '.$discount.'</label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                       
                                        <li class="d-inline-block">

                                            <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                            <label for="discount"><span>'.$p_slot_time.'</span>
                                                '.$discount.'
                                            </label>
                                        </li>
                                        <li class="d-inline-block">
                                            <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                            <label for="radioBanana"><span>'.$c_slot_time.'</span>
                                                '.$discount.'</label>
                                        </li>
                                        <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                            <label for="radioOrange"><span>'.$f_slot_time.'</span>
                                                '.$discount.'</label>
                                        </li>
                                    </ul>';
                            
                            if($offslot_result){
                                    $validity = "Validity: ".date('d-M-Y',strtotime($offslot_result[0]->start_date)).' To '.date('d-M-Y',strtotime($offslot_result[0]->end_date));
                                }
                        }
                        if($discount){
                                $discount = '<div class="tag position-absolute">' . $discount . ' on all menu</div>';
                            }
                        $output .= '
                                                 <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                              <div class="card br-0 custom_card border-0 mb-5">
                                  <div class="card_img position-relative">
                                  '.$discount.'
                                     <a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                      <div class="card-body px-0">
                                          <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> '.$distance.'</h4> 
                                         <ul class="list-unstyled big_size">
                                          <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                        </ul>
                                        <div class="validity">
                                            '.$validity.'
                                        </div>
                                        <div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeresslot_'.$uid.'">'.$slot_data.'</div></div>
         
                                        

                                      </div>
                                    </div>  

                            </div>
                                                        ';
                        }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            case 1:
                $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
                $lat = $lng = $user_id = "";
                $user_id = Session::get('userdata')->id;
                $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

                $lat = $user_Data[0]->latitude;
                $lng = $user_Data[0]->longitude;

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.first_name LIKE '%$search_keyword%' OR tbl_users.last_name LIKE '%$search_keyword%' OR tbl_users.cuisines LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100 limit 0,100");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }
                
                if($restaurant_result){
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }
                    
                break;

            default: $output = "Sorry! No match Found";
                return $output;
                break;
        }

        
    }

    public function showSearchPickup() {
        if (!Session::has(('userdata'))) {
            $user_id = '';
        } else {
            $user_id = Session::get('userdata')->id;
        }
        
        $offslot_result = array();
        $page_name =  $slot_data = "";
        $day = date('D');
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
        } else if($day == 'tue' ){
            $d_index = 1;
        } else if($day == 'wed' ){
            $d_index = 2;
        } else if($day == 'thu' ){
            $d_index = 3;
        } else if($day == 'fri' ){
            $d_index = 4;
        } else if($day == 'sat' ){
            $d_index = 5;
        } else if($day == 'sun' ){
            $d_index = 6;
        } else {
            $d_index = 0;
        }

      $c_time = date('H:i');
        $input = Input::all();
        $data = trim($input['serach']);
        $map_status = trim($input['map_status']);
        $page_name = trim($input['page_name']);
        $search_keyword = trim($data);
        switch ($map_status) {
            case 0:
                if (!empty($data)) {

                    $search = DB::table("users")
                            ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                            ->whereRaw("FIND_IN_SET('Pickup',tbl_users.service_offered)")
                            ->where(function ($query) use ($search_keyword) {
                                $query->where('users.first_name', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.cuisines', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('menu_item.item_name', 'LIKE', '%' . $search_keyword . '%');
                            })->Where(function($query) {
                            //    $query->where("offers.type", "=", 'percentage');
                              //  $query->where("offers.flagstatus", "=", '1');
                                //$query->where("offers.status", "=", '1');
                            })
                            //->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->join('menu_item','menu_item.user_id', '=', 'users.id')
                            ->where("opening_hours.open_close", "=", '1')
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                } else {

                    $search = DB::table("users")
                            ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                            ->whereRaw("FIND_IN_SET('table reservations',tbl_users.service_offered)")
                            ->where("offers.type", "=", 'percentage')
                            //->where("offers.flagstatus", "=", '1')
                            //->where("offers.status", "=", '1')
                            //->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->where("opening_hours.open_close", "=", '1')
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                }
                if (!empty($search)) {
                    $output = $discount = "";
                    foreach ($search as $data) {
                        if (!empty($data->profile_image)) {
                            $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                        } else {
                            $img = HTML::image("public/listingimg/food_a.png");
                        }

                        $uid = $data->userid;

                        $datarev = DB::table('reviews')
                                ->where('user_id', '=', $uid)
                                ->get();

                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();
                        $cnd = array(
                            'user_id' => $uid,
                            'offers.status' => '1',
                            'offers.flagstatus' => '1',
                            'offers.type' => 'percentage'
                        );
                        
                        $revsdays = $starttime = $endtime = $comment = "";
                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";

                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                            if($user_id){
                            $distance = '<span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span>';
                        } else {
                            $distance = '';
                        }
                        $validity = $t_discount = $discount = "";
                            $offslot_result = DB::table('offers')
                                    ->where($cnd)
                                    ->select('offers_slot.*')
                                    ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                    ->orderBY('offers_slot.id', 'ASC')
                                    ->limit(1)
                                    ->get();

                            if(isset($offslot_result[0])){
                                $t_discount = $offslot_result[0]->discount;
                            }
                           
                            $discount = ($t_discount != '') ? '<b>'.$t_discount.'% off</b>' : "";
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                         ' . $discount . '
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                         ' . $discount . '</label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                                <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                         ' . $discount . '
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                         ' . $discount . '</label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            if($offslot_result){
                                    $validity = "Validity: ".date('d-M-Y',strtotime($offslot_result[0]->start_date)).' To '.date('d-M-Y',strtotime($offslot_result[0]->end_date));
                                }
                        }
                        if($discount){
                                $discount = '<div class="tag position-absolute">' . $discount . ' on all menu</div>';
                            }
                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                          <div class="card_img position-relative">
                                  '.$discount . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> '.$distance.'</h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'
                                . '<div class="validity">
                                            '.$validity.'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timepickslot_'.$uid.'">'.$slot_data.'</div></div>
        
                                                                         </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;
            
            case 1:
                $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
                $lat = $lng = $user_id = "";
                $user_id = Session::get('userdata')->id;
                $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

                $lat = $user_Data[0]->latitude;
                $lng = $user_Data[0]->longitude;

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.first_name LIKE '%$search_keyword%' OR tbl_users.last_name LIKE '%$search_keyword%' OR tbl_users.cuisines LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100 limit 0,100");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }
                
                if($restaurant_result){
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }
                    
                break;

            default: $output = "Sorry! No match Found";
                return $output;
                break;
        }
        
    }

    public function showFilterdata() {
        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $offdata_result = array();
        $lat = $lng = $user_id = $slot_data = $page_name = "";

        $input = Input::all();
        $price = $input['price'];
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }

        $c_time = date('H:i');
        $discount = $input['discount'];
        $datadisc = explode("-", $discount);

        $distance = $input['distance'];
        $distance = explode("-", $distance);


        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;


        if ($price == 0) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND FIND_IN_SET('delivery',tbl_users.service_offered) AND  (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]')  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` DESC");
        } else if ($price == 1) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id  where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND FIND_IN_SET('delivery',tbl_users.service_offered) AND (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]')  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` ASC");
        } else {
            $filter = DB::table('offers')
                    ->join('users', 'offers.user_id', '=', 'users.id')
                    ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                    ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                    ->whereRaw("FIND_IN_SET('delivery',tbl_users.service_offered)")
                    ->where("users.user_type", "=", 'Restaurant')
                    ->where("users.status", "=", '1')
                    ->where('opening_hours.open_close', '=', '1')
                    ->where("offers.flagstatus", "=", '1')
                    // ->join('cuisines', 'cuisines.user_id', '=', 'users.id')
                    ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                    //->whereBetween('offers.discount', array($datadisc[0], $datadisc[1]))
                    ->groupBy('users.id')
                    ->get();
        }

        switch ($map_status) {
            case 0:
                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;

                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();
$start_time = explode(',',$datahr[0]->start_time);
$end_time = explode(',',$datahr[0]->end_time);
                        $open_days = explode(',',$datahr[0]->open_days);
                        	$d_index = array_search($day, $open_days);
                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {

                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $start_time[$d_index]) && ($c_time <= $end_time[$d_index])){
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date('h:i A',strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date('h:i A',strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date('h:i A',strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date('h:i A',strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        

                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                           <div class="card_img position-relative"> 
                                                          <div class="tag position-absolute">' . $data->discount . '
                                                        % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
         <!--<ul class="list-unstyled">
                                                    <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                    </li>
                                                    <li><a>Start Time</a>' . $starttime . '
                                                    </li>
                                                      <li><a>End Time</a>' . $endtime . '
                                                      </li>
                                                       <li><a>Reviews</a>' . $comment . '
                                                       </li>
                                                    </ul>-->
                                                                         <ul class="list-unstyled">
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                            </ul>
                                                            <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div>
                                        <div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeslot_'.$uid.'">'.$slot_data.'</div></div>
                                                                         </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                foreach ($filter as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }
                if ($filter) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;
            default:
                $output = "Sorry! No match Found";
                return $output;
                break;
        }
    }

    public function showresFilterdata() {
        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $offslot_result = array();
        $lat = $lng = $user_id = $slot_data = $page_name = "";
        $input = Input::all();
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $price = $input['price'];
        $map_stauts = $input['map_status'];
        $page_name = $input['page_name'];

        $discount = $input['discount'];
        $datadisc = explode("-", $discount);
        $distance = $input['distance'];
        $distance = explode("-", $distance);

        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;


        if ($price == 0) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('table reservations',tbl_users.service_offered) AND FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]')  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` DESC");
        } else if ($price == 1) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('table reservations',tbl_users.service_offered) AND FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]')  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` ASC");
        } else {
            $filter = DB::table('offers')
                    ->join('users', 'offers.user_id', '=', 'users.id')
                    ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                    ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                    ->whereRaw("FIND_IN_SET('table reservations',tbl_users.service_offered)")
                    ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                    ->where("offers.flagstatus", "=", '1')
                    ->groupBy('users.id')
                    ->get();
        }

        switch ($map_stauts) {
            case 0 :
                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        if (!empty($data->profile_image)) {
                            $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                        } else {
                            $img = HTML::image("public/listingimg/food_a.png");
                        }
                        $uid = $data->userid;

                        $datarev = DB::table('reviews')
                                ->where('user_id', '=', $uid)
                                ->get();

                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();
$start_time = explode(',',$datahr[0]->start_time);
$end_time = explode(',',$datahr[0]->end_time);
                        $open_days = explode(',',$datahr[0]->open_days);
                        	$d_index = array_search($day, $open_days);

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
                        
                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";
                        $parameter = Crypt::encrypt($data->id);

                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {

                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        $parameter = Crypt::encrypt($data->id);

                        if(($c_time >= $start_time[$d_index]) && ($c_time <= $end_time[$d_index])){
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                       
                                        <li class="d-inline-block">

                                            <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                            <label for="discount"><span>'.$p_slot_time.'</span>
                                                <b>'.$offslot_result[0]->discount.'% off</b>
                                            </label>
                                        </li>
                                        <li class="d-inline-block">
                                            <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                            <label for="radioBanana"><span>'.$c_slot_time.'</span>
                                                <b>'.$offslot_result[0]->discount.'% off</b></label>
                                        </li>
                                        <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                            <label for="radioOrange"><span>'.$f_slot_time.'</span>
                                                <b>'.$offslot_result[0]->discount.'% off</b></label>
                                        </li>
                                    </ul>';
                        }
                        
                        
                        $output .= '
                                                     <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="card br-0 custom_card border-0 mb-5">
                                      <div class="card_img position-relative">
                                      <div class="tag position-absolute">' . $data->discount . '
                                         % off on all menu
                                      </div>
                                         <a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                          <div class="card-body px-0">
                                              <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4> 
                                             <ul class="list-unstyled big_size">
                                              <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                            </ul>
                                             <!--<ul class="list-unstyled">
                                                        <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                        </li>
                                                        <li><a>Start Time</a>' . $starttime . '
                                                        </li>
                                                          <li><a>End Time</a>' . $endtime . '
                                                          </li>
                                                           <li><a>Reviews</a>' . $comment . '
                                                           </li>
                                                        </ul>-->
                                                        <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div>
                                            <div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeresslot_'.$uid.'">'.$slot_data.'</div></div>

                                          </div>
                                        </div>  

                                </div>
                                                            ';
                    }
                    
                        }
                    return $output;
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }
                break;

            case 1:
                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                foreach ($filter as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }
                if ($filter) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }
                break;

            default:
                $output = "Sorry! No match Found";
                return $output;
                break;
        }
    }

    public function showpickFilterdata() {
        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
        $lat = $lng = $user_id = $slot_data = $page_name = "";
        $input = Input::all();
        $price = $input['price'];
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $discount = $input['discount'];
        $datadisc = explode("-", $discount);
        $distance = $input['distance'];
        $distance = explode("-", $distance);

        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        if ($price == 0) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('Pickup',tbl_users.service_offered) AND FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]')  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1'  group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` DESC");
        } else if ($price == 1) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('Pickup',tbl_users.service_offered) ANDFIND_IN_SET('$day', tbl_opening_hours.open_days) AND  (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]')  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` ASC");
        } else {
            $filter = DB::table('offers')
                    ->join('users', 'offers.user_id', '=', 'users.id')
                    ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                    ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                    ->whereRaw("FIND_IN_SET('Pickup',tbl_users.service_offered)")
                    // ->join('cuisines', 'cuisines.user_id', '=', 'users.id')
                    ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                    //->whereBetween('offers.discount', array($datadisc[0], $datadisc[1]))
                    ->where("users.user_type", "=", 'Restaurant')
                    ->where("users.status", "=", '1')
                    ->where('opening_hours.open_close', '=', '1')
                    ->where("offers.flagstatus", "=", '1')
                    ->groupBy('users.id')
                    ->get();
            
        }
        // ->get();

        switch ($map_status) {
            case 0:
                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        if (!empty($data->profile_image)) {
                            $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                        } else {
                            $img = HTML::image("public/listingimg/food_a.png");
                        }
                        $uid = $data->userid;

                        $datarev = DB::table('reviews')
                                ->where('user_id', '=', $uid)
                                ->get();

                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";
                        $parameter = Crypt::encrypt($data->id);

                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {

                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        
                        
                        $parameter = Crypt::encrypt($data->id);

                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                          <div class="card_img position-relative">
                                  <div class="tag position-absolute">' .
                                $data->discount . '% off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'
                                . '<div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div>
                                        <div class="" id="defaul_height_'.$uid.'">
                                        <div id="timepickslot_'.$uid.'">'.$slot_data.'</div></div>
                                                                          <!--<ul class="list-unstyled">
                                                    <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                    </li>
                                                    <li><a>Start Time</a>' . $starttime . '
                                                    </li>
                                                      <li><a>End Time</a>' . $endtime . '
                                                      </li>
                                                       <li><a>Reviews</a>' . $comment . '
                                                       </li>
                                                    </ul>-->
                                                                         </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            case 1:
                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                foreach ($filter as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }
                if ($filter) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }
                break;

            default:
                $output = "Sorry! No match Found";
                return $output;
                break;
        }
    }

    public function changeProfiledata() {
        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
        $slot_data = $page_name = "";
        $input = Input::all();
        
        $id = Session::get('userdata')->id;
        $user = DB::table('users')
                        ->where('id', $id)->first();
        $user_id = $user->id;

        if ($input['cust_password'] == $user->plain_pwd) {

            $pwd = $user->cust_password;
            $array = array(
                'cust_name' => $input['cust_name'],
                'cust_phone' => $input['cust_phone'],
                'cust_email' => $input['cust_email'],
                'cust_password' => $pwd,
                'modified' => date('Y-m-d H:i:s'),
            );
            DB::table('users')
                    ->where('id', $user_id)
                    ->update($array);

            $mail_data = array(
                'text' => 'Your information has been changed.',
                'name' => $user->cust_name,
                'email' => $user->cust_email,
                'contact_number' => $input['cust_phone'],
                'password' => $user->plain_pwd,
            );

            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email'], $mail_data['contact_number'])->subject('Your information has been changed');
            });

            return Redirect::to('listing')->with('success_message', 'Profile details updated successfully.');
        } else {

            $pwd = md5($input['cust_password']);
            $array = array(
                'cust_name' => $input['cust_name'],
                'cust_phone' => $input['cust_phone'],
                'cust_email' => $input['cust_email'],
                'plain_pwd' => $input['cust_password'],
                'cust_password' => $pwd,
                'modified' => date('Y-m-d H:i:s'),
            );
            DB::table('users')
                    ->where('id', $user_id)
                    ->update($array);

            $mail_data = array(
                'text' => 'Your information has been changed.',
                'name' => $user->cust_name,
                'email' => $user->cust_email,
                'contact_number' => $input['cust_phone'],
                'password' => $user->plain_pwd,
            );


            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email'], $mail_data['contact_number'])->subject('Your information has been changed');
            });

            Session::forget('userdata');
            return Redirect::to('/')->with('success_message', 'Profile details updated successfully.');
        }
    }

    public function showBest() {

        $filter = DB::table('users')
                ->join('reviews', 'reviews.user_id', '=', 'users.id')
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*', 'reviews.*')
                ->where("offers.flagstatus", "=", '1')
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1')
                ->orderBy('offers.discount', 'DESC')
                ->orderBy('reviews.rating', 'DESC')
                ->groupBy('reviews.user_id')
                ->get();



        if (!empty($filter)) {
            $output = "";
            foreach ($filter as $data) {
                if (!empty($data->profile_image)) {
                    $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                } else {
                    $img = HTML::image("public/listingimg/food_a.png");
                }

                $uid = $data->userid;

                $datarev = DB::table('reviews')
                        ->where('user_id', '=', $uid)
                        ->get();

                $datahr = DB::table('opening_hours')
                        ->where('user_id', '=', $uid)
                        ->get();

                $revsdays = "";
                $starttime = "";
                $endtime = "";
                $comment = "";


                if (!empty($datahr)) {
                    foreach ($datahr as $days) {

                        $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                        $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                        $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                    }
                } else {
                    $revsdays = "Not Availabel";
                    $starttime = "Not Availabel";
                    $endtime = "Not Availabel";
                }
                if (!empty($datarev)) {
                    foreach ($datarev as $rev) {
                        $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                    }
                } else {
                    $comment = "No Reviews";
                }

                $output .= '
				        	 <div class="col-12 col-sm-6 col-md-6 col-lg-4">
				        	 <div class="card br-0 custom_card border-0 mb-5">
				        	   <div class="card_img position-relative"> 
				        	  <div class="tag position-absolute">' . $data->discount . '
				                % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                        '</div>' .
                        '<div class="card-body px-0">
								<h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">1.2 KM</span></h4>
								     <ul class="list-unstyled big_size">
				        		<li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                        '</a></li></ul>
 <!--<ul class="list-unstyled">
                                            <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                            </li>
                                            <li><a>Start Time</a>' . $starttime . '
                                            </li>
                                              <li><a>End Time</a>' . $endtime . '
                                              </li>
                                               <li><a>Reviews</a>' . $comment . '
                                               </li>
                                            </ul>-->
 
								 <ul class="list-unstyled">
		                                        <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
		                                        <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
		                                    </ul>
								 </div>' .
                        '</div></div>';
            }
            return $output;
        } else {
            $output = "Sorry! No Data Avaialble";
            return $output;
        }
    }

    public function showResbest() {

        $filter = DB::table('users')
                ->join('reviews', 'reviews.user_id', '=', 'users.id')
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*', 'reviews.*')
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1')
                ->where("offers.flagstatus", "=", '1')
                ->orderBy('offers.discount', 'DESC')
                ->orderBy('reviews.rating', 'DESC')
                ->groupBy('reviews.user_id')
                ->get();



        if (!empty($filter)) {
            $output = "";
            foreach ($filter as $data) {

                if (!empty($data->profile_image)) {
                    $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                } else {
                    $img = HTML::image("public/listingimg/food_a.png");
                }


                $uid = $data->userid;

                $datarev = DB::table('reviews')
                        ->where('user_id', '=', $uid)
                        ->get();

                $datahr = DB::table('opening_hours')
                        ->where('user_id', '=', $uid)
                        ->get();

                $revsdays = "";
                $starttime = "";
                $endtime = "";
                $comment = "";


                if (!empty($datahr)) {
                    foreach ($datahr as $days) {

                        $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                        $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                        $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                    }
                } else {
                    $revsdays = "Not Availabel";
                    $starttime = "Not Availabel";
                    $endtime = "Not Availabel";
                }
                if (!empty($datarev)) {
                    foreach ($datarev as $rev) {
                        $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                    }
                } else {
                    $comment = "No Reviews";
                }

                $output .= '
		        		 <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                      <div class="card br-0 custom_card border-0 mb-5">
                          <div class="card_img position-relative">
                          <div class="tag position-absolute">' . $data->discount . '
                             % off on all menu
                          </div>
                             <a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                              <div class="card-body px-0">
                                  <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">1.2 KM</span></h4> 
                                 <ul class="list-unstyled big_size">
                                  <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>
                                  
                                </ul>
 <!--<ul class="list-unstyled">
                                            <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                            </li>
                                            <li><a>Start Time</a>' . $starttime . '
                                            </li>
                                              <li><a>End Time</a>' . $endtime . '
                                              </li>
                                               <li><a>Reviews</a>' . $comment . '
                                               </li>
                                            </ul>-->
 
                                <ul class="list-unstyled radio-toolbar ">
                                    <li class="d-inline-block">
                                        
                                         <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                         <label for="discount"><span>7:00 PM</span>
                                             <b>20% off</b>
                                         </label>
                                    </li>
                                    <li class="d-inline-block">
                                        <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                <label for="radioBanana"><span>7:30 PM</span>
                                             <b>20% off</b></label></li>
                                    <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                <label for="radioOrange"><span>8:00 PM</span>
                                             <b>20% off</b></label></li>
                                </ul>
                                
                              </div>
                            </div>  
                        
                    </div>
				        	';
            }
            return $output;
        } else {
            $output = "Sorry! No Data Avaialble";
            return $output;
        }
    }

    public function showPickbest() {

        $filter = DB::table('users')
                ->join('reviews', 'reviews.user_id', '=', 'users.id')
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*', 'reviews.*')
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1')
                ->where("offers.flagstatus", "=", '1')
                ->orderBy('offers.discount', 'DESC')
                ->orderBy('reviews.rating', 'DESC')
                ->groupBy('reviews.user_id')
                ->get();
        $user_id = Session::get('userdata')->id;


        if (!empty($filter)) {
            $output = "";
            foreach ($filter as $data) {
                if (!empty($data->profile_image)) {
                    $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                } else {
                    $img = HTML::image("public/listingimg/food_a.png");
                }


                $uid = $data->userid;

                $datarev = DB::table('reviews')
                        ->where('user_id', '=', $uid)
                        ->get();

                $datahr = DB::table('opening_hours')
                        ->where('user_id', '=', $uid)
                        ->get();

                $revsdays = "";
                $starttime = "";
                $endtime = "";
                $comment = "";


                if (!empty($datahr)) {
                    foreach ($datahr as $days) {
                        $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                        $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                        $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                    }
                } else {
                    $revsdays = "Not Availabel";
                    $starttime = "Not Availabel";
                    $endtime = "Not Availabel";
                }
                if (!empty($datarev)) {
                    foreach ($datarev as $rev) {
                        $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                    }
                } else {
                    $comment = "No Reviews";
                }

                $output .= '
				        	 <div class="col-12 col-sm-6 col-md-6 col-lg-4">
				        	 <div class="card br-0 custom_card border-0 mb-5">
				        	  <div class="card_img position-relative">
                          <div class="tag position-absolute">' .
                        $data->discount . '% off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                        '</div>' .
                        '<div class="card-body px-0">
								<h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">{{ App::make("ListingController")->getMiles($user_id,$uid) }} KM</span></h4>
								     <ul class="list-unstyled big_size">
				        		<li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                        '</a></li></ul>
 <!--<ul class="list-unstyled">
                                            <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                            </li>
                                            <li><a>Start Time</a>' . $starttime . '
                                            </li>
                                              <li><a>End Time</a>' . $endtime . '
                                              </li>
                                               <li><a>Reviews</a>' . $comment . '
                                               </li>
                                            </ul>-->
 
								 </div>' .
                        '</div></div>';
            }
            return $output;
        } else {
            $output = "Sorry! No Data Avaialble";
            return $output;
        }
    }

    public function showPrice() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $offslot_result = array();
        $lat = $lng = $user_id = $page_name = $slot_data = "";

        $input = Input::all();
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $map_status = trim($input['map_status']);
        $page_name = trim($input['page_name']);
        switch ($map_status) {
            case 0:
                $filter = DB::table('users')
                        ->join('offers', 'offers.user_id', '=', 'users.id')
                        ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.*')
                        ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                        ->whereRaw("FIND_IN_SET('delivery',tbl_users.service_offered)")
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where("offers.flagstatus", "=", '1')
                        ->where("opening_hours.open_close", "=", '1')
                        ->orderBy('users.average_price', 'ASC')
                        ->groupBy('offers.user_id')
                        ->get();
                $user_id = Session::get('userdata')->id;
                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar" >
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>';
                            }
                        } else {
                            
                        }


                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                           <div class="card_img position-relative"> 
                                                          <div class="tag position-absolute">' . $data->discount . '% off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $data->userid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
         <!--<ul class="list-unstyled">
                                                    <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                    </li>
                                                    <li><a>Start Time</a>' . $starttime . '
                                                    </li>
                                                      <li><a>End Time</a>' . $endtime . '
                                                      </li>
                                                       <li><a>Reviews</a>' . $comment . '
                                                       </li>
                                                    </ul>-->

                                                                         <ul class="list-unstyled">
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                            </ul>
                                                            <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeslot_'.$uid.'">'.$slot_data.'</div></div>
                                                                         </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }

                break;

            case 1:
                $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
                $lat = $lng = $user_id = "";
                $user_id = Session::get('userdata')->id;
                $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

                $lat = $user_Data[0]->latitude;
                $lng = $user_Data[0]->longitude;

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_users.average_price ASC limit 0,100 ");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }
                if ($restaurant_result) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default: $output = "Sorry! No match Found";
                return $output;
                break;
        }
    }

    public function showResprice() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $offslot_result = array();
        $lat = $lng = $user_id = $slot_data = "";

        $input = Input::all();
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        switch ($map_status) {
            case 0;
                $filter = DB::table('users')
                        ->join('offers', 'offers.user_id', '=', 'users.id')
                        ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.*')
                        ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                        ->whereRaw("FIND_IN_SET('Table reservations',tbl_users.service_offered)")
                        ->where("offers.type", "=", 'percentage')
                        ->where("offers.flagstatus", "=", '1')
                        ->where("offers.status", "=", '1')
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where("opening_hours.open_close", "=", '1')
                        ->orderBy('users.average_price', 'ASC')
                        ->groupBy('users.id')
                        ->get();
                                

                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {
                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>';
                            }
                        } else {
                            
                        }
                        

                        $output .= '
                                                     <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="card br-0 custom_card border-0 mb-5">
                                      <div class="card_img position-relative">
                                      <div class="tag position-absolute">' . $data->discount . '
                                         % off on all menu
                                      </div>
                                         <a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                          <div class="card-body px-0">
                                              <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $data->user_id) . ' KM</span></h4> 
                                             <ul class="list-unstyled big_size">
                                              <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                            </ul>
                                            <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeresslot_'.$uid.'">'.$slot_data.'</div></div>
             <!--<ul class="list-unstyled">
                                                        <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                        </li>
                                                        <li><a>Start Time</a>' . $starttime . '
                                                        </li>
                                                          <li><a>End Time</a>' . $endtime . '
                                                          </li>
                                                           <li><a>Reviews</a>' . $comment . '
                                                           </li>
                                                        </ul>-->

                                            

                                          </div>
                                        </div>  

                                </div>
                                                            ';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }
                break;

            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_users.average_price DESC limit 0,100 ");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($restaurant_result) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showPickprice() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
        $lat = $lng = $user_id = $page_name = $slot_data = "";

        $input = Input::all();
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        switch ($map_status) {
            case 0:
                $filter = DB::table('users')
                        ->join('offers', 'offers.user_id', '=', 'users.id')
                        ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.*')
                        ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                        ->whereRaw("FIND_IN_SET('Pickup',tbl_users.service_offered)")
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where("opening_hours.open_close", "=", '1')
                        ->where("offers.flagstatus", "=", '1')
                        ->orderBy('users.average_price', 'ASC')
                        ->groupBy('offers.user_id')
                        ->get();


                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {
                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        
                        $output .= '
                                                             <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                             <div class="card br-0 custom_card border-0 mb-5">
                                                               <div class="card_img position-relative"> 
                                                              <div class="tag position-absolute">' . $data->discount . '
                                                            % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                            <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                                 <ul class="list-unstyled big_size">
                                                                    <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
             <!--<ul class="list-unstyled">
                                                        <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                        </li>
                                                        <li><a>Start Time</a>' . $starttime . '
                                                        </li>
                                                          <li><a>End Time</a>' . $endtime . '
                                                          </li>
                                                           <li><a>Reviews</a>' . $comment . '
                                                           </li>
                                                        </ul>-->
                                                                             <ul class="list-unstyled">
                                                                    <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                    <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                                </ul>
                                                                <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timepickslot_'.$uid.'">'.$slot_data.'</div></div>
                                                                             </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }

                break;

            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_users.average_price ASC limit 0,100 ");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($restaurant_result) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showDistance() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $slotdata_result = array();
        $lat = $lng = $user_id = $slot_data = "";
        $input = Input::all();
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $map_status = trim($input['map_status']);
        $page_name = trim($input['page_name']);
       
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id  WHERE  FIND_IN_SET('delivery',tbl_users.service_offered) AND FIND_IN_SET('$day', tbl_opening_hours.open_days)  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` order by distance ASC");


        switch ($map_status) {
            case 0:

                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        if (!empty($data->profile_image)) {
                            $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                        } else {
                            $img = HTML::image("public/listingimg/food_a.png");
                        }

                        $uid = $data->userid;

                        $datarev = DB::table('reviews')
                                ->where('user_id', '=', $uid)
                                ->get();

                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";
                        $dist = App::make("ListingController")->getMiles($user_id, $uid);

                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {

                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                            
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar" >
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        
                        $output .= '
                                     <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                     <div class="card br-0 custom_card border-0 mb-5">
                                       <div class="card_img position-relative"> 
                                      <div class="tag position-absolute">' . $data->discount . '
                                        % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                        <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . $dist . ' KM</span></h4>
                                             <ul class="list-unstyled big_size">
                                        <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
         

                                         <ul class="list-unstyled">
                                                        <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                    </ul>
                                                    <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeslot_'.$uid.'">'.$slot_data.'</div></div>
                                         </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }

                break;

            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);


                foreach ($filter as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($filter) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showresDistance() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $slotdata_result = array();
        $lat = $lng = $user_id = $slot_data = "";
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id Where FIND_IN_SET('table reservations',tbl_users.service_offered) AND FIND_IN_SET('$day', tbl_opening_hours.open_days)  AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` order by distance ASC");

        $input = Input::all();
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];

        switch ($map_status) {
            case 0:
                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");
                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";
                        $dist = App::make("ListingController")->getMiles($user_id, $uid);

                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {

                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar" >
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        
                        $output .= '
                                     <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="card br-0 custom_card border-0 mb-5">
                                      <div class="card_img position-relative">
                                      <div class="tag position-absolute">' . $data->discount . '
                                         % off on all menu
                                      </div>
                                         <a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                          <div class="card-body px-0">
                                              <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . $dist . 'KM</span></h4> 
                                             <ul class="list-unstyled big_size">
                                              <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                            </ul>
                                            <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeresslot_'.$uid.'">'.$slot_data.'</div></div>
             <!--<ul class="list-unstyled">
                                                        <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                        </li>
                                                        <li><a>Start Time</a>' . $starttime . '
                                                        </li>
                                                          <li><a>End Time</a>' . $endtime . '
                                                          </li>
                                                           <li><a>Reviews</a>' . $comment . '
                                                           </li>
                                                        </ul>-->

                                            

                                          </div>
                                        </div>  

                                </div>
                                        ';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }

                break;

            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);


                foreach ($filter as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($filter) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showpickDistance() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $slotdata_result = array();
        $lat = $lng = $user_id = $slot_data = "";
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;
        $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id  WHERE FIND_IN_SET('Pickup',tbl_users.service_offered) AND FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` order by distance ASC");

        $input = Input::all();
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        switch ($map_status) {
            case 0 :
                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");
                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";
                        $dist = App::make("ListingController")->getMiles($user_id, $uid);

                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        $output .= '
                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                         <div class="card br-0 custom_card border-0 mb-5">
                                           <div class="card_img position-relative"> 
                                          <div class="tag position-absolute">' . $data->discount . '
                                            % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                            <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . $dist . ' KM</span></h4>
                                                 <ul class="list-unstyled big_size">
                                            <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
             <!--<ul class="list-unstyled">
                                                        <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                        </li>
                                                        <li><a>Start Time</a>' . $starttime . '
                                                        </li>
                                                          <li><a>End Time</a>' . $endtime . '
                                                          </li>
                                                           <li><a>Reviews</a>' . $comment . '
                                                           </li>
                                                        </ul>-->
                                             <ul class="list-unstyled">
                                                            <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                    <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                        </ul>
                                                        <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timepickslot_'.$uid.'">'.$slot_data.'</div></div>
                                             </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }
                break;
            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);


                foreach ($filter as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($filter) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showDiscount() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $offslot_result = $slotdata_result = array();
        $lat = $lng = $user_id = $slot_data = $slot_data = "";
        
        $input = Input::all();
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        switch ($map_status) {
            case 0:
                $filter = DB::table('users')
                        ->join('offers', 'offers.user_id', '=', 'users.id')
                        ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                        ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                        ->whereRaw("FIND_IN_SET('delivery',tbl_users.service_offered)")
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where("opening_hours.open_close", "=", '1')
                        ->where("offers.flagstatus", "=", '1')
                        ->orderBy('offers.discount', 'DESC')
                        ->orderBy('users.average_price', 'DESC')
                        ->groupBy('offers.user_id')
                        ->get();
                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;

                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar" >
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        

                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                           <div class="card_img position-relative"> 
                                                          <div class="tag position-absolute">' . $data->discount . '
                                                        % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("listingController")->getMiles($user_id, $data->userid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
         <!--<ul class="list-unstyled">
                                                    <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                    </li>
                                                    <li><a>Start Time</a>' . $starttime . '
                                                    </li>
                                                      <li><a>End Time</a>' . $endtime . '
                                                      </li>
                                                       <li><a>Reviews</a>' . $comment . '
                                                       </li>
                                                    </ul>-->
                                                                         <ul class="list-unstyled">
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                            </ul>
                                                            <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeslot_'.$uid.'">'.$slot_data.'</div></div>
                                                                         </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }
                break;

            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_offers.discount DESC,tbl_users.average_price DESC limit 0,100 ");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($restaurant_result) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showResdiscount() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $slotdata_result = array();
        $lat = $lng = $user_id = $slot_data = "";
        $input = Input::all();
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        switch ($map_status) {
            case 0:
                $filter = DB::table('users')
                        ->join('offers', 'offers.user_id', '=', 'users.id')
                        ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                        ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                    ->whereRaw("FIND_IN_SET('table reservations',tbl_users.service_offered)")
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where("offers.flagstatus", "=", '1')
                        ->where("offers.flagstatus", "=", '1')
                        ->where("opening_hours.open_close", "=", '1')
                        ->orderBy('offers.discount', 'DESC')
                        ->groupBy('offers.user_id')
                        ->get();

                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {
                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");
                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        

                        $output .= '
                                                 <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                              <div class="card br-0 custom_card border-0 mb-5">
                                  <div class="card_img position-relative">
                                  <div class="tag position-absolute">' . $data->discount . '
                                     % off on all menu
                                  </div>
                                     <a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                      <div class="card-body px-0">
                                          <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4> 
                                         <ul class="list-unstyled big_size">
                                          <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                        </ul>
                                        <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timeresslot_'.$uid.'">'.$slot_data.'</div></div>
         <!--<ul class="list-unstyled">
                                                    <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                    </li>
                                                    <li><a>Start Time</a>' . $starttime . '
                                                    </li>
                                                      <li><a>End Time</a>' . $endtime . '
                                                      </li>
                                                       <li><a>Reviews</a>' . $comment . '
                                                       </li>
                                                    </ul>-->
                                        

                                      </div>
                                    </div>  

                            </div>
                                                        ';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }

                break;

            case 1:

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_offers.discount DESC limit 0,100 ");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($restaurant_result) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showPickdiscount() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = $offslot_result = array();
        $lat = $lng = $user_id = $slot_data = "";

        $input = Input::all();
        $map_status = $input['map_status'];
        $page_name = $input['page_name'];
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;
        switch ($map_status) {
            case 0:
                $filter = DB::table('users')
                        ->join('offers', 'offers.user_id', '=', 'users.id')
                        ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                        ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                        ->whereRaw("FIND_IN_SET('Pickup',tbl_users.service_offered)")
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where('opening_hours.open_close', '=', '1')
                        ->where("offers.flagstatus", "=", '1')
                        ->orderBy('offers.discount', 'DESC')
                        ->groupBy('offers.user_id')
                        ->get();


                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {

                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");
                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->get();

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }

                        if(($c_time >= $datahr[$d_index]->start_time) && ($c_time <= $datahr[$d_index]->end_time)){
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.id', $data->id)
                               ->where('offers_slot.status', '1')
                                ->select('offers_slot.*')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->orderBY('offers_slot.id', 'ASC')
                                ->limit(1)
                                ->get();
                        
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar" >
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_'.$uid.'_'.$offslot_result[0]->offer_id.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . date("h:i A", strtotime($offslot_result[0]->start_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . date("h:i A", strtotime($offslot_result[0]->end_time)) . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>
                                            <button class="bg_none more" style="display:block" onclick="showSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View More</button>
                                            <button class="bg_none less" style="display:none"  onclick="hideSlot(this)" id="more_' . $uid . '_' . $offslot_result[0]->offer_id . '" >View Less</button>';
                            }
                        } else {
                            
                        }
                        
                        $output .= '
                                                             <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                             <div class="card br-0 custom_card border-0 mb-5">
                                                               <div class="card_img position-relative"> 
                                                              <div class="tag position-absolute">' . $data->discount . '
                                                            % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                            <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                                 <ul class="list-unstyled big_size">
                                                                    <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
             <!--<ul class="list-unstyled">
                                                        <li class="d-inline-block"><a>Open Days</a>' . $revsdays . '
                                                        </li>
                                                        <li><a>Start Time</a>' . $starttime . '
                                                        </li>
                                                          <li><a>End Time</a>' . $endtime . '
                                                          </li>
                                                           <li><a>Reviews</a>' . $comment . '
                                                           </li>
                                                        </ul>-->
                                                                             <ul class="list-unstyled">
                                                                    <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                    <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                                </ul>
                                                                <div class="validity">
                                            Validity: '.date('d-M-Y',strtotime($data->start_date)).' To '.date('d-M-Y',strtotime($data->end_date)).'
                                        </div><div class="" id="defaul_height_'.$uid.'">
                                        <div id="timepickslot_'.$uid.'">'.$slot_data.'</div></div>
                                                                             </div>' .
                                '</div></div>';
                    }
                    }
                    return $output;
                } else {
                    $output = "Sorry! No Data Avaialble";
                    return $output;
                }

                break;

            case 1:
                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_offers.discount DESC limit 0,100 ");
                foreach ($restaurant_result as $rr) {
                    array_push($address_name, $rr->first_name);
                    array_push($address_slug_name, $rr->slug);
                    array_push($address, str_replace("'", '', $rr->address));
                    array_push($profile_image, $rr->profile_image);
                    array_push($distance, $this->getMiles($user_id, $rr->id));
                    array_push($cuisines, $rr->cuisines);
                    array_push($address_lat, $rr->latitude);
                    array_push($address_lng, $rr->longitude);
                }

                if ($restaurant_result) {
                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }

                break;

            default:
                $output = "Sorry! No Data Avaialble";
                return $output;
                break;
        }
    }

    public function showLocation() {
        if (!Session::has(('userdata'))) {
            $user_id = '';
        } else {
            $user_id = Session::get('userdata')->id;
        }
        $input = Input::all();
        $day = strtolower(date('D'));
        $d_index = 0;
        if($day == 'mon'){
            $d_index = 0;
          } else if($day == 'tue' ){
              $d_index = 1;
          } else if($day == 'wed' ){
              $d_index = 2;
          } else if($day == 'thu' ){
              $d_index = 3;
          } else if($day == 'fri' ){
              $d_index = 4;
          } else if($day == 'sat' ){
              $d_index = 5;
          } else if($day == 'sun' ){
              $d_index = 6;
          } else {
              $d_index = 0;
          }
        
        $c_time = date('H:i');
        $search_keyword = trim($input['locate']);
        $map_status = trim($input['map_status']);
        $res_status = trim($input['res_status']);
        
        switch ($map_status) {
            case 0:
                if ($search_keyword) {
                    $search = DB::table("users")
                            ->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                            ->where(function ($query) use ($search_keyword) {
                                $query->Where('users.city', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.state', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.address', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.zipcode', 'LIKE', '%' . $search_keyword . '%');
                            })->Where(function($query) {
                                $query->where("offers.type", "=", 'percentage');
                                $query->where("offers.flagstatus", "=", '1');
                                $query->where("offers.status", "=", '1');
                            })
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->where("opening_hours.open_close", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                } else {
                    $search = DB::table("users")
                            ->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                            ->where("offers.type", "=", 'percentage')
                            ->where("offers.flagstatus", "=", '1')
                            ->where("offers.status", "=", '1')
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->where("opening_hours.open_close", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                }
           
                

                if (!empty($search)) {
                    $output = "";
                    foreach ($search as $data) {
                        if (!empty($data->profile_image)) {
                            $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image);
                        } else {
                            $img = HTML::image("public/listingimg/food_a.png");
                        }

                        $uid = $data->userid;

                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();

                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->where("opening_hours.open_close", "=", '1')->orderBy('id','DESC')->first();
                        if($datahr){
                        $days = explode(',',$datahr->open_days);
                        $d_index = array_search($day,$days);
                         
                        $start_time_array = explode(',',$datahr->start_time);
                        $end_time_array = explode(',',$datahr->end_time);

                        $revsdays = "";
                        $starttime = "";
                        $endtime = "";
                        $comment = "";


                        /*if (!empty($datahr)) {
                            foreach ($datahr as $days) {
                                $revsdays = $revsdays . "<span>" . $days->open_days . "</span>";
                                $starttime = $starttime . "<span>" . $days->start_time . "</span>";
                                $endtime = $endtime . "<span>" . $days->end_time . "</span>";
                            }
                        } else {
                            $revsdays = "Not Availabel";
                            $starttime = "Not Availabel";
                            $endtime = "Not Availabel";
                        }*/
                        if (!empty($datarev)) {
                            foreach ($datarev as $rev) {
                                $comment = $comment . "<span>" . $rev->comment . "</span>" . "<br>";
                            }
                        } else {
                            $comment = "No Reviews";
                        }
                        
                        if(($c_time >= $start_time_array[0]) && ($c_time <= $end_time_array[0])){
                        $distance = "";
                        if($user_id){
                            $distance = '<span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span>';
                        } 
                        if ($res_status == '1') {
                            $p = '<ul class="list-unstyled radio-toolbar ">
                                        <li class="d-inline-block">

                                            <input type="radio" id="discount" name="radioFruit" value="apple" checked="">
                                            <label for="discount"><span>7:00 PM</span>
                                                <b>' . $data->discount . '%</b>
                                            </label>
                                        </li>
                                        <li class="d-inline-block">
                                            <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                            <label for="radioBanana"><span>7:30 PM</span>
                                                <b>' . $data->discount . '</b></label></li>
                                        <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                            <label for="radioOrange"><span>8:00 PM</span>
                                                <b>' . $data->discount . '</b></label></li>
                                    </ul>';
                        } else {
                            $p = '<ul class="list-unstyled">
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  $ ' . $data->delivery_cost . '</a></li>
                                                                <li class="d-inline-block"><a href="">Min. Order  $ ' . $data->minimum_order . '</a></li>
                                                            </ul>';
                        }

                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                           <div class="card_img position-relative"> 
                                                          <div class="tag position-absolute">' . $data->discount . '
                                                        % off on all menu</div>' . '<a href=' . '/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title"><div class="product_title">' . $data->first_name . '</div><button type="button" class="btn rounded-btn">$' . $data->average_price . '</button> '.$distance.'</h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
                                        ' . $p . '
                                                                         
                                                                         </div>' .
                                '</div></div>';
                    }}
                    }
                    return $output;
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }


                break;
            case 1:
                $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
                $lat = $lng = $user_id = "";
                $user_id = Session::get('userdata')->id;
                $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

                $lat = $user_Data[0]->latitude;
                $lng = $user_Data[0]->longitude;

                array_push($address_name, "Your Location");
                array_push($address_slug_name, "#");
                array_push($address, $user_Data[0]->address);
                array_push($profile_image, "");
                array_push($cuisines, "");
                array_push($distance, "");
                array_push($address_lat, $lat);
                array_push($address_lng, $lng);

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where FIND_IN_SET('$day', tbl_opening_hours.open_days) AND  latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.city LIKE '%$search_keyword%' OR tbl_users.state LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100000 limit 0,100");
                if ($restaurant_result) {
                    foreach ($restaurant_result as $rr) {
                        array_push($address_name, $rr->first_name);
                        array_push($address_slug_name, $rr->slug);
                        array_push($address, str_replace("'", '', $rr->address));
                        array_push($profile_image, $rr->profile_image);
                        array_push($distance, $this->getMiles($user_id, $rr->id));
                        array_push($cuisines, $rr->cuisines);
                        array_push($address_lat, $rr->latitude);
                        array_push($address_lng, $rr->longitude);
                    }

                    $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                    return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
                } else {
                    $output = "Sorry! No match Found";
                    return $output;
                }


                break;

            default: $output = "Sorry! No match Found";
                return $output;
                break;
        }
    }

    public function restaurant_time() {
        $offers_data1 = array();
        $time = $_POST['time'];
        $d_index = 0;
        $s_time = date("H:i", strtotime($time));
        
        $datecon = date('Y-m-d', strtotime(str_replace('.', '/', $_POST['date'])));
        $day = date('D',strtotime($datecon));
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
        
        $data = DB::table('users')->where('users.status', "=", '1')->where("users.user_type", "=", 'Restaurant')->select('*')->get();
        foreach ($data as $val) {
            $data1 = DB::table('offers')
                    ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*','opening_hours.open_days','opening_hours.start_time','opening_hours.end_time')
                    ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                    ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                    ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                    ->where('offers.user_id', $val->id)
                    ->where('offers.status', '1')
                    ->where("offers.type", "=", 'percentage')
                    ->where('opening_hours.open_close', '1')
                    ->where('opening_hours.status', '1')
                    ->orderBY('offers.id', 'DESC')
                    ->limit(1)
                    ->get();
            
            if (!empty($data1['0'])) {

                $data3 = DB::table('users')->select('users.slug as userslug', 'users.profile_image as profileimage', 'users.*')->where('users.status', "=", '1')->where('id', $val->id)->get();
                $offers_data1[] = array_merge((array) $data1['0'], (array) $data3['0']);
            }
        }
        
        
        if (!empty($offers_data1)) { 
            $output = "";
            foreach ($offers_data1 as $data) {
                $start_time = explode(',',$data['start_time']);
                $end_time = explode(',',$data['end_time']);
                $current_time = ($time) ? $time : date('h:i A');
                $current_time = strtotime($current_time);
                $frac = 1800;

                $r = $current_time % $frac;
                $f_time = $current_time + ($frac - $r);
                $f_slot_time = date('h:i A', $f_time) ;

                $c_slot_time = strtotime($f_slot_time) - (30 * 60);
                $c_slot_time = date('h:i A', $c_slot_time);
                
                $l_time = $current_time - ($frac + $r);
                $p_slot_time = date('h:i A', $l_time);


                        $open_days = explode(',',$data['open_days']);
                        	$d_index = array_search($day, $open_days);
                
                if(($s_time >= $start_time[$d_index]) && ($s_time <= $end_time[$d_index])){
                    
                    $img = (!empty($data['profileimage'])) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data['profileimage']) : HTML::image("public/listingimg/food_a.png");

                    $output .= '
                             <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                          <div class="card br-0 custom_card border-0 mb-5">
                              <div class="card_img position-relative">
                              <div class="tag position-absolute">' . $data['discount'] . '
                                 % off on all menu
                              </div>
                                 <a href=' . '/restaurantdetail/' . $data['userslug'] . '>' . $img . '</a>' . '</div>
                                  <div class="card-body px-0">
                                      <h4 class="card-title"><div class="product_title">' . $data['first_name'] . '</div><button type="button" class="btn rounded-btn">$' . $data['average_price'] . '</button> <span class="float-right">1.2 KM</span></h4> 
                                     <ul class="list-unstyled big_size">
                                      <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data['userslug'] . '">' . str_replace(',', ' | ', $data['cuisines']) . '</a></li>

                                    </ul>

                                    <ul class="list-unstyled radio-toolbar ">
                                        <li class="d-inline-block">

                                             <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                             <label for="discount"><span>'.$p_slot_time.'</span>
                                                 <b>' . $data['discount'] . '% off</b>
                                             </label>
                                        </li>
                                        <li class="d-inline-block">
                                            <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                    <label for="radioBanana"><span>'.$c_slot_time.'</span>
                                                 <b>' . $data['discount'] . '% off</b></label></li>
                                        <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                    <label for="radioOrange"><span>'.$f_slot_time.'</span>
                                                 <b>' . $data['discount'] . '% off</b></label></li>
                                    </ul>

                                  </div>
                                </div>  

                        </div>
                                ';
                }
                
            }
            
            return ($output) ? $output : "Sorry! No match Found";
        } else {
            $output = "Sorry! No match Found";
            return $output;
        }
    }

    public function restaurant_date() {

        $date = $_POST['date'];

        //$timeconv = date("H:i", strtotime($time));
        $datecon = date('Y-m-d', strtotime(str_replace('.', '/', $date)));
        $data = DB::table('users')->where('users.status', "=", '1')->select('*')->get();
        foreach ($data as $val) {
            $data1 = DB::table('offers')
                    ->where('user_id', $val->id)
                    ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                    ->where('flagstatus', "=", 1)
                    ->where('offers_slot.start_date', "=", $datecon)
                    ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                    ->get();

            if (!empty($data1['0'])) {

                $data3 = DB::table('users')->select('users.slug as userslug', 'users.profile_image as profileimage', 'users.*')->where('users.status', "=", '1')->where('id', $val->id)->get();
                $offers_data1[] = array_merge((array) $data1['0'], (array) $data3['0']);
            }
        }
        //  echo"<pre>";print_r($offers_data1);exit;
        if (!empty($offers_data1)) { //print_r(Session::all());exit;
            $output = "";
            foreach ($offers_data1 as $data) {
                //print_r($data);exit;
                if (!empty($data['profileimage'])) {
                    $img = HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data['profileimage']);
                } else {
                    $img = HTML::image("public/listingimg/food_a.png");
                }

                $output .= '
                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                      <div class="card br-0 custom_card border-0 mb-5">
                          <div class="card_img position-relative">
                          <div class="tag position-absolute">' . $data['discount'] . '
                             % off on all menu
                          </div>
                             <a href=' . '/restaurantdetail/' . $data['userslug'] . '>' . $img . '</a>' . '</div>
                              <div class="card-body px-0">
                                  <h4 class="card-title"><div class="product_title">' . $data['first_name'] . '</div><button type="button" class="btn rounded-btn">$' . $data['average_price'] . '</button> <span class="float-right">1.2 KM</span></h4> 
                                 <ul class="list-unstyled big_size">
                                  <li class="d-inline-block"><a href="' . '/restaurantdetail/' . $data['userslug'] . '">' . str_replace(',', ' | ', $data['cuisines']) . '</a></li>
                                  
                                </ul>
 
                                <ul class="list-unstyled radio-toolbar ">
                                    <li class="d-inline-block">
                                        
                                         <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                         <label for="discount"><span>7:00 PM</span>
                                             <b>20% off</b>
                                         </label>
                                    </li>
                                    <li class="d-inline-block">
                                        <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                <label for="radioBanana"><span>7:30 PM</span>
                                             <b>20% off</b></label></li>
                                    <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                <label for="radioOrange"><span>8:00 PM</span>
                                             <b>20% off</b></label></li>
                                </ul>
                                
                              </div>
                            </div>  
                        
                    </div>
                            ';
            }
            return $output;
        } else {
            $output = "Sorry! No match Found";
            return $output;
        }
    }

    public function logout() {

        Session::forget('userdata');

        return Redirect::to('/');
    }

    public function restaurent_map() {
        $address_name = $address_lat = $address_lng = $address_slug_name = $address = $profile_image = $cuisines = $distance = array();
        $lat = $lng = $user_id = "";
//        $address = "Rudra, Karve Nagar, Pune, Maharashtra, India";
//        $result = $this->_Get_lat_lang_address($address);
//        echo "<pre>";print_r($result);die;
        $input = Input::all();
        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')
                ->select('users.id', 'users.address', 'users.latitude', 'users.longitude')
                ->where('users.id', $user_id)
                ->get();

        $search_keyword = $input['search'];
        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;
//        $d_pramater = "users.((ACOS(SIN('".$lat."' * PI() / 180) * SIN(users.latitude * PI() / 180) + COS('".$lat."' * PI() / 180) * COS(users.latitude * PI() / 180) * COS(('".$lng."' - users.longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance";
//        $filter = DB::table('users')
//                ->select('users.address', 'users.id','users.latitude','users.longitude',$d_pramater)
//                ->where('user_type', 'Restaurant')
//                ->where('status', '1')
//                ->get();
//      
        array_push($address_name, "Your Location");
        array_push($address_slug_name, "#");
        array_push($address, $user_Data[0]->address);
        array_push($profile_image, "");
        array_push($cuisines, "");
        array_push($distance, "");
        array_push($address_lat, $lat);
        array_push($address_lng, $lng);

        $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN  tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where tbl_opening_hours.open_close = '1' AND latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND (tbl_users.first_name LIKE '%$search_keyword%' OR tbl_users.last_name LIKE '%$search_keyword%' OR tbl_users.cuisines LIKE '%$search_keyword%' OR tbl_users.city LIKE '%$search_keyword%' OR tbl_users.state LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 1000 limit 0,25");
        //echo "<pre>";print_r($restaurant_result);die;
        foreach ($restaurant_result as $rr) {
            array_push($address_name, $rr->first_name);
            array_push($address_slug_name, $rr->slug);
            array_push($address, str_replace("'", '', $rr->address));
            array_push($profile_image, $rr->profile_image);
            array_push($distance, $this->getMiles($user_id, $rr->id));
            array_push($cuisines, $rr->cuisines);
            array_push($address_lat, $rr->latitude);
            array_push($address_lng, $rr->longitude);
        }

        $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
        return $this->layout->content = View::make('map.index')->with('title', $address_name)->with('slug', $address_slug_name)->with('lat', $address_lat)->with('lng', $address_lng)->with('user_lat', $lat)->with('user_lng', $lng)->with('address', $address)->with('profile_image', $profile_image)->with('cuisines', $cuisines)->with('distance', $distance);
    }

    function _Get_lat_lang_address($address = "") {
        $prepAddr = urlencode($address);
        $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=' . API_KEY . '&address=' . $prepAddr . '&sensor=false');
        $output = json_decode($geocode);

        if ($output->status == 'REQUEST_DENIED' || $output->status == 'ZERO_RESULTS' || $output->status == 'OVER_QUERY_LIMIT') {
            $latitude = 0;
            $longitude = 0;
        } else {
            $latitude = $output->results[0]->geometry->location->lat;
            $longitude = $output->results[0]->geometry->location->lng;
        }

        $arr = array('lat' => $latitude, 'lang' => $longitude);
        return $arr;
    }

    function saveLocation() {
        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
        $update_D = array();
        $input = Input::all();
        $user_id = Session::get('userdata')->id;
        $address = $input['address'];
        $address_d = $this->_Get_lat_lang_address($address);
        $update_D = array(
            'address' => $address,
            'latitude' => $address_d['lat'],
            'longitude' => $address_d['lang']
        );
        $result = DB::table('users')
                ->where('id', $user_id)
                ->update($update_D);
        return $data = array('status' => 0, 'msg' => 'Success');
    }

    function updateLocation() {


        if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }

        $data = DB::table('users')
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1')
                ->where("offers.type", "=", 'percentage')
                ->where("offers.status", "=", '1')
                ->where("offers.flagstatus", "=", '1')
                ->groupBy('offers.user_id')
                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.id as offersid', 'offers.*')
                ->orderBy('offers.discount', 'DESC')
                ->orderBy('offers.created', 'DESC')
                ->get();

        if (Session::has('userdata')) {
            $id = Session::get('userdata')->id;
            $profile = DB::table('users')
                    ->where("users.id", $id)
                    ->first();
            Session::put('profile', $profile);

            $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
            $this->layout->content = View::make('listing.address')->with('data', $data)->with('profile', $profile);
        }
    }

    function getMiles($user_id = NULL, $res_id = NULL) {
        $result = array();
        $user_Data = DB::table('users')
                ->select('users.id', 'users.address', 'users.latitude', 'users.longitude')
                ->where('users.id', $user_id)
                ->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;
        $result = DB::select("SELECT latitude,longitude,((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users where id = '$res_id'");

        if ((isset($result[0]->distance)) && ($result[0]->distance)) {
            return number_format($result[0]->distance, 1);
        } else {
            return "N/A";
        }
    }
    function searchLocation(){
            Session::put('location_city', $_POST['location_city']);
            if(isset($_POST['lat']) && $_POST['lat']){
                Session::put('lat', $_POST['lat']);
                Session::put('long', $_POST['long']);
            }
            Session::save();
            echo json_encode(array('status' => 1,'msg' => 'redirect'));die;
    }

}

?>
