<?php

class ReservationController extends BaseController {

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

    public function showtoday($type = 'all') {

        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $input = Input::all();
        $search_keyword = "";

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        $start = date('Y-m-d');
        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
//                ->where('reservations.reservation_date', "=", date('Y-m-d'));
                ->whereRaw("DATE(reservation_date) = '$start'");

        if ($type == 'new') {
            $query->where('reservations.reservation_status', 'Pending');
        } else if ($type == 'confirm') {
            $query->where('reservations.reservation_status', 'Confirm');
        } else if ($type == 'noshow') {
            $query->where('reservations.reservation_status', 'No Show');
        } else if ($type == 'cancel') {
            $query->where('reservations.reservation_status', 'Cancel');
        }

        $records = $query->orderBy('reservations.id', 'desc')->sortable()->paginate(10);
//       echo'<pre>'; dd(DB::getQueryLog());  exit;
        // set content view and title

        $html = View::make('/Reservation/todayreservation')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('type', $type)
                ->with('records', $records);
        return $html->render();
    }

    public function showreceivedorders($type = 'all') {

        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $input = Input::all();
        $search_keyword = "";

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }

        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->where('reservations.reservation_date', "<", date('Y-m-d'));

        if ($type == 'new') {
            $query->where('reservation_date', "<", date('Y-m-d'))->where('reservation_status', "=", 'pending');
        } else if ($type == 'confirm') {
            $query->where('reservations.reservation_status', 'Confirm');
        } else if ($type == 'noshow') {
            $query->where('reservations.reservation_status', 'No Show');
        } else if ($type == 'cancel') {
            $query->where('reservations.reservation_status', 'Cancel');
        }

        $records = $query->orderBy('reservations.id', 'desc')->sortable()->paginate(10);
//        dd(DB::getQueryLog());  exit;
        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . "Reservations History";
        $this->layout->content = View::make('/Reservation/receivedorders')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('type', $type)
                ->with('records', $records);
    }

    public function showschedule($type = 'all') {

        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }


        $input = Input::all();
        $search_keyword = "";

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }

        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->whereRaw('reservation_date > DATE_ADD( NOW(), INTERVAL 2 HOUR)');

        if ($type == 'schedule') {
            $query->where('reservations.reservation_status', 'Pending');
        } else if ($type == 'cancel') {
            $query->where('reservations.reservation_status', 'Cancel');
        }

        $records = $query->orderBy('reservations.id', 'desc')->sortable()->paginate(10);
//        dd(DB::getQueryLog());  exit;
        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . "Scheduled's Orders";
        $this->layout->content = View::make('/Reservation/scheduleorders')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('type', $type)
                ->with('records', $records);
    }

    public function showupdatestatus() {
        $input = Input::all();
        if (!empty($input['id'])) {
            $id = trim($input['id']);
            //    echo $search_keyword; exit;
        }
        $orderData = DB::table('reservations')
                ->where('id', $id)
                ->first();

        if ($orderData) {
            DB::table('reservations')
                    ->where('id', $orderData->id)
                    ->update(['reservation_status' => $input['status'], 'offer_id' => $input['offer_id']]);
        }
    }

    //In reservation listing
    public function showreservation() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $input = Input::all();
        $user_id = Session::get('user_id');

        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name')
                ->where('reservations.caterer_id', $user_id);

        // Get all the users
        $reservations = $query->orderBy('reservations.id', 'desc')->sortable()->paginate(10);

        $this->layout->title = TITLE_FOR_PAGES . "Scheduled's Orders";
        $this->layout->content = View::make('/Reservation/reservation')
                ->with('reservations', $reservations);
    }

    //delete payment record by slug in admin
    public function showajaxreservation() {
        $input = Input::all();
        if (!empty($input['start'])) {
            $start = trim($input['start']);
        }

        if (!empty($input['end'])) {
            $end = trim($input['end']);
        }
        $user_id = Session::get('user_id');
        $reservationData = DB::table('reservations')
                ->where('reservation_date', ">=", $start)
                ->where('reservation_date', "<=", $end)
                ->where('reservations.caterer_id', $user_id)
                ->get();

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

        foreach ($Newreservation as $key => $value) {

            $jsondata[$i]['title'] = 'Total Reservations (' . $value['total'] . ')';
            $jsondata[$i]['start'] = $key;
            $i++;
            $jsondata[$i]['title'] = 'Total Seats (' . $value['size'] . ')';
            $jsondata[$i]['start'] = $key;
            $i++;
        }



        echo json_encode($jsondata);
        die;
    }

    //reservation status
    public function showreservationstatus($start = null) {
//        $start = date('Y-m-d', $num);
//        echo $start;die;
        $user_id = Session::get('user_id');
        $reservationData = DB::table('reservations')
//                ->where('DATE(reservation_date)', "=", $start)
                ->whereRaw("DATE(reservation_date) = '$start'")
                ->where('reservations.caterer_id', $user_id)
                ->get();
//        dd(DB::getQueryLog());
//print_r($reservationData);die;
        $html = '<div class="order_div div_class">
                        <div class="order order_txt">Reservation</div>
                        <div class="order ordertxt">Status</div>
                    </div>';
        if ($reservationData) {
            foreach ($reservationData as $reservation) {


                $html .= '

                    <div class="subtotlall"><div class="left_totla">' . $reservation->reservation_number . '</div>  
                        <div class="right_totla right_txt">' . $reservation->reservation_status . '</div> 
                    </div>';
            }
        } else {
            echo '<div class="subtotlall">No record found</div>';
        }

        echo $html;
        die;
    }

    //Alok Reservation dashboard 

    public function showdashboard() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }



        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all addresses        
        $query = DB::table('orders');

        $date = date('Y-m-d');
        $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $date))
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');
        $orders = $query->orderBy('orders.order_by', 'asc')->get();


