<?php

class HomeController extends BaseController {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected $layout = 'layouts.homedefault';

    //protected $layouts = 'front.homedefault';
    public function showHome() {  
        
        $day = date('D');
        $data = DB::table('users')
                ->select('users.slug as userslug', 'users.id as userid', 'offers.slug as offersslug', 'offers.id as offersid', 'offers.*', 'opening_hours.open_close as hrstatus', 'opening_hours.*','users.*')
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
                ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1')
                ->where("offers.type", "=", 'percentage')
                ->where("offers.status", "=", '1')
                ->where("offers.flagstatus", "=", '1')
                ->where("opening_hours.open_close", "=", '1')
                ->groupBy('offers.user_id')
                //->orderBy('offers.created', 'DESC')
                ->orderBy('offers.discount', 'DESC')
                ->orderBy('offers.created', 'DESC')
                ->limit(3)
                ->get();
        //$this->layout = View::make('layouts.landing');
        $this->layout->title = 'Welcome :: Bitebargain ' . TITLE_FOR_PAGES;
        $this->layout->content = View::make('front.index')->with('data', $data);
        
    }

    public function signup() {
        $input = Input::all();
        $email_address = trim($_POST['email']);

        $queryuseremail = DB::table('users')
                ->where('cust_email', '=', $email_address)
                ->first();
        /*$queryuserphone = DB::table('users')
                ->where('cust_phone', '=', $_POST['phone'])
                ->first();*/

        if (!empty($queryuseremail)) {
            // echo 'Sorry! Email id is already exist !.';exit;
            echo 'erremail';
            exit;
        }
        /* if (!empty($queryuserphone)) {
             return Redirect::to('/')->with('error_message', 'Sorry! Phone number is already exist !.'); 
            echo 'errphone';
            exit;
        }*/


        /* $rules = array(

          'cust_name' => 'required|unique:users', // make sure the email address field is not empty
          'cust_password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
          'cust_email' => 'required|unique:users|email', // make sure the email address field is not empty
          'cust_phone' => 'required',
          );
          $validator = Validator::make(Input::all(), $rules);

          // if the validator fails, redirect back to the form
          if ($validator->fails()) {

          return Redirect::to('/')->withErrors($validator)->withInput(Input::all());
          }
          else
          { */

        $array = array(
            'first_name' => $_POST['name'],
            'last_name' => $_POST['lastname'],
            'slug' => App::make("BaseController")->createUniqueSlug($_POST['name'], 'users'),
            'email_address' => $_POST['email'],
            'password' => md5($_POST['pwd']),
            //'contact' => $_POST['phone'],
            'cust_name' => $_POST['name'],
            'cust_lastname' => $_POST['lastname'],
            //'cust_phone' => $_POST['phone'],
            'cust_email' => $_POST['email'],
            'cust_password' => md5($_POST['pwd']),
            'plain_pwd' => $_POST['pwd'],
            'status' => '1',
            'created' => date('Y-m-d H:i:s'),
        );
        DB::table('users')->insert(
                $array
        );


        $userEmail = $_POST['email'];
        // send email to administrator
        $mail_data = array(
            'text' => 'Your account is successfully created Below are your login credentials.',
            'email' => $_POST['email'],
            'password' => $_POST['pwd'],
            'cust_name' => $_POST['name'],
        );
        //print_r($mail_data);exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
        Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {

            $message->setSender(array(MAIL_FROM => SITE_TITLE));

            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
            $message->to($mail_data['email'], $mail_data['cust_name'])->subject('Your account successfully created');
        });

        $data = DB::table('users')
                ->where("users.cust_email", "=", $_POST['email'])
                ->first();
        Session::put('userdata', $data);
        return Redirect::to('/listing')->with('success', 'Welcome');

        /* Redirect::to('/')->with('success_message', 'Your account is created successfully. Now you can login with your details.'); */

