<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class OrderController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default User Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add rroute:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    protected $layout = 'layouts.default';

    public function logincheck($url) {
//        echo $url;
//        echo Session::get('user_id');
//        die;
        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/admin')->with('error_message', 'You must login to see this page.');
        }
    }

    //*******************ADMIN Order Instance **************************//
    //admin order listing
    public function showAdmin_index() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        $query = Order::sortable()
                ->where(function ($query) use ($search_keyword) {
            $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
        });

        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
        }

        $separator = implode("/", $separator);

        // Get all the users
        $mainorders = $query->orderBy('orders.id', 'desc')->sortable()->paginate(3);


        // Show the page
        return View::make('Orders/adminindex', compact('mainorders'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdminSub_view($slug = null) {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        $query = Order::sortable()
                ->where(function ($query) use ($search_keyword) {
            $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
        });

        if ($slug != "") {
            $mainData = DB::table('main_order')
                    ->where('slug', $slug)
                    ->first();
            //  print_r($mainData);

            $query->whereIn('orders.order_number', explode(',', $mainData->order_id));
        }


        // $query->join('users as u1', DB::raw('u1.id'), '=', 'orders.caterer_id');
        $query->join('users', 'users.id', '=', 'orders.caterer_id')
                ->select('orders.*', 'users.first_name');


//        $query->join('users as u2', DB::raw('u2.id'), '=', 'orders.user_id');
//        $query->join('users as u3', DB::raw('u3.id'), '=', 'orders.caterer_id');
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
        }

        $separator = implode("/", $separator);

        // Get all the users
        $orders = $query->orderBy('orders.id', 'desc')->sortable()->paginate(10);


        // Show the page
        return View::make('Orders/adminsubindex', compact('orders'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo)
                        ->with('slug', $slug);
    }

    //admin delete order listing 
    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/order/admin_index')->with('success_message', 'Order deleted successfully');
        }
    }

    //admin view order details
    public function showAdmin_view($slug = null) {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $order = DB::table('orders')
                        ->where('slug', $slug)->first();


        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

//         $main_ordershopData = DB::table('main_order')
//           // ->whereIn('order_id', $orders)
//            ->whereRaw("FIND_IN_SET('$orderData->order_number',order_id)")
//           
//            ->get();


        if (empty($orderData)) {
            return Redirect::to(
                            '/admin/order/admin_index');
        }

        $user_id = $orderData->caterer_id;

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $input = Input::all();


        $cartItems = DB::table('order_item')
                ->whereIn('menu_id', explode(',', $orderData->order_item_id))
                ->where('order_id', $orderData->id)
                ->get(); // get cart menu of this order

        if (!empty($input)) {
            $inputStatus = $input['status'];
            switch ($input['status']) {

                case "Confirm":
                    $orderStatus = "Confirmed";
                    $subjectMessageCustomer = "Your order has been confirmed by " . SITE_TITLE;
                    $subjectMessageRestaurant = "An order has been confirmed by " . SITE_TITLE;
                    $subjectMessageAdmin = "An order has been confirmed by " . SITE_TITLE;
                    $subjectMessageCouieer = "An order has been assigned to you on " . SITE_TITLE;

                    // check courier conditions start
                    $courierData = DB::table('users')
                                    ->where('mark_default', '1')->first();
                    if (!empty($courierData)) {
                        DB::table('orders')
                                ->where('id', $orderData->id)
                                ->update(['is_courier' => 1, 'courier_id' => $courierData->id]); // update order status
                    }

                    // check courier conditions end
                    break;

                case "Delivered":
                    $orderStatus = "Delivered";
                    $subjectMessageCustomer = "Your order has been delivered by " . SITE_TITLE;

                    // check courier conditions start
//                    $courierData = DB::table('users')
//                                    ->where('mark_default', '1')->first();
//                    if (!empty($courierData)) {
//                        DB::table('orders')
//                                ->where('id', $orderData->id)
//                                ->update(['status' => 'Delivered']); // update order status
//                    }
                    // check courier conditions end
                    break;


                case "Modify":
                    $orderStatus = "Modify";
                    $subjectMessageCustomer = "Restaurant requested to modify your order on " . SITE_TITLE;

                    break;
                case "Cancel":
                    $orderStatus = "Cancel";
                    DB::table('orders')
                            ->where('id', $orderData->id)
                            ->update(['cancel_reason' => $input['reason']]);
                    $subjectMessageCustomer = "You order has been cancelled by  " . SITE_TITLE;
                    $subjectMessageRestaurant = "You have cancelled order on " . SITE_TITLE;
                    $subjectMessageAdmin = "Restaurant cancelled order on " . SITE_TITLE;
                    break;
            }


            if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {

                $delivery_charge = $orderData->delivery_charge;
                $delivery_type = $orderData->delivery_type;
            } else {
                $delivery_charge = "0";
                $delivery_type = "N/A";
            }
            $orderNumber = $orderData->order_number;
            $customerContent = "";
            $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

            $customerContent .= '</table>';


            $orderContent = "";
            // send mails
            /// send mail to customer 
            $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';

            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';
            $total = array();
            if (!empty($cartItems)) {

                $total = array();
                foreach ($cartItems as $cartData) {


                    $menuData = DB::table('menu_item')
                                    ->where('id', $cartData->menu_id)->first();  // get menu data from menu table
//                    $sub_total = $cartData->base_price * $cartData->quantity;
                    $sub_total = $menuData->price;
                    $total[] = $sub_total;

                    $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuData->item_name . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($cartData->base_price, 2) . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . $cartData->quantity . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                    </td>
                    </tr>';
                }
            }

            $catererData = DB::table('users')
                    ->where('id', $orderData->caterer_id)
                    ->first();
            $gTotal = array_sum($total);
            if ($orderData->tax) {
                $tax = $orderData->tax;
            } else {
                $tax = 0;
            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valig n = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';
            if ($orderData->discount) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal - $orderData->discount;
            }
            if ($adminuser->is_tax) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax;
            }
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge;
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';

            // send mail to couier 
            if ($input['status'] == 'Confirm' && !empty($courierData)) {

                $saveData = array(
                    'order_id' => $orderData->id,
                    'user_id' => $courierData->id,
                    'slug' => $this->createSlug('cservice'),
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('order_courier')->insert(
                        $saveData
                );

                /*                 * * send mail to customer ** */
                $mail_courier_data = array(
                    'text' => $subjectMessageCouieer,
                    'customerContent' => $customerContent, 'orderContent' => $orderContent, 'orderStatus' => $orderStatus,
                    'sender_email' => $courierData->email_address,
                    'firstname' => $courierData->first_name . ' ' . $courierData->last_name,
                );

//                return View::make('emails.template')->with($mail_courier_data); // to check mail template data to view

                Mail::send('emails.template', $mail_courier_data, function($message) use ($mail_courier_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_courier_data['sender_email'], $mail_courier_data ['firstname'])->subject($mail_courier_data['text']);
                });
            }

            if ($input['status'] == 'Delivered') {
                /*                 * * send mail to customer ** */
                $mail_data = array(
                    'text' => $subjectMessageCustomer,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $customerData->email_address,
                    'firstname' => $customerData->first_name . ' ' . $customerData->last_name,);

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message ) use ( $mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                });
            } else {

                /*                 * * send mail to customer ** */
                $mail_data = array(
                    'text' => $subjectMessageCustomer,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $customerData->email_address,
                    'firstname' => $customerData->first_name . ' ' . $customerData->last_name,);

//            return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message ) use ( $mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                });

                /*                 * * send mail to restaurant ** */
                $caterer_mail_data = array(
                    'text' => $subjectMessageRestaurant,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $catererData->email_address,
                    'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
                );

//            return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

                Mail::send('emails.template', $caterer_mail_data, function($message) use($caterer_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['text']);
                });


                /*                 * * send mail to admin ** */

                $admin_mail_data = array(
                    'text' => $subjectMessageAdmin,
                    'customerContent' => $customerContent,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $adminuser->email,
                    'firstname' => "Admin",
                );