//        echo '<pre>'; print_r($date);die;
//        dd(DB::getQueryLog($orders));

        $schedule = DB::table('orders');
        $schedule->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $date, 'orders.status' => 'Confirm'))
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');
        //->whereRaw('delivery_date > DATE_ADD( NOW(), INTERVAL 2 HOUR)')

        $orderschedule = $schedule->orderBy('orders.id', 'desc')->get();

        // get all posted input
        $input = Input::all();



        $this->layout->title = TITLE_FOR_PAGES . "Reservation Dashboard";
        $this->layout->content = View::make('/Reservation/dashboard', compact('orders'))
                ->with('userData', $userData)
                ->with('orderschedules', $orderschedule);
    }

    public function shownextorder() {

        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $query = DB::table('orders');
        $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']))
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');

        $orders = $query->orderBy('orders.order_by', 'asc')->get();

//        dd(DB::getQueryLog()); 
//        echo '<pre>'; print_r($orders);die;

        $view = View::make('/Reservation/nextorder', compact('orders'))
                ->with('userData', $userData);

        $html = $view->render();
        return $html;
    }

    public function showsearchorder() {

        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $search_keyword = $input['keyword'];
        $query = DB::table('orders');
        $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']))
                ->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                    ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                    ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                    ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                })
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


        $orders = $query->orderBy('orders.order_by', 'asc')->get();

        //dd(DB::getQueryLog());

        $view = View::make('/Reservation/nextorder', compact('orders'))
                ->with('userData', $userData);

        $html = $view->render();
        return $html;
    }

    public function showscheduleorder() {
        $user_id = Session::get('user_id');
        $input = Input::all();

        $schedule = DB::table('orders');
        $schedule->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']))
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');
        //->whereRaw('delivery_date > DATE_ADD( NOW(), INTERVAL 2 HOUR)')

        $orderschedule = $schedule->orderBy('orders.id', 'desc')->get();

        $view = View::make('/Reservation/scheduleorder')
                ->with('orderschedules', $orderschedule);

        $html = $view->render();
        //print_r($input);exit;
        return $html;
    }

    public function showtaborder() {
        $user_id = Session::get('user_id');
        $input = Input::all();

        $cat = $input['cat'];
        $search_keyword = $input['keyword'];
        $current_dat = $input['current_dat'];

//        print_r($input);die;

        if ($cat == 'new') {
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat'], 'orders.status' => 'Pending'));
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $query->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


            $orders = $query->orderBy('orders.order_by', 'asc')->get();

            //dd(DB::getQueryLog()); 
        }

        if ($cat == 'confirm') {
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat'], 'orders.status' => 'Confirm'));
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $query->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


            $orders = $query->orderBy('orders.order_by', 'asc')->get();

            //dd(DB::getQueryLog()); 
        }

        if ($cat == 'complete') {
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat'], 'orders.status' => 'Complete'));
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $query->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


            $orders = $query->orderBy('orders.order_by', 'asc')->get();

            //dd(DB::getQueryLog()); 
        }

        if ($cat == 'cancel') {
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat'], 'orders.status' => 'Cancel'));
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $query->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


            $orders = $query->orderBy('orders.order_by', 'asc')->get();

            //dd(DB::getQueryLog()); 
        }

        if ($cat == 'all') {
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']));
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $query->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


            $orders = $query->orderBy('orders.order_by', 'asc')->get();

            //dd(DB::getQueryLog()); 
        }

        $view = View::make('/Reservation/taborder', compact('orders'))->with('cat', $cat);

        $html = $view->render();
        return $html;
    }

    public function showtabreservation() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return 'errorlogin';
        }


        $input = Input::all();
        $search_keyword = "";

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($input['keyword'])) {
            $search_keyword = trim($input['keyword']);
        }
        $start = $input['current_dat'];
        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->where('reservations.caterer_id', "=", $user_id)
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->whereRaw("DATE(reservation_date) = '$start'");

        if (!empty($search_keyword)) {
            $query->where(function ($query) use ($search_keyword) {
                $query->where('reservations.reservation_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('reservations.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('reservations.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('reservations.contact', 'LIKE', '%' . $search_keyword . '%');
            });
        }

        $records = $query->orderBy('reservations.order_by', 'asc')->get();

        $html = View::make('/Reservation/showreservation')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('records', $records);
        return $html->render();
    }

    public function showtabreserve() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $search_keyword = '';

        $cat = $input['cat'];
        $search_keyword = $input['keyword'];
        $current_dat = $input['current_dat'];

        if ($cat == 'new') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'Pending')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.order_by', 'asc')->get();
        }

        if ($cat == 'confirm') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'Confirm')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.order_by', 'asc')->get();
        }



        if ($cat == 'noshow') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'No Show')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.order_by', 'asc')->get();
        }

        if ($cat == 'cancel') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'Cancel')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.order_by', 'asc')->get();
        }

        if ($cat == 'complete') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'Complete')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.order_by', 'asc')->get();
        }

        if ($cat == 'all') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.order_by', 'asc')->get();
        }
        
