<?php

class CustomerController extends BaseController {
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
                ->where("user_type", "=", 'Customer')
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
                    Session::put('success_message', 'Customer(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Customer(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('users')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Customer(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the users
        $users = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Customers/adminindex', compact('users'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
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
                'first_name' => 'required', // make sure the first name field is not empty
                'last_name' => 'required', // make sure the last name field is not empty
                'contact' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/customer/Admin_edituser/' . $user->slug)
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
                    'last_name' => $input['last_name'],
                    'contact' => $input['contact'],
                    'address' => $input['address'],
                    'status' => '1',
                    'profile_image' => $profileImageName,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')
                        ->where('id', $user_id)
                        ->update($data);


                return Redirect::to('/admin/customer/admin_index')->with('success_message', 'Customer profile details updated successfully.');
            }
        } else {

            return View::make('/Customers/admin_edituser')->with('detail', $user);
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
                    'text' => 'Your account has been successfully confirmed by ' . SITE_TITLE . ' as Customer.',
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

            return Redirect::back()->with('success_message', 'Customer(s) activated successfully');
        } else {
		return Redirect::to('/admin/customer/admin_index');
	}
    }

    public function showAdmin_deactiveuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Customer(s) deactivated successfully');
        }
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            $user = DB::table('users')
                            ->where('slug', $slug)->first();
            $user_id = $user->id;

            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/customer/admin_index')->with('success_message', 'Customer deleted successfully');
        }
    }

// --------------------------------------------------------------------
// Create slug for secure URL
    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
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
                'last_name' => 'required', // make sure the last name field is not empty
                'email_address' => 'required|unique:users|email', // make sure the email address field is not empty
                'password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'confirm_password' => 'required|min:8', // password can only be alphanumeric and has to be greater than 3 characters
                'profile_image' => 'mimes:jpeg,png,jpg',
                'contact_number' => 'required',
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/customer/admin_add')
                                ->withErrors($validator)
                                ->withInput(Input::all());
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
'cust_name' => $input['first_name'],
                       'cust_phone' => $input['contact_number'],
                        'cust_email' =>$input['email_address'],
                         'cust_password' => md5($input['password']),
                          'plain_pwd' => $input['password'],
                    'last_name' => $input['last_name'],
                    'contact' => $input['contact_number'],
                    'address' => $input['address'],
                    'email_address' => $input['email_address'],
                    'password' => md5($input['password']),
                    'approve_status' => '1',
                    'profile_image' => $profileImageName,
                    'activation_status' => 1,
                    'status' => '1',
                    'slug' => $slug,
                    'user_type' => "Customer",
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('users')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();
                $userEmail = $input['email_address'];

                // send email to administrator
                $mail_data = array(
                    'text' => 'Your account has been successfully created by admin as Customer. Below are your login credentials.',
                    'email' => $input['email_address'],
                    'password' => $input['password'],
                    'firstname' => $input['first_name'] . ' ' . $input['last_name'],
                );

//                return View::make('emails.template')->with($mail_data); // to check mail template data to view
                Mail::send('emails.template', $mail_data, function($message) use ($mail_data) {
                    $message->setSender(array(MAIL_FROM => SITE_TITLE));
                    $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                    $message->to($mail_data['email'], $mail_data['firstname'])->subject('Your account successfully created by admin as Customer');
                });

                return Redirect::to('/admin/customer/admin_index')->with('success_message', 'Customer saved successfully.');
            }
        } else {
            return View::make('/Customers/admin_add');
        }
    }

}