//            return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

                Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['text']);
                });
            }



            DB::table('orders')
                    ->where('id', $orderData->id)
                    ->update(['status' => $inputStatus]);
            return Redirect::to('/admin/order/view/' . $slug)->with('success_message', 'Order status changed successfully.');
        } else {

            return View::make('/Orders/admin_view')
                            ->with('userData', $userData)
                            ->with('orderData', $orderData)->with('detail', $order);
            //->with('main_ordershopData', $main_ordershopData);
        }
    }

    //******************************End of admin instance ********************//
// Create slug for secure URL
    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

    public function showreceivedview($slug = null, $type = null) {

        $this->logincheck('order/receivedview/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

        $numberofOrder = 1;

        $tax = $orderData->tax;
        $delivery_charge = $orderData->delivery_charge;
        $discount = $orderData->discount;

        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }


        $this->layout->content = View::make('/Orders/showreceivedview')
                ->with('userData', $userData)
                ->with('type', $type)
                ->with('orderData', $orderData);

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $input = Input::all();

        // echo "<pre>"; print_r($orderData); exit;
        $cartItems = DB::table('order_item')
                ->whereIn('menu_id', explode(',', $orderData->order_item_id))
                ->where('order_id', $orderData->id)
                ->get();  // get cart menu of this order
//echo "<pre>"; print_r($cartItems); exit;
        $orderstatus = Orderstatus::orderBy('status_name', 'asc')->where('status', "=", "1")->lists('status_name', 'id');

        if (!empty($input)) { //echo "<pre>"; print_r($input); exit;
            $inputStatus = $input['status'];
            switch ($input['status']) {
                case "Confirm":
                    $orderStatus = "Confirmed";
                    $subjectMessageCustomer = "Your order has been confirmed by restaurant on " . SITE_TITLE;
                    $subjectMessageRestaurant = "You have confirmed order on " . SITE_TITLE;
                    $subjectMessageAdmin = "An order has been confirmed by restaurant on " . SITE_TITLE;
                    $subjectMessageCouieer = "An order has been assigned to you on " . SITE_TITLE;

                    // check courier conditions start
                    $courierData = DB::table('users')
                                    ->where('mark_default', '1')->first();
                    if (!empty($courierData)) {
                        DB::table('orders')
                                ->where('id', $orderData->id)
                                ->update(['is_courier' => 1, 'courier_id' => $courierData->id]); // update order status
                    }
                    // check courier conditions end
                    break;
                case "Modify":
                    $orderStatus = "Modify";
                    $modifyArr = $input['modfiy'];
                    if (!empty($modifyArr)) {
                        foreach ($modifyArr as $mData) {
                            DB::table('order_item')
                                    ->where('id', $mData['id'])
                                    ->update(['modification'
                                        => $mData['comment'], 'is_modify' => '1']);
                        }
                    }
                    $subjectMessageCustomer = "Restaurant requested to modify your order on " . SITE_TITLE;
                    $subjectMessageRestaurant = "You have requested to modify order on " . SITE_TITLE;
                    $subjectMessageAdmin = "Restaurant placed modification request for order on " . SITE_TITLE;

                    break;
                case "Cancel":
                    $orderStatus = "Cancel";
                    DB::table('orders')
                            ->where('id', $orderData->id)
                            ->update(['cancel_reason' => $input['reason']]);
                    $subjectMessageCustomer = "Your order has been cancelled by restaurant on " . SITE_TITLE;
                    $subjectMessageRestaurant = "Your have cancelled order on " . SITE_TITLE;
                    $subjectMessageAdmin = "Restaurant cancelled order on " . SITE_TITLE;
                    break;

                case "Delivered":
                    $orderStatus = "Delivered";
                    $subjectMessageCustomer = "Your order has been delivered by restaurant on " . SITE_TITLE;
                    $subjectMessageRestaurant = "You have mark order status to delivered on " . SITE_TITLE;
                    $subjectMessageAdmin = "Restaurant mark order as delivered on " . SITE_TITLE;

                    $data = array(
                        'user_id' => $customerData->id,
                        'caterer_id' => $orderData->caterer_id,
                        'created' => date('Y-m-d H:i:s'),
                    );
                    DB::table('user_reviews')->insert($data
                    );
                    break;
                default:
                    $orderStatus = $input['status'];
//                    DB::table('orders')
//                            ->where('id', $orderData->id)
//                            ->update(['comment' => $input['comment']]);
                    $subjectMessageCustomer = "Your order status has been changed to " . $input['status'] . " by restaurant on " . SITE_TITLE;
                    $subjectMessageRestaurant = "Your have changed the order status to " . $input['status'] . " on " . SITE_TITLE;
                    $subjectMessageAdmin = "Restaurant changed order status to " . $input['status'] . " on " . SITE_TITLE;

                    break;
            }


            if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {

                // $delivery_charge = $orderData->delivery_charge;
                $delivery_type = $orderData->delivery_type;
            } else {
                // $delivery_charge = "0";
                $delivery_type = "N/A";
            }
            $orderNumber = $orderData->order_number;
            $customerContent = "";
            $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';

            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

            $customerContent .= '</table>';


            $orderContent = "";
            // send mails
            /// send mail to customer 
            $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';
//            if ($input['status'] != "Confirm" && $input['status'] != "Cancel" && $input['status'] != "Delivered") {
//                if ($input['comment'] != "") {
//
//                    $orderContent .= '<tr>
//                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
//                                     Order note: ' . $input['comment'] . '
//                                </td>
//                                
//                            </tr>';
//                }
//            }

            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';

            $total = array();
            if (!empty($cartItems)) {


                foreach ($cartItems as $cartData) {
                    //echo "<pre>"; print_r($cartData); exit;
                    $menuItem = DB::table('menu_item')
                            ->where('id', $cartData->menu_id)
                            ->first();
                    $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuItem->item_name . '</td>';


                    $variant_id = explode(',', $cartData->variant_id);
//                    $menuDataVal = DB::table('variants')
//                            ->whereIn('id', $variant_id)
//                            ->get();
//                    foreach ($menuDataVal as $menuData) {
//                        $sub_total = $menuData->price * $cartData->quantity;
                    $sub_total = $menuItem->price;
                    $total[] = $sub_total;
                    $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   <strong>Variant </strong> (' . $menuItem->item_name . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $cartData->quantity . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                </td>
                                  </tr>';
//                    }





                    $orderContent .= '</tr>';
                }
            }

            $catererData = DB::table('users')
                    ->where('id', $orderData->caterer_id)
                    ->first();
            $gTotal = array_sum($total);

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . ' ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            if ($orderData->discount) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal - $orderData->discount;
            }
            if ($adminuser->is_tax) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax / $numberofOrder;
            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge / $numberofOrder, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge / $numberofOrder;

            $newcnn = '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $customerCC = $orderContent . $newcnn . '</table>';
            //  echo $customerCC; //exit;
            /*             * * send mail to customer ** */
            $mail_data = array(
                'text' => $subjectMessageCustomer,
                'orderContent' => $customerCC,
                'orderStatus' => $orderStatus,
                'sender_email' => $customerData->email_address,
                'firstname' => $customerData->first_name . ' ' . $customerData->last_name,
            );


//            if ($adminuser->is_commission == 1) {
//
//                $comm_per = $adminuser->commission;
//                $tax_amount = $comm_per * $gTotal / 100;
//
//
//                $orderContent .= '<tr>
//                            <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                               Admin Commission
//                            </td>
//                            <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                               ' . App::make("HomeController")->numberformat($tax_amount, 2) . '
//                            </td>
//                              </tr>';
//                $gTotal = $gTotal - $tax_amount;
//            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';
//             echo $orderContent; exit;
// send mail to couier 
            if ($input['status'] == 'Confirm' && !empty($courierData)) {

                $saveData = array(
                    'order_id' => $orderData->id,
                    'user_id' => $courierData->id,
                    'slug' => $this->createSlug('cservice'),
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('order_courier')->insert(
                        $saveData
                );


                /*                 * * send mail to customer ** */
                $mail_courier_data = array(
                    'text' => $subjectMessageCouieer,
                    'customerContent' => $customerContent,
                    'orderContent' => $orderContent,
                    'orderStatus' => $orderStatus,
                    'sender_email' => $courierData->email_address,
                    'firstname' => $courierData->first_name . ' ' . $courierData->last_name,
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_courier_data, function($message) use($mail_courier_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_courier_data['sender_email'], $mail_courier_data['firstname'])->subject($mail_courier_data['text']);
                });
            }


//            return View::make('emails.template')->with($mail_data); // to check mail template data to view


            if ($inputStatus != "Assign To Delivery") {
                Mail::send('emails.template', $mail_data, function($message) use($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject($mail_data['text']);
                });
            }
            /*             * * send mail to restaurant ** */

            $caterer_mail_data = array(
                'text' => $subjectMessageRestaurant,
                'orderContent' => $orderContent,
                'orderStatus' => $orderStatus,
                'sender_email' => $catererData->email_address,
                'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
            );

//               return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

            Mail::send('emails.template', $caterer_mail_data, function($message) use($caterer_mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($caterer_mail_data['sender_email'], $caterer_mail_data['firstname'])->subject($caterer_mail_data['text']);
            });


            /*             * * send mail to admin ** */

            $admin_mail_data = array(
                'text' => $subjectMessageAdmin,
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'orderStatus' => $orderStatus,
                'sender_email' => $adminuser->email,
                'firstname' => "Admin",
            );

            // return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

            Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($admin_mail_data['sender_email'], 'Admin')->subject($admin_mail_data['text']);
            });


            /*             * **************************************** */
            if ($input['status'] == 'Confirm') {
                DB::table('orders')
                        ->where('id', $orderData->id)
                        ->update(['status' => $inputStatus, 'preparation_time' => $input['preparation_time']]);
            } else {
                DB::table('orders')
                        ->where('id', $orderData->id)
                        ->update(['status' => $inputStatus]);
            }
            return Redirect::to('/order/receivedview/' . $slug . '/' . $type)->with('success_message', 'Order status changed successfully.');
        }
    }

    public function showView($slug = null) {
        $this->logincheck('order/view/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }
        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

        $main_ordershopData = DB::table('main_order')
                // ->whereIn('order_id', $orders)
                ->whereRaw("FIND_IN_SET('$orderData->order_number',order_id)")
                ->get();
        //  print_r($main_ordershopData); exit;
        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }

        $this->layout->content = View::make('/Orders/view')
                ->with('userData', $userData)
                ->with('orderData', $orderData)
                ->with('main_ordershopData', $main_ordershopData);
    }

    public function reorder($slug = null) {
        $cart = new Cart(new CartSession, new Cookie);
        $cart->destroy();

        $this->logincheck('restaurants/reorder/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }
        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();





        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        } else {
            $orderItem = DB::table('order_item')
                    ->where('order_id', $orderData->id)
                    ->get();
            //  echo "<pre>"; print_r($orderItem); exit;
            if ($orderItem) {
                $mesage = "";

                foreach ($orderItem as $orderItemVal) {

                    $menu_item = DB::table('menu_item')
                            ->where('id', $orderItemVal->menu_id)
                            ->first();
                    if ($menu_item) {
//                         echo "<pre>"; print_r($orderItemVal); exit;
                        if (isset($orderItemVal->addon_id) && $orderItemVal->addon_id != "") {
                            $cart->insert(array(
                                'id' => $menu_item->id,
                                'name' => $menu_item->item_name,
                                'price' => $menu_item->price,
                                'quantity' => $orderItemVal->quantity,
                                'caterer_id' => $menu_item->user_id,
                                'variant_type' => $orderItemVal->variant_id,
                                'addons' => $orderItemVal->addon_id
                            ));
                        } else {
                            $cart->insert(array(
                                'id' => $menu_item->id,
                                'name' => $menu_item->item_name,
                                'price' => $menu_item->price,
                                'quantity' => $orderItemVal->quantity,
                                'caterer_id' => $menu_item->user_id,
                                'variant_type' => $orderItemVal->variant_id
                            ));
                        }
                    } else {
                        if ($mesage == "") {
                            $mesage = "Note: The menu items that have been deleted by the restaurant will not be added in the cart.";
                        }
                    }
                }
            }
            if ($mesage != "") {
                return Redirect::to('/order/confirm')->with('error_message', $mesage);
            } else {
                return Redirect::to('/order/confirm');
            }
        }
    }

    function cancelOrder($orderSlug = NULL) {
        $this->layout = false;

        // get order data

        $orderData = DB::table('orders')
                ->where('slug', $orderSlug)
                ->first();
        $numberofOrder = 1;

//        $main_ordershopData = DB::table('main_order')
//           // ->whereIn('order_id', $orders)
//            ->whereRaw("FIND_IN_SET('$orderData->order_number',order_id)")
//           
//            ->get();
//         
//        $numberofOrder = count(explode(',',$main_ordershopData[0]->order_id));

        $tax = $orderData->tax;
        $delivery_charge = $orderData->delivery_charge;
        $discount = $orderData->discount;


        // get Customer data
        $customerData = DB::table('users')
                ->where('users.id', $orderData->user_id)
                ->first();

        // get Cateter data
        $catererData = DB::table('users')
                ->where('users.id', $orderData->caterer_id)
                ->first();

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();


        $numberofOrder = 1;


        $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order
        if (!empty($orderData)) {

            $orderStatus = "Cancel";
            if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {

                $delivery_charge = $orderData->delivery_charge;
                $delivery_type = $orderData->delivery_type;
            } else {
                $delivery_charge = "0";
                $delivery_type = "N/A";
            }
            $orderNumber = $orderData->order_number;
            $customerContent = "";
            $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

            $customerContent .= '</table>';


            $orderContent = "";
            // send mails
            /// send mail to customer 
            $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';

            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';
            if (!empty($cartItems)) {

                $total = array();
                foreach ($cartItems as $cartData) {
                    //echo "<pre>"; print_r($cartData); exit;
                    $menuItem = DB::table('menu_item')
                            ->where('id', $cartData->menu_id)
                            ->first();
                    $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuItem->item_name . '</td>';


                    $variant_id = explode(',', $cartData->variant_id);
                    $menuDataVal = DB::table('variants')
                            ->whereIn('id', $variant_id)
                            ->get();
                    //   echo "<pre>"; print_r($menuDataVal); exit;

                    foreach ($menuDataVal as $menuData) {

                        $sub_total = $menuData->price * $cartData->quantity;

                        $total[] = $sub_total;
                        $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   <strong>Variant </strong> (' . $menuData->name . ')
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($menuData->price, 2) . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $cartData->quantity . '
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                </td>
                                  </tr>';
                    }


                    if ($cartData->addon_id != "") {
                        $addon_id = explode(',', $cartData->addon_id);
                        $menuDataVal = DB::table('addons')
                                ->whereIn('id', $addon_id)
                                ->get();
                        foreach ($menuDataVal as $menuData) {
                            $sub_total = $menuData->addon_price * $cartData->quantity;

                            $total[] = $sub_total;
                            //  echo $sub_total;
                            $orderContent .= '<tr>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       <strong>Add-on </strong> (' . $menuData->addon_name . ')
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($menuData->addon_price, 2) . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . $cartData->quantity . '
                                    </td>
                                    <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                       ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                                    </td>
                                      </tr>';
                        }
                    }


                    $orderContent .= '</tr>';
                }
            }

            $catererData = DB::table('users')
                    ->where('id', $orderData->caterer_id)
                    ->first();
            $gTotal = array_sum($total);
//            if ($orderData->tax) {
//                $tax = $orderData->tax;
//            } else {
//                $tax = 0;
//            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . ' ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';



            if ($orderData->discount) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal - $orderData->discount;
            }
            if ($adminuser->is_tax) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax / $numberofOrder, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax / $numberofOrder;
            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge / $numberofOrder, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge / $numberofOrder;

            $newcnn = '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $customerCty = $orderContent . $newcnn . '</table>';


            //echo $customerCty; exit;


            $mail_data = array('text' => 'Order status changed successfully as ' . $orderStatus . '  on ' . SITE_TITLE . '.',
                'orderContent' => $customerCty,
                'sender_email' => $customerData->email_address,
                'firstname' => $customerData->first_name . ' ' . $customerData->last_name,
            );

//            if ($adminuser->is_commission == 1) {
//
//                $comm_per = $adminuser->commission;
//                $tax_amount = $comm_per * $gTotal / 100;
//
//
//                $orderContent .= '<tr>
//                            <td colspan="3" valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
//                               Admin Commission
//                            </td>
//                            <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
//                               ' . App::make("HomeController")->numberformat($tax_amount, 2) . '
//                            </td>
//                              </tr>';
//                $gTotal = $gTotal - $tax_amount;
//            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';



            //echo $orderContent; exit;

            /*             * * send mail to customer ** */


            //return View::make('emails.template')->with($mail_data); // to check mail template data to view

            Mail::send('emails.template', $mail_data, function($message) use($mail_data) {
                $message->setSender(array(
                    MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject('Order status changed successfully');
            });

            /*             * * send mail to restaurant ** */
            $caterer_mail_data = array(
                'text' => 'Order status changed successfully as ' . $orderStatus . '  on ' . SITE_TITLE . '.', 'orderContent' => $orderContent,
                'sender_email' => $catererData->email_address,
                'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
            );

            //   return View::make('emails.template')->with($caterer_mail_data); // to check mail template data to view

            Mail::send('emails.template', $caterer_mail_data, function($message) use($caterer_mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($caterer_mail_data['sender_email']
                        , $caterer_mail_data['firstname'])->subject('Order cancelled successfully');
            });


            /*             * * send mail to admin ** */

            $admin_mail_data = array(
                'text' => 'Order status changed successfully as ' . $orderStatus . '  on ' . SITE_TITLE . '.',
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'sender_email' => $adminuser->email,
                'firstname' => "Admin",
            );

            // return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

            Mail::send('emails.template', $admin_mail_data, function($message) use ($admin_mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($admin_mail_data['sender_email'], 'Admin')->subject('Order cancelled successfully');
            });


            DB::table('orders')
                    ->where('id', $orderData->id)
                    ->update(['status' => $orderStatus, 'cancel_by_user' => '1']);
        }
        return Redirect::to('/order/view/' . $orderSlug)->with('success_message', 'Order cancelled successfully.');
    }

    public function showcourierview($slug = null) {

        $this->logincheck('order/courierview/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Courier') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Order Details';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $details = DB::table('order_courier')
                ->where('slug', $slug)
                ->first();

        $orderData = DB::table('orders')
                ->where('id', $details->order_id)
                ->first();
        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }

        $this->layout->content = View::make('/Orders/showcourierview')
                ->with('userData', $userData)
                ->with('details', $details);

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $input = Input::all();

        $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order

        if (!empty($input)) {
            $inputStatus = $input['status'];
            switch ($input['status']) {
                case "Confirm":
                    $cMessage = "Confirmed";
                    $orderStatus = "Confirmed";
                    break;
                case "Cancel":
                    $cMessage = "Cancelled";
                    $orderStatus = "Cancelled";
                    break;
            }


            if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {

                $delivery_charge = $orderData->delivery_charge;
                $delivery_type = $orderData->delivery_type;
            } else {
                $delivery_charge = "0";
                $delivery_type = "N/A";
            }
            $orderNumber = $orderData->order_number;
            $customerContent = "";
            $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Customer Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->first_name . ' ' . $customerData->last_name . '
                                </td>
                            </tr>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Customer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $customerData->contact . '
                                </td>
                            </tr>';

            $customerContent .= '</table>';


            $orderContent = "";
            // send mails
            /// send mail to customer 
            $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                 </td>
                            </tr>';

            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';
            if (!empty($cartItems)) {

                $total = array();
                foreach ($cartItems as $cartData) {


                    $menuData = DB::table('menu_item')
                                    ->where('id', $cartData->menu_id)->first();  // get menu data from menu table

                    $sub_total = $cartData->base_price * $cartData->quantity;
                    $total[] = $sub_total;

                    $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuData->item_name . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($cartData->base_price, 2) . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . $cartData->quantity . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                    </td>
                    </tr>';
                }
            }

            $catererData = DB::table('users')
                    ->where('id', $orderData->caterer_id)
                    ->first();
            $gTotal = array_sum($total);
            if ($orderData->tax) {
                $tax = $orderData->tax;
            } else {
                $tax = 0;
            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';
            if ($orderData->discount) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal - $orderData->discount;
            }
            if ($adminuser->is_tax) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax;
            }
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge;
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';



            /*             * * send mail to admin ** */
            $courier_name = $userData->first_name . ' ' . $userData->last_name;
            $admin_mail_data = array(
                'text' => 'Order status changed successfully by courier  "' . $courier_name . '" ' . $orderStatus . '  on ' . SITE_TITLE . '.',
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'sender_email' => $adminuser->email,
                'firstname' => "Admin",
            );

//            return View::make('emails.template')->with($admin_mail_data); // to check mail template data to view

            Mail::send('emails.template', $admin_mail_data, function($message) use($admin_mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($admin_mail_data['sender_email'], 'Admin')->subject('Order status changed successfully by courier on ' . SITE_TITLE . '.');
            });


            DB::table('order_courier')
                    ->where('id', $details->id)
                    ->update(['status' => $inputStatus]);

            DB::table('orders')
                    ->where('id', $orderData->id)
                    ->update(['courier_id' => $userData->id]);

            return Redirect::to('/order/courierorders')->with('success_message', 'You have successfully ' . $cMessage . ' order ' . SITE_TITLE . '.');
        }
    }

    public function showMyorders($slug = null) {

        $this->logincheck('user/myorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->chkUserType('Customer');

        // get my all addresses        
        $query = DB::table('orders');

        if ($slug != "") {
            $mainData = DB::table('main_order')
                    ->where('slug', $slug)
                    ->first();
            //  print_r($mainData);

            $query->where('orders.user_id', $user_id)
                    ->whereIn('orders.order_number', explode(',', $mainData->order_id))
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        } else {
            $query->where('orders.user_id', $user_id)
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        }

        $records = $query->orderBy('orders.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'My Orders';
        $this->layout->content = View::make('/Orders/myorders')
                ->with('userData', $userData)
                ->with('slug', $slug)
                ->with('records', $records);
    }

    public function showFavorders($slug = null) {

        $this->logincheck('orders/favorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->chkUserType('Customer');

        // get my all addresses        
        $query = DB::table('orders');
        if ($slug != "") {
            $mainData = DB::table('main_order')
                    ->where('slug', $slug)
                    ->first();
            //  print_r($mainData);

            $query->where('orders.user_id', $user_id)
                    ->whereIn('orders.order_number', explode(',', $mainData->order_id))
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        } else {
            $query->where('orders.user_id', $user_id)->where('orders.is_favorite', 1)
                    ->join('users', 'users.id', '=', 'orders.caterer_id')
                    ->select('orders.*', 'users.first_name', 'users.slug as restroslug');
        }

        $records = $query->orderBy('orders.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Favourite Orders';
        $this->layout->content = View::make('/Orders/favorders')
                ->with('userData', $userData)
                ->with('slug', $slug)
                ->with('records', $records);
    }

    public function showMainorders() {

        $this->logincheck('user/mainorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Customer') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->chkUserType('Customer');

        // get my all addresses        
        $query = DB::table('main_order');
        $query->where('main_order.user_id', $user_id)
                ->select('main_order.*');
        $records = $query->orderBy('main_order.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'My Orders';
        $this->layout->content = View::make('/Orders/mainorders')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showreceivedorders($type = 'all') {

        $this->logincheck('user/receivedorders/' . $type);

        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }

        if ($this->chkUserType('Restaurant') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }
        $input = Input::all();
        $search_keyword = "";
        $orderstatus = "";
        $search_to = "";
        $search_end = "";
        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
//print_r($input);die;
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        if (!empty($input['status'])) {
            $orderstatus = trim($input['status']);
        }
        if (!empty($input['search_to'])) {
            $search_to = trim($input['search_to']);
        }
        if (!empty($input['search_end'])) {
            $search_end = trim($input['search_end']);
        }
        // get my all addresses     

        $query = DB::table('orders');
//        $query->where('orders.caterer_id', $user_id)
//                
//                ->select('orders.*');

        $query = Order::sortable()
                ->where('orders.caterer_id', $user_id)
                ->where('delivery_date', "<", date('Y-m-d'))
                ->where(function ($query) use ($search_keyword) {
            $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
        });
        if ($type == 'new') {
            $query->where('delivery_date', "=", date('Y-m-d'));
        } else if ($type == 'confirm') {
            $query->where('status', 'Confirm');
        } else if ($type == 'cancel') {
            $query->where('status', 'Cancel');
        }

        if ($search_to) {
            $query->where('created', ">=", $search_to);
        }
        if ($search_end) {
            $query->where('created', "<=", $search_end);
        } else {
            $query->where('created', "<=", date('Y-m-d'));
        }
        $records = $query->orderBy('orders.id', 'desc')->paginate(10);
//        echo '<pre>'; print_r($records);die;
        // get all posted input
//dd(DB::getQueryLog());  
        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Orders History';
        $this->layout->content = View::make('/Orders/receivedorders')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('orderstatus', $orderstatus)
                ->with('search_to', $search_to)
                ->with('search_end', $search_end)
                ->with('type', $type)
                ->with('records', $records);
    }

    public function showcourierorders() {

        $this->logincheck('user/courierorders');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Courier') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all addresses        
        $query = DB::table('order_courier')
                ->select("order_courier.*", "users.first_name", "users.last_name", "orders.order_number", "orders.slug as order_slug")
                ->join('users', 'users.id', ' = ', 'order_courier.user_id')
                ->join('orders', 'orders.id', ' = ', 'order_courier.order_id');
        $query->where('order_courier.user_id', $user_id);
        $records = $query->orderBy('order_courier.id', 'desc')->paginate(10);

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Assinged Orders';
        $this->layout->content = View::make('/Orders/courierorders')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    function notify_customer($orderSlug = NULL) {
        $this->layout = false;

        // get order data
        $orderData = DB::table('orders')
                ->where('orders.slug', $orderSlug)
                ->first();

        // get Customer data
        $customerData = DB::table('users')
                ->where('users.id', $orderData->user_id)
                ->first();

        // get Cateter data
        $catererData = DB::table('users')
                ->where('users.id', $orderData->caterer_id)
                ->first();

        $adminuser = DB::table('admins')
                ->where('id', '1')
                ->first();

        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $couirerData = DB::table('users')
                ->where('id', $orderData->courier_id)
                ->first();



        $cartItems = DB::table('order_item')->whereIn('order_id', explode(',', $orderData->id))->get(); // get cart menu of this order
        if (!empty($orderData)) {


            if (isset($orderData->delivery_charge) && $orderData->delivery_charge != '') {

                $delivery_charge = $orderData->delivery_charge;
                $delivery_type = $orderData->delivery_type;
            } else {
                $delivery_charge = "0";
                $delivery_type = "N/A";
            }
            $orderNumber = $orderData->order_number;
            $customerContent = "";
            $customerContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $customerContent .= '<td valign="top" style="color: rgb(0, 0, 0); word-wrap: break-word; font-weight: bold; font-size: 14px; text-align: center; background-color: rgb(108, 158, 22); padding: 7px;" colspan="4">Courier Details</td>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Courier Name: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $couirerData->first_name . ' ' . $couirerData->last_name . '
                                </td>
                            </tr>';
            $customerContent .= '<tr>
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Couirer Contact Number: 
                                </td>
                                
                                <td colspan="2" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    ' . $couirerData->contact . '
                                </td>
                            </tr>';

            $customerContent .= '</table>';


            $orderContent = "";
            // send mails
            /// send mail to customer 
            $orderContent .= '<table style="border:1px solid #ddd; width:100%; border-collapse: collapse; text-align: left;">';
            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 16px;padding: 10px;word-wrap: break-word; background-color:rgb(108, 158, 22); font-weight:bold; text-align:center;">
                                    Order Details
                                </td>
                            </tr>';

            $orderContent .= '<tr>
                                <td colspan="4" valign="top" style="color: #000;font-size: 12px;padding: 10px;word-wrap: break-word; background-color:#fff; font-weight:bold; text-align:left;">
                                    Order Number: ' . $orderNumber . '
                                </td>
                                
                            </tr>';

            $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;border-bottom:1px solid #ddd;word-wrap: break-word;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Items
                                </td>
                                 <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Base Price
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Quantity
                                </td>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#efefef; font-weight:bold;">
                                   Sub Total
                                </td>
                            </tr>';
            if (!empty($cartItems)) {

                $total = array();
                foreach ($cartItems as $cartData) {


                    $menuData = DB::table('menu_item')
                                    ->where('id', $cartData->menu_id)->first();  // get menu data from menu table

                    $sub_total = $cartData->base_price * $cartData->quantity;
                    $total[] = $sub_total;

                    $orderContent .= '<tr>
                                <td valign="top" style="color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                                   ' . $menuData->item_name . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($cartData->base_price, 2) . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . $cartData->quantity . '
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-bottom:1px solid #ddd;border-right:1px solid #ddd;">
                    ' . App::make("HomeController")->numberformat($sub_total, 2) . '
                    </td>
                    </tr>';
                }
            }

            $catererData = DB::table('users')
                    ->where('id', $orderData->caterer_id)
                    ->first();
            $gTotal = array_sum($total);
            if ($orderData->tax) {
                $tax = $orderData->tax;
            } else {
                $tax = 0;
            }

            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd; font-weight:normal;">
                    Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; border-bottom:1px solid #ddd; font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';
            if ($orderData->discount) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Discount
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    - ' . App::make("HomeController")->numberformat($orderData->discount, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal - $orderData->discount;
            }
            if ($adminuser->is_tax) {
                $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Tax
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($tax, 2) . '
                    </td>
                    </tr>';
                $gTotal = $gTotal + $tax;
            }
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; ;border-bottom:1px solid #ddd;font-weight:normal;">
                    Delivery Charge (' . $delivery_type . ')
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;border-bottom:1px solid #ddd;  font-weight:normal;">
                    ' . App::make("HomeController")->numberformat($delivery_charge, 2) . '
                    </td>
                    </tr>';
            $gTotal = $gTotal + $delivery_charge;
            $orderContent .= '<tr>
                    <td colspan = "3" valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd; font-weight:bold;">
                    Grand Total
                    </td>
                    <td valign = "top" style = "color: #000;font-size: 13px;padding:10px;word-wrap: break-word;border-right:1px solid #ddd;  font-weight:bold;">
                    ' . App::make("HomeController")->numberformat($gTotal, 2) . '
                    </td>
                    </tr>';

            $orderContent .= '</table>';

            /*             * * send mail to customer ** */
            $mail_data = array(
                'text' => 'Your order has been approved on ' . SITE_TITLE . '.',
                'customerContent' => $customerContent,
                'orderContent' => $orderContent,
                'sender_email' => $customerData->email_address,
                'firstname' => $customerData->first_name . ' ' . $customerData->last_name,
            );

            // return View::make('emails.template')->with($mail_data); // to check mail template data to view

            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['sender_email'], $mail_data['firstname'])->subject('Order status changed successfully');
            });
        }
        return Redirect::to('/admin/order/admin_index')->with('success_message', 'Notify Customer Successfully.');
    }

    function showModifyorders($slug = null, $orderSlug = null) {
        $this->layout = false;

        // get carters details
        $caterer = DB::table('users')
                ->where('users.slug', $slug)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->select("users.*", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name")
                ->first();


        $orderData = DB::table('orders')
                ->where("orders.slug", "=", $orderSlug)
                ->first();

        Session::put('modifyorder', '1');
        Session::put('order_id', $orderData->id);


        $cartItems = DB::table('order_item')->whereIn('menu_id', explode(',', $orderData->order_item_id))->get(); // get cart menu of this order

        if (!empty($cartItems)) {
            $cart = new Cart(new CartSession, new Cookie);
            foreach ($cartItems as $cartData) {

                // get item details
                $item = DB::table('menu_item')
                        ->where('menu_item.id', $cartData->menu_id)
                        ->select("item_name", "price")
                        ->first();

                $cart->insert(array(
                    'id' => $cartData->menu_id,
                    'order_item_id' => $cartData->id,
                    'name' => $item->item_name,
                    'price' => $cartData->base_price,
                    'quantity' => $cartData->quantity,
                    'submenus' => $cartData->submenus
                ));
            }
        }
        return Redirect::to('/caterers/menu/' . $slug);
    }

    public function showtoday($type = 'all') {

//        echo '<pre>'; print_r($type);
        //   $this->logincheck('order/today');

        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Restaurant') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }
        $input = Input::all();
        $search_keyword = "";
        $orderstatus = "";
        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
            //    echo $search_keyword; exit;
        }
        if (!empty($input['status'])) {
            $orderstatus = trim($input['status']);
            //    echo $search_keyword; exit;
        }
        // get my all addresses     
//        $query = DB::table('orders');
        $start = date('Y-m-d');
        $query = Order::sortable()
                ->where('orders.caterer_id', $user_id)
                ->whereRaw('delivery_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
//                ->where('delivery_date', "=", date('Y-m-d'))
                ->whereRaw("DATE(delivery_date) = '$start'");
//                ->where(function ($query) use ($search_keyword) {
//            $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
//        });

        if ($type == 'new') {
            $query->where('status', "=", 'Pending');
        } else if ($type == 'confirm') {
            $query->where('status', 'Confirm');
        } else if ($type == 'cancel') {
            $query->where('status', 'Cancel');
        }
        if ($search_keyword) {
            $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
        }
//        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');

        $records = $query->orderBy('orders.id', 'desc')->paginate(10);
//        echo '<prE>'; dd(DB::getQueryLog());
        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . "Today's Orders";
        $this->layout->content = View::make('/Orders/todayorders')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('orderstatus', $orderstatus)
                ->with('type', $type)
                ->with('records', $records);
    }

    public function showschedule($type = 'all') {

        $this->logincheck('order/scheduleorders' . $type);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        if ($this->chkUserType('Restaurant') == false) {
            Session::put('error_message', "You do not have permission for access it!");
            return Redirect::to('/user/myaccount');
        }
        $input = Input::all();
        $search_keyword = "";
        $orderstatus = "";
        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
            //    echo $search_keyword; exit;
        }
        if (!empty($input['status'])) {
            $orderstatus = trim($input['status']);
            //    echo $search_keyword; exit;
        }
        // get my all addresses     

        $query = DB::table('orders');
//        $query->where('orders.caterer_id', $user_id)
//                
//                ->select('orders.*');

        $query = Order::sortable()
                ->where('orders.caterer_id', $user_id)
//                ->where('created', ">", DATE_ADD( NOW(), INTERVAL 1 HOUR )
                ->whereRaw('delivery_date > DATE_ADD( NOW(), INTERVAL 2 HOUR)')
                ->where(function ($query) use ($search_keyword) {
            $query->where('order_number', 'LIKE', '%' . $search_keyword . '%');
        });
        if ($type == 'schedule') {
            $query->where('status', 'Pending');
        } else if ($type == 'cancel') {
            $query->where('status', 'Cancel');
        }
//                ->where(function ($query) use ($orderstatus) {
//            $query->where('status', 'LIKE', '%' . $orderstatus . '%');
//        });

        $records = $query->orderBy('orders.id', 'desc')->paginate(10);
//dd(DB::getQueryLog()); 
        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Scheduled Orders';
        $this->layout->content = View::make('/Orders/scheduleorders')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('orderstatus', $orderstatus)
                ->with('type', $type)
                ->with('records', $records);
    }

    public function showPrint($slug = null) {

        $this->logincheck('order/printorder/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $this->layout = View::make('layouts.print');
        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

        if (empty($orderData)) {
            return Redirect::to('/user/myaccount');
        }
        $customerData = DB::table('users')
                ->where('id', $orderData->user_id)
                ->first();

        $this->layout->content = View::make('/Orders/printorders')->with('orderData', $orderData)->with('customerData', $customerData);
    }

    public function showcancelorder() {
        $input = Input::all();
        if (!empty($input['slug'])) {
            $slug = trim($input['slug']);
            //    echo $search_keyword; exit;
        }
        $orderData = DB::table('orders')
                ->where('slug', $slug)
                ->first();

        if ($orderData) {

            DB::table('orders')
                    ->where('id', $orderData->id)
                    ->update(['status' => 'Cancel']);
        }
    }

    public function showupdatetime() {
        $input = Input::all();
        if (!empty($input['id'])) {
            $id = trim($input['id']);
            //    echo $search_keyword; exit;
        }
        $orderData = DB::table('orders')
                ->where('id', $id)
                ->first();

        if ($orderData) {

            DB::table('orders')
                    ->where('id', $orderData->id)
                    ->update(['status' => 'Confirm', 'preparation_time' => $input['preparation_time']]);
        }
    }

    public function showeditorder() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();

        $order_slug = $input['order'];

        $orderData = DB::table('orders')
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug')
                ->where('orders.slug', $order_slug)
                ->first();

//        print_r($orderData);die;

        $html_view = View::make('/Orders/editorder')->with('order', $orderData)->with('userData', $userData);

        $html = $html_view->render();
        return $html;
    }

    public function showsubeditorder() {

        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();

//        echo '<pre>';
//        print_r($input);
//        exit;

        $order_status = $input['status'];

        $order_id = $input['order_id'];

        $orderDetail = DB::table('orders')->where('id', $order_id)->select('orders.*')->first();

        DB::table('order_item')->where('order_id', $order_id)->delete();

        $orderitemids = array();
        $orders = $input['order_item'];
        //echo '<pre>';print_r($input);exit;
        foreach ($orders as $orderAtr) {
            $itemDetail = DB::table('menu_item')->where('id', $orderAtr['item_id'])->select('menu_item.*')->first();
            $currentdate = date('Y-m-d H:i');
            if (!empty($orderAtr['addon_id'])) {
                $addon = implode(',', $orderAtr['addon_id']);
            } else {
                $addon = '';
            }
            $data = array(
                'user_id' => $orderDetail->user_id,
                'order_id' => $order_id,
                'slug' => $this->createSlug('cart'),
                'menu_id' => $orderAtr['item_id'],
                'caterer_id' => $user_id,
                'base_price' => $itemDetail->price,
                'quantity' => 1,
                'addon_id' => $addon,
                'created' => $currentdate
            );

            DB::table('order_item')->insert(
                    $data
            );
            $orderitemids[] = DB::getPdo()->lastInsertId();
        }


        $updata = array(
            'status' => $order_status,
            'order_item_id' => implode(',', $orderitemids),
            'item_total' => $input['subtotal'],
            'tax' => $input['taxt_calculate'],
            'total' => $input['total_amount'],
            'discount' => $input['hordiscount']
        );


//
        DB::table('orders')
                ->where('id', $order_id)
                ->update($updata); // update order status

        echo 'success';
        exit;
    }

    public function showvieworder() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();

        $order_slug = $input['order'];

        $orderData = DB::table('orders')
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.address', 'users.slug as restroslug')
                ->where('orders.slug', $order_slug)
                ->first();

//        print_r($orderData);die;

        $html_view = View::make('/Orders/vieworder')->with('order', $orderData)->with('userData', $userData);

        $html = $html_view->render();
        return $html;
    }

    //Invoices Page
    public function showInvoices() {

        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $query = DB::table('orders');

        $date = date('Y-m-d');
        $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $date))
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


