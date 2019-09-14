<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class UsercopyController extends BaseController {
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
            return Redirect::to('/');
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

    public function showAdmin_add() {
        if (!Session::has('adminid')) {
            return Redirect::to('/');
        }

        $input = Input::all();
        //echo '<prE>'; print_r($input);die;
        if (!empty($input)) {


            $email_address = trim($input['email_address']);
            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'cpassword' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact' => 'required'
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
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'email_address' => $input['email_address'],
                    'password' => md5($input['password']),
                    'profile_image' => $profileImageName,
                    'status' => '1',
                    'average_price' => $input['average_price'],
                    'zipcode' => $input['zipcode'],
                    'estimated_time' => $input['estimated_time'],
                    'description' => $input['description'],
                    'delivery_hours_start' => $input['delivery_hours_start'],
                    'delivery_hours_end' => $input['delivery_hours_end'],
                    'pickup_hour_start' => $input['pickup_hour_start'],
                    'pickup_hour_end' => $input['pickup_hour_end'],
                    'service_offered' => isset($input['service_offered']) ? implode(',', $input['service_offered']) : '',
                    'payment_options' => $input['payment_options'],
                    'parking' => $input['parking'],
                    'capacity' => $input['capacity'],
                    'slug' => $slug,
                    'user_type' => "Restaurant",
                    'created' => date('Y-m-d H:i:s'),
                );

                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                $user_id = DB::getPdo()->lastInsertId();

                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account is successfully created by admin as Restaurant. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'],
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

    public function showAdmin_edituser($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/');
        }
        $input = Input::all();

        $user = DB::table('users')
                        ->where('slug', $slug)->first();
        $user_id = $user->id;


        if (!empty($input)) {
            $old_profile_image = $input['old_profile_image'];
            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact' => 'required'
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

                $data = array(
                    'first_name' => $input['first_name'],
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'profile_image' => $profileImageName,
                    'average_price' => $input['average_price'],
                    'zipcode' => $input['zipcode'],
                    'estimated_time' => $input['estimated_time'],
                    'delivery_hours_start' => $input['delivery_hours_start'],
                    'delivery_hours_end' => $input['delivery_hours_end'],
                    'pickup_hour_start' => $input['pickup_hour_start'],
                    'pickup_hour_end' => $input['pickup_hour_end'],
                    'service_offered' => isset($input['service_offered']) ? implode(',', $input['service_offered']) : '',
                    'payment_options' => $input['payment_options'],
                    'parking' => $input['parking'],
                    'capacity' => $input['capacity'],
                    'description' => $input['description'],
                );

                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);

                return Redirect::to('/admin/restaurants/admin_index')->with('success_message', 'Restaurant profile details updated successfully.');
            }
        } else {



            return View::make('/Users/admin_edituser')->with('detail', $user);
        }
    }

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

    public function showAdmin_deactiveuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Restaurant(s) deactivated successfully');
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
            return Redirect::to('/');
        }

        $user_id = Session::get('user_id');
        $user = DB::table('users')
                ->where('id', $user_id)
                ->where('status', '1')
                ->first();

        if (empty($user)) {
            Session::forget('user_id');
            return Redirect::to('/')->with('error_message', 'Your account might have been temporarily disabled.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Account';
        $this->layout->content = View::make('Users.myaccount')->with('userData', $user);
    }

    public function showLogout() {
        Session::forget('user_id');
        Session::put('success_message', "You have successfully logout.");
        return Redirect::to('/');
    }

    public function showEditProfile() {

        $this->logincheck('user/editProfile');

        if (Session::has('user_id')) {
            // return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Profile';

        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        $this->layout->content = View::make('/Users/editProfile')
                ->with('userData', $userData);

        $input = Input::all();


        if (!empty($input)) {

            $rules = array(
                'first_name' => 'required', // make sure the first name field is not empty
                'contact' => 'required', // make sure the  contact field is not empty
                'address' => 'required', // make sure the address field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                return Redirect::to('/user/editProfile')
                                ->withErrors($validator);
            } else {

                $data = array(
                    'first_name' => $input['first_name'],
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'average_price' => $input['average_price'],
                    'zipcode' => $input['zipcode'],
                    'estimated_time' => $input['estimated_time'],
                    'delivery_hours_start' => $input['delivery_hours_start'],
                    'delivery_hours_end' => $input['delivery_hours_end'],
                    'pickup_hour_start' => $input['pickup_hour_start'],
                    'pickup_hour_end' => $input['pickup_hour_end'],
                    'service_offered' => isset($input['service_offered']) ? implode(',', $input['service_offered']) : '',
                    'payment_options' => $input['payment_options'],
                    'parking' => $input['parking'],
                    'capacity' => $input['capacity'],
                    'description' => $input['description'],
                );

                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);

                return Redirect::to('/user/myaccount')->with('success_message', 'Profile updated successfully.');
            }
        }
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

        // echo '<pre>';print_r($opening_hours);die;


        $this->layout->title = TITLE_FOR_PAGES . 'Manage Opening Hours';
        $this->layout->content = View::make('/Users/openinghours')
                ->with('userData', $userData)
                ->with('opening_hours', $opening_hours);

        $input = Input::all();


        if (!empty($input)) {
            $rules = array(
                'open_days' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'minimum_order' => 'required'
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

//                 echo "<pre>"; print_r($open); 
//                 echo "<pre>"; print_r($close); exit;

                $data = array(
                    'open_days' => $open_days,
                    'start_time' => implode(',', $open),
                    'end_time' => implode(',', $close),
                    'minimum_order' => $input['minimum_order'],
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
            return Redirect::to('/');
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

    /*
     * add menu item  */

    public function showAddmenu() {
        $this->logincheck('user/addmenu');

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

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add Menu';
        $this->layout->content = View::make('/Users/addmenus')
                ->with('userData', $userData);

        if (!empty($input)) {

            $rules = array(
                'cuisine' => 'required',
                'item_name' => 'required',
                'description' => 'required',
                'price' => 'required',
//                'modifiers' => 'required'
            );
//            echo "<pre>"; print_r($input); exit;
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addmenus')
                                ->withErrors($validator)->withInput();
            } else {
//echo'<pre>';print_r($input);die;
                $data = array(
                    'cuisines_id' => $input['cuisine'],
                    'item_name' => $input['item_name'],
                    'description' => $input['description'],
                    'price' => $input['price'],
//                    'modifiers' => $input['modifiers']?implode(',', $input['modifiers']):'',
                    // 'submenu' => $input['submenu'],
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'slug' => $this->createUniqueSlug($input['item_name'], 'menu_item')
                );

                if (isset($input['discounted']) && $input['discounted']) {
                    $data['discounted'] = 1;
                } else {
                    $data['discounted'] = 0;
                }
                if (isset($input['spicy']) && $input['spicy']) {
                    $data['spicy'] = 1;
                } else {
                    $data['spicy'] = 0;
                }

                if (isset($input['visible']) && $input['visible']) {
                    $data['visible'] = 1;
                } else {
                    $data['visible'] = 0;
                }
                if (isset($input['popular']) && $input['popular']) {
                    $data['popular'] = 1;
                } else {
                    $data['popular'] = 0;
                }

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

                $id = DB::getPdo()->lastInsertId();

                /* insert item sizes */

                if (isset($input['size']) && count($input['size']) > 0) {
                    foreach ($input['size'] as $node => $addon) {
                        $data = array(
                            'item_id' => $id,
                            'size' => $addon,
                            'size_price' => $input['size_price'][$node],
                            'size_description' => $input['size_description'][$node],
                            'slug' => $this->createUniqueSlug($input['size_description'][$node], 'item_size'),
                            'created' => date('Y-m-d H:i:s')
                        );
                        DB::table('item_size')
                                ->insert($data);
                    }
                }
                /* insert item modifiers */

                if (isset($input['name']) && count($input['name']) > 0) {
                    foreach ($input['name'] as $node => $addon) {
                        $data = array(
                            'item_id' => $id,
                            'selection' => $input['selection'],
                            'type' => $input['type'],
                            'name' => $input['name'][$node],
                            'qty' => $input['qty'][$node],
                            'slug' => $this->createUniqueSlug($input['name'][$node], 'item_modifier'),
                            'created' => date('Y-m-d H:i:s')
                        );
                        DB::table('item_modifier')
                                ->insert($data);
                    }
                }


                return Redirect::to('/user/addmenus')->with('success_message', 'Menu item successfully added.');
            }
        }
    }

    /*
     * add item modifiers  */

    public function showAddModifier() {
        $this->logincheck('user/addmenu');

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

        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Add Modifier';
        $this->layout->content = View::make('/Users/addmodifier')
                ->with('userData', $userData);

        if (!empty($input)) {

            $rules = array(
                'cuisine' => 'required',
                'item_name' => 'required',
                'description' => 'required',
                'price' => 'required',
//                'modifiers' => 'required',
//                'image' => 'mimes:jpeg,png,jpg',
            );
            //echo "<pre>"; print_r($input); exit;
            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/user/addmodifier')
                                ->withErrors($validator);
            } else {
//echo'<pre>';print_r($input);die;
                $data = array(
                    'cuisines_id' => $cuisineData->id,
                    'item_name' => $input['item_name'],
                    'description' => $input['description'],
                    'price' => $input['price'],
                    'modifiers' => $input['modifiers'] ? implode(',', $input['modifiers']) : '',
                    // 'submenu' => $input['submenu'],
                    'user_id' => $user_id,
                    'created' => date('Y-m-d H:i:s'),
                    'status' => '1',
                    'slug' => $this->createSlug($input['item_name'])
                );

                if (isset($input['discounted']) && $input['discounted']) {
                    $data['discounted'] = 1;
                } else {
                    $data['discounted'] = 0;
                }
                if (isset($input['spicy']) && $input['spicy']) {
                    $data['spicy'] = 1;
                } else {
                    $data['spicy'] = 0;
                }

                if (isset($input['visible']) && $input['visible']) {
                    $data['visible'] = 1;
                } else {
                    $data['visible'] = 0;
                }
                if (isset($input['popular']) && $input['popular']) {
                    $data['popular'] = 1;
                } else {
                    $data['popular'] = 0;
                }

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

                $id = DB::getPdo()->lastInsertId();

                /* insert item sizes */

                if (isset($input['size']) && count($input['size']) > 0) {
                    foreach ($input['size'] as $node => $addon) {
                        $data = array(
                            'user_id' => $user_id,
                            'item_id' => $id,
                            'size' => $addon,
                            'size_price' => $input['size_price'][$node],
                            'size_description' => $input['size_description'][$node],
                            'slug' => $this->createUniqueSlug($input['size_description'][$node], 'item_size'),
                            'created' => date('Y-m-d H:i:s')
                        );
                        DB::table('item_size')
                                ->insert($data);
                    }
                }



                return Redirect::to('/user/addmenus')->with('success_message', 'Menu item successfully added.');
            }
        }
    }

    /*
     * Edit menu item 
     */

    public function showEditmenuitem($slug = "") {

        $this->logincheck('user/editmenuitem/' . $slug);
        if (Session::has('user_id')) {
            
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

//      echo '<pre>'; print_r($menudata);die;

        if (empty($menudata)) {
            // redirect to the menu page
            return Redirect::to('/user/managemenuitem')->with('error_message', 'Something went wrong, please try after some time.');
        }

        $this->layout->title = TITLE_FOR_PAGES . 'Edit Menu Item';
        $this->layout->content = View::make('/Users/editmenuitem')
                ->with('userData', $userData)
                ->with('menudata', $menudata);
        $input = Input::all();


        if (!empty($input)) {
//              echo "<pre>"; print_r($input); exit;

            $rules = array(
                'cuisine' => 'required',
                'item_name' => 'required',
                'description' => 'required',
                'price' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);
            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/user/editmenuitem/' . $slug)
                                ->withErrors($validator)->withInput();
            } else {
//                echo '<pre>'; print_r($input);die;
                $data = array(
                    'cuisines_id' => $input['cuisine'],
                    'item_name' => $input['item_name'],
                    'description' => $input['description'],
                    'price' => $input['price'],
                );

                if (isset($input['discounted']) && $input['discounted']) {
                    $data['discounted'] = 1;
                } else {
                    $data['discounted'] = 0;
                }
                if (isset($input['spicy']) && $input['spicy']) {
                    $data['spicy'] = 1;
                } else {
                    $data['spicy'] = 0;
                }

                if (isset($input['visible']) && $input['visible']) {
                    $data['visible'] = 1;
                } else {
                    $data['visible'] = 0;
                }
                if (isset($input['popular']) && $input['popular']) {
                    $data['popular'] = 1;
                } else {
                    $data['popular'] = 0;
                }
                if (isset($input['discount']) && $input['discount']) {
                    $data['discount'] = $input['discount'];
                } else {
                    $data['discount'] = 0;
                }

                if (Input::hasFile('image')) {
                    $file = Input::file('image');
                    $image = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_ITEM_IMAGE_PATH, time() . $file->getClientOriginalName());
                } else {
                    $image = "";
                }
                $data['image'] = $image;

                DB::table('menu_item')
                        ->where('slug', $slug)
                        ->update($data);

                $id = $menudata->id;
                /* insert item sizes */

                if (isset($input['size']) && count($input['size']) > 0) {
                    foreach ($input['size'] as $node => $addon) {
                        $size_id = isset($input['size_id'][$node]) ? $input['size_id'][$node] : '';
                        if ($size_id) {
                            $data = array(
                                'size' => $addon,
                                'size_price' => $input['size_price'][$node],
                                'size_description' => $input['size_description'][$node],
                            );
                            DB::table('item_size')
                                    ->where('id', $size_id)
                                    ->update($data);
                        } else {
                            $data = array(
                                'item_id' => $id,
                                'size' => $addon,
                                'size_price' => $input['size_price'][$node],
                                'size_description' => $input['size_description'][$node],
                                'slug' => $this->createSlug($input['size_description'][$node]),
                                'created' => date('Y-m-d H:i:s')
                            );
                            DB::table('item_size')
                                    ->insert($data);
                        }
                    }
                }
                /* insert item modifiers */

                if (isset($input['name']) && count($input['name']) > 0) {
                    foreach ($input['name'] as $node => $addon) {
                        $modifier_id = isset($input['modifier_id'][$node]) ? $input['modifier_id'][$node] : '';
                        if ($modifier_id) {
                            $data = array(
                                'selection' => $input['selection'],
                                'type' => $input['type'],
                                'name' => $input['name'][$node],
                                'qty' => $input['qty'][$node],
                            );
                            DB::table('item_modifier')
                                    ->where('id', $modifier_id)
                                    ->update($data);
                        } else {
                            $data = array(
                                'item_id' => $id,
                                'selection' => $input['selection'],
                                'type' => $input['type'],
                                'name' => $input['name'][$node],
                                'qty' => $input['qty'][$node],
                                'slug' => $this->createSlug($input['name'][$node]),
                                'created' => date('Y-m-d H:i:s')
                            );
                            DB::table('item_modifier')
                                    ->insert($data);
                        }
                    }
                }

                return Redirect::to('/user/managemenuitem')->with('success_message', 'Menu item successfully updated.');
            }
        }
    }

    public function showManagemenuItem() {
        $this->logincheck('user/managemenuitem');
        if (Session::has('user_id')) {
            
        } else {
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }

        // get current user details
        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();

        // get my all menu        
        $query = DB::table('menu_item');
        $query
                ->select('cuisines.name', 'menu_item.*')
                ->leftjoin("cuisines", 'cuisines.id', '=', 'menu_item.cuisines_id')
                ->where('cuisines.user_id', $user_id);
//echo $query;die;
        $records = $query->orderBy('menu_item.id', 'desc')->paginate(10);
//echo '<pre>';print_r($records);die;
        // get all posted input
        $input = Input::all();

        // set content view and title
        $this->layout->title = TITLE_FOR_PAGES . 'Manage Menu Item';
        $this->layout->content = View::make('/Users/managemenuitem')
                ->with('userData', $userData)
                ->with('records', $records);
    }

    public function showAdmiSlot($slug) {
        $this->logincheck('user/showAdmiSlot');
        
        echo $slug;die;
    }

}
