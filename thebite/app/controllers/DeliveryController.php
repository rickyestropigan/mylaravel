<?php
//echo"hhk";exit;
class DeliveryController extends BaseController {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	 public function __construct() {
       
     	if(!Session::has(('userdata')))
	 	{ 
	 		 return Redirect::to('/');
	 	}
       
    }

	protected $layout = 'layouts.deliverydefault';
	
	public function showpage()
	{	 
		if (!Session::has(('userdata'))) {
            return Redirect::to('/');
        }
		$segment1 =  Request::segment(2);
		//echo $segment1;
		//echo"hh";exit;
		// $seg = Crypt::decrypt($segment1);
		// echo $seg;exit; 
		$data = DB::table('users')
		->where('users.slug', $segment1)
		->join('cuisines', 'cuisines.user_id', '=', 'users.id')
	    ->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
		//->join('opendays', 'users.id', '=', 'opendays.user_id')
		->first();


		//print_r($data);exit;
		 $this->layout->title = 'Welcome :: Bitebargain '.TITLE_FOR_PAGES;
    	 $this->layout->content = View::make('Delivery.index')->with('data', $data);

	}
}
?>