//        $orders = $query->orderBy('orders.id', 'desc')->paginate(10);
        $orders = $query->orderBy('orders.id', 'desc')->get();

        $query1 = DB::table('orders');
        $query1->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $date))
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select(DB::raw("SUM(total) as totalsale"));
        $totalsales = $query1->orderBy('orders.id', 'desc')->get();

        $this->layout->title = TITLE_FOR_PAGES . 'Order And Reservation Invoices';
        $this->layout->content = View::make('/Orders/invoices')->with('userData', $userData)
                ->with('totalsales', $totalsales)
                ->with('orders', $orders);
    }

    public function showReserveInvoices() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return 'errorlogin';
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
//        echo '<pre>';
//        print_r($input);
//        exit;
        $search_keyword = "";

        if (!empty($input['keyword'])) {
            $search_keyword = trim($input['keyword']);
        }
        $start = $input['current_dat'];

        if (isset($input['serch_mt']) && $input['serch_mt'] == 'range') {
            $dates = explode(' - ', $input['current_dat']);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    //->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
//                ->where('reservations.reservation_date', "=", date('Y-m-d'));
                    ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('reservation_date', array($start_date, $end_date));
            });
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('reservations.reservation_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $records = $query->orderBy('reservations.id', 'desc')->get();
        } else {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    //->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
//                ->where('reservations.reservation_date', "=", date('Y-m-d'));
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->whereRaw("DATE(reservation_date) = '$start'");

            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('reservations.reservation_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $records = $query->orderBy('reservations.id', 'desc')->get();
        }

//        echo'<pre>';
//        dd(DB::getQueryLog());
//        exit;
        // set content view and title

        $html = View::make('/Orders/reserveinvoice')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('records', $records);

        return $html->render();
    }

    public function showSearchInvoices() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return 'errorlogin';
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $search_keyword = "";

        if (!empty($input['keyword'])) {
            $search_keyword = trim($input['keyword']);
        }
        $start = $input['current_dat'];
        if (isset($input['serch_mt']) && $input['serch_mt'] == 'range') {
            $dates = explode(' - ', $input['current_dat']);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id))
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('orders.delivery_date', array($start_date, $end_date));
                    })
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');

            $orders = $query->orderBy('orders.id', 'desc')->get();

            $query1 = DB::table('orders');
            $query1->where(array('orders.caterer_id' => $user_id))
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('orders.delivery_date', array($start_date, $end_date));
                    })
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select(DB::raw("SUM(total) as totalsale"));
            $totalsales = $query1->orderBy('orders.id', 'desc')->get();
        } else {
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

            $orders = $query->orderBy('orders.id', 'desc')->get();

            $query1 = DB::table('orders');
            $query1->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']))
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select(DB::raw("SUM(total) as totalsale"));
            $totalsales = $query1->orderBy('orders.id', 'desc')->get();
        }
        $view = View::make('/Orders/searchorder', compact('orders'))
                        ->with('userData', $userData)->with('totalsales', $totalsales);

        $html = $view->render();
        //print_r($input);exit;
        return $html;
    }

    public function showPrintOrdInvoices() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $search_keyword = "";

        if (!empty($input['keyword'])) {
            $search_keyword = trim($input['keyword']);
        }
        if (isset($input['serch_mt']) && $input['serch_mt'] == 'range') {
            $dates = explode(' - ', $input['current_dat']);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id))
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('orders.delivery_date', array($start_date, $end_date));
                    })
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');

            $orders = $query->orderBy('orders.id', 'desc')->get();

            $query1 = DB::table('orders');
            $query1->where(array('orders.caterer_id' => $user_id))
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('orders.delivery_date', array($start_date, $end_date));
                    })
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select(DB::raw("SUM(total) as totalsale"));
            $totalsales = $query1->orderBy('orders.id', 'desc')->get();
        } else {
            $start = $input['current_dat'];
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

            $orders = $query->orderBy('orders.id', 'desc')->get();

            $query1 = DB::table('orders');
            $query1->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']))
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select(DB::raw("SUM(total) as totalsale"));
            $totalsales = $query1->orderBy('orders.id', 'desc')->get();
        }
        $view = View::make('/Orders/printsearchorder', compact('orders'))
                        ->with('userData', $userData)->with('totalsales', $totalsales);

        $html = $view->render();
        //print_r($input);exit;
        return $html;
    }

    public function showPrintResInvoices() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $search_keyword = "";

        if (!empty($input['keyword'])) {
            $search_keyword = trim($input['keyword']);
        }
        if (isset($input['serch_mt']) && $input['serch_mt'] == 'range') {
            $dates = explode(' - ', $input['current_dat']);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    //->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
                    //                ->where('reservations.reservation_date', "=", date('Y-m-d'));
                    ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('reservation_date', array($start_date, $end_date));
            });
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('reservations.reservation_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $records = $query->orderBy('reservations.id', 'desc')->get();
        } else {
            $start = $input['current_dat'];
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    //->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
                    //                ->where('reservations.reservation_date', "=", date('Y-m-d'));
                    ->whereRaw("DATE(reservation_date) = '$start'");
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('reservations.reservation_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }
            $records = $query->orderBy('reservations.id', 'desc')->get();
        }
//       echo'<pre>'; dd(DB::getQueryLog());  exit;
        // set content view and title

        $html = View::make('/Orders/printreserveinvoice')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('records', $records);
        return $html->render();
    }

    public function showOrderHistory() {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();

        $query = DB::table('orders');

        $date = date('Y-m-d');
        $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $date))
                ->whereRaw("(tbl_orders.status='Complete' or tbl_orders.status='Cancel')")
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


        $orders = $query->orderBy('orders.order_by', 'desc')->get();
        // dd(DB::getQueryLog());
        // get all posted input
        $input = Input::all();



        $this->layout->title = TITLE_FOR_PAGES . "History";
        $this->layout->content = View::make('/Orders/history', compact('orders'))
                ->with('userData', $userData);
    }

    public function showOrderHistorySearch() {
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

        if (isset($input['serch_mt']) && $input['serch_mt'] == 'range') {
            $dates = explode(' - ', $input['current_dat']);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));

            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->whereRaw("(tbl_reservations.reservation_status='Complete' or tbl_reservations.reservation_status='Cancel')")
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    //->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
                    //                ->where('reservations.reservation_date', "=", date('Y-m-d'));
                    ->where(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('reservations.reservation_date', array($start_date, $end_date));
            });
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('reservations.reservation_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }


            $records = $query->orderBy('reservations.id', 'desc')->get();
            //echo'<pre>'; dd(DB::getQueryLog());  exit;
            // set content view and title
        } else {
            $start = $input['current_dat'];
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->whereRaw("(tbl_reservations.reservation_status='Complete' or tbl_reservations.reservation_status='Cancel')")
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    //->whereRaw('reservation_date < DATE_ADD( NOW(), INTERVAL 2 HOUR)')
                    //                ->where('reservations.reservation_date', "=", date('Y-m-d'));
                    ->whereRaw("DATE(reservation_date) = '$start'");
            if (!empty($search_keyword)) {
                $query->where(function ($query) use ($search_keyword) {
                    $query->where('reservations.reservation_number', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.first_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.last_name', 'LIKE', '%' . $search_keyword . '%')
                            ->orwhere('reservations.contact', 'LIKE', '%' . $search_keyword . '%');
                });
            }


            $records = $query->orderBy('reservations.order_by', 'desc')->get();
        }
        $html = View::make('/Orders/showreservation')->with('search_keyword', $search_keyword)
                ->with('userData', $userData)
                ->with('records', $records);
        return $html->render();
    }

    public function showorHistorySearch() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        if (isset($input['serch_mt']) && $input['serch_mt'] == 'range') {
            $dates = explode(' - ', $input['current_dat']);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));
            $search_keyword = $input['keyword'];
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id))
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereBetween('orders.delivery_date', array($start_date, $end_date));
                    })
                    ->whereRaw("(tbl_orders.status='Complete' or tbl_orders.status='Cancel')")
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


            $orders = $query->orderBy('orders.id', 'desc')->get();
        } else {
            $search_keyword = $input['keyword'];
            $query = DB::table('orders');
            $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']))
                    ->whereRaw("(tbl_orders.status='Complete' or tbl_orders.status='Cancel')")
                    ->where(function ($query) use ($search_keyword) {
                        $query->where('order_number', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.first_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
                        ->orwhere('users.contact', 'LIKE', '%' . $search_keyword . '%');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug');


            $orders = $query->orderBy('orders.order_by', 'desc')->get();
        }
        //dd(DB::getQueryLog());

        $view = View::make('/Orders/nextordersearch', compact('orders'))
                ->with('userData', $userData);

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

//        print_r($cat);
//        die;

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
            $query->where(array('orders.caterer_id' => $user_id, 'orders.delivery_date' => $input['current_dat']))->whereRaw("(tbl_orders.status='Complete' or tbl_orders.status='Cancel')");
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

//        echo '<pre>';
//        print_r($orders);
//        die;
        $view = View::make('/Reservation/taborder', compact('orders'))->with('cat', $cat);

        $html = $view->render();
        return $html;
    }

    public function showtabreserve() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();

        $cat = $input['cat'];
//        echo $cat;die
        $search_keyword = $input['keyword'];
        $current_dat = $input['current_dat'];

        if ($cat == 'complete') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'Complete')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.id', 'desc')->get();
        }

        if ($cat == 'noshow') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'No Show')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.id', 'desc')->get();
        }

        if ($cat == 'cancel') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    ->where('reservations.reservation_status', "=", 'Cancel')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.id', 'desc')->get();
        }

        if ($cat == 'all') {
            $query = Reservation::sortable()
                    ->join('users', 'reservations.caterer_id', '=', 'users.id')
                    ->where('reservations.caterer_id', "=", $user_id)
                    ->whereRaw("(tbl_reservations.reservation_status='Complete' or tbl_reservations.reservation_status='Cancel')")
                    ->select('reservations.*', 'users.first_name as res_first_name', 'users.last_name as res_last_name', 'users.email_address as user_email_address', 'users.phone1 as user_phone')
                    //->where('reservations.reservation_status', "=", 'Cancel')
                    ->whereRaw("DATE(reservation_date) = '$current_dat'");

            $records = $query->orderBy('reservations.order_by', 'desc')->get();
        }

        $html = View::make('/Orders/showtabreserve')
                ->with('userData', $userData)
                ->with('records', $records);
        return $html->render();
    }

    public function showhisvieworder() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();

        $order_slug = $input['order'];

        $orderData = DB::table('orders')
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.slug as restroslug')
                ->where('orders.slug', $order_slug)
                ->first();

        $html_view = View::make('/Orders/hisvieworder')->with('order', $orderData);

        $html = $html_view->render();
        return $html;
    }

    public function showhisviewreserve() {
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

        $html_view = View::make('/Orders/viewhisreservation')->with('order', $orderData);

        $html = $html_view->render();
        return $html;
    }

    public function showsubeditreser() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();

