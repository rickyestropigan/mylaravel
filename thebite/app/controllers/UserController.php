<?php

class UserController extends BaseController {
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

    public function logincheck($url) {

        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/admin')->with('error_message', 'You must login to see this page.');
        } else {

            $user_id = Session::get('user_id');
            $userData = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
            if (empty($userData)) {
                Session::forget('user_id');
                return Redirect::to('/admin');
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

    //--------------Start Restaurant admin modules ------------------//

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
        $query = User::sortable()
                ->select("users.*","bank_account.routing","bank_account.account","bank_account.business_tax","bank_account.ssn","bank_account.dob","bank_account.first_name as ac_first_name","bank_account.last_name as ac_last_name")
                ->leftjoin("bank_account", "bank_account.user_id", '=', "users.id")
                ->where("user_type", "=", 'Restaurant')
                ->where(function ($query) use ($search_keyword) {
            $query->where('users.first_name', 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('users.last_name', 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('email_address', 'LIKE', '%' . $search_keyword . '%');
        });
        
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));

                    Session::put('success_message', 'Restaurant(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Restaurant(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Restaurant(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the users
        $users = $query->orderBy('id', 'desc')->sortable()->paginate(10);


        // Show the page
        return View::make('Users/adminindex', compact('users'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_add() {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }

        $input = Input::all();

        if (!empty($input)) {


            $email_address = trim($input['email_address']);
            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty
                'username' => 'required|unique:users', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'phone1' => 'required',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zipcode' => 'required',
                'service_offered' => 'required',
                'cuisines' => 'required',
                'payment_options' => 'required',
                'average_price' => 'required',
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/restaurants/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {

                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_PROFILE_IMAGE_PATH, time() . $file->getClientOriginalName());
                } else {
                    $profileImageName = "";
                }

                $slug = $this->createUniqueSlug($input['first_name'], 'users');
		$l_result = App::make("ListingController")->_Get_lat_lang_address($input['address']);

                $saveUser = array(
                    'first_name' => $input['first_name'],
                    'restaurant_cat' => isset($input['restaurant_cat']) ? implode(',', $input['restaurant_cat']) : '',
                    'phone1' => $input['phone1'],
                    'phone2' => $input['phone2'],
                    'phone3' => $input['phone3'],
                    'phone4' => $input['phone4'],
                    'cell_phone1' => $input['cell_phone1'],
                    'cell_phone2' => $input['cell_phone2'],
		    'cell_phone3' => $input['cell_phone3'],
                    'cell_phone4' => $input['cell_phone4'],
                    'address' => $input['address'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'zipcode' => $input['zipcode'],
                    'email_address' => $input['email_address'],
                    'password' => md5(trim($input['password'])),
			'plain_pwd' => trim($input['password']),
                    'profile_image' => $profileImageName,
                    'status' => '1',
                    'average_price' => $input['average_price'],
                    'estimated_time' => $input['estimated_time'],
                    'description' => $input['description'],
                    'delivery_type' => $input['delivery_type'],
                    'delivery_cost' => ($input['delivery_cost']) ? $input['delivery_cost'] : '0.00'  ,
                    'cuisines' => isset($input['cuisines']) ? implode(',', $input['cuisines']) : '',
                    'service_offered' => isset($input['service_offered']) ? implode(',', $input['service_offered']) : '',
                    'payment_options' => isset($input['payment_options']) ? implode(',', $input['payment_options']) : '',
                    'parking' => $input['parking'],
                    'fax_number' => $input['fax_number'],
                    'sales_tax' => $input['sales_tax'],
                    'username' => $input['username'],
                    'slug' => $slug,
                    'user_type' => "Restaurant",
                    'minimum_order' => $input['minimum_order'],
			'latitude' => $l_result['lat'],
			'longitude' => $l_result['lang'],
                    'created' => date('Y-m-d H:i:s'),
                );

                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

//                $cuisines = array();
//                $cuisines = $input['cuisines'];
//
//                foreach ($cuisines as $cuisiness) {
//
//                    $data = array(
//                        'user_id' => $id,
//                        'name' => $cuisiness,
//                        'status' => '1',
//                        'created' => date('Y-m-d H:i:s'),
//                        'slug' => $this->createSlug($cuisiness),
//                    );
//                    //    echo '<pre>'; print_r($data);die;
//                    DB::table('cuisines')
//                            ->insert($data);
//                }

                $data = array(
                    'user_id' => $id,
                    'open_close' => '0',
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s')
                );

                DB::table('opening_hours')
                        ->insert($data);


                $data1 = array(
                    'user_id' => $id,
                    'status' => '1',
                    'slug' => $this->createUniqueSlug('back-' . $input['first_name'], 'bank_account'),
                    'created' => date('Y-m-d H:i:s')
                );

                DB::table('bank_account')
                        ->insert($data1);

                $user_id = DB::getPdo()->lastInsertId();




                $userEmail = $input['email_address'];
                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account is successfully created by admin as Restaurant. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'],
                    'username' => $input['username'],
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created by admin as Restaurant');
                });

                return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant saved successfully.');
            }
        } else {
            return View::make('/Users/admin_add');
        }
    }

    public function showRestBank($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }



        $userData = DB::table('users')
                ->where('slug', $slug)
                ->first();


        $bankdetails = DB::table('bank_account')
                ->where('user_id', $userData->id)
                ->where('status', '1')
                ->first();


        $this->layout->title = TITLE_FOR_PAGES . 'Bank Account';
        $this->layout->content = View::make('Users.bankdetails')->with('bankdetails', $bankdetails);
    }

    public function showAdmin_edituser($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }
        $input = Input::all();
	
        $user = DB::table('users')
                        ->where('slug', $slug)->first();
        $user_id = $user->id;


        if (!empty($input)) {
            $old_profile_image = $input['old_profile_image'];

            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty // password can only be alphanumeric and has to be greater than 3 characters/ make sure the email address field is not empty
                'phone1' => 'required',
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zipcode' => 'required',
                'service_offered' => 'required',
                'cuisines' => 'required',
                'payment_options' => 'required',
                'average_price' => 'required',
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/restaurants/Admin_edituser/' . $user->slug)
                                ->withErrors($validator) // send back all errors
                                ->withInput(Input::all());
            } else {

                if (Input::hasFile('profile_image')) {
                    $file = Input::file('profile_image');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_PROFILE_IMAGE_PATH, time() . $file->getClientOriginalName());
                    @unlink(UPLOAD_FULL_PROFILE_IMAGE_PATH . $old_profile_image);
                } else {
                    $profileImageName = $old_profile_image;
                }

                //echo '<pre>'; print_r($input);die;
	$l_result = App::make("ListingController")->_Get_lat_lang_address($input['address']);
                $data = array(
                    'first_name' => $input['first_name'],
                    'restaurant_cat' => isset($input['restaurant_cat']) ? implode(',', $input['restaurant_cat']) : '',
                    'phone1' => $input['phone1'],
                    'phone2' => $input['phone2'],
                    'phone3' => $input['phone3'],
                    'phone4' => $input['phone4'],
                    'cell_phone1' => $input['cell_phone1'],
                    'cell_phone2' => $input['cell_phone2'],
                    'cell_phone3' => $input['cell_phone3'],
                    'cell_phone4' => $input['cell_phone4'],
                    'address' => $input['address'],
			  'email_address' => $input['email_address'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'profile_image' => $profileImageName,
                    'average_price' => $input['average_price'],
                    'zipcode' => $input['zipcode'],
                    'estimated_time' => $input['estimated_time'],
                    'delivery_type' => $input['delivery_type'],
                    'delivery_cost' => $input['delivery_cost'],
                    'cuisines' => isset($input['cuisines']) ? implode(',', $input['cuisines']) : '',
                    'service_offered' => isset($input['service_offered']) ? implode(',', $input['service_offered']) : '',
                    'payment_options' => isset($input['payment_options']) ? implode(',', $input['payment_options']) : '',
                    'parking' => $input['parking'],
                    'minimum_order' => $input['minimum_order'],
                    'fax_number' => $input['fax_number'],
                    'description' => $input['description'],
			'latitude' => $l_result['lat'],
			'longitude' => $l_result['lang'],
                );



                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);


