<?php

class DiscountController extends BaseController {

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

    //protected $layout = 'layouts.discountdefault';
    protected $layout = 'layouts.listingdefault';

    public function showDiscount() {

        if (!Session::has(('userdata'))) {
            Session::forget('userdata');
            return Redirect::to('/');
        }

        $offers_data = $day = $offers_data1 = array();
        $day = date('D');
        $data = DB::table('users')
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->select('*')->get();
        foreach ($data as $val) {
            $data1 = DB::table('offers')
                    ->select('offers.created as datecreated', 'offers.id as offersid', 'offers.discount as offerdis', 'offers.start_time as offerstart', 'offers.end_time as offerend', 'offers_slot.discount as offerslotdis', 'offers_slot.offer_name as offerslotoffer_name', 'offers_slot.start_date as offerslotstart_date', 'offers_slot.end_date as offerslotend_date', 'offers_slot.start_time as offerslotstart_time', 'offers_slot.end_time as offerslotend_time', 'offers_slot.*','opening_hours.start_time','opening_hours.end_time')
                    ->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')
                    ->join('opening_hours', 'opening_hours.user_id', '=', 'offers.user_id')
                    ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                    ->where('offers.user_id', $val->id)
                    ->where('flagstatus', "=", 1)
                    ->where('opening_hours.open_close', "=", 1)
                    ->orderBY('offers_slot.created', 'DESC')
                    ->limit(1)
                    ->get();
            
            if (!empty($data1['0'])) {

                $data3 = DB::table('users')->select('*')->where('id', $val->id)
                        ->where("users.user_type", "=", 'Restaurant')
                        ->where("users.status", "=", '1')
                        ->get();
                $offers_data1[] = array_merge((array) $data1['0'], (array) $data3['0']);
            }
        }
        
        $id = Session::get('userdata')->id;
        $profile = DB::table('users')
                ->where("users.id", $id)
                ->first();
        Session::put('profile', $profile);
        
        $this->layout->content = View::make('discount.index')->with('data', $offers_data1)->with('profile', $profile);
    }

}

?>
