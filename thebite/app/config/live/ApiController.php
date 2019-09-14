<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class ApiController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default User Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    protected $layout = 'layouts.default';

    public function register() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        //print_r($_REQUEST['data']); exit;
        //print_r($dataJson); exit;


        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $email_address = trim($dataJson['email_address']);

            $ifexist = DB::table('users')
                    ->where('email_address', $email_address)
                    ->first();
            //print_r($ifexist); exit;
            if (!empty($ifexist)) {

                echo $this->errorOutput('Email address is already exists.');
                exit;
            }
            $first_name = $dataJson['first_name'];
            $last_name = $dataJson['last_name'];
            $contact_number = $dataJson['contact_number'];

            $saveUser = array(
                'first_name' => $dataJson['first_name'],
                // 'city' => $dataJson['city'],
                //  'area' => $dataJson['area'],
                'last_name' => $dataJson['last_name'],
                'email_address' => $email_address,
                'contact' => $dataJson['contact_number'],
                //  'address' => $dataJson['address'],
                'device_id' => $dataJson['device_id'],
                'device_type' => $dataJson['device_type'],
                'language' => $dataJson['language'],
                'password' => md5($dataJson['password']),
                'slug' => $this->createUniqueSlug($dataJson['first_name'], 'users'),
                'user_type' => "Customer",
                'created' => date('Y-m-d H:i:s'),
            );

            DB::table('users')->insert(
                    $saveUser
            );


            $user_id = DB::getPdo()->lastInsertId();

            // setup for activation link
            $reset_data = array(
                'user_id' => $user_id,
                'status' => '1',
                'type' => 'signup',
                'created' => date('Y-m-d H:i:s'),
                'code' => rand(90786778678, 8978978867857678),
                'slug' => $this->createUniqueSlug($dataJson['first_name'], 'reset_link'),
            );
            DB::table('reset_link')->insert(
                    $reset_data
            );
            $reset_link = HTTP_PATH . "activateprofile/" . $reset_data['slug'];

            // send email to user
            $userEmail = $dataJson['email_address'];

            // send email to administrator
            $mail_data = array(
                'text' => 'Your account is successfully created. Your registration is being reviewed and pending acceptance. After acceptance you can use below credentials for login to ' . SITE_TITLE . '.',
                'email' => $dataJson['email_address'],
                'password' => $dataJson['password'],
                'firstname' => $dataJson['first_name'],
                'resetLink' => '<a href="' . $reset_link . '">Click here<a> to activate your account.'
            );
            // print_r($mail_data); exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created');
                    });

            // get admin data
            $adminuser = DB::table('admins')
                    ->where('id', 1)
                    ->first();
            $adminEmail = $adminuser->email;

            // send email to administrator
            $mail_data = array(
                'text' => 'A request for new account has been received on ' . SITE_TITLE . '. Below are the details.',
                'name' => $first_name,
                'contact_number' => $contact_number,
                'email_address' => $email_address,
                'adminEmail' => $adminEmail,
                'firstname' => "Admin"
            );

            //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['adminEmail'], $mail_data['firstname'])->subject('New Account Request on ' . SITE_TITLE . ' for Customer');
                    });

            echo $this->successOutput('Congratulation! You are registered successfully, please check your email to activate your account. We are checking your details and will contact you shortly.');
            exit;
            //  return Redirect::to('/user/register')->withSuccess('You have successfully register on ' . SITE_TITLE);
        }
    }

    public function restroregister() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        //print_r($_REQUEST); exit;

        if (!empty($dataJson)) {

            $email_address = trim($dataJson['email_address']);

            $ifexist = DB::table('users')
                    ->where('email_address', $email_address)
                    ->first();
            //print_r($ifexist); exit;
            if (!empty($ifexist)) {

                echo $this->errorOutput('Email address is already exists.');
                exit;
            }
            $name = $dataJson['name'];
            $contact_number = $dataJson['contact_number'];
            $location = $dataJson['location'];

            $saveUser = array(
                'first_name' => $dataJson['name'],
                'city' => $dataJson['city'],
                'area' => $dataJson['area'],
                'email_address' => $email_address,
                'contact' => $dataJson['contact_number'],
                'address' => $dataJson['location'],
                'device_id' => $dataJson['device_id'],
                'device_type' => $dataJson['device_type'],
                'language' => $dataJson['language'],
                'paypal_email_address' => $dataJson['paypal_email_address'],
                'password' => md5($dataJson['password']),
                'slug' => $this->createUniqueSlug($dataJson['name'], 'users'),
                'user_type' => "Restaurant",
                'created' => date('Y-m-d H:i:s'),
            );

            DB::table('users')->insert(
                    $saveUser
            );


            $user_id = DB::getPdo()->lastInsertId();

            $data = array(
                'user_id' => $user_id,
                'open_days' => 'mon,tue,wed,thu,fri,sat,sun',
                'status' => '1',
                'start_time' => "08:00:00",
                'end_time' => "23:00:00",
                'created' => date('Y-m-d H:is')
            );
            DB::table('opening_hours')
                    ->insert($data);

            // setup for activation link
            $reset_data = array(
                'user_id' => $user_id,
                'status' => '1',
                'type' => 'signup',
                'created' => date('Y-m-d H:i:s'),
                'code' => rand(90786778678, 8978978867857678),
                'slug' => $this->createUniqueSlug($dataJson['name'], 'reset_link'),
            );
            DB::table('reset_link')->insert(
                    $reset_data
            );
            $reset_link = HTTP_PATH . "activateprofile/" . $reset_data['slug'];

            // send email to user
            $userEmail = $dataJson['email_address'];

            // send email to administrator
            $mail_data = array(
                'text' => 'Your account is successfully created. Your registration is being reviewed and pending acceptance. After acceptance you can use below credentials for login to ' . SITE_TITLE . '.',
                'email' => $dataJson['email_address'],
                'password' => $dataJson['password'],
                'firstname' => $dataJson['name'],
                'resetLink' => '<a href="' . $reset_link . '">Click here<a> to activate your account.'
            );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created as Restaurant');
                    });


            // get admin data
            $adminuser = DB::table('admins')
                    ->where('id', 1)
                    ->first();
            $adminEmail = $adminuser->email;

            // send email to administrator
            $mail_data = array(
                'text' => 'A request for new account has been received on ' . SITE_TITLE . '. Below are the details.',
                'name' => $name,
                'location' => $location,
                'contact_number' => $contact_number,
                'email_address' => $email_address,
                //    'message2' => $message,
                'adminEmail' => $adminEmail,
                'firstname' => "Admin"
            );

            //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
            Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($mail_data['adminEmail'], $mail_data['firstname'])->subject('New Account Request on ' . SITE_TITLE . ' for Restaurant');
                    });


            echo $this->successOutput('Congratulation! You are registered successfully, please check your email to activate your account. We are checking your details and will contact you shortly.');
            exit;
            //  return Redirect::to('/user/register')->withSuccess('You have successfully register on ' . SITE_TITLE);
        }
    }

    public function login() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {

            $email_address = $dataJson['email'];
            $planPass = $dataJson['password'];
            $password = md5($dataJson['password']);

            $userData = DB::table('users')
                    ->where('email_address', $email_address)
                    ->where('password', $password)
                    ->first();
            // }
            if (!empty($userData)) {

                if ($userData->activation_status == 0) {
                    echo $this->errorOutput('Your email address is not verified yet. Please check your email for verification link to verify your profile');
                    exit;
                }

                // check admin approval
                if ($userData->approve_status == 0) {
                    echo $this->errorOutput('We are checking your details and will contact you shortly once we approve your account');
                    exit;
                }

                // check activation status
                if ($userData->status == 0) {
                    echo $this->errorOutput('Your account might have been temporarily disabled.');
                    exit;
                }
                $device_id = $dataJson['email'];
                $device_type = $dataJson['device_type'];
                $language = $dataJson['language'];

                DB::table('users')
                ->where('id', $userData->id)
                ->update(['device_id' => "$device_id", 'device_type' => "$device_type", 'language' => "$language"]);

                if (empty($userData->profile_image)) {
                    $userData->profile_image = "";
                }
                if (empty($userData->country_id)) {
                    $userData->country_id = 0;
                }
                if (empty($userData->address)) {
                    $userData->address = "";
                }

                echo $this->output(json_encode($userData));
                exit;
            } else {
                echo $this->errorOutput('Invalid email or password.');
                exit;
            }
        }
    }

    public function forgotpassword() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        if (!empty($dataJson)) {

            $email_address = $dataJson['email'];

            // create our user data for the authentication
            $userData = DB::table('users')
                    ->where('email_address', $email_address)
                    ->first();

            if (!empty($userData)) {
                $userEmail = $userData->email_address;

                $userid = md5($userData->id);
                $user_id = $userData->id;
                $resetLink = "<a href='" . HTTP_PATH . "user/resetPassword/" . $user_id . "/" . $userid . "'>Click here</a> for reset your password</a>";

                // send email to user
                $mail_data = array(
                    'text' => 'Please reset your password.',
                    'email' => $userData->email_address,
                    'resetLink' => $resetLink,
                    'firstname' => $userData->first_name . ' ' . $userData->last_name,
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


                echo $this->successOutput('A link to reset your password was sent to your email address. Please reset your password');
                exit;
            } else {

                // return error message
                echo $this->errorOutput('We cannot recognize your email address.');
                exit;
            }
        }
    }

    public function showResetPassword($user_id = null, $md_user_id = null) {

        $this->layout->title = TITLE_FOR_PAGES . 'Reset Password';
        $this->layout->content = View::make('/Users/resetPassword');

        if (Session::has('user_id')) {
            return Redirect::to('/user/myaccount');
        }


        $dataJson = Input::all();
        if (!empty($dataJson)) {

            $password = md5($dataJson['password']);
            $rules = array(
                'password' => 'required|min:8', // make sure the password field is not empty
                'cpassword' => 'required', // make sure the confirm password field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($dataJson, $rules);
            if ($validator->fails()) {
                return Redirect::to('/user/resetPassword')
                                ->withErrors($validator) // send back all errors to the login form
                                ->withInput(Input::except('password'));
            } else {

                // create our user data for the authentication
                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();


                if (!empty($userData)) {
                    // check activation status
                    if ($userData->forget_password_status == 0) {
                        Session::put('captcha', 1);
                        Session::put('error_message', "You have already use this link.");
                        return Redirect::to('/user/resetPassword/' . $user_id . '/' . $md_user_id);
                    }
                    DB::table('users')
                    ->where('id', $user_id)
                    ->update(['password' => $password]);

                    DB::table('users')
                    ->where('id', $user_id)
                    ->update(['forget_password_status' => 0]);




                    // return to dashboard page
                    Session::put('success_message', "Your password changed succesfully. Please login.");
                    return Redirect::to('/');
                } else {

                    // return error message
                    Session::put('captcha', 1);
                    Session::put('error_message', "Invalid email or password");
                    return Redirect::to('/user/resetPassword/' . $user_id . '/' . $md_user_id);
                }
            }
        }
    }

    public function getCity() {

        $this->checkAPI($_REQUEST['api_key']);
        $cityData = DB::table('cities')
                ->select('cities.id', 'cities.name')
                ->where('cities.status', 1)
                ->get();
        echo $this->output(json_encode($cityData));
        exit;
    }

    public function getArea() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $cityData = DB::table('areas')
                ->select('areas.id', 'areas.name')
                ->where('areas.status', 1)
                ->where('areas.city_id', $dataJson['city_id'])
                ->get();
        echo $this->output(json_encode($cityData));
        exit;
    }

    public function editProfile() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);
        $user_id = $dataJson['user_id'];

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        if (!empty($dataJson)) {
            if ($userData->user_type == "Restaurant") {

                $rules = array(
                    'first_name' => 'required', // make sure the first name field is not empty
                    'contact' => 'required', // make sure the  contact field is not empty
                    'address' => 'required', // make sure the address field is not empty
                );
            } else {
                $rules = array(
                    'first_name' => 'required', // make sure the first name field is not empty
                    'last_name' => 'required', // make sure the last name field is not empty
                    'contact' => 'required', // make sure the  contact field is not empty
                    'address' => 'required', // make sure the address field is not empty
                );
            }

            if ($userData->user_type == "Restaurant") {

                $data = array(
                    'first_name' => $dataJson['first_name'],
                    'city' => $input['city'],
                    'area' => $input['area'],
                    //'last_name' => $dataJson['last_name'],
                    'contact' => $dataJson['contact'],
                    'address' => $dataJson['address'],
                );
            } else {
                $data = array(
                    'first_name' => $dataJson['first_name'],
                    'last_name' => $dataJson['last_name'],
                    'city' => $input['city'],
                    'area' => $input['area'],
                    'contact' => $dataJson['contact'],
                    'address' => $dataJson['address'],
                );
            }


            if ($userData->user_type == 'Restaurant') {
                // $data['deliver_to'] = implode(",", $dataJson['deliver_to']);
            }


            DB::table('users')
                    ->where('id', $user_id)
                    ->update($data);


            $userData = DB::table('users')
                    ->where('id', $user_id)
                    ->first();


            if (empty($userData->profile_image)) {
                $userData->profile_image = "";
            }
            if (empty($userData->country_id)) {
                $userData->country_id = 0;
            }
            if (empty($userData->address)) {
                $userData->address = "";
            }

            $data['response_status'] = 'success';
            $data['response_msg'] = 'Profile image updated successfully.';
            $data['response_data'] = $userData;

            echo json_encode($data);
            exit;
        }
    }

    public function changePassword() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $user_id = $dataJson['user_id'];
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();


        if (!empty($dataJson)) {
            $new_password = $dataJson['new_password'];
            $confirm_password = $dataJson['confirm_password'];
            $rules = array(
                'new_password' => 'required|min:8', // make sure the new password field is not empty
                'confirm_password' => 'required' // make sure the confirm password field is not empty
            );

            $oldDBpassword = $userData->password;

            $opassword = md5($dataJson['old_password']);



            $newPassword = md5($new_password);

            if ($oldDBpassword == $newPassword) {

                // return error message
                echo $this->errorOutput('Please do not enter new password same as old password');
                exit;
            }
            if ($oldDBpassword <> $opassword) {

                // return error message
                echo $this->errorOutput("Please enter correct old password");
                exit;
            } else {

                $data = array(
                    'password' => $newPassword,
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);

                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();


                if (empty($userData->profile_image)) {
                    $userData->profile_image = "";
                }
                if (empty($userData->country_id)) {
                    $userData->country_id = 0;
                }
                if (empty($userData->address)) {
                    $userData->address = "";
                }

                $data['response_status'] = 'success';
                $data['response_msg'] = 'Profile image updated successfully.';
                $data['response_data'] = $userData;

                echo json_encode($data);
                exit;
            }
        }
    }

    public function showList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);



        $query = DB::table('users');
        $query->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id');
        $query->leftjoin('areas', 'areas.id', '=', 'users.area');
        $query->leftjoin('cities', 'cities.id', '=', 'users.city');
        $query->leftjoin("menu_item", 'menu_item.user_id', '=', 'users.id')->groupBy('menu_item.user_id');

        $query->where('users.status', "=", 1);
        $query->where('users.approve_status', "=", 1);
        $query->where('users.user_type', "=", "Restaurant");





        // echo "<pre>"; print_r($dataJson); exit;
        // get search params
        $city = isset($dataJson['city']) ? addslashes($dataJson['city']) : "";
        $area = isset($dataJson['area']) ? addslashes($dataJson['area']) : "";
        $keyword = isset($dataJson['keyword']) ? addslashes($dataJson['keyword']) : "";
        $catering_type = isset($dataJson['catering_type']) ? explode(",", $dataJson['catering_type']) : array();
        $cuisine = isset($dataJson['cuisine']) ? explode(",", $dataJson['cuisine']) : array();

        $sort = isset($dataJson['sort']) ? addslashes($dataJson['sort']) : "";
        $order = isset($dataJson['order']) ? addslashes($dataJson['order']) : "asc";




        //$cuisine = (isset($dataJson['cuisine']) and $dataJson['cuisine'][0]) ? $dataJson['cuisine'] : array();
        //$catering_type = (isset($dataJson['catering_type']) and $dataJson['catering_type'][0]) ? $dataJson['catering_type'] : array();
        //
        //$query = User::sortable();
        //$query->leftjoin("menu_item", 'menu_item.user_id', '=', 'users.id')->groupBy('menu_item.user_id');

        if (!empty($cuisine)) {
            //print_r($cusinaname); exit;
            $query
                    ->where(function ($query) use ($cuisine) {
                                foreach ($cuisine as $c)
                                    $query->orwhere('menu_item.cuisines_id', '=', $c);
                            });
        }
        if ($city)
            $query->where('users.city', '=', $city);
        if ($area)
            $query->where('users.area', '=', $area);
        //$query->whereRaw("FIND_IN_SET('$area',users.area)");



        if (!empty($catering_type)) {
            $query->where(function ($query) use ($catering_type) {
                        foreach ($catering_type as $c)
                            $query->orwhereRaw("FIND_IN_SET('$c',tbl_opening_hours.catering_type)");
                    });
        }
        if ($keyword) {
            $keyword = trim($keyword);
            if (!empty($keyword)) {

                $cusinaname = DB::table('cuisines')
                        ->select('cuisines.id')
                        ->where("cuisines.name", "like", "%$keyword%")
                        ->first();
                if ($cusinaname) {
                    //print_r($cuisine);
                    if (isset($cuisine) && count($cuisine) > 0) {
                        $query->orwhere(function ($query) use ($cusinaname) {
                                    foreach ($cusinaname as $c)
                                    //echo 'menu_item.cuisines_id'.$c;
                                        $query->where('menu_item.cuisines_id', '=', $c);
                                });
                    } else {
                        $query->where(function ($query) use ($cusinaname) {
                                    foreach ($cusinaname as $c)
                                    //echo 'menu_item.cuisines_id'.$c;
                                        $query->where('menu_item.cuisines_id', '=', $c);
                                });
                    }
                } else {
                    $query->where('menu_item.item_name', "LIKE", "%$keyword%")
                            ->OrWhere(DB::raw("CONCAT_WS(' ', tbl_users.first_name,tbl_users.last_name)"), "like", "%$keyword%");
                }
                //print_r($cusinaname); exit;
//               
            }

//            $query->orwhere('menu_item.item_name', "=", "$keyword");
//            $query->orwhere(DB::raw("CONCAT_WS(' ', tbl_users.first_name,tbl_users.last_name)"), "like", "%$keyword%");
        }

        //echo "hello"; exit;
        //print_r($keyword);
        // Get all the users
