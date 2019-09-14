<?php

class RestaurantdetailController extends BaseController {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    public function __construct() {

        if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
    }

    protected $layout = 'layouts.restaurantdetaildefault';

   public function showpage() {
//        if (!Session::has(('userdata'))) {
//            return Redirect::to('/');
//        }
        $segment1 = Request::segment(2);
        $cnd = array(
            'opening_hours.open_close' => '1',
            'offers.flagstatus' => '1',
            
        );
        $data = DB::table('users')
                ->where('users.slug', $segment1)
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->join('cuisines', 'cuisines.user_id', '=', DB::raw('tbl_users.id'))
                ->join('menu_item', 'menu_item.user_id', '=', DB::raw('tbl_users.id'))
                // ->join('reviews', 'reviews.user_id', '=', DB::raw('tbl_users.id'))
                ->join('opening_hours', 'opening_hours.user_id', '=', DB::raw('tbl_users.id'))
                ->select('offers.discount as off_dis','users.slug as userslug', 'users.id as userid', 'users.description as restdesc', 'users.minimum_order as minorder', 'users.*', 'cuisines.id as cuisinesid', 'cuisines.*', 'menu_item.slug as menu_itemslug', 'menu_item.id as menu_itemid', 'menu_item.*', 'opening_hours.id as opening_hoursid', 'opening_hours.*')
                ->where($cnd)
                ->orderBy('offers.discount', 'DESC')
                ->first();
        
        
        $menu = DB::table('cuisines')->where('cuisines.user_id', $data->userid)->where('cuisines.visibility', '1')->get();
        
        $reviews = DB::table('reviews')->where('reviews.user_id', $data->userid)->get();
        $item = DB::table('menu_item')->where('menu_item.user_id', $data->userid)->get();
        
        $openinghr = DB::table('opening_hours')->where('opening_hours.user_id', $data->userid)->get();
        if(Session::has('userdata')){
            $id = Session::get('userdata')->id;
        } else {
            $id = 0;
        }
        $offslot = DB::table('offers')->select('offers.flagstatus','offers_slot.*')->join('offers_slot', 'offers_slot.offer_id', '=', 'offers.id')->where('user_id', $data->userid)->where('flagstatus', "=", 1)->orderBY('offers.id', 'DESC')->get();
        $profile = DB::table('users')->where("users.id", $id)->first();
        if($profile){
            Session::put('profile', $profile);
        } else {
            Session::put('profile', array());
        }
        
        $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
        $this->layout->content = View::make('Restaurantdetail.index')->with('data', $data)->with('menu', $menu)->with('reviews', $reviews)->with('item', $item)->with('openinghr', $openinghr)->with('profile', $profile)->with('offer_slots',$offslot);
    }

    public function showMenu() {
        $mid = $_POST['id'];
        $menudata = DB::table('menu_item')->where('menu_item.cuisines_id', $mid)->get();
        if (!empty($menudata)) {
            $output = "";
            $output .= ' <div class="titl text-center py-5" id="menucatname">BEST DISHES</div>';
            foreach ($menudata as $data) {
                $output .= '
				          <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#">' . $data->item_name . '</a>  
                                    <p>' . $data->description . '</p>
                                    <div class="float-left">
                                       <span class="actual_rate"><i class="fa fa-usd"></i>' . $data->price . '</span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                </div>   
                            </div></div></div>';
            }
            return $output;
        } else {
            $output = "Sorry no dish available";
            return $output;
        }
    }

    public function showFavourite() {
        $resid = $_POST['resid'];
        $userid = $_POST['userid'];
        $resname = $_POST['resname'];
        $fav = DB::table('favourite')
                ->where('favourite.res_id', $resid)
                ->where('favourite.user_id', $userid)
                ->where('favourite.restaurent_name', $resname)
                ->first();

        if (!empty($fav)) {
            DB::table('favourite')->where('favourite.res_id', $resid)
                    ->where('favourite.user_id', $userid)
                    ->where('favourite.restaurent_name', $resname)
                    ->delete();

            $output = "";
            $output .= '<input type="hidden">';
            return $output;
        } else {
            //echo"hh";exit;
            $savefav = array(
                'res_id' => $resid,
                'user_id' => $userid,
                'restaurent_name' => $resname,
                'status' => 1,
            );
            //print_r($savefav);exit;
            DB::table('favourite')->insert(
                    $savefav
            );
            $output = "";
            $output .= '<input type="hidden">';
            return $output;
        }
    }

    public function showReviews() {
        $resid = $_POST['resid'];
        $reviews = DB::table('reviews')
                ->where('reviews.user_id', $resid)
                ->get();

        if (!empty($reviews)) {
            $output = "";

            foreach ($reviews as $data) {
                $output .= '
				          <div class="top_details">
                                      <a >' . $data->comment . '</a>  
                                      </div>';
            }
            return $output;
        } else {
            $output = "Sorry! no Review Available";
            return $output;
        }
    }

}

?>
