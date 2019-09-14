<?php

class SlotController extends BaseController {

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

//	protected $layout = 'layouts.slotdefault';
    protected $layout = 'layouts.listingdefault';

    public function showSlot() {

        if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }
        $day = date('D');

        $data = DB::table('users')->select('*')->where('status', '=', '1')->where("users.user_type", "=", 'Restaurant')->get();
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

                $data3 = DB::table('users')->where("users.user_type", "=", 'Restaurant')
                        ->select('users.id', 'users.first_name', 'users.profile_image', 'users.user_type', 'users.average_price', 'users.cuisines', 'users.slug as userslug', 'users.delivery_cost', 'users.minimum_order','users.service_offered')
                        ->where('id', $val->id)
                        ->where('users.status', '1')
                        ->get();

                $offers_data1[] = array_merge((array) $data1['0'], (array) $data3['0']);
            }
        }
        
        
        $id = Session::get('userdata')->id;
        $profile = DB::table('users')->where("users.id", $id)->first();
        Session::put('profile', $profile);
        $this->layout->content = View::make('slot.index')->with('data', $data)->with('profile', $profile)->with('offers_detail', $offers_data1);
    }

    function getslot() {
        if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }
        $button = "";
        $page_name = $_POST['page_name'];
        $id = $_POST['id'][1];
        $data = DB::table('users')
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->where('id', $id)
                        ->select('*')->first();
        
        $offslot = DB::table('offers')
                ->select('offers.id','offers.user_id','offers_slot.*')
                ->where('offers.user_id', $id)
                ->where('offers_slot.status', '1')
                ->where('flagstatus', "=", 1)
                ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                ->orderBY('offers.id', 'DESC')
                ->groupBy('offers_slot.start_time')
                ->groupBy('offers_slot.end_time')
                ->get();
        
        if (!empty($offslot)) {
            
            $output = "";
            foreach ($offslot as $res) {
                if($page_name != 'discountdetails'){
                    $button = '<button type="button" class="bg_none">BookSlot</button>';
                }
                $output .= '<ul class="list-unstyled radio-toolbar " >
                                        <li class="d-inline-block">

                                            <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                            <label for="discount"><span>' . date("h:i A", strtotime($res->start_time)) . '</span>
                                                <b>' . $res->discount . ' % off</b>
                                            </label>
                                        </li>
                                        <li class="d-inline-block">
                                            <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                            <label for="radioBanana"><span>' . date("h:i A", strtotime($res->end_time)) . '</span>
                                                <b>' . $res->discount . ' % off</b></label></li>
                                       		 <li class="d-inline-block">'.$button.'</li>
                                       
                                    </ul>
                                    ';
            }
            $output .= '<button class="bg_none more" style="display:none" onclick="showSlot(this)" id="more_'.$res->user_id.'_'.$res->id.'" >View More</button>
                        <button class="bg_none less" style="display:block"  onclick="hideSlot(this)" id="more_'.$res->user_id.'_'.$res->id.'" >View Less</button>';
            return $output;
        }
        
    }

}

?>