        //echo 'success'; exit;
    }

    public function logincheck() {

        //$this->layout = View::make('layouts.landing');

        /*  if (Session::has('adminid')) {
          return Redirect::to('/admin/admindashboard');
          exit;
          } */

        //echo 'hii';die;


        $input = Input::all();
        //print_r($input);
        if (!empty($input)) {

            $username = trim($input['username']);
            $password = md5(trim($input['password'])); // exit;
            // print_r($password);
            $rules = array(
                'username' => 'required', // make sure the username field is not empty
                'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                return Redirect::to('/')
                                ->withErrors($validator) // send back all errors to the login form
                                ->withInput(Input::except('password'));
                // ->withInput(Input::except('captcha')); // send back the input (not the password) so that we can repopulate the form
            } else { //echo"hoo";
                $userData = DB::table('users')
                        ->where('cust_email', $username)
                        ->where('cust_password', $password)
                        ->where('status', "=", '1')
                        ->first();
                $userPhone = DB::table('users')
                        ->where('cust_phone', $username)
                        ->where('cust_password', $password)
                        ->where('status', "=", '1')
                        ->first();
                
                if (!empty($userData)) {
                    Session::put('userdata', $userData);
                    //echo"hh";exit;
                    // create our user data for the authentication
                    /* $userData = DB::table('users')
                      ->where('email_address', $username)
                      ->orwhere('username', $username)
                      ->where('password', $password)
                      ->first(); */

//                    echo '<pre>'; print_r($password);die;
                    // check activation status
                    /* if ($userData->status == 0) {
                      Session::put('error_message', "Your account might have been temporarily disabled.");
                      return Redirect::to('/');
                      } */

                    if ($input['Users']['rememberme'] == 1) { //echo"rem";exit;
                        Session::put('email_address', $username); // 30 days
                        Session::put('planPass', $input['password']); // 30 days
                        Session::put('remember', '1'); // 30 days
                    } else {
                        Session::put('email_address', ''); // 30 days
                        Session::put('password', ''); // 30 days
                        Session::put('planPass', '');
                        Session::put('remember', ''); // 30 days
                    }

                    return Redirect::to('/listing')->with('success', 'Welcome');
                    // return to dashboard page
                    /* Session::put('user_id', $userData->id);
                      Session::forget('captcha');
                      return Redirect::to('/user/myaccount'); */
                }if (!empty($userPhone)) {
                    Session::put('userdata', $userPhone);


                    if (isset($input['Users']['rememberme'])) { //echo"rem";exit;
                        Session::put('email_address', $username); // 30 days
                        Session::put('planPass', $input['password']); // 30 days
                        Session::put('remember', '1'); // 30 days
                    } else {
                        Session::put('email_address', ''); // 30 days
                        Session::put('password', ''); // 30 days
                        Session::put('remember', ''); // 30 days
                    }

                    return Redirect::to('/listing')->with('success', 'Welcome');
                } else {
                    return Redirect::to('/')->with('error_message', 'Please check Username or Password Credentials and try again or your account is not activated till yet!.');
                }
            }
        }
        //$this->layout->title = TITLE_FOR_PAGES . 'Sign In';
        //$this->layout->content = View::make('home.index');
    }

    public function showForgotpassword() {
        // $this->layout = false;
        $input = Input::all();
        //print_r( $input);exit;
        $email = $input['username'];
        $rules = array(
            'username' => 'required'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($input, $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            $errors->first('username');
            return json_encode(array('message' => $errors, 'valid' => 0));
            exit;
        } else {

            // create our user data for the authentication
            $userData = DB::table('users')
                    ->where('cust_email', $email)
                    ->first();
            // print_r($userData);exit;        
            if (!empty($userData)) {
                // generate random password
                $userEmail = $userData->cust_email;

                $userid = md5($userData->id);
                $user_id = $userData->id;
                $resetLink = "Please reset your password on Bitebargain<br/><a href='" . HTTP_PATH . "home/resetPassword/" . $user_id . "/" . $userid . "'>Click here</a> to reset your password</a>";


                // send email to user
                $mail_data = array(
                    // 'text' => 'Please reset your password.',
                    'email' => $userData->cust_email,
                    'resetLink' => $resetLink,
                    'firstname' => $userData->cust_name,
                );


                DB::table('users')
                        ->where('id', $user_id)
                        ->update(['forget_password_status' => 1]);

                // return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Reset Your Password');
                });

                /* echo json_encode(array('message' => 'A link to reset your password was sent to your email address. Please reset your password', 'redirect' => HTTP_PATH, 'valid' => true));
                  die; */
                // end mail script

                /* return json_encode(array('message' => 'Your password has been sent on your email id.', 'valid' => 1));
                  exit; */
                return Redirect::to('/')->with('success_message', 'A link to reset your password was sent to your email address. Please reset your password');
            } else {

                // return error message
                /* return json_encode(array('message' => 'You have entered wrong email address please re-enter.', 'valid' => 0));
                  exit; */
                return Redirect::to('/')->with('error_message', 'You have entered wrong email address please re-enter !.');
            }
        }
    }

    public function showResetPassword($user_id = null, $md_user_id = null) {
        $segment1 = Request::segment(3);
        $segment4 = Request::segment(4);
        // echo $segment4;exit;
        $userData = DB::table('users')
                ->where('id', $segment1)
                ->first();
        // print_r($userData);exit;
        if ($userData->forget_password_status == 0) {

            Session::put('error_message', "You have already use this link.");
            $this->layout->content = View::make('Users.resetPassword');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Reset Password';
        $this->layout->content = View::make('Users.resetPassword');

        /* if (Session::has('user_id')) {
          return Redirect::to('/user/myaccount');
          } */
        //echo"hh";exit;

        $input = Input::all();
        // print_r($input);exit;
        if (!empty($input)) {

            $password = md5($input['password']);
            $plianpwd = $input['password'];
            $rules = array(
                'password' => 'required|min:8', // make sure the password field is not empty
                'cpassword' => 'required', // make sure the confirm password field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Redirect::to('/home/resetPassword')
                                ->withErrors($validator) // send back all errors to the login form
                                ->withInput(Input::except('password'));
            } else {

                // create our user data for the authentication
                // echo"hh";exit;
                //print_r($userData->plain_pwd);exit;
                if ($input['password'] == $userData->plain_pwd) {
                    return Redirect::back()->with('error_message', 'Sorry you had enter password same as previous password please enter new password !');
                }

                if (!empty($userData)) {
                    $segment1 = Request::segment(3);
                    //echo $segment1;exit;
                    // check activation status
                    // print_r($userData);exit;
                    if ($userData->forget_password_status == 0) {
                        Session::put('captcha', 1);
                        Session::put('error_message', "You have already use this link.");
                        return Redirect::to('/home/resetPassword/' . $user_id . '/' . $md_user_id);
                    }
                    // echo"ff";exit;
                    DB::table('users')
                            ->where('id', $segment1)
                            ->update(['cust_password' => $password, 'plain_pwd' => $plianpwd]);

                    DB::table('users')
                            ->where('id', $segment1)
                            ->update(['forget_password_status' => 0]);




                    // return to dashboard page

                    return Redirect::to('/')->with('forgotsuccess_message', 'Your password changed succesfully. Please login. !.');
                } else {

                    // return error message
                    Session::put('captcha', 1);
                    Session::put('error_message', "Invalid email or password");
                    return Redirect::to('/home/resetPassword/' . $user_id . '/' . $md_user_id);
                }
            }
        }
    }

    public function termsconditions() {
        $this->layout->title = 'Terms & Conditions' . TITLE_FOR_PAGES;
        $this->layout->content = View::make('termsconditions.index');
    }

    public function contactus() {
        $input = Input::all();
        $email_address = trim($input['email']);
        $rules = array(
            'firstname' => 'required',
            'lastname' => 'required', // make sure the email address field is not empty
            'email' => 'required|email', // make sure the email address field is not empty
            'phone' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {

            return Redirect::to('/')->withErrors($validator)->withInput(Input::all());
        } else {

            $array = array(
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'message' => $input['message'],
                'created' => date('Y-m-d H:i:s'),
            );

            DB::table('contactus')->insert(
                    $array
            );


            $userEmail = $input['email'];
            // send email to administrator
            $mail_data = array(
                'text' => 'Your message is successfully send to Bitebargain our Team contact you soon',
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'email' => $input['email'],
                'message2' => $input['message'],
                'contact_number' => $input['phone'],
            );
            //print_r($mail_data);exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {

                $message->setSender(array(MAIL_FROM => SITE_TITLE));

                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your message is successfully send');
            });




            return Redirect::to('/')->with('success_message', 'Your message is successfully send to Bitebargain our Team contact you soon.');
        }
    }

    function subscribe(){
        $input = Input::all();
        $email = $input['s_email'];
        $rules = array(
            'email' => 'trim|required|email', // make sure the email address field is not empty
        );
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {

            return Response::json(array('status' => 0, 'error' => $validator->errors()->first()))->setCallback(Input::get('callback'));
        } else {

            $array = array(
                'email' => $input['email'],
                's_status' => 0,
                's_added_datetime' => date('Y-m-d H:i:s'),
                's_update_datetime' => date('Y-m-d H:i:s'),
            );

            DB::table('tbl_subscription')->insert(
                    $array
            );


            
            // send email to administrator
            $mail_data = array(
                'text' => 'Thank you for subscription',
                'email' => $email,
            );
            
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {

                $message->setSender(array(MAIL_FROM => SITE_TITLE));

                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                $message->to($mail_data['email'], '')->subject('subscription');
            });




            return Response::json(array('status' => 1, 'error' => 'success'))->setCallback(Input::get('callback'));
        }
    }
    
    function unsubscribe(){
        
    }
    function showgetRestaurants(){
        $day = date('D');
        $section = 'delivery';
        $html = '';
        $section = $_POST['section'];
        $search_keyword = $_POST['location'];
        $data = DB::table('users')
                ->select('users.id','users.slug','offers.discount','users.slug','users.first_name','users.profile_image','users.delivery_cost','users.minimum_order','users.cuisines','users.average_price','users.first_name')
                ->join('offers', 'offers.user_id', '=', 'users.id')
                ->join('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                //->join('menu_item', 'menu_item.cuisines_id', '=', 'cuisines.id')
                ->whereRaw('FIND_IN_SET("'.$day.'", tbl_opening_hours.open_days)')
                ->where(function ($query) use ($search_keyword) {
                    $query->Where('users.address', 'LIKE', '%' . $search_keyword . '%');
                    $query->Where('users.city', 'LIKE', '%' . $search_keyword . '%');
                    $query->orWhere('users.state', 'LIKE', '%' . $search_keyword . '%');
                })->Where(function($query) {
                    $query->where("offers.type", "=", 'percentage');
                    $query->where("offers.flagstatus", "=", '1');
                    $query->where("offers.status", "=", '1');
                })
                ->where("users.user_type", "=", 'Restaurant')
                ->where("users.status", "=", '1')
                ->where("opening_hours.open_close", "=", '1')
                ->groupBy('offers.user_id')
                ->orderBy('offers.discount', 'DESC')
                ->orderBy('offers.created', 'DESC')
                ->limit(3)
                ->get();
 

    if($data){ 
        foreach($data as $r){
            $img = (isset($r->profile_image) && ($r->profile_image)) ? HTML::image(DISPLAY_FULL_PROFILE_IMAGE_PATH . $r->profile_image,'',array('width' => '350px','highth' => '250px')) : HTML::image("public/listingimg/food_a.png");
            $uid = $r->id;
            if($section == 'delivery'){
                
                $current_time = date('h:i A');
                $current_time = strtotime($current_time);
                $frac = 1800;

                $q = $current_time % $frac;
                $f_time = $current_time + ($frac - $q);
                $f_slot_time = date('h:i A', $f_time);

                $c_slot_time = strtotime($f_slot_time) - (30 * 60);
                $c_slot_time = date('h:i A', $c_slot_time);

                $l_time = $current_time - ($frac + $q);
                $p_slot_time = date('h:i A', $l_time);
                
                $html .= '<div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                    <div class="card br-0 custom_card border-0 mb-5">
                                        <div class="card_img position-relative">
                                            <div class="tag position-absolute">
                                                '.$r->discount.'% off on all menu
                                            </div>
                                            <a href="'.HTTP_PATH.'restaurantdetail/'.$r->slug.'"> 
                                                '.$img.'
                                            </a>
                                        </div>
                                        <div class="card-body px-0">
                                            <h4 class="card-title">  <div class="product_title">'.$r->first_name.'</div> <button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i> ' . $r->average_price . '</button> <span class="float-right"> N/A KM</span></h4> 
                                            <ul class="list-unstyled big_size">
                                                <li class="d-inline-block"><a href="'.HTTP_PATH.'restaurantdetail/'.$r->slug.'">' . str_replace(',', ' | ', $r->cuisines) .'</li>
                                            </ul>
                                            <ul class="list-unstyled radio-toolbar ">
                                                <li class="d-inline-block">

                                                    <input type="radio" id="discount" name="radioFruit" value="apple" checked>
                                                    <label for="discount"><span>'.$p_slot_time.'</span>
                                                        <b>'.$r->discount.'% off</b>
                                                    </label>
                                                </li>
                                                <li class="d-inline-block">
                                                    <input type="radio" id="radioBanana" name="radioFruit" value="banana">
                                                    <label for="radioBanana"><span>'.$c_slot_time.'</span>
                                                        <b>'.$r->discount.'% off</b></label>
                                                </li>
                                                <li class="d-inline-block"> <input type="radio" id="radioOrange" name="radioFruit" value="orange">
                                                    <label for="radioOrange"><span>'.$f_slot_time.'</span>
                                                        <b>'.$r->discount.'% off</b></label>
                                                </li>
                                            </ul>
                                       

                                            <ul class="list-unstyled">
                                                <li class="d-inline-block"><a href="">Free Delivery Above <i class="fa fa-inr"></i> ' . $r->delivery_cost . '</a></li>
                                                <li class="d-inline-block"><a href="">Min. Order <i class="fa fa-inr"></i> ' . $r->minimum_order . '</a></li>
                                            </ul>

                                        </div>
                                    </div>  

                                </div>';
            
        } else {
            $html .= '<div class="col-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="card br-0 custom_card border-0 mb-5">
                                    <div class="card_img position-relative">
                                        <div class="tag position-absolute">
                                            '.$r->discount.'% off on all menu
                                        </div>
                                        <a href="'.HTTP_PATH.'restaurantdetail/'.$r->slug.'"> 
                                            '.$img.'
                                        </a>
                                    </div>
                                    <div class="card-body px-0">
                                        <h4 class="card-title">  <div class="product_title">'.$r->first_name.'</div> <button type="button" class="btn rounded-btn"><i class="fa fa-inr"></i> ' . $r->average_price . '</button> <span class="float-right"> N/A KM</span></h4> 
                                        <ul class="list-unstyled big_size">
                                            <li class="d-inline-block"><a href="'.HTTP_PATH.'restaurantdetail/'.$r->slug.'">' . str_replace(',', ' | ', $r->cuisines) .'</li>
                                        </ul>
                                       
                                        <ul class="list-unstyled">
                                            <li class="d-inline-block"><a href="">Free Delivery Above <i class="fa fa-inr"></i> ' . $r->delivery_cost . '</a></li>
                                            <li class="d-inline-block"><a href="">Min. Order <i class="fa fa-inr"></i> ' . $r->minimum_order . '</a></li>
                                        </ul>

                                    </div>
                                </div>  

                            </div>';
        }
        
            }
    } else {
        $html = "No more restaurants are available!";
    }
        $html = "<div class='row'>".$html."</div>";
        return $html;
    }
}