//        echo '<pre>';
//        print_r($input);
//        exit;

        $order_id = $input['order_id'];
        $data = array(
            'first_name' => trim($input['first_name']),
            'last_name' => trim($input['last_name']),
            'contact' => $input['contact'],
            'address' => $input['address'],
            'email_address' => $input['email_address'],
            'size' => $input['size'],
            'note' => $input['note'],
            'reservation_status' => $input['reservation_status']
        );

        DB::table('reservations')
                ->where('id', $order_id)
                ->update($data);
        return 'success';
        exit;
    }

    public function showsubeditorderstatus() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $order_status = $input['status'];
        $order_id = $input['order_id'];
        $orderDetail = DB::table('orders')->where('id', $order_id)->select('orders.*')->first();

        $updata = array(
            'status' => $order_status
        );


        if ($order_status == 'Cancel') {
            $updata['order_by'] = 3;
        } else if ($order_status == 'Complete') {
            $updata['order_by'] = 2;
        } else if ($order_status == 'Confirm') {
            $updata['order_by'] = 1;
        } else {
            $updata['order_by'] = 0;
        }

        DB::table('orders')
                ->where('id', $order_id)
                ->update($updata); // update order status

        echo 'success';
        exit;
    }

    public function showchangeorstatusall() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $order_status = $input['status'];
        $order_id = $input['order_id'];

        $orderDetail = DB::table('orders')->where('id', $order_id)->select('orders.*')->first();

        $updata = array(
            'status' => $order_status
        );

        DB::table('orders')
                ->where('id', $order_id)
                ->update($updata); // update order status


        $orderData = DB::table('orders')
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.contact', 'users.address', 'users.slug as restroslug')
                ->where('orders.id', $order_id)
                ->first();

        $html_view = View::make('/Orders/shownstatusnew')->with('order', $orderData)->with('userData', $userData);
        $html = $html_view->render();
        return $html;
    }

}