                $mail_data = array(
                    'text' => 'Your information has been changed by admin.',
                    'email' => $user->email_address,
                    'firstname' => $user->first_name,
                );

//                echo '<prE>';print_r($mail_data);die;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view

                Mail::send('emails.template', $mail_data, function($message) use ($user) {
                    //  echo '<prE>'; print_r($users);die;
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($user->email_address, 'User')->subject('Your information has been changed');
                });

                return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant profile details updated successfully.');
            }
        } else {

            return View::make('/Users/admin_edituser')->with('detail', $user);
        }
    }

    public function showAdmin_activeuser($slug = 'ls_restaurant') {
	
        if (!empty($slug)) {
	
            // check admin approval
            $Data = DB::table('users')
                    ->select("approve_status", "email_address", "first_name")
                    ->where('slug', $slug)
                    ->first();

            if (!$Data->approve_status) {
                // send email to user for account varification
                $userEmail = $Data->email_address;

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account has been successfully confirmed by ' . SITE_TITLE . ' as Restaurant.',
                    'email' => $userEmail,
                    'firstname' => $Data->first_name
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully approved by ' . SITE_TITLE);
                });
 DB::table('users')
                    ->where('slug', $slug)
                    ->update(array('status' => 1, 'approve_status' => 1));

            return Redirect::back()->with('success_message', 'Restaurant(s) activated successfully');
            } 

           
        }else {  return Redirect::to('/admin/restaurants/admin_index');
			}
    }

    public function showAdmin_deactiveuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
                    ->where('slug', $slug)
                    ->update(array('status' => 0));

            return Redirect::back()->with('success_message', 'Restaurant(s) deactivated successfully');
        } else {  return Redirect::to('/admin/restaurants/admin_index');
			}
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant deleted successfully');
        }
    }

    //------------------------- End Restaurant admin modules ------------------------------//

    public function showMyaccount() {
        $this->layout = View::make('layouts.default');

        if (!Session::has('user_id')) {
            return Redirect::to('/admin');
        }

        $user_id = Session::get('user_id');
        $user = DB::table('users')
                ->where('id', $user_id)
                ->where('status', '1')
                ->first();

        if (empty($user)) {
            Session::forget('user_id');
            return Redirect::to('/admin')->with('error_message', 'Your account might have been temporarily disabled.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Account';
        $this->layout->content = View::make('Users.myaccount')->with('userData', $user);
    }

  
    public function showBankaccount() {
        $this->logincheck('user/editAccount');
        $this->layout = View::make('layouts.default');

        if (!Session::has('user_id')) {
            return Redirect::to('/admin');
        }
        $user_id = Session::get('user_id');

        $bankAccount = DB::table('bank_account')
                ->where('user_id', $user_id)
                ->where('status', '1')
                ->first();

        $this->layout->title = TITLE_FOR_PAGES . 'Bank Account';
        $this->layout->content = View::make('Users.bankaccount')->with('bankAccount', $bankAccount);
    }

    public function showEditbank($slug = null) {

        $this->logincheck('user/editAccount');

        if (Session::has('user_id')) {
            // return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Bank Account';
        if ($slug == 'new') {
            $bankAccount = '';
        } elseif ($slug == 'newins') {
            $insert = 1;
        } else {
            $bankAccount = DB::table('bank_account')
                    ->where('slug', $slug)
                    ->first();
        }
        if (!isset($insert)) {
            $this->layout->content = View::make('/Users/editBank')
                    ->with('bankAccount', $bankAccount);

            $input = Input::all();

            //  echo '<pre>'; print_r($input);die;
//
            if (!empty($input)) {

                $rules = array(
                    'routing' => 'required',
                    'account' => 'required', // make sure the first name field is not empty
                    'business_tax' => 'required', // make sure the address field is not empty
                    'first_name' => 'required', // make sure the address field is not empty
                    'last_name' => 'required',
                    'dob' => 'required',
                    'ssn' => 'required',
                );

                // run the validation rules on the inputs from the form
                $validator = Validator::make(Input::all(), $rules);

                //  echo '<pre>'; print_r($validator);die;
                // if the validator fails, redirect back to the form
                if ($validator->fails()) {
                    return Redirect::to('/user/editBank')
                                    ->withErrors($validator);
                } else {

//                echo '<prE>'; print_r($input);die;
//              
                    $data = array(
                        'routing' => $input['routing'],
                        'account' => $input['account'],
                        'business_tax' => $input['business_tax'],
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'dob' => date('Y-m-d', strtotime($input['dob'])),
                        'ssn' => $input['ssn'],
                    );
//                
//                echo '<prE>';
//                print_r($data);
//                die;
                    // die;
                    DB::table('bank_account')
                            ->where('slug', $slug)
                            ->update($data);
                    //die;
                    return Redirect::to('user/bankaccount')->with('success_message', 'Bank Account updated successfully.');
                }
            }
        } elseif (isset($insert) && $insert == '1') {

            $input = Input::all();

            //  echo '<pre>'; print_r($input);die;
//
            if (!empty($input)) {

                $rules = array(
                    'routing' => 'required',
                    'account' => 'required', // make sure the first name field is not empty
                    'business_tax' => 'required', // make sure the address field is not empty
                    'first_name' => 'required', // make sure the address field is not empty
                    'last_name' => 'required',
                    'dob' => 'required',
                    'ssn' => 'required',
                );

                // run the validation rules on the inputs from the form
                $validator = Validator::make(Input::all(), $rules);

                //  echo '<pre>'; print_r($validator);die;
                // if the validator fails, redirect back to the form
                if ($validator->fails()) {
                    return Redirect::to('/user/editBank/newins')
                                    ->withErrors($validator);
                } else {

//                echo '<prE>'; print_r($input);die;
//  

                    $user_id = Session::get('user_id');
                    $data = array(
                        'routing' => $input['routing'],
                        'account' => $input['account'],
                        'business_tax' => $input['business_tax'],
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'dob' => date('Y-m-d', strtotime($input['dob'])),
                        'ssn' => $input['ssn'],
                        'user_id' => $user_id,
                        'status' => '1',
                        'slug' => $this->createSlug($input['first_name'] . $user_id)
                    );
//                
//                echo '<prE>';
//                print_r($data);
//                die;
                    // die;
                    DB::table('bank_account')->insert(
                            $data
                    );
                    $id = DB::getPdo()->lastInsertId();
                    //die;
                    return Redirect::to('user/bankaccount')->with('success_message', 'Bank Account updated successfully.');
                }
            }
        }
    }

    public function showLogout() {
        Session::forget('user_id');
        Session::put('success_message', "You have successfully logout.");
        return Redirect::to('/admin');
    }

    public function showEditProfile() {

        $this->logincheck('user/editProfile');

        if (Session::has('user_id')) {
            // return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Profile';

        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->layout->content = View::make('/Users/editProfile')
                ->with('userData', $userData);

        $input = Input::all();

//        echo '<pre>'; print_r($input);die;
//

        if (!empty($input)) {

            $rules = array(
                'cuisines' => 'required',
                'payment_options' => 'required', // make sure the first name field is not empty
                'average_price' => 'required'// make sure the address field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/user/editProfile')
                                ->withErrors($validator);
            } else {

//                echo '<prE>'; print_r($input);die;

                $data = array(
                    'phone2' => $input['phone2'],
                    'phone3' => $input['phone3'],
                    'phone4' => $input['phone4'],
                    'cell_phone1' => $input['cell_phone1'],
                    'cell_phone2' => $input['cell_phone2'],
                    'average_price' => $input['average_price'],
                    'estimated_time' => $input['estimated_time'],
                    'delivery_type' => $input['delivery_type'],
                    'sales_tax' => $input['sales_tax'],
                    'delivery_cost' => $input['delivery_cost'],
                    'cuisines' => isset($input['cuisines']) ? $input['cuisines'] : '',
                    'payment_options' => isset($input['payment_options']) ? $input['payment_options'] : '',
                    'restaurant_cat' => isset($input['restaurant_cat']) ? $input['restaurant_cat'] : '',
                    'parking' => $input['parking'],
                    'description' => $input['description'],
                    'minimum_order' => $input['minimum_order'],
                );

//                echo '<prE>';
//                print_r($data);
//                die;
                // die;
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);
                //die;
                return Redirect::to('/user/myaccount')->with('success_message', 'Profile updated successfully.');
            }
        }
    }

    public function showchangePicture() {
        $this->logincheck('user/changePicture');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin')->with('error_message', 'You must login to see this page.');
            ;
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Change Picture';
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $input = Input::all();
        $this->layout->content = View::make('/Users/changePicture')
                ->with('userData', $userData);

        if (!empty($input)) {
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

                if (isset($input['add_photo'])) {

                    $manipulator = new ImageManipulator('uploads/temp/' . $input['profile_image']);
                    $width = $manipulator->getWidth();
                    $height = $manipulator->getHeight();
                    $centreX = round($width / 2);
                    $centreY = round($height / 2);
                    // our dimensions will be 200x130
                    $x1 = $centreX - $input['w'] / 2; // 200 / 2
                    $y1 = $centreY - $input['h'] / 2; // 130 / 2

                    $x2 = $centreX + 100; // 200 / 2
                    $y2 = $centreY + 65; // 130 / 2
                    //
//                    echo "<pre>";
//                    print_r($input);
                    // center cropping to 200x130
                    $newImage = $manipulator->crop($input['x'], $input['y'], $input['w'], $input['h']);

                    // saving file to uploads folder
                    $manipulator->save("uploads/users/" . $input['profile_image']);

                    // update it to database
                    $data = array(
                        'profile_image' => $input['profile_image'],
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

    public function showOpeninghours() {
        $this->logincheck('user/openinghours');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {

            return Redirect::to('/admin');
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

        // get opening hours details
        $day_hours = DB::table('opendays')
                ->where('user_id', $user_id)
                ->get();

        // echo '<pre>';print_r($opening_hours);die;


        $this->layout->title = TITLE_FOR_PAGES . 'Manage Opening Hours';
        $this->layout->content = View::make('/Users/openinghours')
                ->with('userData', $userData)
                ->with('opening_hours', $opening_hours)
                ->with('day_hours', $day_hours);

        $input = Input::all();


        if (!empty($input)) {
            $rules = array(
                'open_days' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/openinghours')
                                ->withErrors($validator);
            } else {

                $open_days = $input['open_days'];
                $open_days = implode(',', $open_days);

                foreach ($input['open_days'] as $varr) {
                    $open[] = date("H:i", strtotime($input['start_time'][$varr]));
                    $close[] = date("H:i", strtotime($input['end_time'][$varr]));
                }

                $data = array(
                    'open_days' => $open_days,
                    'start_time' => implode(',', $open),
                    'end_time' => implode(',', $close),
                    'open_close' => isset($input['open_close']) ? $input['open_close'] : '0'
                );

                DB::table('opening_hours')
                        ->where('id', $opening_hours->id)
                        ->update($data);

                return Redirect::to('/user/myaccount')->with('success_message', 'Opening hours successfully updated.');
            }
        }
    }

    public function showchangePassword() {
        $this->logincheck('user/changePassword');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $this->layout->title = TITLE_FOR_PAGES . 'Change Password';
        $this->layout->content = View::make('/Users/changePassword')
                ->with('userData', $userData);
        $input = Input::all();


        if (!empty($input)) {
            $new_password = $input['new_password'];
            $confirm_password = $input['confirm_password'];
            $rules = array(
                'new_password' => 'required|min:8', // make sure the new password field is not empty
                'confirm_password' => 'required' // make sure the confirm password field is not empty
            );

            $oldDBpassword = $userData->password;

            $opassword = md5($input['old_password']);

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/changePassword')
                                ->withErrors($validator);
            } else {

                $newPassword = md5($new_password);


                // check new password with old password
                if ($oldDBpassword == $newPassword) {

                    // return error message
                    Session::put('error_message', "Please do not enter new password same as old password");
                    return Redirect::to('/user/changePassword');
                }
                if ($oldDBpassword <> $opassword) {
                    // return error message
                    Session::put('error_message', "Please enter correct old password");
                    return Redirect::to('/user/changePassword');
                } else {

                    $data = array(
                        'password' => $newPassword,
                    );
                    DB::table('users')
                            ->where('id', $user_id)
                            ->update($data);

                    return Redirect::to('/user/myaccount')->with('success_message', 'Password changed successfully.');
                }
            }
        }
    }

    public function showAddmenu() {
        $this->logincheck('user/addmenu');
        if (Session::has('user_id')) {
            
        } else {
            return "Error";
        }
        
        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $input = Input::all();
        
        
        // set content view and title
        
        if (!empty($input)) {
            $rules = array(
                'item_name' => 'required',
                'service_visibility' => 'required',
                'discount_type' => 'required',
            );

  
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);
            
            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                
                return 'error';
            } else {
                
                $data = array(
                    'name' => $input['item_name'],
                    'user_id' => $user_id,
		     'service_month' => '',
			'service_start' => '',
			'service_end' => '',
			'menu_description' => '',
			'service_visibility' => $input['service_visibility'],
                    'discount_type' => $input['discount_type'],
                    'discount' => $input['discount'] ? $input['discount'] : '0',
                    'slug' => $this->createSlug($input['item_name']),
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                );

                if (isset($input['visibility']) && $input['visibility']) {
                    $data['visibility'] = 1;
                } else {
                    $data['visibility'] = 0;
                }

                if (isset($input['service']) && $input['service']) {
                    $data['service'] = 1;
                } else {
                    $data['service'] = 0;
                }

                DB::table('cuisines')->insert(
                        $data
                );

                $id = DB::getPdo()->lastInsertId();

                return 'Success';
            }
        }
        exit;
    }

    public function showManagemenu() {
        $this->logincheck('user/managemenu');
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/admin')->with('error_message', 'You must login to see this page.');
        }
        
        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $search_keyword = "";
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        // get my all menu        
        $query = DB::table('cuisines');
        $query->select('cuisines.*')
		->where('user_id',$user_id)
                ->where(function ($query) use ($search_keyword) {
                    $query->where('cuisines.name', 'LIKE', '%' . $search_keyword . '%');
                });
        
        $records = $query->orderBy('cuisines.id', 'desc')->get();

        
        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Menu';
        $this->layout->content = View::make('/Users/managemenu')
                ->with('userData', $userData)
                ->with('search', $search_keyword)
                ->with('records', $records);
        
    }
    
    public function showRearrangemenu(){
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $query = DB::table('cuisines');
        $records = $query->where('cuisines.user_id', $user_id)
                ->select('cuisines.*')
                ->orderBy('cuisines.menu_order', 'asc')->get();
        
        $html = View::make('/Users/rearrangemenu')
                ->with('userData', $userData)
                ->with('records', $records);
        return $html->render();
    }
    
    public function showRearrangeitem(){
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $cuision_id = $input['selected_menu'];
        $queryc = DB::table('cuisines');
        $cuisions = $queryc->where('cuisines.id', $cuision_id)
                ->select('cuisines.*')
                ->first();
        $query = DB::table('menu_item'); 
        $records = $query->where('menu_item.user_id',$user_id)
                        ->where('menu_item.cuisines_id',$cuision_id)
                        ->select('menu_item.*')
                        ->orderBy('menu_item.item_order', 'asc')->get();
//        echo '<prE>'; dd(DB::getQueryLog());exit;
        $html = View::make('/Users/rearrangemenuitem')
                ->with('userData', $userData)
                ->with('cuisions', $cuisions)
                ->with('records', $records);
        return $html->render();
    }
    
    public function showMenuorderchange(){
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $menu_id  = $input['id'];
        $order = $input['order'];
        $data = array('menu_order'=>$order);
        DB::table('cuisines')
            ->where('id', $menu_id)
            ->update($data);
        return 'success';
        exit;
    }
    
    public function showMenuitemorderchange(){
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $id = $input['id'];
        $item_order = $input['order'];
        $menuData = DB::table('menu_item')
            ->where('id', $id)
            ->first();
        if(!empty($menuData)){
        $data = array('item_order'=>$item_order);
        DB::table('menu_item')
            ->where('id', $id)
            ->update($data);
        return 'Success';
        }
        else{
            return 'Error';
        }
        
    }

    public function showEditmenu() {

        //$this->logincheck('user/editmenu');
        if (Session::has('user_id')) {
            
        } else {
            //return 'You must login to see this page';
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        // get menu item details
        $menudata = DB::table('cuisines')
                ->where('id', $input['menu'])
                ->first();

//      echo '<pre>'; print_r($menudata);die;

        if (empty($menudata)) {
            // redirect to the menu page
            return 'Error!';
        }

        //$this->layout->title = TITLE_FOR_PAGES . 'Edit Menu';
        $html = View::make('/Users/editmenu')
                ->with('userData', $userData)
                ->with('menudata', $menudata);
        return $html->render();
        //$input = Input::all();


//        if (!empty($input)) {
//
////              echo "<pre>"; print_r($input); exit;
//
//            $rules = array(
//                'name' => 'required',
//                'service_visibility' => 'required',
//                'discount_type' => 'required',
//            );
//
//            // run the validation rules on the inputs from the form
//            $validator = Validator::make(Input::all(), $rules);
//            // if the validator fails, redirect back to the form
//            if ($validator->fails()) {
//                //die('dfdf');
//
//                return Redirect::to('/user/editmenu/' . $slug)
//                                ->withErrors($validator)->withErrors($validator);
//            } else {
////echo "<pre>"; print_r($input); exit;
//
//                $data = array(
//                    'name' => $input['name'],
//                    'service_month' => $input['service_month'],
//                    'service_start' => $input['service_start'],
//                    'service_end' => $input['service_end'],
//                    'service_visibility' => implode(',', $input['service_visibility']),
//                    'discount_type' => $input['discount_type'],
//                    'discount' => $input['discount'] ? $input['discount'] : '0',
//                );
//
//                if (isset($input['visibility']) && $input['visibility']) {
//                    $data['visibility'] = 1;
//                } else {
//                    $data['visibility'] = 0;
//                }
//
//                if (isset($input['service']) && $input['service']) {
//                    $data['service'] = 1;
//                } else {
//                    $data['service'] = 0;
//                }
//
//
//                DB::table('cuisines')
//                        ->where('slug', $slug)
//                        ->update($data);
//
//                return Redirect::to('/user/managemenu')->with('success_message', 'Menu successfully updated.');
//            }
//        }
    }

    

    /*
     * add menu item  */

    public function showAddmenuItem($slug = null) {
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return 'errorlogin';
        }
        
        //$this->logincheck('user/addmenuitem/' . $slug);

        $cuisines = DB::table('cuisines')
                ->where('slug', $slug)
                ->first();
        if ($cuisines) {
            View::share('cuisines', $cuisines);
        } else {
            //return Redirect::to('/user/managemenu')->with('error_message', 'No data found.');
        }
        
        


        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $input = Input::all();

        // set content view and title

        if (!empty($input)) {

            $rules = array(
                'item_name' => 'required',
                'description' => 'required',
                'price' => 'required',
                'service_visibility' => 'required',
                'discount_type' => 'required',
//                'modifiers' => 'required'
            );
           
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

//                return Redirect::to('/user/addmenuitem')
//                                ->withErrors($validator)->withInput();
                return 'Error';
            } else {
                
                if($input['discount_type']=='%'){
                    $discount_type = 'discounts';
                }
                else
                {
                    $discount_type = 'currency';
                }
                
                $data = array(
                    'cuisines_id' => $slug,
                    'item_name' => $input['item_name'],
                    'description' => $input['description'],
                    'price' => $input['price'],
                    'service_visibility' => $input['service_visibility'],
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'discount_type' => $discount_type,
                    'discount' => $input['discount'] ? $input['discount'] : '0',
                    'slug' => $this->createSlug($input['item_name'])
                );



                if (isset($input['discount']) && $input['discount']>0) {
                    $data['discounted'] = 1;
                } else {
                    $data['discounted'] = 0;
                }
                
                if (strpos($input['spicy_pop'], 'Spicy') !== false) {
                    $data['spicy'] = 1;
                }
                else
                {
                    $data['spicy'] = 0;
                }
                
                if (strpos($input['spicy_pop'], 'Popular') !== false) {
                    $data['popular'] = 1;
                } else {
                    $data['popular'] = 0;
                }
                
                if (isset($input['status']) && $input['status']==1) {
                    $data['visible'] = 1;
                } else {
                    $data['visible'] = 0;
                }
                
                if (isset($input['discount']) && $input['discount']) {
                    $data['discount'] = $input['discount'];
                } else {
                    $data['discount'] = 0;
                }

                
                DB::table('menu_item')->insert(
                        $data
                );

                $id = DB::getPdo()->lastInsertId();

                /* insert item sizes */

                if (isset($input['size']) && count($input['size']) > 0) {
                    foreach ($input['size'] as $node => $addon) {
                        $data = array(
                            'item_id' => $id,
                            'size' => $addon['title'],
                            'size_price' => $addon['size_price'],
                            'size_description' => $addon['description'],
                            'slug' => $this->createSlug($addon['title']),
                            'created' => date('Y-m-d H:i:s')
                        );
                        DB::table('item_size')
                                ->insert($data);
                    }
                }
                /* insert item modifiers */

                if (isset($input['prom']) && count($input['prom']) > 0) {
                    foreach ($input['prom'] as $node => $addon) {
                        foreach($addon['modifier'] as $ket => $ads){
                            $data = array(
                                'item_id' => $id,
                                'selection' => $addon['title'],
                                'type' => $addon['optional'],
                                'name' => $ads['modifier_name'],
                                'price' => $ads['modifier_price'],
                                'slug' => $this->createSlug($ads['modifier_name']),
                                'created' => date('Y-m-d H:i:s')
                            );
                            DB::table('item_modifier')
                                ->insert($data);
                        }
                        
                    }
                }


                return 'Success';
            }
        }
    }

    /*
     * Edit menu item 
     */

    public function showEditmenuitem($slug = "") {

        $this->logincheck('user/editmenuitem');
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/admin')->with('error_message', 'You must login to see this page.');
        }
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        
        $input = Input::all();
        
        $cuisines_id = $input['menu'];
        $slug = $input['slug'];
        $id = $input['id'];
        
        // get menu item details
        $menudata = DB::table('menu_item')
                ->where('slug', $slug)
                ->first();
        // get menu item details
        // get modifier item details
        $modifiers = DB::table('item_modifier')
                    ->where('item_id',$id)
                    ->get();
        // get modifier item details
        // get sizes item details
        $sizes = DB::table('item_size')
                    ->where('item_id',$id)
                    ->get();
        // get sizes item details
        // get menu item details
        $cuisinesdata = DB::table('cuisines')
                ->where('id', $cuisines_id)
                ->first();

        //echo '<pre>'; print_r($menudata);die;

        if (empty($menudata)) {
            // redirect to the menu page
            return 'Error!';
        }

        $html = View::make('/Users/editmenuitem')
                ->with('userData', $userData)
                ->with('cuisinesdata', $cuisinesdata)
                ->with('menudata', $menudata)
                ->with('modifiers', $modifiers)
                ->with('sizes', $sizes);
        
        return $html->render();
        
//        $input = Input::all();


//        if (!empty($input)) {
////              echo "<pre>"; print_r($input); exit;
//
//            $rules = array(
//                'item_name' => 'required',
//                'description' => 'required',
//                'price' => 'required',
//                'service_visibility' => 'required',
//                'discount_type' => 'required',
//            );
//
//            if (Input::hasFile('image'))
//                $rules['image'] = 'mimes:jpeg,png,jpg';
//            else
//                $rules['image'] = '';
//
//            // run the validation rules on the inputs from the form
//            $validator = Validator::make(Input::all(), $rules);
//            // if the validator fails, redirect back to the form
//            if ($validator->fails()) {
////                die('dfdf');
//
//                return Redirect::to('/user/editmenuitem/' . $slug)
//                                ->withErrors($validator);
//            } else {
//
//                $old_image = $input['old_image'];
//
//                $data = array(
//                    'item_name' => $input['item_name'],
//                    'description' => $input['description'],
//                    'price' => $input['price'],
//                    'service_visibility' => implode(',', $input['service_visibility']),
//                    'discount_type' => $input['discount_type'],
//                    'discount' => $input['discount'] ? $input['discount'] : '0',
//                );
//
//                if (isset($input['discounted']) && $input['discounted']) {
//                    $data['discounted'] = 1;
//                } else {
//                    $data['discounted'] = 0;
//                }
//                if (isset($input['spicy']) && $input['spicy']) {
//                    $data['spicy'] = 1;
//                } else {
//                    $data['spicy'] = 0;
//                }
//
//                if (isset($input['visible']) && $input['visible']) {
//                    $data['visible'] = 1;
//                } else {
//                    $data['visible'] = 0;
//                }
//                if (isset($input['popular']) && $input['popular']) {
//                    $data['popular'] = 1;
//                } else {
//                    $data['popular'] = 0;
//                }
//                if (isset($input['discount']) && $input['discount']) {
//                    $data['discount'] = $input['discount'];
//                } else {
//                    $data['discount'] = 0;
//                }
//
//                if (Input::hasFile('image')) {
//                    $file = Input::file('image');
//                    $image = time() . $file->getClientOriginalName();
//                    $file->move(UPLOAD_FULL_ITEM_IMAGE_PATH, time() . $file->getClientOriginalName());
//                    @unlink(DISPLAY_FULL_ITEM_IMAGE_PATH . $old_image);
//                } else {
//                    $image = $old_image;
//                }
//                $data['image'] = $image;
//
//                DB::table('menu_item')
//                        ->where('slug', $slug)
//                        ->update($data);
//
//                $id = $menudata->id;
//                /* insert item sizes */
//
//                if (isset($input['size']) && count($input['size']) > 0) {
//                    foreach ($input['size'] as $node => $addon) {
//                        $size_id = isset($input['size_id'][$node]) ? $input['size_id'][$node] : '';
//                        if ($size_id) {
//                            $data = array(
//                                'size' => $addon,
//                                'size_price' => $input['size_price'][$node],
//                                'size_description' => $input['size_description'][$node],
//                            );
//                            DB::table('item_size')
//                                    ->where('id', $size_id)
//                                    ->update($data);
//                        } else {
//                            $data = array(
//                                'item_id' => $id,
//                                'size' => $addon,
//                                'size_price' => $input['size_price'][$node],
//                                'size_description' => $input['size_description'][$node],
//                                'slug' => $this->createSlug($input['size_description'][$node]),
//                                'created' => date('Y-m-d H:i:s')
//                            );
//                            DB::table('item_size')
//                                    ->insert($data);
//                        }
//                    }
//                }
//                /* insert item modifiers */
//
//                if (isset($input['name']) && count($input['name']) > 0) {
//                    foreach ($input['name'] as $node => $addon) {
//                        $modifier_id = isset($input['modifier_id'][$node]) ? $input['modifier_id'][$node] : '';
//                        if ($modifier_id) {
//                            $data = array(
//                                'selection' => $input['selection'],
//                                'type' => $input['type'],
//                                'name' => $input['name'][$node],
//                                'qty' => $input['qty'][$node],
//                            );
//                            DB::table('item_modifier')
//                                    ->where('id', $modifier_id)
//                                    ->update($data);
//                        } else {
//                            $data = array(
//                                'item_id' => $id,
//                                'selection' => $input['selection'],
//                                'type' => $input['type'],
//                                'name' => $input['name'][$node],
//                                'qty' => $input['qty'][$node],
//                                'slug' => $this->createSlug($input['name'][$node]),
//                                'created' => date('Y-m-d H:i:s')
//                            );
//                            DB::table('item_modifier')
//                                    ->insert($data);
//                        }
//                    }
//                }
//
//                return Redirect::to('/user/managemenu')->with('success_message', 'Menu item successfully updated.');
//            }
//        }
    }

    public function showEditmenuitemsub(){
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        
        $input = Input::all();
        
        if (!empty($input)) {
        $cusine_id = $input['selected_menu'];
        $item_id = $input['id'];
        
        DB::table('item_size')->where('item_id', $item_id)->delete();

        DB::table('item_modifier')->where('item_id', $item_id)->delete();
        
        $data = array(
            'item_name' => $input['item_name'],
            'description' => $input['description'],
            'price' => $input['price'],
            'service_visibility' => $input['service_visibility'],
            'discount_type' => $input['discount_type'],
            'discount' => $input['discount'] ? $input['discount'] : '0'
        );

        if (isset($input['discount']) && $input['discount']>0) {
            $data['discounted'] = 1;
        } else {
            $data['discounted'] = 0;
        }

        if (strpos($input['spicy_pop'], 'Spicy') !== false) {
            $data['spicy'] = 1;
        }
        else
        {
            $data['spicy'] = 0;
        }

        if (strpos($input['spicy_pop'], 'Popular') !== false) {
            $data['popular'] = 1;
        } else {
            $data['popular'] = 0;
        }

        if (isset($input['status']) && $input['status']==1) {
            $data['visible'] = 1;
        } else {
            $data['visible'] = 0;
        }

        if (isset($input['discount']) && $input['discount']) {
            $data['discount'] = $input['discount'];
        } else {
            $data['discount'] = 0;
        }
        
        DB::table('menu_item')
            ->where('id', $item_id)
            ->update($data);
        
        $id = $item_id;
        
        /* insert item sizes */

        if (isset($input['size']) && count($input['size']) > 0) {
            foreach ($input['size'] as $node => $addon) {
                $data = array(
                    'item_id' => $id,
                    'size' => $addon['title'],
                    'size_price' => $addon['size_price'],
                    'size_description' => $addon['description'],
                    'slug' => $this->createSlug($addon['title']),
                    'created' => date('Y-m-d H:i:s')
                );
                DB::table('item_size')
                        ->insert($data);
            }
        }
        /* insert item modifiers */

        if (isset($input['prom']) && count($input['prom']) > 0) {
            foreach ($input['prom'] as $node => $addon) {
                foreach($addon['modifier'] as $ket => $ads){
                    $data = array(
                        'item_id' => $id,
                        'selection' => $addon['title'],
                        'type' => $addon['optional'],
                        'name' => $ads['modifier_name'],
                        'price' => $ads['modifier_price'],
                        'slug' => $this->createSlug($ads['modifier_name']),
                        'created' => date('Y-m-d H:i:s')
                    );
                    DB::table('item_modifier')
                        ->insert($data);
                }

            }
        }
        return 'Success';
        }
        else
        {
            return 'Error!';
        }
    }
    
    public function showManagemenuItem() {
        $this->logincheck('user/managemenuitem');
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/admin')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $input = Input::all();
        $search_keyword = "";
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        // get my all menu        
        $query = DB::table('menu_item');
        $query
                ->select('cuisines.name', 'cuisines.discount as cdiscount', 'menu_item.*')
                ->leftjoin("cuisines", 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->where('cuisines.user_id', $user_id)
                ->where(function ($query) use ($search_keyword) {
                    $query->where('menu_item.item_name', 'LIKE', '%' . $search_keyword . '%');
                });
//echo $query;die;
        $records = $query->orderBy('menu_item.id', 'desc')->paginate(10);
//echo '<pre>';print_r($records);die;
        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Menu Item';
        $this->layout->content = View::make('/Users/managemenuitem')
                ->with('userData', $userData)
                ->with('search', $search_keyword)
                ->with('records', $records);
    }

    public function showDeletemenuitem($slug = null) {
        // get menu item details
        
        $input = Input::all();
        $slug = $input['menu_slug'];
        $id = $input['menu_id'];
        
        $menudata = DB::table('menu_item')
                ->where('slug', $slug)
                ->first();
        
        $old_image = $menudata->image;
        @unlink(DISPLAY_FULL_ITEM_IMAGE_PATH . $old_image);
        $id = $menudata->id;

        DB::table('menu_item')->where('slug', $slug)->delete();
//        
        DB::table('item_size')->where('item_id', $id)->delete();

        DB::table('item_modifier')->where('item_id', $id)->delete();

        return 'success';
    }

    public function deleteActionMenu($type, $id) {
        // get menu item details
        if ($type != "") {
            if ($type == "size") {
                DB::table('item_size')->where('id', $id)->delete();
            } else {
                DB::table('item_modifier')->where('id', $id)->delete();
            }
        }
    }

    public function showAdminMenu($slug = null) {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }
        if (!empty($slug)) {

            $usersdata = DB::table('users')
                    ->where('slug', $slug)
                    ->first();
            if (empty($usersdata)) {
                // redirect to the menu page
                return Redirect::to('/admin/restaurants/admin_index')->with('error_message', 'Something went wrong, please try after some time.');
            }
            $input = Input::all();
            $search_keyword = "";
            $searchByDateFrom = "";
            $searchByDateTo = "";
            $separator = array();

            if (!empty($input['search'])) {
                $search_keyword = trim($input['search']);
            }
            $query = Cuisine::sortable()
                    ->select('cuisines.*', 'users.first_name', 'users.last_name')
                    ->leftjoin("users", 'cuisines.user_id', '=', 'users.id')
                    ->where("cuisines.user_id", "=", $usersdata->id)
                    ->where(function ($query) use ($search_keyword) {
                $query->where('cuisines.name', 'LIKE', '%' . $search_keyword . '%');
            });

            if (!empty($input['action'])) {
                $action = $input['action'];
                $idList = $input['chkRecordId'];
                switch ($action) {
                    case "Activate":
                        DB::table('cuisines')
                                ->whereIn('id', $idList)
                                ->update(array('status' => 1));
                        Session::put('success_message', 'Customer(s) activated successfully');
                        break;
                    case "Deactivate":
                        DB::table('cuisines')
                                ->whereIn('id', $idList)
                                ->update(array('status' => 0));
                        Session::put('success_message', 'Customer(s) deactivated successfully');
                        break;
                    case "Delete":
                        DB::table('cuisines')
                                ->whereIn('id', $idList)
                                ->delete();
                        Session::put('success_message', 'Customer(s) deleted successfully');
                        break;
                }
            }

            $separator = implode("/", $separator);

            // Get all the users
            $menu = $query->orderBy('id', 'desc')->sortable()->paginate(10);
            // Show the page
            return View::make('Users/adminmenu', compact('menu'))->with('search_keyword', $search_keyword)
                            ->with('searchByDateFrom', $searchByDateFrom)
                            ->with('searchByDateTo', $searchByDateTo)
                            ->with('usersdata', $usersdata);
        }
    }

    public function getCuisineDiscount($cuisineId = null) {
        $cuisinedata = DB::table('cuisines')
                ->where('id', $cuisineId)
                ->first();

        if ($cuisinedata->discount > 0) {
            echo '1';
            die;
        }
        echo '0';
        die;
    }

    public function showAdminopeninghours($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }

//        echo $slug;die;

        $input = Input::all();

        $user = DB::table('users')
                        ->where('slug', $slug)->first();

        $user_id = $user->id;

        $openinghours = DB::table('opening_hours')
                        ->leftjoin("users", 'users.id', '=', 'opening_hours.user_id')
                        ->select("opening_hours.*", 'users.slug as user_slug')
                        ->where('user_id', $user_id)->first();

//         echo '<pre>'; print_r($input);die;

        if (!empty($input)) {

            $rules = array(
                'open_days' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {


                return Redirect::to('/admin/restaurants/Admin_openinghours/' . $user->slug)
                                ->withErrors($validator) // send back all errors
                                ->withInput(Input::all());
            } else {

//                        echo '<pre>'; print_r($input);die;

                $open_days = $input['open_days'];
                $open_days = implode(',', $open_days);

                foreach ($input['open_days'] as $varr) {
                    $open[] = date("H:i", strtotime($input['start_time'][$varr]));
                    $close[] = date("H:i", strtotime($input['end_time'][$varr]));
                }

                $data = array(
                    'open_days' => $open_days,
                    'start_time' => implode(',', $open),
                    'end_time' => implode(',', $close),
                    'open_close' => isset($input['open_close']) ? $input['open_close'] : '0'
                );

                DB::table('opening_hours')
                        ->where('user_id', $user->id)
                        ->update($data);


                return Redirect::back()->with('success_message', 'Restaurant profile details updated successfully.');
            }
        } else {
//            echo '<pre>'; print_r($openinghours);die;
            return View::make('/Users/admin_openinghours')->with('opening_hours', $openinghours);
        }
    }

    public function showViewmenu($slug = null) {
        $this->layout = View::make('layouts.default');

        if (!Session::has('user_id')) {
            return Redirect::to('/admin');
        }

        $user_id = Session::get('user_id');
        $user = DB::table('users')
                ->where('id', $user_id)
                ->where('status', '1')
                ->first();

        if (empty($user)) {
            Session::forget('user_id');
            return Redirect::to('/admin')->with('error_message', 'Your account might have been temporarily disabled.');
        }

        $cuisines = DB::table('cuisines')
                ->where('slug', $slug)
                ->where('status', '1')
                ->first();

//        echo '<pre>';        p/rint_r($cuisines);die;

        $this->layout->title = TITLE_FOR_PAGES . 'Menu Information';
        $this->layout->content = View::make('Users.menuview')->with('cuisines', $cuisines);
    }

    public function showViewmenuitem($slug = null) {
        $this->layout = View::make('layouts.default');

        if (!Session::has('user_id')) {
            return Redirect::to('/admin');
        }

        $user_id = Session::get('user_id');
        $user = DB::table('users')
                ->where('id', $user_id)
                ->where('status', '1')
                ->first();

        if (empty($user)) {
            Session::forget('user_id');
            return Redirect::to('/admin')->with('error_message', 'Your account might have been temporarily disabled.');
        }

        $menuitem = DB::table('menu_item')
                ->leftjoin("cuisines", 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->select("menu_item.*", 'cuisines.name as menu_name')
                ->where('menu_item.slug', $slug)
                ->where('menu_item.status', '1')
                ->first();

//        echo '<pre>';print_r($menuitem);die;

        $this->layout->title = TITLE_FOR_PAGES . 'Menu Item Information';
        $this->layout->content = View::make('Users.menuviewitem')->with('menuitem', $menuitem);
    }

    public function receivedpayment() {
        $this->logincheck('user/receivedpayment');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }

        $user_id = Session::get('user_id');
//        echo $user_id;
        $slug = "purchase";
        $this->layout->title = TITLE_FOR_PAGES . 'Received Payment History';

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }

        if (!empty($input['search_from'])) {
            $searchByDateFrom = trim($input['search_from']);
        }

        if (!empty($input['search_to'])) {
            $searchByDateTo = trim($input['search_to']);
        }

        $total = DB::table('payments')
                ->select(DB::raw("IFNULL(sum(price),0) as total"), 'users.first_name', 'users.last_name')
                ->join('users', 'users.id', '=', 'payments.user_id')
                ->where('payments.user_id', $user_id)
                ->where('payments.status', 'Complete');
//                ->where(function ($total) use ($search_keyword) {
//            $total->where('payments.transaction_id', 'LIKE', '%' . $search_keyword . '%');
//        });

        if ($search_keyword) {
            $total->where('payments.transaction_id', 'LIKE', '%' . $search_keyword . '%');
        }
        if ($searchByDateFrom) {
            $total->where('payments.created', ">=", $searchByDateFrom);
        }
        if ($searchByDateTo) {
            $total->where('payments.created', "<=", $searchByDateTo);
        }

        $total = $total->get();
//echo '<prE>'; dd(DB::getQueryLog());
        $query = Payment::sortable();

        $separator = implode("/", $separator);

        $user_id = Session::get('user_id');

        $query->where('caterer_id', '=', $user_id);
        // Get all the users
        $payments = $query->join('orders', 'orders.id', '=', 'payments.order_id')
                ->select('payments.*', 'orders.caterer_id as caterer_id')
                ->where(function ($query) use ($search_keyword) {
            $query->where('payments.transaction_id', 'LIKE', '%' . $search_keyword . '%');
        });
        if ($searchByDateFrom) {
            $query->where('payments.created', ">=", $searchByDateFrom);
        }
        if ($searchByDateTo) {
            $query->where('payments.created', "<=", $searchByDateTo);
        }
        $payments = $payments->orderBy('id', 'desc')->sortable()->paginate(10);
//echo '<prE>'; dd(DB::getQueryLog());
        // Show the page
        $this->layout->content = View::make('Users/receivedpayment', compact('payments'))->with('search_keyword', $search_keyword)->with('total', $total)
                ->with('searchByDateFrom', $searchByDateFrom)
                ->with('searchByDateTo', $searchByDateTo)
                ->with('paymentslug', $slug);
    }

    //View slot management 
    public function showSlot() {
        $this->logincheck('user/showSlot');
        if (Session::has('user_id')) {
            //return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/admin');
        }
        $user_id = Session::get('user_id');
        $user = DB::table('users')
                ->where('id', $user_id)
                ->where('status', '1')
                ->first();

        $opening_hours = DB::table('opening_hours')
                ->where('user_id', $user->id)
                ->where('status', '1')
                ->first();
        // Show the page
        $this->layout->title = TITLE_FOR_PAGES . 'Slot Management';
        $this->layout->content = View::make('Users/slot', compact('opening_hours'));


        $input = Input::all();
//        echo '<pre>'; print_r($input);die;

        if (!empty($input)) {
            $rules = array(
                'start_time' => 'required',
                'end_time' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/slot')
                                ->withErrors($validator);
            } else {
                $open_days = $input['start_time'];
                DB::table('slots')->where('user_id', $user_id)->delete();
                foreach ($input['start_time'] as $key => $varr) {
                    $open = strtotime($input['start_time'][$key]);
                    $day = ($input['day'][$key]);
                    $close = strtotime($input['end_time'][$key]);
//                    $open[] = date("H:i", strtotime($input['start_time'][$key]));
//                    $close[] = date("H:i", strtotime($input['end_time'][$key]));
                    $status = isset($input['open_close_' . $key]) ? $input['open_close_' . $key] : '0';

                    $data = array(
                        'user_id' => $user_id,
                        'day' => $day,
                        'start_time' => $open,
                        'end_time' => $close,
                        'status' => $status,
                        'created' => date('Y-m-d H:i:s'),
                    );
//                    echo '<pre>'; print_r($data);die;
                    DB::table('slots')->insert(
                            $data
                    );
                }


                return Redirect::to('/user/myaccount')->with('success_message', 'Restaurant slot time updated successfully.');
            }
        }
    }

    //View slot management backend 

    public function showAdminSlot($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }

//        echo $slug;die;

        $input = Input::all();

        $user = DB::table('users')
                        ->where('slug', $slug)->first();

        $user_id = $user->id;

        $openinghours = DB::table('opening_hours')
                        ->leftjoin("users", 'users.id', '=', 'opening_hours.user_id')
                        ->select("opening_hours.*", 'users.slug as user_slug')
                        ->where('user_id', $user_id)->first();

//         echo '<pre>'; print_r($input);die;

        if (!empty($input)) {

            $rules = array(
//                'open_days' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
            );
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/restaurants/Admin_slotmanagemnt/' . $user->slug)
                                ->withErrors($validator); // send back all errors
            } else {

                $open_days = $input['start_time'];
                DB::table('slots')->where('user_id', $user_id)->delete();
                foreach ($input['start_time'] as $key => $varr) {
                    $open = strtotime($input['start_time'][$key]);
                    $day = ($input['day'][$key]);
                    $close = strtotime($input['end_time'][$key]);
                    $status = isset($input['open_close_' . $key]) ? $input['open_close_' . $key] : '0';

                    $data = array(
                        'user_id' => $user_id,
                        'day' => $day,
                        'start_time' => $open,
                        'end_time' => $close,
                        'status' => $status,
                        'created' => date('Y-m-d H:i:s'),
                    );

                    DB::table('slots')->insert(
                            $data
                    );
                }
                return Redirect::back()->with('success_message', 'Restaurant slot time updated successfully.');
            }
        } else {
            return View::make('/Users/admin_slotmanagemnt')->with('opening_hours', $openinghours);
        }
    }

    public function showChangeVisiblity() {
        $this->layout = false;
        $input = Input::all();
        $menu_id = '';
        $type = '';
        $visible = '';
        if (!empty($input['menu_id'])) {
            $menu_id = trim($input['menu_id']);
        }
        if (!empty($input['type'])) {
            $type = trim($input['type']);
        }
        if (!empty($input['visible'])) {
            $visible = trim($input['visible']);
        }

//        echo $visible;die;
        if ($type == 'menu') {
            $msg = 'Menu successfully updated.';
            DB::table('cuisines')
                    ->where('id', $menu_id)
                    ->update(array('status' => $visible));
        } else {
            $msg = 'Menu Item successfully updated.';
            DB::table('menu_item')
                    ->where('id', $menu_id)
                    ->update(array('status' => $visible));
//             dd(DB::getQueryLog());  exit;
        }
        echo json_encode(array('valid' => TRUE, 'smessage' => $msg));
        exit;
    }

    //Alok Start Functions for opening hours update
    public function showOpeningstatus() {
        $this->layout = false;
        $input = Input::all();
        $open_close = $input['status_up'];
        $user_id = $input['user_id'];
        $id = $input['id'];

        $opning_data = DB::table('opening_hours')
                ->where('id', $id)
                ->first();
        if ($open_close == 1) {
            $res_data = 0;
        } else {
            $res_data = 1;
        }
        if (!empty($opning_data)) {
            DB::table('opening_hours')
                    ->where('id', $id)
                    ->update(array('open_close' => $open_close));

            return json_encode(array('message' => 'Your status has been updated successfully.', 'res_data' => $res_data, 'valid' => 1));
            exit;
        } else {
            return json_encode(array('message' => 'you data not matched.', 'valid' => 0));
            exit;
        }
    }

    public function showOpeningdays() {
        $this->layout = false;
        $input = Input::all();
        $user_id = $input['user_id'];
        $open_id = $input['open_id'];
        $day = $input['day'];
        $status = $input['status'];

        $opendays = DB::table('opendays')
                ->where(array('open_id' => $open_id, 'day' => $day, 'user_id' => $user_id))
                ->first();

        if (!empty($opendays)) {

            DB::table('opendays')
                    ->where(array('open_id' => $open_id, 'day' => $day, 'user_id' => $user_id))
                    ->update(array('status' => $status));

            return json_encode(array('message' => 'Your status has been updated successfully.', 'valid' => 1));
            exit;
        } else {

            $savedays = array(
                'open_id' => $open_id,
                'day' => $day,
                'user_id' => $user_id,
                'status' => $status
            );

            DB::table('opendays')->insert(
                    $savedays
            );
            $id = DB::getPdo()->lastInsertId();
            if (!empty($id)) {
                return json_encode(array('message' => 'Your status has been updated successfully.', 'valid' => 1));
                exit;
            } else {
                return json_encode(array('message' => 'you data not matched.', 'valid' => 0));
                exit;
            }
        }
//        print_r($input);exit;
    }

    public function showOpeningupdatehours() {
        $this->layout = false;
        $input = Input::all();
        
        $alltime = $input['alltime'];
        $user_id = $input['user_id'];
        $open_id = $input['open_id'];
        $day = $input['day'];

        //  dd($alltime);

        $timearray = array();
        $timearray = explode(',', $alltime);

        $start_time = str_pad($timearray[0], 2, '0', STR_PAD_LEFT) . ':00';
        $end_time = str_pad($timearray[1], 2, '0', STR_PAD_LEFT) . ':00';


        if ($input) {

            $opendays = DB::table('opendays')
                    ->where(array('open_id' => $open_id, 'day' => $day, 'user_id' => $user_id))
                    ->first();
       
            if (!empty($opendays)) {

                DB::table('opendays')
                        ->where(array('open_id' => $open_id, 'day' => $day, 'user_id' => $user_id))
                        ->update(array('start_time' => $start_time, 'end_time' => $end_time,));
               
                $data_opening_result = DB::table('opendays')
                        ->where(array('open_id' => $open_id,'user_id' => $user_id))->get();
                
                foreach ($data_opening_result as $varr) {
                    $open[] = date("H:i", strtotime($varr->start_time));
                    $close[] = date("H:i", strtotime($varr->end_time));
                    $open_days[] = $varr->day;
                }
                 
                
                $data = array(
                    'open_days' => implode(',',$open_days),
                    'start_time' => implode(',',$open),
                    'end_time' => implode(',',$close),
                );

                DB::table('opening_hours')
                        ->where('id', $open_id)
                        ->update($data);
                   
               
                $tmclass = 'tm_' . $day;
                $tmvalue = date("g:i a", strtotime($start_time)) . '- ' . date("g:i a", strtotime($end_time));
                return json_encode(array('message' => 'Your selection has been updated successfully.', 'tmcl' => $tmclass, 'tmup' => $tmvalue, 'valid' => 1));
                exit;
            } else {



                return json_encode(array('message' => 'you data not matched.', 'valid' => 0));
                exit;
            }
        }
//        print_r($input);exit;
    }

    public function showstatusonoff(){
        $user_id = Session::get('user_id');
        $input = Input::all();
        
        if($input['status']=='offline'){
            $id = $input['id'];
            DB::table('menu_item')
                    ->where(array('id' => $id))
                    ->update(array('status' => 0));
            $view = 0;
        }
        else
        {
            $id = $input['id'];
            DB::table('menu_item')
                    ->where(array('id' => $id))
                    ->update(array('status' => 1));
            $view = 1;
        }
        
        $html = View::make('/Users/statusonoff')->with('view', $view)->with('id',$id);
        return $html->render();
    }
    
    public function shownextitems(){
        $user_id = Session::get('user_id');
        $input = Input::all();
        $menuId = $input['current_menu'];
        
        $items = DB::table('menu_item')->select('menu_item.*')
                            ->where('menu_item.cuisines_id', $menuId)->orderBy('menu_item.item_order', 'asc')->get();
        
        $html = View::make('/Users/nextmenuitem')->with('items', $items);
        return $html->render();
    }
    
    public function showsearchmenuitem(){
        $user_id = Session::get('user_id');
        $input = Input::all();
        $menuid = $input['selected_menu'];
        $keyword = $input['keyword'];
        
        $items = DB::table('menu_item')->select('menu_item.*')
                            ->where('menu_item.cuisines_id', $menuid)
                            ->where('menu_item.item_name', 'LIKE', '%' . $keyword . '%')
                            ->orderBy('menu_item.item_order', 'asc')->get();
        
        $html = View::make('Users/searchmenuitem')->with('items',$items);
        return $html->render();
    }
    
    public function showsubeditmenu(){
        $this->logincheck('user/addmenu');
        if (Session::has('user_id')) {
            
        } else {
            return "Error";
        }
        
        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get all posted input
        $input = Input::all();
//        echo '<pre>';print_r($input);
        
        // set content view and title
        
        if (!empty($input)) {
            $rules = array(
                'item_name' => 'required',
                'service_visibility' => 'required',
                'discount_type' => 'required',
            );

//            echo "<pre>"; print_r($input); exit;
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);
            
            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                
                return 'error';
            } else {

                $data = array(
                    'name' => $input['item_name'],
                    'user_id' => $user_id,
                    'service_visibility' => $input['service_visibility'],
                    'discount_type' => $input['discount_type'],
                    'discount' => $input['discount'] ? $input['discount'] : '0',
                );
// echo '<pre>';print_r($data);die;
                if (isset($input['visibility']) && $input['visibility']) {
                    $data['visibility'] = 1;
                } else {
                    $data['visibility'] = 0;
                }

                if (isset($input['service']) && $input['service']) {
                    $data['service'] = 1;
                } else {
                    $data['service'] = 0;
                }

                DB::table('cuisines')
                    ->where('id', $input['id'])
                    ->update($data);

                return 'Success';
            }
        }
    }
    
    public function showmenulist(){
        if (Session::has('user_id')) {
            
        } else {
            return "Error";
        }
        $input = Input::all();
        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        
        $items = DB::table('menu_item')->select('menu_item.*')
                            ->where('menu_item.user_id', $user_id)
                            ->where('menu_item.item_name', 'LIKE', '%' . $input['data'] . '%')
                            ->orderBy('menu_item.id', 'desc')->get();
        
        $html = View::make('Users/menulist')->with('items',$items)->with('userData',$userData)->with('not_id',$input['not_id']);
        return $html->render();
    }
    public function showmodifierlist(){
        if (Session::has('user_id')) {
            
        } else {
            return "Error";
        }
        $input = Input::all();
        // get current user details
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        
        $items = DB::table('item_modifier')->select('item_modifier.*')
                            ->where('item_modifier.item_id', $input['menuitem'])
                            ->where('item_modifier.name', 'LIKE', '%' . $input['data'] . '%')
                            ->orderBy('item_modifier.name', 'asc')->get();
        
        $html = View::make('Users/modifierlist')->with('items',$items)->with('userData',$userData)->with('itemid',$input['menuitem']);
        return $html->render();
    }
    
    public function showdeletemenu(){
        $input = Input::all();
        $slug = $input['menu_slug'];
        $menudata = DB::table('cuisines')
                ->where('slug', $slug)
                ->first();
        $id = $menudata->id;
        $cuisinesItems = DB::table('menu_item')
                ->where('cuisines_id', $id)
                ->get();
        DB::table('cuisines')->where('slug', $slug)->delete();
        if ($cuisinesItems) {
            foreach ($cuisinesItems as $Items) {
                $old_image = $Items->image;
                $item_id = $Items->id;

                @unlink(DISPLAY_FULL_ITEM_IMAGE_PATH . $old_image);


                DB::table('menu_item')->where('id', $item_id)->delete();

                DB::table('item_size')->where('item_id', $item_id)->delete();

                DB::table('item_modifier')->where('item_id', $item_id)->delete();
            }
        }
        return 'success';
        exit;
    }
}
