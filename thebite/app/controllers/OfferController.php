<?php

class OfferController extends BaseController {

    protected $layout = 'layouts.default';

    public function logincheck($url) {

        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        } else {

            $user_id = Session::get('user_id');
            $userData = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
            if (empty($userData)) {
                Session::forget('user_id');
                return Redirect::to('/');
            }
        }
    }

    // Create slug for secure URL
    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

    public function showAdmin_index() {
	   
        if (!Session::has('adminid')) {
            return Redirect::to('/');
        }
        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();

        if (!empty($input['restaurant_status'])) {
            $search_keyword = trim($input['restaurant_status']);
        }
//        print_r($input);die;
        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name')
                ->where(function ($query) use ($search_keyword) {
            $query->where('reservation_status', '=', $search_keyword);
        });



        $separator = implode("/", $separator);

        // Get all the users
        $reservations = $query->orderBy('reservations.id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Reservation/adminindex', compact('reservations'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/user/admin_index')->with('success_message', 'Service Provider deleted successfully');
        }
    }

    //In Admin payment listing
    public function showAdmin_reservation_index() {
        if (!Session::has('adminid')) {
            return Redirect::to('/');
        }

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        if (!empty($input['restaurant_status'])) {
            $search_keyword = trim($input['restaurant_status']);
        }

        $query = Reservation::sortable()
                ->join('users', 'reservations.user_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name')
                ->where(function ($query) use ($search_keyword) {
            $query->where('reservation_status', '=', $search_keyword);
        });

        $separator = implode("/", $separator);

//        $query->where('type', '=', 'Purchase');
        // Get all the users
        $reservations = $query->orderBy('reservations.id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Reservation/ajax_index', compact('reservations'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    //delete payment record by slug in admin
    public function showAdmin_deletepayment($slug = null) {
        // get menu item details
        $payments = DB::table('payments')
                ->where('slug', $slug)
                ->delete();
        return Redirect::back()->with('success_message', 'Payment record deleted successfully');
    }

    //delete payment record by slug in admin
    public function showAdmin_reservation() {
//$this->layout="";
        $input = Input::all();
//        $start = '2017-11-26';
//        $end = '2018-01-07';
        if (!empty($input['start'])) {
            $start = trim($input['start']);
        }

        if (!empty($input['end'])) {
            $end = trim($input['end']);
        }

        $reservationData = DB::table('reservations')
                ->where('reservation_date', ">=", $start)
                ->where('reservation_date', "<=", $end)
                ->get();

//        dd(DB::getQueryLog());
//        echo '<pre>';
//        print_r($reservationData);
//        die;

        $jsondata = array();
        $i = 0;
        $Newreservation = array();

        foreach ($reservationData as $reservation) {
            $date = date('Y-m-d', strtotime($reservation->reservation_date));
            if (isset($Newreservation[$date])) {
                $value = $Newreservation[$date];
                $Newreservation[$date] = array('size' => $value['size'] + $reservation->size, 'total' => $value['total'] + '1');
            } else {
                $Newreservation[$date] = array('size' => $reservation->size, 'total' => '1');
            }
        }

//        echo '<pre>';
//        print_r($Newreservation);
//        die;

        foreach ($Newreservation as $key => $value) {

            $jsondata[$i]['title'] = 'Total Reservations (' . $value['total'] . ')';
            $jsondata[$i]['start'] = $key;
            $i++;
            $jsondata[$i]['title'] = 'Total Seats (' . $value['size'] . ')';
            $jsondata[$i]['start'] = $key;
            $i++;
        }

//           echo '<pre>';
//           print_r($jsondata);
//        die;


        echo json_encode($jsondata);
//        echo '<pre>';
//        print_r($jsondata);
//        echo '<pre>';
//        print_r($reservationData);
        die;
    }

    public function showmanageoffer() {
        ini_set('memory_limit', '-1');
        $this->logincheck('offer/manageoffer');
//        print_r('hii');die;
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $currentdate = date('Y-m-d');
        $query = DB::table('offers');
        $query->where('offers.user_id', $user_id)
                ->where(function ($query) use ($currentdate) {
                    $query->where('offers.start_date', '<=', $currentdate)
                    ->where('offers.end_date', '>=', $currentdate);
                })
                ->select('offers.*');

        $records = $query->orderBy('offers.id', 'desc')->get();
//        echo '<pre>';print_r($records);
//dd(DB::getQueryLog());
        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Offers';
        $this->layout->content = View::make('/Offers/manageoffer')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showAddoffer() {

        $this->layout = false;
        $this->logincheck('offer/addoffer');
        if (Session::has('user_id')) {
            
        } else {
            return 'Error';
        }

        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $input = Input::all();
        // set content view and title
//        $this->layout->title = TITLE_FOR_PAGES . 'Add Offer';
//        $this->layout->content = View::make('/Offers/addoffer')
//                ->with('userData', $userData);

        if (!empty($input)) {
//        echo '<pre>';
//        print_r($input);
//        exit;

            $start_endtime = explode(',', $input['timegap']);
            $start_time = str_pad($start_endtime[0], 2, '0', STR_PAD_LEFT) . ':00';
            $end_time = str_pad($start_endtime[1], 2, '0', STR_PAD_LEFT) . ':00';
// echo "<pre>"; print_r($input); exit;
            $start_date = strtotime($input['startdate']);
            $end_date = strtotime($input['enddate']);
            $datediff = $end_date - $start_date;
            $days = round($datediff / (60 * 60 * 24));


            $get_start_res = DB::table('offers')
                            ->where('offers.user_id', $user_id)
                            ->where('offers.status', '1')
                            ->where(function ($query) use ($start_date, $end_date) {
                                $query->where('offers.start_date', '<=', date('Y-m-d', $start_date))
                                ->where('offers.end_date', '>=', date('Y-m-d', $start_date))
                                ->orwhere(function ($query) use ($end_date) {
                                    $query->where('offers.start_date', '<=', date('Y-m-d', $end_date))
                                    ->where('offers.end_date', '>=', date('Y-m-d', $end_date));
                                });
                            })
                            ->select('offers.*')->get();


            $save = 1;
            $period = '';
            if (!empty($get_start_res)) {
                foreach ($get_start_res as $saveornotOfr) {
                    $dbstarttime = explode(":", $saveornotOfr->start_time);
                    $dbendtime = explode(":", $saveornotOfr->end_time);
                    if ($start_endtime[0] < $dbstarttime[0] && $start_endtime[1] < $dbendtime[0]) {
                        $save = 1;
                        break;
                    } elseif ($start_endtime[0] >= $dbstarttime[0] && (($start_endtime[1] <= $dbendtime[0]) || ($start_endtime[1] > $dbendtime[0]))) {
                        $period = $saveornotOfr->start_date . " - " . $saveornotOfr->end_date . "(" . date("g:i a", strtotime($saveornotOfr->start_time)) . '-' . date('g:i a', strtotime($saveornotOfr->end_time)) . ")";
                        $save = 0;
                        break;
                    } elseif ($start_endtime[0] > $dbendtime[0] && $start_endtime[1] > $dbendtime[0]) {
                        $save = 1;
                        break;
                    }
                }
            }

            if ($save == 1) {

                if (isset($input['days']) && $input['days'] == '1') {
                    $all_time = 'All Day';
                } else {
                    $all_time = 0;
                }
                if ($input['menu'] == 1) {
                    $all_menu = 1;
                } else {
                    $all_menu = 0;
                }
                $data = array(
                    'type' => 'percentage',
                    'user_id' => $user_id,
                    'discount' => $input['offer'],
                    'offer_name' => 'test_name',
                    'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                    'all_menu' => $all_menu,
                    'note' => $input['note'],
                    'start_date' => date('Y-m-d', strtotime($input['startdate'])),
                    'start_time' => $start_time,
                    'end_date' => date('Y-m-d', strtotime($input['enddate'])),
                    'end_time' => $end_time,
                    'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                    'days' => $all_time,
                    'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                    'service_visibility' => $input['service_visibility']['0'],
                    'slug' => $this->createSlug($input['note']),
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
  	            'flagstatus' => '1',
                );

//                echo '<pre>';
//                print_r($data);
//                die;
		  if(!empty( $userData))
                {    $dataflag = array(
                        'flagstatus'=>'0'
                    );
                    DB::table('offers')
                    ->where(array('flagstatus' => '1'))
                    ->where('offers.user_id','=',$user_id)

                    ->update(
                                $dataflag
                        );
                }

                DB::table('offers')->insert(
                        $data
                );
//
                $offerid = DB::getPdo()->lastInsertId();

//                dd($offerid);
                if ($all_time != 'All Day') {

                    $enddate = strtotime($input['enddate'] . ' ' . $end_time);
                    $startdate = strtotime($input['startdate'] . ' ' . $start_time);
                    $add_mins = 30 * 60;
                    while ($startdate < $enddate) {
                        $from = date('H:i', $startdate);
                        $startdate += $add_mins;
                        $to = date('H:i', $startdate);

                        $data = array(
                            'type' => 'percentage',
                            'offer_id' => $offerid,
                            'discount' => $input['offer'],
                            'offer_name' => 'test_name',
                            'item_name_text' => '',
                            'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                            'start_date' => date('Y-m-d', strtotime($input['startdate'])),
                            'start_time' => $from,
                            'end_date' => date('Y-m-d', strtotime($input['enddate'])),
                            'end_time' => $to,
                            'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                            'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                            'slug' => $this->createSlug($input['note'] . rand(0, 5)),
                            'all_menu' => $all_menu,
                            'created' => date('Y-m-d H:i:s'),
                            'status' => '1',
                        );

                        DB::table('offers_slot')->insert(
                                $data
                        );
                    }
                } elseif ($all_time == 0) {

                    $starttime = $start_time;  // your start time
                    $endtime = $end_time;  // End time
                    $duration = '30';  // split by 30 mins

                    $array_of_time = array();
                    $starttime = strtotime($starttime); //change to strtotime
                    $endtime = strtotime($endtime); //change to strtotime


                    $add_mins = $duration * 60;

                    while ($starttime < $endtime) { // loop between time
                        $from = date("H:i", $starttime);
                        $starttime += $add_mins; // to check endtie=me
                        $to = date('H:i', $starttime);
                        $data = array(
                            'type' => 'percentage',
                            'offer_id' => $offerid,
                            'discount' => $input['offer'],
                            'offer_name' => 'test_name',
                            'item_name_text' => '',
                            'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                            'start_date' => date('Y-m-d', strtotime($input['startdate'])),
                            'start_time' => $from,
                            'end_date' => date('Y-m-d', strtotime($input['enddate'])),
                            'end_time' => $to,
                            'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                            'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                            'slug' => $this->createSlug($input['note'] . rand(0, 5)),
                            'all_menu' => $all_menu,
                            'created' => date('Y-m-d H:i:s'),
                            'status' => '1',
                        );

                        DB::table('offers_slot')->insert(
                                $data
                        );
                    }
                }

                echo json_encode(array('message' => 'success'));
                exit;
            } else {
                echo json_encode(array('message' => 'alreadyadded', 'period' => $period));
                exit;
            }
        }
    }

    public function showEditOfferSlot($slug = null) {

        $this->logincheck('offer/editofferslot');
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();



//      echo "<pre>"; print_r($offers_slot); exit;
        // get all posted input
        $input = Input::all();



        if (!empty($input)) {
            $rules = array(
                'offer' => 'required',
                'coupon' => 'required',
            );

            //echo "<pre>"; print_r($input); exit;
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return 'Error';
            } else {
                $discount = $input['offer'];

// echo "<pre>"; print_r($input); exit;

                $oferslotData = DB::table('offers_slot')
                        ->where('slug', $input['slug'])
                        ->first();

                $offer_same = DB::table('offers_slot')
                        ->where('offer_id', $oferslotData->offer_id)
                        ->where('discount', $discount)
                        ->count();

                //$get_next_offerslot = 
//                print_r($offer_same);die;
                if ($offer_same > 1) {
                    if ($input['menu'] == 1) {
                        $all_menu = 1;
                    } else {
                        $all_menu = 0;
                    }
                    $data = array(
                        'discount' => $input['offer'],
                        'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                        'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                        'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                        'all_menu' => $all_menu,
                        'disclaimer' => isset($input['disclaimer']) ? $input['disclaimer'] : '',
//                        'color' => isset($input['color']) ? $input['color'] : '#369F5C',
                        'status' => isset($input['status']) ? $input['status'] : '0',
                    );
                    $offer_color = DB::table('offercolors')
                            ->where('offer_id', $oferslotData->offer_id)
                            ->where('color', $input['color'])
                            ->count();

                    if ($offer_color > 0) {
                        if ($input['menu'] == 1) {
                            $all_menu = 1;
                        } else {
                            $all_menu = 0;
                        }
                        $nwdata = array(
                            'offer_id' => $oferslotData->offer_id,
                            'discount' => $input['offer'],
                            'all_menu' => $all_menu,
                            'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : ''
                        );

                        DB::table('offercolors')->where(array('color' => $input['color'], 'offer_id' => $oferslotData->offer_id))->update(
                                $nwdata
                        );
                    } else {
                        if ($input['menu'] == 1) {
                            $all_menu = 1;
                        } else {
                            $all_menu = 0;
                        }
                        $nwdata = array(
                            'offer_id' => $oferslotData->offer_id,
                            'discount' => $input['offer'],
                            'all_menu' => $all_menu,
                            'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                            'color' => isset($input['color']) ? $input['color'] : ''
                        );

                        DB::table('offercolors')->insert(
                                $nwdata
                        );
                    }
                } else {
                    $data = array(
                        'discount' => $input['offer'],
                        'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                        'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                        'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                        'disclaimer' => isset($input['disclaimer']) ? $input['disclaimer'] : '',
                        'status' => isset($input['status']) ? $input['status'] : '0',
                        'color' => isset($input['color']) ? $input['color'] : '#369F5C',
                    );
//                    DB::table('offers_slot')->where(array('color'=> $input['color'],'offer_id'=> $oferslotData->offer_id))->update(
//                            array('item_name' => implode(',', $input['menu_items']))
//                    );
                    $offer_color = DB::table('offercolors')
                            ->where('offer_id', $oferslotData->offer_id)
                            ->where('color', $input['color'])
                            ->count();

                    if ($offer_color > 0) {
                        if ($input['menu'] == 1) {
                            $all_menu = 1;
                        } else {
                            $all_menu = 0;
                        }
                        $nwdata = array(
                            'offer_id' => $oferslotData->offer_id,
                            'discount' => $input['offer'],
                            'all_menu' => $all_menu,
                            'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : ''
                        );
                        DB::table('offercolors')->where(array('color' => $input['color'], 'offer_id' => $oferslotData->offer_id))->update(
                                $nwdata
                        );
                    } else {
                        if ($input['menu'] == 1) {
                            $all_menu = 1;
                        } else {
                            $all_menu = 0;
                        }
                        $nwdata = array(
                            'offer_id' => $oferslotData->offer_id,
                            'discount' => $input['offer'],
                            'all_menu' => $all_menu,
                            'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                            'color' => isset($input['color']) ? $input['color'] : ''
                        );
                        DB::table('offercolors')->insert(
                                $nwdata
                        );
                    }
                }


                DB::table('offers_slot')->where('slug', $input['slug'])->update(
                        $data
                );

                return 'success';
                exit;
            }
        }
    }

    public function shownEditofferslotpage() {
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $slot_id = $input['slot_id'];
        $slot_text = $input['slot_text'];
        $offerslotdata = DB::table('offers_slot')
                ->where('id', $slot_id)
                ->first();
        $record = DB::table('offers')->where('id', $offerslotdata->offer_id)->first();
        $html = View::make('/Offers/editofferslotpage')
                        ->with('userData', $userData)->with('offerslotdata', $offerslotdata)->with('slot_text', $slot_text)->with('record', $record);
        return $html->render();
        exit;
    }

    public function showChangeVisiblity() {
        $this->layout = false;
        $input = Input::all();
        $offer_id = '';
        $type = '';
        $visible = '';
        if (!empty($input['offer_id'])) {
            $offer_id = trim($input['offer_id']);
        }
        if (!empty($input['type'])) {
            $type = trim($input['type']);
        }
        if (!empty($input['visible'])) {
            $visible = trim($input['visible']);
        }

//        echo $visible;die;
        if ($type == 'offer') {
            $msg = 'Offer successfully updated.';
            DB::table('offers')
                    ->where('id', $offer_id)
                    ->update(array('status' => $visible));
        } else {
            $msg = 'Offer Slot successfully updated.';
            DB::table('offers_slot')
                    ->where('id', $offer_id)
                    ->update(array('status' => $visible));
//             dd(DB::getQueryLog());  exit;
        }
        echo json_encode(array('valid' => TRUE, 'smessage' => $msg));
        exit;
    }

    public function showEditOffer() {

        $this->logincheck('offer/editoffer');
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();


//      echo '<pre>'; print_r($menudata);die;
        // get all posted input
        $input = Input::all();

        // set content view and title

        if (!empty($input)) { //echo '<pre>';print_r($input);
            $rules = array(
                'offer' => 'required',
//                'item_name_text' => 'required',
//                'note' => 'required',
//                'start_date' => 'required',
//                'end_date' => 'required',
                'coupon' => 'required',
            );

//            echo "<pre>"; print_r($input); exit;
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return 'Error';
            } else {
// echo "<pre>"; print_r($input); 
                $records = DB::table('menu_item')->select('menu_item.*')
                                ->where('menu_item.user_id', $user_id)->orderBy('menu_item.id', 'desc')->lists('item_name', 'id');

                $cuisine_array = array();
                $records = $cuisine_array + $records;
                //echo'<pre>'; print_r($input['menu_items']);
                $exp = $input['menu_items'];
                //echo'<pre>'; print_r(count($exp));
                //echo'<pre>'; print_r(count($records));
                if (count($exp) == count($records)) {
                    $all_menu = 1;
                } else {
                    $all_menu = 0;
                }
                $start_endtime = explode(',', $input['timegap']);
                $start_time = str_pad($start_endtime[0], 2, '0', STR_PAD_LEFT) . ':00';
                $end_time = str_pad($start_endtime[1], 2, '0', STR_PAD_LEFT) . ':00';
//                if ($input['menu'] == 1) {
//                    $all_menu = 1;
//                } else {
//                    $all_menu = 0;
//                }

                $data = array(
                    'discount' => $input['offer'],
                    'item_name_text' => '',
                    'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                    'all_menu' => $all_menu,
                    'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                    'service_visibility' => $input['service_visibility']['0']
                );
//                echo '<pre>';
//                print_r($data);
//                die;

                DB::table('offers')->where('slug', $input['slug'])->update(
                        $data
                );

                DB::table('offers_slot')->where(array('offer_id' => $input['offerid']))->update(array('status' => '1'));
                DB::table('offers_slot')->where(array('offer_id' => $input['offerid']))->whereRaw("start_time<'" . $start_time . "' OR end_time>'" . $end_time . "'")->update(array('status' => '0'));


                if (isset($input['days']) && $input['days'] == '1') {
                    $all_time = 'All Day';
                } else {
                    $all_time = 0;
                }
                if ($all_time != 'All Day') {
                    $enddate = strtotime($input['enddate'] . ' ' . $end_time);
                    $startdate = strtotime($input['startdate'] . ' ' . $start_time);
                    $add_mins = 30 * 60;
                    while ($startdate < $enddate) {
                        $from = date('H:i', $startdate);
                        $startdate += $add_mins;
                        $to = date('H:i', $startdate);
                        $timeVal = DB::table('offers_slot')->select('offers_slot.*')
                                        ->where(array('offer_id' => $input['offerid'], 'start_date' => date('Y-m-d', strtotime($input['startdate'])), 'end_date' => date('Y-m-d', strtotime($input['enddate'])), 'start_time' => $from, 'end_time' => $to))->lists('id', 'id');
                        if ($timeVal) {
                            $data = array(
                                'type' => 'percentage',
                                'offer_id' => $input['offerid'],
                                'discount' => $input['offer'],
                                'offer_name' => 'test_name',
                                'item_name_text' => '',
                                'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                                'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                                'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                            );

                            DB::table('offers_slot')->where(array('offer_id' => $input['offerid'], 'color' => null))->update(
                                    $data
                            );
                        } else {
                            $data = array(
                                'type' => 'percentage',
                                'offer_id' => $input['offerid'],
                                'discount' => $input['offer'],
                                'offer_name' => 'test_name',
                                'item_name_text' => '',
                                'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                                'start_date' => date('Y-m-d', strtotime($input['startdate'])),
                                'start_time' => $from,
                                'end_date' => date('Y-m-d', strtotime($input['enddate'])),
                                'end_time' => $to,
                                'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                                'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                                'slug' => $this->createSlug($input['note'] . rand(0, 5)),
                                'all_menu' => $all_menu,
                                'created' => date('Y-m-d H:i:s'),
                                'status' => '1',
                            );

                            DB::table('offers_slot')->insert(
                                    $data
                            );
                        }
                    }
                } elseif ($all_time == 0) {

                    $starttime = $start_time;  // your start time
                    $endtime = $end_time;  // End time
                    $duration = '30';  // split by 30 mins

                    $array_of_time = array();
                    $starttime = strtotime($starttime); //change to strtotime
                    $endtime = strtotime($endtime); //change to strtotime


                    $add_mins = $duration * 60;

//                    $strt = date("H:i", $starttime);
//                    $endd = date("H:i", $endtime);
//                    
//                    DB::table('offers_slot')->where(array('offer_id' => $input['offerid'],'start_time'< $strt,'end_time'< $endd))->delete();
                    while ($starttime < $endtime) {
                        $from = date("H:i", $starttime);
                        $starttime += $add_mins; // to check endtie=me
                        $to = date('H:i', $starttime);
                        $timeVal = DB::table('offers_slot')->select('offers_slot.*')
                                        ->where(array('offer_id' => $input['offerid'], 'start_date' => date('Y-m-d', strtotime($input['startdate'])), 'end_date' => date('Y-m-d', strtotime($input['enddate'])), 'start_time' => $from, 'end_time' => $to))->lists('id', 'id');
                        if ($timeVal) {
                            $data = array(
                                'type' => 'percentage',
                                'offer_id' => $input['offerid'],
                                'discount' => $input['offer'],
                                'offer_name' => 'test_name',
                                'item_name_text' => '',
                                'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                                'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                                'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                            );

                            DB::table('offers_slot')->where(array('offer_id' => $input['offerid'], 'color' => null))->update(
                                    $data
                            );
                        } else {
                            $data = array(
                            'type' => 'percentage',
                            'offer_id' => $input['offerid'],
                            'discount' => $input['offer'],
                            'offer_name' => 'test_name',
                            'item_name_text' => '',
                            'item_name' => isset($input['menu_items']) ? implode(',', $input['menu_items']) : '',
                            'start_date' => date('Y-m-d', strtotime($input['startdate'])),
                            'start_time' => $from,
                            'end_date' => date('Y-m-d', strtotime($input['enddate'])),
                            'end_time' => $to,
                            'above_price' => isset($input['above_price']) ? $input['above_price'] : '0',
                            'allocate' => isset($input['coupon']) ? $input['coupon'] : '0',
                            'slug' => $this->createSlug($input['note'] . rand(0, 5)),
                            'all_menu' => $all_menu,
                            'created' => date('Y-m-d H:i:s'),
                            'status' => '1',
                        );

                            DB::table('offers_slot')->insert(
                                    $data
                            );
                        }
                    }
                }

                

                return 'success';
                exit;
            }
        }
    }

    public function shownEditofferpage() {
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $offer_id = $input['offer_id'];
        $offer_slug = $input['offer_slug'];

        $offerdata = DB::table('offers')
                ->where('slug', $offer_slug)
                ->first();
        $html = View::make('/Offers/editofferpage')
                        ->with('userData', $userData)->with('offerdata', $offerdata);
        return $html->render();
        exit;
    }

    public function shownextoffer() {
        if (Session::has('user_id')) {
            
        } else {
            return 'errorlogin';
        }
        $user_id = Session::get('user_id');
        //$this->layout = false;
        $input = Input::all();

        $currentdate = date('Y-m-d', strtotime($input['current_dat']));
        $query = DB::table('offers');
        $query->where('offers.user_id', $user_id)
                ->where(function ($query) use ($currentdate) {
                    $query->where('offers.start_date', '<=', $currentdate)
                    ->where('offers.end_date', '>=', $currentdate);
                })
                ->select('offers.*');

        $records = $query->orderBy('offers.id', 'desc')->get();
        //dd(DB::getQueryLog());
        $result = View::make('/Offers/nextoffer')
                ->with('records', $records);
        return $result->render();
        exit;
    }

    public function showsearchoffer() {
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        $user_id = Session::get('user_id');
        //$this->layout = false;
        $input = Input::all();
        $dates = explode(' - ', $input['current_dat']);
        $start_date = date('Y-m-d', strtotime($dates[0]));
        $end_date = date('Y-m-d', strtotime($dates[1]));
        $query = DB::table('offers');
        $query->where('offers.user_id', $user_id)
                ->where(function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('offers.start_date', array($start_date, $end_date))
                    ->orwhereBetween('offers.end_date', array($start_date, $end_date));
                })
                ->select('offers.*');

        $records = $query->orderBy('offers.id', 'desc')->get();
        //dd(DB::getQueryLog());
        $result = View::make('/Offers/nextoffer')
                ->with('records', $records);
        return $result->render();
        exit;
    }

    public function showDeleteoffer($slug = null) {
        // get menu item details

        $input = Input::all();

        if (!empty($input['offer_slug'])) {
            $offerdata = DB::table('offers')
                    ->where('slug', $input['offer_slug'])
                    ->first();

            $id = $offerdata->id;

            DB::table('offers')->where('slug', $input['offer_slug'])->delete();
            DB::table('offers_slot')->where('offer_id', $id)->delete();

            return 'success';
            exit;
        }
    }

    public function showupdatetime() {
        $this->layout = false;
        $input = Input::all();
        $alltime = $input['alltime'];
        $timearray = array();
        $timearray = explode(',', $alltime);
        $start_time = str_pad($timearray[0], 2, '0', STR_PAD_LEFT) . ':00';
        $end_time = str_pad($timearray[1], 2, '0', STR_PAD_LEFT) . ':00';
        $tmvalue = '<div class="start_time">' . date("g:i a", strtotime($start_time)) . '</div><div class="end_time">' . date("g:i a", strtotime($end_time)) . '</div>';
        return json_encode(array('tmup' => $tmvalue, 'valid' => 1));
        exit;
    }

    public function showofferstatus() {
        $this->layout = false;
        $input = Input::all();
        $user_id = Session::get('user_id');


        if ($input['status'] == 'offline') {
            $id = $input['id'];
            DB::table('offers')
                    ->where(array('id' => $id))
                    ->update(array('status' => 0));
            $view = 0;
        } else {
            $id = $input['id'];
            DB::table('offers')
                    ->where(array('id' => $id))
                    ->update(array('status' => 1));
            $view = 1;
        }

        $html = View::make('/Offers/statusonoff')->with('view', $view)->with('id', $id);
        return $html->render();
    }

    function showUpdateofferslotchange() {
        $this->layout = false;
        $input = Input::all();
        $user_id = Session::get('user_id');
        $id = $input['slot_id'];
        $color = $input['color'];
        $discount = $input['offer'];
        $same = $input['same'];
        $offerslotdata = DB::table('offers_slot')
                ->where('id', $id)
                ->first();

        $offer_same = '';

        if ($same) {

            $offer_same = DB::table('offers_slot')
                    ->where('offer_id', $offerslotdata->offer_id)
                    ->where('discount', $discount)
                    ->count();

            if ($offer_same < 1) {
                DB::table('offers_slot')
                        ->where(array('id' => $id))
                        ->update(array('color' => $color, 'discount' => $discount));
            }
        } else {

            $offerslotcolor = DB::table('offers_slot')
                    ->where('color', $color)
                    ->first();
            DB::table('offers_slot')
                    ->where(array('id' => $id))
                    ->update(array('color' => $color, 'item_name' => $offerslotcolor->item_name, 'discount' => $discount));
        }

//        print_r($id);die;


        $record = DB::table('offers')->where('id', $offerslotdata->offer_id)->first();

        $result = View::make('/Offers/slotupdate')
                ->with('offerslot', $offerslotdata)
                ->with('record', $record)
                ->with('color', $color)
                ->with('offer_same', $offer_same)
                ->with('same', $same);
        return $result->render();
        exit;
    }

    function showsloteditpage() {

        $this->layout = false;
        $input = Input::all();
        $user_id = Session::get('user_id');
        $id = $input['slot_id'];
        $offerslotdata = DB::table('offers_slot')
                ->where('id', $id)
                ->first();

//        print_r($offerslotdata);

        $record = DB::table('offers')->where('id', $offerslotdata->offer_id)->first();
        $result = View::make('/Offers/sloteditpage')
                ->with('offerslotdetails', $offerslotdata)
                ->with('record', $record);
//        echo '<pre>'; print_r($result);die;
        return $result->render();
        exit;
    }

}
?>

