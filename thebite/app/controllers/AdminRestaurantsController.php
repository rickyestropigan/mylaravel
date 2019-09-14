<?php
class AdminRestaurantsController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Admin User Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'AdminUserController);
      |
     */

    protected $layout = 'layouts.adminlayout';

    //Check login for every module
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
    public function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }
    
    //Show all Restaurants listing 
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
                ->where("user_type", "=", 'Restaurant')
                ->where(function ($query) use ($search_keyword) {
            $query->where('first_name', 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('last_name', 'LIKE', '%' . $search_keyword . '%')
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

    //Add Restaurants
    public function showAdmin_add() {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin');
        }

        $input = Input::all();
//          echo '<prE>'; print_r($input);die;
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
                    'password' => md5($input['password']),
                    'profile_image' => $profileImageName,
                    'status' => '1',
                    'average_price' => $input['average_price'],
                    'estimated_time' => $input['estimated_time'],
                    'description' => $input['description'],
                    'delivery_type' => $input['delivery_type'],
                    'delivery_cost' => $input['delivery_cost'],
                    'cuisines' => isset($input['cuisines']) ? implode(',', $input['cuisines']) : '',
                    'service_offered' => isset($input['service_offered']) ? implode(',', $input['service_offered']) : '',
                    'payment_options' => isset($input['payment_options']) ? implode(',', $input['payment_options']) : '',
                    'parking' => $input['parking'],
                    'fax_number' => $input['fax_number'],
                    'username' => $input['username'],
                    'slug' => $slug,
                    'user_type' => "Restaurant",
                    'minimum_order' => $input['minimum_order'],
                    'created' => date('Y-m-d H:i:s'),
                );

                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                $cuisines = array();
                $cuisines = $input['cuisines'];

                foreach ($cuisines as $cuisiness) {

                    $data = array(
                        'user_id' => $id,
                        'name' => $cuisiness,
                        'status' => '1',
                        'created' => date('Y-m-d H:i:s'),
                        'slug' => $this->createSlug($cuisiness),
                    );
                    //    echo '<pre>'; print_r($data);die;
                    DB::table('cuisines')
                            ->insert($data);
                }

                $data = array(
                    'user_id' => $id,
                    'open_close' => '0',
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s')
                );

                DB::table('opening_hours')
                        ->insert($data);

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

    //Edit Restaurants
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

    //Activate  Restaurants 
    public function showAdmin_activeuser($slug = null) {
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
            }

            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['status' => 1, 'approve_status' => 1]);

            return Redirect::back()->with('success_message', 'Restaurant(s) activated successfully');
        }
    }

    // Deactive  Restaurants 
    public function showAdmin_deactiveuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Restaurant(s) deactivated successfully');
        }
    }

    // Delete  Restaurants 
    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant deleted successfully');
        }
    }

}