//        $query->orderBy('users.id', 'desc')
//                        ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
//                        ->leftjoin('areas', 'areas.id', '=', 'users.area')
//                        ->leftjoin('cities', 'cities.id', '=', 'users.city')
//                        ->where('users.status', "=", "1")
//                        ->where('users.approve_status', "=", "1")
//                        ->where('users.user_type', "=", "'Restaurant'");
////                        ->offset($page)
////                        ->limit($order_per_page)
        //echo $sort; echo $order; exit;

        if (!empty($dataJson['page'])) {
            $page = $dataJson['page'];
        }
        if (!empty($dataJson['order_per_page'])) {
            $order_per_page = $dataJson['order_per_page'];
        }

        if ($page > 1) {
            $page = $page - 1;
            $pageData = $page * $order_per_page;
        } else {
            $pageData = 0;
        }
        if (empty($pageData)) {
            $pageData = "0";
        }
        if (empty($page)) {
            $page = "0";
        }

        if ($sort == "name") {
            $query->orderBy('users.first_name', $order);
        } elseif ($sort == "rating") {
            $query->orderBy('rating', $order);
        } else {
            $query->orderBy('users.id', 'desc');
        }

        //$restroCount = $query->get(array('users.*'));


        $query->offset($pageData);
        $query->limit($order_per_page);


        $restroData = $query->get(array('users.*', "areas.name as area_name", "cities.name as city_name", "opening_hours.open_close", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days", DB::raw("(select (avg(tbl_reviews.quality)+avg(tbl_reviews.packaging))/2 from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as rating"), DB::raw("(select count(tbl_reviews.id) from `tbl_reviews` where tbl_reviews.caterer_id = tbl_users.id and tbl_reviews.status = '1') as counter")));
        //echo "<pre>"; print_r($restroData); exit;


        $i = 0;
        $restro = array();

        //$count = sizeof($restroCount);


        if ($restroData) {
            foreach ($restroData as $restroDt) {
                $restro[$i] = (array) $restroDt;
                if (empty($restro[$i]['last_name'])) {
                    $restro[$i]['last_name'] = "";
                }
                if (empty($restro[$i]['country_id'])) {
                    $restro[$i]['country_id'] = "";
                }
                if (empty($restro[$i]['paypal_email_address'])) {
                    $restro[$i]['paypal_email_address'] = "";
                }
                if (empty($restro[$i]['device_id'])) {
                    $restro[$i]['device_id'] = "";
                }
                if (empty($restro[$i]['device_type'])) {
                    $restro[$i]['device_type'] = "";
                }
                if (empty($restro[$i]['language'])) {
                    $restro[$i]['language'] = "";
                }
                if (empty($restro[$i]['rating'])) {
                    $restro[$i]['rating'] = "";
                }


                $i++;
            }
            echo $this->output(json_encode($restro));
        } else {
            echo $this->errorOutput('No More Records.');
        }

        //$restro['restroCount'] = $count;


        exit;
    }

    function showMenu() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $restaurant_id = $dataJson['restaurant_id'];
        if (empty($restaurant_id)) {
            echo $this->errorOutput('Restaurant is required field.');
            exit;
        }

        $sort = isset($dataJson['sort']) ? addslashes($dataJson['sort']) : "";
        $order = isset($dataJson['order']) ? addslashes($dataJson['order']) : "asc";




        $caterer = DB::table('users')
                ->where('users.id', $restaurant_id)->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
                ->leftjoin('areas', 'areas.id', '=', 'users.area')
                ->leftjoin('cities', 'cities.id', '=', 'users.city')
                ->select("users.*", "opening_hours.open_close", "opening_hours.open_days", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "areas.name as area_name", "cities.name as city_name", 'opening_hours.catering_type')
                ->first();

        if (empty($caterer)) {
            echo $this->errorOutput('Invalid Restaurant Id.');
            exit;
        }

        //echo "<pre>"; print_r($caterer);
        // get caterer menu
        $query = DB::table('menu_item')
                ->where("menu_item.user_id", "=", $caterer->id)
                ->where("menu_item.status", "=", '1');




        if ($sort == "price") {
            $query->orderBy('cuisines.name', 'asc');
            $query->orderBy('menu_item.price', $order);
        } elseif ($sort == "loved") {
            $query->orderBy('cuisines.name', 'asc');
            $query->orderBy('counter', $order);
        } elseif ($sort == "item_name") {
            $query->orderBy('cuisines.name', 'asc');
            $query->orderBy('menu_item.item_name', $order);
        } else {
            $query->orderBy('cuisines.name', 'asc');
        }










//        if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'price') {
//            $items = $query
//                    ->orderBy('cuisines.name', 'asc')
//                    ->orderBy('menu_item.price', $order)
//                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
//                    ->select("cuisines.name as cuisines_name", "menu_item.*")
//                    ->paginate(10);
//        } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'loved') {
//            $items = $query
//                    ->orderBy('cuisines.name', 'asc')
//                    ->orderBy('counter', $order)
//                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
//                    ->select("cuisines.name as cuisines_name", "menu_item.*", DB::raw("(select count(tbl_favorite_menu.id) from `tbl_favorite_menu` where tbl_favorite_menu.menu_id = tbl_menu_item.id) as counter"))
//                    ->paginate(10);
//        } elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == 'item_name') {
//
//            $items = $query
//                    ->orderBy('cuisines.name', 'asc')
//                    ->orderBy('menu_item.item_name', $order)
//                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
//                    ->select("cuisines.name as cuisines_name", "menu_item.*")
//                    ->paginate(10);
//        } else {
//            $items = $query
//                    ->orderBy('cuisines.name', 'asc')
//                    ->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
//                    ->select("cuisines.name as cuisines_name", "menu_item.*")
//                    ->paginate(10);
//        }


        $query->orderBy('cuisines.name', 'asc');
        $query->leftjoin('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id');
//        $query->offset($pageData);
//        $query->limit($pageData, $order_per_page);
        $query->groupBy('cuisines_name');


        $items = $query->get(array("cuisines.name as cuisines_name", "menu_item.*"));


        //  echo "<pre>"; print_r($items);
        // get restaurant cuisines
        $cuisine = DB::table('cuisines')
                ->orderBy('cuisines.name', 'asc')
                ->where("menu_item.user_id", "=", $caterer->id)
                ->where("cuisines.status", "=", 1)
                ->join('menu_item', 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select("cuisines.name", "cuisines.id")
                ->groupBy('cuisines.id')
                ->get();


        $newArray = array();
        $response = array();
        $cuisineArray = array();


        if ($items) {
            $j = 0;
            foreach ($items as $item) {
                //echo $item->id;

                $value = $item->cuisines_name;
                $idd = $item->id;

                $variantData = DB::table('variants')
                        ->where('menu_id', $idd)
                        ->orderBy('parent', 'desc')
                        ->get();

                $addonData = DB::table('addons')
                        ->where('menu_id', $idd)
                        ->get();

                $item->variant = $variantData;
                $item->addon = $addonData;

                $newArray[$value][] = $item;


//                $newArray[$value][]['variant'] = $variantData;
//                $newArray[$value][]['addon'] = $addonData;


                $j++;
            }


            // echo "<pre>";print_r($newArray); exit;
            //  exit;

            $i = 0;
            foreach ($cuisine as $c) {
                $cuisineArray[$i]['id'] = $c->id;
                $cuisineArray[$i]['name'] = $c->name;
                $i++;
            }




            $response[] = $newArray;


            $data['response_status'] = 'success';
            $data['response_msg'] = '';
            $data['response_data'] = $newArray;
            $data['response_data_cuisine'] = $cuisineArray;

            echo json_encode($data);

//            echo $this->output(json_encode($response));
            exit;
        } else {
            echo $this->errorOutput('No Menu Added Yet.');
            exit;
        }
    }

    public function getCuisine() {

        $this->checkAPI($_REQUEST['api_key']);
        $cuisineData = DB::table('cuisines')
                ->select('cuisines.id', 'cuisines.name')
                ->where('cuisines.status', 1)
                ->orderBy('cuisines.name', 'asc')
                ->get();
        echo $this->output(json_encode($cuisineData));
        exit;
    }

    public function getMealType() {
        $this->checkAPI($_REQUEST['api_key']);
        $mealData = DB::table('mealtypes')
                ->select('mealtypes.id', 'mealtypes.name')
                ->where('mealtypes.status', 1)
                ->orderBy('mealtypes.name', 'asc')
                ->get();
        echo $this->output(json_encode($mealData));
        exit;
    }

    public function getBannerList() {
        $this->checkAPI($_REQUEST['api_key']);

        $bannerData = array();
        $bannerData[0]['id'] = 26;
        $bannerData[0]['slug'] = "73be0f1d9f865bbed9223d31dc8d80e8-jp";
        $bannerData[0]['image'] = URL::asset('public/img/front') . "/home_banner.png";
        $bannerData[0]['status'] = "1";
        $bannerData[0]['created'] = "2017-08-09 06:06:26";
        //$bannerData[] = "" 
        // echo "<pre>"; print_r($bannerData);
        echo $this->output(json_encode($bannerData));
        exit;
    }

    public function changePicture() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $user_id = $dataJson['user_id'];

        if (!empty($dataJson)) {
            if (isset($_FILES['profile_image']) && $_FILES['profile_image'] != '') {
                $file = $_FILES['profile_image'];
                $image = time() . $file->getClientOriginalName();
                $file->move(UPLOAD_FULL_PROFILE_IMAGE_PATH, time() . $file->getClientOriginalName());

                $data = array(
                    'profile_image' => $image,
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);

                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();


                if (empty($userData->profile_image)) {
                    $userData->profile_image = "";
                }
                if (empty($userData->country_id)) {
                    $userData->country_id = 0;
                }
                if (empty($userData->address)) {
                    $userData->address = "";
                }

                $data['response_status'] = 'success';
                $data['response_msg'] = 'Profile image updated successfully.';
                $data['response_data'] = $userData;

                echo json_encode($data);
                exit;
            } else {
                $msgString = "No Image Selected";
                echo $this->errorOutputResult($msgString);
                exit;
            }
        }
    }

    public function logout() {
        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $user_id = $dataJson['user_id'];
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();


        if (!empty($dataJson)) {
            $data = array(
                'device_id' => "",
                'device_type' => ""
            );
            DB::table('users')
                    ->where('id', $user_id)
                    ->update($data);

            $data['response_status'] = 'success';
            $data['response_msg'] = 'Profile image updated successfully.';
            $data['response_data'] = array();

            echo json_encode($data);
            exit;
        }
    }

    public function addressList() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        $user_id = isset($dataJson['user_id']) ? addslashes($dataJson['user_id']) : "";
        if (empty($user_id)) {
            echo $this->errorOutput('Invalid Request.');
            exit;
        }

        $query = DB::table('addresses');
        $query->leftjoin('areas', 'areas.id', '=', 'addresses.area');
        $query->leftjoin('cities', 'cities.id', '=', 'addresses.city');
        $query->where('addresses.user_id', "=", $user_id);


        if (!empty($dataJson['page'])) {
            $page = $dataJson['page'];
        }
        if (!empty($dataJson['order_per_page'])) {
            $order_per_page = $dataJson['order_per_page'];
        }

        if ($page > 1) {
            $page = $page - 1;
            $pageData = $page * $order_per_page;
        } else {
            $pageData = 0;
        }
        if (empty($pageData)) {
            $pageData = "0";
        }
        if (empty($page)) {
            $page = "0";
        }

        $query->orderBy('addresses.id', 'desc');

        $query->offset($pageData);
        $query->limit($order_per_page);


        $addressData = $query->get(array('addresses.*', "areas.name as area_name", "cities.name as city_name"));
        //echo "<pre>"; print_r($restroData); exit;

        if (empty($addressData)) {
            echo $this->output(json_encode($addressData));
            exit;
        } else {
            echo $this->errorOutput('No Address Records Saved.');
            exit;
        }
        exit;
    }

    public function addAddress() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $user_id = trim($dataJson['user_id']);
            $input = $dataJson;

            if (empty($input['address_title']) || empty($input['address_type']) || empty($input['city']) || empty($input['area']) || empty($input['street_name']) || empty($input['phone_number'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }


            $data = array(
                'address_title' => $input['address_title'],
                'address_type' => $input['address_type'],
                'city' => $input['city'],
                'area' => $input['area'],
                'street_name' => $input['street_name'],
                'building' => $input['building'],
                'floor' => $input['floor'],
                'apartment' => $input['apartment'],
                'phone_number' => $input['phone_number'],
                'extension' => $input['extension'],
                'directions' => $input['directions'],
                'user_id' => $user_id,
                'created' => date('Y-m-d H:i:s'),
                'status' => '1',
                'slug' => $this->createSlug($input['address_title'])
            );

            DB::table('addresses')->insert(
                    $data
            );
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    public function editAddress() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $user_id = trim($dataJson['user_id']);
            $address_id = trim($dataJson['address_id']);
            $input = $dataJson;

            if (empty($input['address_title']) || empty($input['address_type']) || empty($input['city']) || empty($input['area']) || empty($input['street_name']) || empty($input['phone_number'])) {
                echo $this->errorOutput('Invalid Request');
                exit;
            }

            $data = array(
                'address_title' => $input['address_title'],
                'address_type' => $input['address_type'],
                'city' => $input['city'],
                'area' => $input['area'],
                'street_name' => $input['street_name'],
                'building' => $input['building'],
                'floor' => $input['floor'],
                'apartment' => $input['apartment'],
                'phone_number' => $input['phone_number'],
                'extension' => $input['extension'],
                'directions' => $input['directions'],
            );

            DB::table('addresses')
                    ->where('id', $address_id)
                    ->update($data);
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }

    public function deleteAddress() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);

        if (!empty($dataJson)) {
            //print_r($dataJson); exit;
            $user_id = trim($dataJson['user_id']);
            $address_id = trim($dataJson['address_id']);

            DB::table('addresses')->where('id', $address_id)->delete();
        } else {
            echo $this->errorOutput('Invalid Request');
            exit;
        }
    }
    
    

    public function showOpeninghours() {
        $this->logincheck('user/openinghours');
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

        // get opening hours details
        $opening_hours = DB::table('opening_hours')
                ->where('user_id', $user_id)
                ->first();
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Opening Hours';
        $this->layout->content = View::make('/Users/openinghours')
                ->with('userData', $userData)
                ->with('opening_hours', $opening_hours);

        $dataJson = Input::all();


        if (!empty($dataJson)) {
            $rules = array(
                'open_days' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                //  'minimum_order' => 'required',
                'catering_type' => 'required'
            );
            $messages = array('catering_type.required' => 'Meal type field is required.');
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules, $messages);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/openinghours')
                                ->withErrors($validator);
            } else {
                $open_days = $dataJson['open_days'];
                $open_days = implode(',', $open_days);
                $open = $dataJson['start_time'];
                $close = $dataJson['end_time'];
                $data = array(
                    'open_days' => $open_days,
                    'start_time' => date("H:i", strtotime($dataJson['start_time'])),
                    'end_time' => date("H:i", strtotime($dataJson['end_time'])),
                    // 'minimum_order' => $dataJson['minimum_order'],
                    'catering_type' => implode(",", $dataJson['catering_type']),
                    'open_close' => isset($dataJson['open_close']) ? $dataJson['open_close'] : '0'
                );
                DB::table('opening_hours')
                        ->where('id', $opening_hours->id)
                        ->update($data);

                return Redirect::to('/user/myaccount')->with('success_message', 'Opening hours successfully updated.');
            }
        }
    }

    public function showManagemenu() {

        $dataJson = json_decode($_REQUEST['data'], true);
        $this->checkAPI($_REQUEST['api_key']);


        $user_id = $dataJson['user_id'];
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all menu        
        $query = DB::table('menu_item');
        $query->where('menu_item.user_id', $user_id)->join('cuisines', 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select('menu_item.*', 'cuisines.name');

        if (!empty($dataJson['page'])) {
            $page = $dataJson['page'];
            $page = $page - 1;
        }
        if (!empty($dataJson['order_per_page'])) {
            $order_per_page = $dataJson['order_per_page'];
        }


        $pageData = $page * $order_per_page . ",";
        if (empty($pageData) || $pageData == $order_per_page) {
            $pageData = ", ";
        }

        $records = $query->orderBy('menu_item.id', 'desc')->offset($page)
                ->limit($order_per_page)
                ->get();
        ;

        // get all posted input
        $dataJson = Input::all();

        echo $this->output(json_encode(compact('records')));
        exit;
    }

    public function showorderstatus() {
        $this->logincheck('user/showorderstatus');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all menu        
        $query = DB::table('orderstatus');
        $query->where('orderstatus.user_id', $user_id)
                ->select('orderstatus.*');
        $records = $query->orderBy('orderstatus.id', 'desc')->paginate(10);

        // get all posted input
        $dataJson = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Additional Order Stauts';
        $this->layout->content = View::make('/Users/orderstatus')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showAddmenu() {
        $this->logincheck('user/addmenu');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $dataJson = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add Menu';
        $this->layout->content = View::make('/Users/addmenu')
                ->with('userData', $userData);

        if (!empty($dataJson)) {

//            Validator::extend('img_min_size', function($attribute, $value, $parameters) {
//                        $file = Request::file($attribute);
//                        $image_info = getimagesize($file);
//                        $image_width = $image_info[0];
//                        $image_height = $image_info[1];
//                        if ((isset($parameters[0]) && $parameters[0] != 0) && $image_width < $parameters[0])
//                            return false;
//                        if ((isset($parameters[1]) && $parameters[1] != 0) && $image_height < $parameters[1])
//                            return false;
//                        return true;
//                    });

            $rules = array(
                'cuisine' => 'required',
                'item_name' => 'required',
                'price' => 'required',
                'preparation_time' => 'required',
                'image' => 'mimes:jpeg,png,jpg',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addmenu')
                                ->withErrors($validator);
            } else {

                $data = array(
                    'cuisines_id' => $dataJson['cuisine'],
                    'item_name' => $dataJson['item_name'],
                    'preparation_time' => $dataJson['preparation_time'],
                    'description' => $dataJson['description'],
                    'price' => $dataJson['price'],
                    // 'submenu' => $dataJson['submenu'],
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'slug' => $this->createSlug($dataJson['item_name'])
                );

                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $image = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_ITEM_IMAGE_PATH, time() . $file->getClientOriginalName());
                } else {
                    $image = "";
                }
                $data['image'] = $image;

                DB::table('menu_item')->insert(
                        $data
                );

                return Redirect::to('/user/managemenu')->with('success_message', 'Menu item successfully added.');
            }
        }
    }

    public function showaddstatus() {
        $this->logincheck('user/addstatus');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $dataJson = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add Status';
        $this->layout->content = View::make('/Users/addstatus')
                ->with('userData', $userData);

        if (!empty($dataJson)) {

//            Validator::extend('img_min_size', function($attribute, $value, $parameters) {
//                        $file = Request::file($attribute);
//                        $image_info = getimagesize($file);
//                        $image_width = $image_info[0];
//                        $image_height = $image_info[1];
//                        if ((isset($parameters[0]) && $parameters[0] != 0) && $image_width < $parameters[0])
//                            return false;
//                        if ((isset($parameters[1]) && $parameters[1] != 0) && $image_height < $parameters[1])
//                            return false;
//                        return true;
//                    });

            $rules = array(
                'status_name' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addstatus')
                                ->withErrors($validator);
            } else {

                $data = array(
                    'status_name' => $dataJson['status_name'],
                    // 'submenu' => $dataJson['submenu'],
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'slug' => $this->createSlug($dataJson['status_name'])
                );



                DB::table('orderstatus')->insert(
                        $data
                );

                return Redirect::to('/user/orderstatus')->with('success_message', 'Status successfully added.');
            }
        }
    }

    public function showEditmenu($slug = "") {

        $this->logincheck('user/editmenu/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get menu item details
        $menudata = DB::table('menu_item')
                ->where('slug', $slug)
                ->first();
        if (empty($menudata)) {
            // redirect to the menu page
            return Redirect::to('/user/managemenu')->with('error_message', 'Something went wrong, please try after some time.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Menu';
        $this->layout->content = View::make('/Users/editmenu')
                ->with('userData', $userData)
                ->with('menudata', $menudata);
        $dataJson = Input::all();


        if (!empty($dataJson)) {
            //echo "<pre>"; print_r($dataJson); exit;

            $rules = array(
                'cuisine' => 'required',
                'item_name' => 'required',
                'price' => 'required',
                'preparation_time' => 'required',
                'image' => 'mimes:jpeg,png,jpg',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/editmenu/' . $slug)
                                ->withErrors($validator);
            } else {

                $data = array(
                    'cuisines_id' => $dataJson['cuisine'],
                    'item_name' => $dataJson['item_name'],
                    'price' => $dataJson['price'],
                    'status' => 1,
                    // 'submenu' => $dataJson['submenu'],
                    'preparation_time' => $dataJson['preparation_time'],
                    'description' => $dataJson['description'],
                    'slug' => $this->createSlug($dataJson['item_name'])
                );

                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $image = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_ITEM_IMAGE_PATH, time() . $file->getClientOriginalName());
                    @unlink(UPLOAD_FULL_ITEM_IMAGE_PATH . $menudata->image);
                } else {
                    $image = $dataJson['old_image'];
                }
                $data['image'] = $image;

                DB::table('menu_item')
                        ->where('slug', $slug)
                        ->update($data);

                return Redirect::to('/user/managemenu')->with('success_message', 'Menu item successfully updated.');
            }
        }
    }

    public function showeditstatus($slug = "") {

        $this->logincheck('user/editstatus/' . $slug);
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get menu item details
        $menudata = DB::table('orderstatus')
                ->where('slug', $slug)
                ->first();
        if (empty($menudata)) {
            // redirect to the menu page
            return Redirect::to('/user/orderstatus')->with('error_message', 'Something went wrong, please try after some time.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Status';
        $this->layout->content = View::make('/Users/editstatus')
                ->with('userData', $userData)
                ->with('menudata', $menudata);
        $dataJson = Input::all();


        if (!empty($dataJson)) {
            //echo "<pre>"; print_r($dataJson); exit;

            $rules = array(
                'status_name' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/editstatus/' . $slug)
                                ->withErrors($validator);
            } else {

                $data = array(
                    'status_name' => $dataJson['status_name'],
                    'slug' => $this->createSlug($dataJson['status_name'])
                );



                DB::table('orderstatus')
                        ->where('slug', $slug)
                        ->update($data);

                return Redirect::to('/user/orderstatus')->with('success_message', 'Status successfully updated.');
            }
        }
    }

    public function showDeletemenu($slug = null) {
        // get menu item details
        $menudata = DB::table('menu_item')
                ->where('slug', $slug)
                ->first();
        if (empty($menudata)) {
            // delete image
            @unlink(UPLOAD_FULL_ITEM_IMAGE_PATH . $menudata->image);
        }
        DB::table('menu_item')->where('slug', $slug)->delete();
        return Redirect::to('/user/managemenu')->with('success_message', 'Menu item deleted successfully');
    }

    public function showchangePicture() {
        $this->logincheck('user/changePicture');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
            ;
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Change Picture';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $dataJson = Input::all();
        $this->layout->content = View::make('/Users/changePicture')
                ->with('userData', $userData);

        if (!empty($dataJson)) {
            if (Input::hasFile('profile_image'))
                $rules = array(
                    'profile_image' => 'required|mimes:jpeg,png,jpg',
                );
            else
                $rules = array(
                    'profile_image' => 'required',
                );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/changePicture')
                                ->withErrors($validator);
            } else {

                include("vendor/ImageManipulator.php");
                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(TEMP_PATH, time() . $file->getClientOriginalName());

                    list($width, $height, $type, $attr) = getimagesize('uploads/temp/' . $profileImageName);
                    if ($width > 600) {
                        $manipulator = new ImageManipulator('uploads/temp/' . $profileImageName);

                        // resizing to 200x200
                        $manipulator->resample(600, 600);
                        $manipulator->save('uploads/temp/' . $profileImageName);
                    }

                    $data['image'] = $profileImageName;
                    if ($width > $height) {
                        $data['width'] = $height;
                        $data['height'] = $height;
                    } elseif ($width < $height) {
                        $data['width'] = $width;
                        $data['height'] = $width;
                    } else {
                        $data['width'] = $width;
                        $data['height'] = $height;
                    }
                    $this->layout->content = View::make('/Users/changePicture')
                            ->with('userData', $userData)
                            ->with('data', $data);
                }

                if (isset($dataJson['add_photo'])) {

                    $manipulator = new ImageManipulator('uploads/temp/' . $dataJson['profile_image']);
                    $width = $manipulator->getWidth();
                    $height = $manipulator->getHeight();
                    $centreX = round($width / 2);
                    $centreY = round($height / 2);
                    // our dimensions will be 200x130
                    $x1 = $centreX - $dataJson['w'] / 2; // 200 / 2
                    $y1 = $centreY - $dataJson['h'] / 2; // 130 / 2

                    $x2 = $centreX + 100; // 200 / 2
                    $y2 = $centreY + 65; // 130 / 2
                    //
//                    echo "<pre>";
//                    print_r($dataJson);
                    // center cropping to 200x130
                    $newImage = $manipulator->crop($dataJson['x'], $dataJson['y'], $dataJson['w'], $dataJson['h']);

                    // saving file to uploads folder
                    $manipulator->save("uploads/users/" . $dataJson['profile_image']);

                    // update it to database
                    $data = array(
                        'profile_image' => $dataJson['profile_image'],
                    );
                    DB::table('users')
                            ->where('id', $user_id)
                            ->update($data);

                    // remove old image
                    @unlink(UPLOAD_FULL_PROFILE_IMAGE_PATH . $userData->image);

                    // return to error/success message
                    return Redirect::to('/user/myaccount')->with('success_message', 'Image updated successfully.');
                }
            }
        }
    }

    public function showDeleteUserImage() {

        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        DB::table('users')
                ->where('id', $user_id)
                ->update(array('profile_image' => ''));
        @unlink(UPLOAD_FULL_PROFILE_IMAGE_PATH . $userData->profile_image);

        return Redirect::to('/user/changePicture')->with('success_message', 'Image deleted successfully');
    }

    public function showMyfavourite() {

        if (isset($_COOKIE["browser_session_id"]) && $_COOKIE["browser_session_id"] != '') {
            $browser_session_id = $_COOKIE["browser_session_id"];
        } else {
            $browser_session_id = session_id();
            setcookie("browser_session_id", $browser_session_id, time() + 60 * 60 * 24 * 7, "/");
        }


        $this->logincheck('user/myfavourite');
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
        $query = DB::table('favorite_menu');
        $query->leftjoin("menu_item", 'menu_item.id', '=', 'favorite_menu.menu_id');
        $query->leftjoin("users", 'users.id', '=', 'favorite_menu.caterer_id');
        $query->where('favorite_menu.user_id', $user_id)
                ->orwhere('favorite_menu.session_id', $browser_session_id)
                ->select('favorite_menu.*', 'menu_item.id as menu_id', 'menu_item.item_name', 'users.slug as user_slug');
        $records = $query->orderBy('favorite_menu.id', 'desc')->paginate(10);

        // get all posted input
        $dataJson = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'My Favorite';
        $this->layout->content = View::make('/Users/myfavourite')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showDeletefav($slug = null) {
        // get menu item details
        $menudata = DB::table('favorite_menu')
                ->where('slug', $slug)
                ->first();
        DB::table('favorite_menu')->where('slug', $slug)->delete();
        return Redirect::to('/user/myfavourite')->with('success_message', 'Favorite Menu item deleted successfully');
    }

    public function showMakefav($slug = null, $mainorder = null) {
        // get menu item details

        DB::table('orders')
                ->where('slug', $slug)
                ->update(array('is_favorite' => 1));
        return Redirect::to('/order/myorders/' . $mainorder)->with('success_message', 'Your order is successfully added in favourite list.');
    }

    public function showRemovefav($slug = null, $mainorder = null) {
        // get menu item details
        DB::table('orders')
                ->where('slug', $slug)
                ->update(array('is_favorite' => 0));
        return Redirect::to('/order/myorders/' . $mainorder)->with('success_message', 'Order remove from favourite list successfully');
    }

    public function contactcaterer() {


        $this->layout = false;

        $dataJson = Input::all();
        if (!empty($dataJson)) {

            $message = $dataJson['message'];
            $order_id = $dataJson['order_id'];
            $rules = array(
                'message' => 'required', // make sure the message field is not empty
                'order_id' => 'required', // make sure the message field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($dataJson, $rules);
            if ($validator->fails()) {
                $errors_input = $validator->messages()->all();
                $err = implode("<br/>", $errors_input);
                echo json_encode(array('message' => $err, 'valid' => false));
                die;
            } else {

                $user_id = Session::get('user_id');
                // create our user data for the authentication
                $userData = DB::table('users')
                        ->where('id', $user_id)
                        ->first();
                $orderData = DB::table('orders')
                        ->where('id', $order_id)
                        ->first();
                $catererId = $orderData->caterer_id;
                $catererData = DB::table('users')
                        ->where('id', $catererId)
                        ->first();


                if (!empty($catererData)) {
                    // send email to user
                    $mail_data = array(
                        'text' => 'Contact query from customer ' . $userData->first_name . ' ' . $userData->last_name . ' regarding order number ' . $orderData->order_number . '.',
                        'email' => $catererData->email_address,
                        'message2' => $message,
                        'firstname' => $catererData->first_name . ' ' . $catererData->last_name,
                    );
                    //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Contact query from customer');
                            });


                    // send email to admin
                    $adminuser = DB::table('admins')
                            ->where('id', 1)
                            ->first();
                    $adminEmail = $adminuser->email;
                    $mail_data = array(
                        'text' => 'Contact query from customer ' . $userData->first_name . ' ' . $userData->last_name . ' of regarding order number ' . $orderData->order_number . '.',
                        'email' => $adminEmail,
                        'message2' => $message,
                        'firstname' => "Admin",
                    );
                    //   return View::make('emails.template')->with($mail_data); // to check mail template data to view
                    Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                                $message->setSender(array(MAIL_FROM => SITE_TITLE));
                                $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                                $message->to($mail_data['email'], $mail_data['firstname'])->subject('Contact query from customer');
                            });
                    echo json_encode(array('message' => 'Thank you for contacting us. We will get back to you shortly', 'valid' => true, 'redirect' => HTTP_PATH));
                    die;
                }
            }
        }
    }

}