//         echo $cat;die;

        $html = View::make('/Reservation/showtabreserve')
                ->with('userData', $userData)
                ->with('records', $records);
        
        return $html->render();
    }

    public function shownextreservation() {
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $current_dat = $input['current_dat'];

        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->where('reservations.caterer_id', "=", $user_id)
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->whereRaw("DATE(reservation_date) = '$current_dat'");


        $records = $query->orderBy('reservations.order_by', 'asc')->get();
//        echo'<pre>';
//        print_r($records);
        $html = View::make('/Reservation/shownextreservation')->with('userData', $userData)
                ->with('records', $records);
        return $html->render();
    }

    public function showschedulereserve() {
        $user_id = Session::get('user_id');
        $input = Input::all();
        $start = $input['current_dat'];

        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
                ->where('reservations.reservation_status', "=", 'Pending')
                ->whereRaw("DATE(reservation_date) = '$start'");

        $records = $query->orderBy('reservations.id', 'desc')->get();

        $view = View::make('/Reservation/schedulereserve')
                ->with('orderschedules', $records);

        $html = $view->render();
        //print_r($input);exit;
        return $html;
    }

    public function showviewreserve() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();

        $order_slug = $input['order'];

        $orderData = DB::table('reservations')
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.address as user_address', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->where('reservations.id', $order_slug)
                ->first();

        $html_view = View::make('/Reservation/viewreservation')->with('order', $orderData);

        $html = $html_view->render();
        return $html;
    }

    public function showeditreserve() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();

        $order_slug = $input['order'];

        $orderData = DB::table('reservations')
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->where('reservations.id', $order_slug)
                ->first();

        $html_view = View::make('/Reservation/editreservation')->with('order', $orderData);

        $html = $html_view->render();
        return $html;
    }

    public function showchangeresstatus() {

        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

//        print_r($userData);die;

        $input = Input::all();
        $order_id = $input['order_id'];
        $data = array(
            'reservation_status' => $input['reservation_status']
        );

        if ($input['reservation_status'] == 'Cancel') {
            $data['order_by'] = 4;
        } else if ($input['reservation_status'] == 'Complete') {
            $data['order_by'] = 3;
        } else if ($input['reservation_status'] == 'No Show') {
            $data['order_by'] = 2;
        } else if ($input['reservation_status'] == 'Confirm') {
            $data['order_by'] = 1;
        } else {
            $data['order_by'] = 0;
        }

        
//        print_r($data);die;
        
        
        DB::table('reservations')
                ->where('id', $order_id)
                ->update($data);


        $reservations = DB::table('reservations')
                ->where('id', $order_id)
                ->first();

        // send to customer
//
//        $mail_data = array(
//            'text' => "Your reservation has been confirmed by " . $userData->first_name . ' ' . $userData->last_name . ' Restaurant',
//            'email' => $userData->email_address,
//            'firstname' => $reservations->first_name . ' ' . $reservations->last_name,
//            'rname' => $userData->first_name,
//            'reservation_number' => $reservations->reservation_number,
//        );
//
////        return View::make('emails.template')->with($mail_data); // to check mail template data to view
//
//        Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
//            $message->setSender(array(MAIL_FROM => SITE_TITLE));
//            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
//            $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your reservations has been confirmed by ' . $userData->first_name);
//        });


        return 'success';
        exit;
    }

    public function showafterchangeres() {
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();

        $current_dat = $input['current_dat'];

        $query = Reservation::sortable()
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->where('reservations.caterer_id', "=", $user_id)
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->whereRaw("DATE(reservation_date) = '$current_dat'");

        $records = $query->orderBy('reservations.order_by', 'asc')->get();



        $html = View::make('/Reservation/shownextreservation')->with('userData', $userData)
                ->with('records', $records);
        return $html->render();
    }

    public function showchangeresstatusall() {
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $order_id = $input['order_id'];
        $data = array(
            'reservation_status' => $input['reservation_status']
        );

//        echo '<prE>'; print_r($input);die;

        DB::table('reservations')
                ->where('id', $order_id)
                ->update($data);

        $orderData = DB::table('reservations')
                ->join('users', 'reservations.caterer_id', '=', 'users.id')
                ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                ->where('reservations.id', $order_id)
                ->first();

//        if ($input['reservation_status'] == 'Confirm') {
//            
//            // send to customer
//
//            $mail_data = array(
//                'text' => "Your reservation has been confirmed by " . $userData->first_name . ' ' . $userData->last_name . ' Restaurant',
//                'email' => $userData->email_address,
//                'firstname' => $orderData->first_name . ' ' . $orderData->last_name,
//                'rname' => $userData->first_name,
//                'reservation_number' => $orderData->reservation_number,
//            );
//
////        return View::make('emails.template')->with($mail_data); // to check mail template data to view
//
//            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
//                $message->setSender(array(MAIL_FROM => SITE_TITLE));
//                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
//                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your reservations has been confirmed by ' . $userData->first_name);
//            });
//        }

        $html_view = View::make('/Reservation/shownstatusnew')->with('order', $orderData);
        $html = $html_view->render();
        return $html;
    }
    
    
    public function showPrintres($slug = null) {
        
        $this->logincheck('order/printorder/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $this->layout = View::make('layouts.printres');
        $orderData = DB::table('reservations')
                ->where('reservation_number', $slug)
                ->first();

        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }
        $offerData = DB::table('offers')
                ->where('id', $orderData->offer_id)
                ->first();
        $ownerData = DB::table('users')
                ->where('id', $orderData->caterer_id)
                ->first();
        $this->layout->content = View::make('/Reservation/printreservations')->with('orderData', $orderData)->with('offerData', $offerData)->with('ownerData',$ownerData);
    }

}
?>

