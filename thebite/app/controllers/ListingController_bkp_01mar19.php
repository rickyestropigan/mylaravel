<?php

use Illuminate\Support\Facades\Crypt;

class ListingController extends BaseController {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    public function __construct() {

        if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }
    }

    protected $layout = 'layouts.listingdefault';

    public function showListing() {
        if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }

        $data = DB::table('users')
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1')
                ->where("offers.type", "=", 'percentage')
                ->where("offers.status", "=", '1')
                ->where("offers.flagstatus", "=", '1')
                ->where("opening_hours.open_close", "=", '1')
                ->groupBy('offers.user_id')
                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.id as offersid', 'offers.*', 'opening_hours.open_close as hrstatus', 'opening_hours.*')
                //->orderBy('offers.created', 'DESC')
                ->orderBy('offers.discount', 'DESC')
                ->orderBy('offers.created', 'DESC')
                ->get();
        
        if (Session::has('userdata')) {
            $id = Session::get('userdata')->id;
            $profile = DB::table('users')->where("users.id", $id)->first();
            if ($profile) {
                Session::put('profile', $profile);

                $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
                $this->layout->content = View::make('listing.index')->with('data', $data)->with('profile', $profile);
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
        }
    }

    public function showSearch() {
        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
        $offslot_result = array();
        $page_name = $slot_data = "";
        $input = Input::all();
        $search_keyword = trim($input['serach']);
        $map_status = trim($input['map_status']);
        $page_name = $input['page_name'];
        $user_id = Session::get('userdata')->id;
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
                                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.discount as offerdisc', 'offers.id as offersid', 'offers.*')
                                ->join('offers', 'offers.user_id', '=', 'users.id')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                ->where(function ($query) use ($search_keyword) {
                                    $query->where('users.first_name', 'LIKE', '%' . $search_keyword . '%');
                                    $query->orWhere('users.cuisines', 'LIKE', '%' . $search_keyword . '%');
                                })->Where(function($query) {
                                    $query->where("offers.type", "=", 'percentage');
                                    $query->where("offers.flagstatus", "=", '1');
                                    $query->where("offers.status", "=", '1');
                                })
                                ->where("users.user_type", "=", 'Restaurant')
                                ->where("users.status", "=", '1')
                                ->where("opening_hours.open_close", "=", '1')
                                ->orderBy('offers.discount', 'DESC')
                                ->groupBy('users.id')
                                ->get();
                        
                    }  
                } else {
                    $search = DB::table('users')
                                ->join('offers', 'offers.user_id', '=', 'users.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
                                ->where("users.user_type", "=", 'Restaurant')
                                ->where("users.status", "=", '1')
                                ->where("offers.type", "=", 'percentage')
                                ->where("offers.status", "=", '1')
                                ->where("offers.flagstatus", "=", '1')
                                ->where("opening_hours.open_close", "=", '1')
                                ->groupBy('offers.user_id')
                                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.id as offersid', 'offers.*', 'opening_hours.open_close as hrstatus', 'opening_hours.*')
                                //->orderBy('offers.created', 'DESC')
                                ->orderBy('offers.discount', 'ASC')
                                ->orderBy('offers.created', 'ASC')
                                ->get();
                }

                //set data to html and create view then return it
                if (!empty($search)) {
                    $output = "";
                    foreach ($search as $data) {
                        $img = (isset($data->profile_image) && ($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar"  id="timeslot_'.$offslot_result[0]->user_id.'">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:none" onclick="showSlot(this)" id="more_'.$offslot_result[0]->user_id.'_'.$offslot_result[0]->offersid.'" >View More</button>
                                                <button class="bg_none less" style="display:block"  onclick="hideSlot(this)" id="more_'.$offslot_result[0]->user_id.'_'.$offslot_result[0]->offersid.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                                        % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
        
                                                                         <ul class="list-unstyled">
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                            </ul>'.$slot_data.'
                                                                         </div>' .
                                '</div></div>';
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.first_name LIKE '%$search_keyword%' OR tbl_users.last_name LIKE '%$search_keyword%' OR tbl_users.cuisines LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100 limit 0,100");
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
            return Redirect::to('/');
        }
        $input = Input::all();
        $data = trim($input['serach']);
        $map_status = trim($input['map_status']);
        $search_keyword = trim($data);
        switch ($map_status) {
            case 0:
                if (!empty($data)) {

                    $search = DB::table("users")
                            ->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->where(function ($query) use ($search_keyword) {
                                $query->where('users.first_name', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.cuisines', 'LIKE', '%' . $search_keyword . '%');
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

                    $search = DB::table('users')
                                ->join('offers', 'offers.user_id', '=', 'users.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
                                ->where("users.user_type", "=", 'Restaurant')
                                ->where("users.status", "=", '1')
                                ->where("offers.status", "=", '1')
                                ->where("offers.flagstatus", "=", '1')
                                ->where("opening_hours.open_close", "=", '1')
                                ->groupBy('offers.user_id')
                                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.id as offersid', 'offers.*', 'opening_hours.open_close as hrstatus', 'opening_hours.*')
                                //->orderBy('offers.created', 'DESC')
                                ->orderBy('offers.discount', 'ASC')
                                ->orderBy('offers.created', 'ASC')->get();
                    
                    
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
                                     <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                      <div class="card-body px-0">
                                          <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">1.2 KM</span></h4> 
                                         <ul class="list-unstyled big_size">
                                          <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.first_name LIKE '%$search_keyword%' OR tbl_users.last_name LIKE '%$search_keyword%' OR tbl_users.cuisines LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100 limit 0,100");
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

    public function showSearchpickup() {
        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
        $offslot_result = array();
        $page_name = "";
        $input = Input::all();
        $data = trim($input['serach']);
        $map_status = trim($input['map_status']);
        $page_name = trim($input['page_name']);
        $search_keyword = trim($data);
        switch ($map_status) {
            case 0:
                if (!empty($data)) {

                    $search = DB::table("users")
                            ->where(function ($query) use ($search_keyword) {
                                $query->where('users.first_name', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.cuisines', 'LIKE', '%' . $search_keyword . '%');
                            })->Where(function($query) {
                                $query->where("offers.type", "=", 'percentage');
                                $query->where("offers.flagstatus", "=", '1');
                                $query->where("offers.status", "=", '1');
                            })
                            ->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->where("opening_hours.open_close", "=", '1')
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                } else {

                    $search = DB::table('users')
                                ->join('offers', 'offers.user_id', '=', 'users.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                                //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
                                ->where("users.user_type", "=", 'Restaurant')
                                ->where("users.status", "=", '1')
                                ->where("offers.status", "=", '1')
                                ->where("offers.flagstatus", "=", '1')
                                ->where("opening_hours.open_close", "=", '1')
                                ->groupBy('offers.user_id')
                                ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.id as offersid', 'offers.*', 'opening_hours.open_close as hrstatus', 'opening_hours.*')
                                //->orderBy('offers.created', 'DESC')
                                ->orderBy('offers.discount', 'ASC')
                                ->orderBy('offers.created', 'ASC')->get();
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

                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar " id="timeslot_'.$offslot_result[0]->user_id.'">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                
                                            </ul>
                                            <button class="bg_none more" style="display:none" onclick="showSlot(this)" id="more_'.$offslot_result[0]->user_id.'_'.$offslot_result[0]->offersid.'" >View More</button>
                                                <button class="bg_none less" style="display:block"  onclick="hideSlot(this)" id="more_'.$offslot_result[0]->user_id.'_'.$offslot_result[0]->offersid.'" >Less</button>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                            </ul>';
                            }
                        } else {
                            
                        }
                        
                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                          <div class="card_img position-relative">
                                  <div class="tag position-absolute">' .
                                $data->discount . '% off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">1.2 KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'.$slot_data.'
        
                                                                         </div>' .
                                '</div></div>';
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.first_name LIKE '%$search_keyword%' OR tbl_users.last_name LIKE '%$search_keyword%' OR tbl_users.cuisines LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100 limit 0,100");
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
        $lat = $lng = $user_id = $slot_data = $apge_name = "";

        $input = Input::all();
        $price = $input['price'];
        $map_status = $input['map_status'];
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
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]') AND tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` DESC");
        } else if ($price == 1) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id  where (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]') AND tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` ASC");
        } else {
            $filter = DB::table('offers')
                    ->join('users', 'offers.user_id', '=', 'users.id')
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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                                        % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
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
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                            </ul>'.$slot_data.'
                                                                         </div>' .
                                '</div></div>';
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
        $lat = $lng = $user_id = $slot_data = "";
        $input = Input::all();
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
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]') AND tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` DESC");
        } else if ($price == 1) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]') AND tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` ASC");
        } else {
            $filter = DB::table('offers')
                    ->join('users', 'offers.user_id', '=', 'users.id')
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

                        $datahr = DB::table('opening_hours')
                                ->where('user_id', '=', $uid)
                                ->get();

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

                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                         <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                          <div class="card-body px-0">
                                              <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn">' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4> 
                                             <ul class="list-unstyled big_size">
                                              <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

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
        $lat = $lng = $user_id = "";
        $input = Input::all();
        $price = $input['price'];
        $map_status = $input['map_status'];

        $discount = $input['discount'];
        $datadisc = explode("-", $discount);
        $distance = $input['distance'];
        $distance = explode("-", $distance);

        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        if ($price == 0) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]') AND tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1'  group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` DESC");
        } else if ($price == 1) {
            $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where (tbl_offers.discount BETWEEN '$datadisc[0]'  AND  '$datadisc[1]') AND tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` HAVING distance BETWEEN '$distance[0]' AND '$distance[1]' order by `tbl_users`.`average_price` ASC");
        } else {
            $filter = DB::table('offers')
                    ->join('users', 'offers.user_id', '=', 'users.id')
                    // ->join('cuisines', 'cuisines.user_id', '=', 'users.id')
                    ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                    //->whereBetween('offers.discount', array($datadisc[0], $datadisc[1]))
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

                        $datahr = DB::table('opening_hours')
                                ->where('user_id', '=', $uid)
                                ->get();

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

                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                          <div class="card_img position-relative">
                                  <div class="tag position-absolute">' .
                                $data->discount . '% off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn">' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
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
				                % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                        '</div>' .
                        '<div class="card-body px-0">
								<h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">1.2 KM</span></h4>
								     <ul class="list-unstyled big_size">
				        		<li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
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
		                                        <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
		                                        <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
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
                             <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                              <div class="card-body px-0">
                                  <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">1.2 KM</span></h4> 
                                 <ul class="list-unstyled big_size">
                                  <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>
                                  
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
                        $data->discount . '% off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                        '</div>' .
                        '<div class="card-body px-0">
								<h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">{{ App::make("ListingController")->getMiles($user_id,$uid) }} KM</span></h4>
								     <ul class="list-unstyled big_size">
				        		<li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
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
        $map_status = trim($input['map_status']);
        $page_name = trim($input['page_name']);
        switch ($map_status) {
            case 0:
                $filter = DB::table('users')
                        ->join('offers', 'offers.user_id', '=', 'users.id')
                        ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                        ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.*')
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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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

                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data = '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                                        % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $data->userid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>' . $slot_data . '
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
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                            </ul>
                                                                         </div>' .
                                '</div></div>';
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_users.average_price ASC limit 0,100 ");
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
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where("offers.flagstatus", "=", '1')
                        ->where("opening_hours.open_close", "=", '1')
                        ->orderBy('users.average_price', 'ASC')
                        ->groupBy('offers.user_id')
                        ->get();

                if (!empty($filter)) {
                    $output = "";
                    foreach ($filter as $data) {
                        $img = (!empty($data->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $data->profile_image) : HTML::image("public/listingimg/food_a.png");

                        $uid = $data->userid;
                        $datarev = DB::table('reviews')->where('user_id', '=', $uid)->get();
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                         <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                          <div class="card-body px-0">
                                              <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $data->user_id) . ' KM</span></h4> 
                                             <ul class="list-unstyled big_size">
                                              <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                            </ul>'.$slot_data.'
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_users.average_price DESC limit 0,100 ");
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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                                            % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                            <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                                 <ul class="list-unstyled big_size">
                                                                    <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'.$slot_data.'
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
                                                                    <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                    <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                                </ul>
                                                                             </div>' .
                                '</div></div>';
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_users.average_price ASC limit 0,100 ");
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
        $map_status = trim($input['map_status']);
        $page_name = trim($input['page_name']);

        $user_id = Session::get('userdata')->id;
        $user_Data = DB::table('users')->select('users.id', 'users.address', 'users.latitude', 'users.longitude')->where('users.id', $user_id)->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id  WHERE  tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` order by distance ASC");


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

                        $datahr = DB::table('opening_hours')
                                ->where('user_id', '=', $uid)
                                ->get();

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

                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                        % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . $dist . ' KM</span></h4>
                                             <ul class="list-unstyled big_size">
                                        <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'.$slot_data.'
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
                                                        <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                    </ul>
                                         </div>' .
                                '</div></div>';
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

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;

        $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id  AND tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` order by distance ASC");

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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                         <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                          <div class="card-body px-0">
                                              <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . $dist . 'KM</span></h4> 
                                             <ul class="list-unstyled big_size">
                                              <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                            </ul>'.$slot_data.'
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

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;
        $filter = DB::select("SELECT tbl_users.slug as userslug, tbl_users.id as userid, tbl_users.*, tbl_offers.discount as offerdisc, tbl_offers.*, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers on tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id  WHERE tbl_users.latitude != '' AND tbl_users.longitude != '' AND tbl_users.user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' group by `tbl_offers`.`user_id` order by distance ASC");

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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                            % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                            <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . $dist . ' KM</span></h4>
                                                 <ul class="list-unstyled big_size">
                                            <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'.$slot_data.'
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
                                                            <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                    <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                        </ul>
                                             </div>' .
                                '</div></div>';
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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                                        % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("listingController")->getMiles($user_id, $data->userid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'.$slot_data.'
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
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                            </ul>
                                                                         </div>' .
                                '</div></div>';
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_offers.discount DESC,tbl_users.average_price DESC limit 0,100 ");
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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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
                        
                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                     <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' . '</div>
                                      <div class="card-body px-0">
                                          <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4> 
                                         <ul class="list-unstyled big_size">
                                          <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) . '</a></li>

                                        </ul>'.$slot_data.'
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_offers.discount DESC limit 0,100 ");
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
                        $datahr = DB::table('opening_hours')->where('user_id', '=', $uid)->get();

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

                        
                        $offslot_result = DB::table('offers')
                                ->where('offers.user_id', $uid)
                                ->where('opening_hours.open_close', '1')
                                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                                ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                                ->orderBY('offers_slot.created', 'DESC')
                                ->limit(1)
                                ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                                ->get();
                        if ($page_name == 'slotdetails') {
                            if ($offslot_result) {
                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">
                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b></label></li>
                                                         <li class="d-inline-block"><button type="button" class="bg_none rounded-btn">BookSlot</button>
                                                </li>
                                                <button class="bg_none" onclick="myFunction(this)" id="more_3">View More</button>
                                            </ul>';
                            }
                        } else if ($page_name == 'discountdetails') {
                            if ($offslot_result) {

                                $slot_data .= '<ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>' . $offslot_result[0]->start_time . '</span>
                                                        <b>' . $offslot_result[0]->discount . '% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>' . $offslot_result[0]->end_time . '</span>
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
                                                            % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                            <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                                 <ul class="list-unstyled big_size">
                                                                    <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>'.$slot_data.'
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
                                                                    <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                    <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                                </ul>
                                                                             </div>' .
                                '</div></div>';
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id JOIN tbl_opening_hours ON tbl_opening_hours.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND tbl_opening_hours.open_close = '1' HAVING distance BETWEEN 0 AND 1000 ORDER BY tbl_offers.discount DESC limit 0,100 ");
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
            return Redirect::to('/');
        }
        $input = Input::all();
        $search_keyword = trim($input['locate']);
        $map_status = trim($input['map_status']);
        $res_status = trim($input['res_status']);
        $user_id = Session::get('userdata')->id;
        switch ($map_status) {
            case 0:
                if ($search_keyword) {
                    $search = DB::table("users")
                            ->where(function ($query) use ($search_keyword) {
                                $query->Where('users.city', 'LIKE', '%' . $search_keyword . '%');
                                $query->orWhere('users.state', 'LIKE', '%' . $search_keyword . '%');
                            })->Where(function($query) {
                                $query->where("offers.type", "=", 'percentage');
                                $query->where("offers.flagstatus", "=", '1');
                                $query->where("offers.status", "=", '1');
                            })
                            ->join('offers', 'offers.user_id', '=', 'users.id')
                            ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                            ->where("users.user_type", "=", 'Restaurant')
                            ->where("users.status", "=", '1')
                            ->where("opening_hours.open_close", "=", '1')
                            ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.discount as offerdisc', 'offers.*')
                            ->groupBy('users.id')
                            //->orderBy('offers.discount', 'DESC')
                            ->get();
                } else {
                    $search = DB::table('users')
                                    ->join('offers', 'offers.user_id', '=', 'users.id')
                                    // ->join('cuisines', 'cuisines.user_id', '=', 'users.id')
                                    //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
                                    ->where("offers.flagstatus", "=", '1')
                                    ->where("users.user_type", "=", 'Restaurant')
                                    ->groupBy('users.id')
                                    ->select('users.slug as userslug', 'users.id as userid', 'users.*', 'offers.slug as offersslug', 'offers.discount as offerdisc', 'offers.id as offersid', 'offers.*')
                                    ->orderBy('offers.discount', 'DESC')->get();
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
                                                                <li class="d-inline-block"><a href="">Free Delivery Above  <i class="fa fa-inr"></i> ' . $data->delivery_cost . '</a></li>
                                                                <li class=print_r(DB::getQueryLog());"d-inline-block"><a href="">Min. Order  <i class="fa fa-inr"></i> ' . $data->minimum_order . '</a></li>
                                                            </ul>';
                        }

                        $output .= '
                                                         <div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                                         <div class="card br-0 custom_card border-0 mb-5">
                                                           <div class="card_img position-relative"> 
                                                          <div class="tag position-absolute">' . $data->discount . '
                                                        % off on all menu</div>' . '<a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '>' . $img . '</a>' .
                                '</div>' .
                                '<div class="card-body px-0">
                                                                        <h4 class="card-title">' . $data->first_name . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data->average_price . '</button> <span class="float-right">' . App::make("ListingController")->getMiles($user_id, $uid) . ' KM</span></h4>
                                                                             <ul class="list-unstyled big_size">
                                                                <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data->userslug . '">' . str_replace(',', ' | ', $data->cuisines) .
                                '</a></li></ul>
                                        ' . $p . '
                                                                         
                                                                         </div>' .
                                '</div></div>';
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

                $restaurant_result = DB::select("SELECT tbl_users.id,first_name,tbl_users.slug,profile_image,cuisines,address,latitude,longitude, ((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users JOIN tbl_offers ON tbl_offers.user_id = tbl_users.id where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND tbl_offers.flagstatus = '1' AND ( tbl_users.city LIKE '%$search_keyword%' OR tbl_users.state LIKE '%$search_keyword%') HAVING distance BETWEEN 0 AND 100000 limit 0,100");
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

        $time = $_POST['time'];

        $timeconv = date("H:i", strtotime($time));
        $data = DB::table('users')->where('users.status', "=", '1')->select('*')->get();
        foreach ($data as $val) {
            $data1 = DB::table('offers')
                    ->where('user_id', $val->id)
                    ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                    ->where('flagstatus', "=", 1)
                    ->where('offers_slot.start_time', "=", $timeconv)
                    ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.*', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*')
                    ->get();

            if (!empty($data1['0'])) {

                $data3 = DB::table('users')->select('users.slug as userslug', 'users.profile_image as profileimage', 'users.*')->where('users.status', "=", '1')->where('id', $val->id)->get();
                $offers_data1[] = array_merge((array) $data1['0'], (array) $data3['0']);
            }
        }
        //echo"<pre>";print_r($offers_data1);exit;
        if (!empty($offers_data1)) { //print_r(Session::all());exit;
            $output = "";
            foreach ($offers_data1 as $data) {
                //  echo"<pre>";print_r($data);
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
                             <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data['userslug'] . '>' . $img . '</a>' . '</div>
                              <div class="card-body px-0">
                                  <h4 class="card-title">' . $data['first_name'] . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data['average_price'] . '</button> <span class="float-right">1.2 KM</span></h4> 
                                 <ul class="list-unstyled big_size">
                                  <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data['userslug'] . '">' . str_replace(',', ' | ', $data['cuisines']) . '</a></li>
                                  
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
                             <a href=' . '/wordpress/bitebargain/restaurantdetail/' . $data['userslug'] . '>' . $img . '</a>' . '</div>
                              <div class="card-body px-0">
                                  <h4 class="card-title">' . $data['first_name'] . '<button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i>' . $data['average_price'] . '</button> <span class="float-right">1.2 KM</span></h4> 
                                 <ul class="list-unstyled big_size">
                                  <li class="d-inline-block"><a href="' . '/wordpress/bitebargain/restaurantdetail/' . $data['userslug'] . '">' . str_replace(',', ' | ', $data['cuisines']) . '</a></li>
                                  
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

}

?>
