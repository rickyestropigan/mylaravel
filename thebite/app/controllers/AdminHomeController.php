<?php

use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

class AdminHomeController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Home Controller
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

    /**
     * Website homepahe
     */
    public function showWelcome() {

        if (Session::has('adminid')) {
            return Redirect::to('/admin/admindashboard');
            exit;
        }
        
        if (Session::has('user_id')) {
            return Redirect::to('/user/myaccount');
            exit;
        }
        

        $this->layout = View::make('layouts.landing');
        $this->layout->title =  'Welcome - '.TITLE_FOR_PAGES;
        $this->layout->content = View::make('home.index');
    }

    // generate captcha code
    /*public function showCapcha() { 
        $this->layout = false;
        /*
         *
         * this code is based on captcha code by Simon Jarvis 
         * http://www.white-hat-web-design.co.uk/articles/php-captcha.php
         *
         * This program is free software; you can redistribute it and/or 
         * modify it under the terms of the GNU General Public License 
         * as published by the Free Software Foundation
         *
         * This program is distributed in the hope that it will be useful, 
         * but WITHOUT ANY WARRANTY; without even the implied warranty of 
         * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
         * GNU General Public License for more details: 
         * http://www.gnu.org/licenses/gpl.html
         */

//Settings: You can customize the captcha here
       /* $image_width = 120;
        $image_height = 40;
        $characters_on_image = 6;
        $font = 'public/img/monofont.ttf';

//The characters that can be used in the CAPTCHA code.
//avoid confusing characters (l 1 and i for example)
        $possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
        $random_dots = 0;
        $random_lines = 20;
        $captcha_text_color = "0x142864";
        $captcha_noice_color = "0x142864";

        $code = '';


        $i = 0;
        while ($i < $characters_on_image) {
            $code .= substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
            $i++;
        }


        $font_size = $image_height * 0.75;
        $image = @imagecreate($image_width, $image_height);


        /* setting the background, text and noise colours here */
        /*$background_color = imagecolorallocate($image, 255, 255, 255);

        $arr_text_color = $this->hexrgb($captcha_text_color);
        $text_color = imagecolorallocate($image, $arr_text_color['red'], $arr_text_color['green'], $arr_text_color['blue']);

        $arr_noice_color = $this->hexrgb($captcha_noice_color);
        $image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], $arr_noice_color['green'], $arr_noice_color['blue']);


        /* generating the dots randomly in background */
       /* for ($i = 0; $i < $random_dots; $i++) {
            imagefilledellipse($image, mt_rand(0, $image_width), mt_rand(0, $image_height), 2, 3, $image_noise_color);
        }*/


        /* generating lines randomly in background of image */
        /*for ($i = 0; $i < $random_lines; $i++) {
            imageline($image, mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(0, $image_width), mt_rand(0, $image_height), $image_noise_color);
        }


        /* create a text box and add 6 letters code in it */
        /*$textbox = imagettfbbox($font_size, 0, $font, $code);
        $x = ($image_width - $textbox[4]) / 2;
        $y = ($image_height - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $code);
        

        /* Show captcha image in the page html page */
       /* header('Content-Type: image/jpeg'); // defining the image type to be shown in browser widow
        imagejpeg($image); //showing the image
        imagedestroy($image); //destroying the image instance
        Session::put('security_number', $code);
    }*/

    // function for captcha
    /*function hexrgb($hexstr) {
        $int = hexdec($hexstr);

        return array("red" => 0xFF & ($int >> 0x10),
            "green" => 0xFF & ($int >> 0x8),
            "blue" => 0xFF & $int);
    }*/

    public function showLogin() {
        $this->layout = View::make('layouts.landing');
        
        if (Session::has('adminid')) {
            return Redirect::to('/admin/admindashboard');
            exit;
        }

        //echo 'hii';die;


        $input = Input::all();
        if (!empty($input)) {

            $username = trim($input['username']);
            $password = md5(trim($input['password'])); // exit;

            $rules = array(
                'username' => 'required', // make sure the username field is not empty
                'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
            );

          /*  if (Session::has('captcha'))
                $rules['captcha'] = 'required|checkcaptcha';*/

            //captcha custom validation rule
            /*Validator::extend('checkcaptcha', function($attribute, $value, $parameters) {
                if (Session::get('security_number') <> $value) {
                    return false;
                }
                return true;
            });*/

            // captcha custom message
           /* $messages = array(
                'checkcaptcha' => 'Please enter valid security code.'
            );*/

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                return Redirect::to('/admin')
                                ->withErrors($validator) // send back all errors to the login form
                                ->withInput(Input::except('password'));
                                //->withInput(Input::except('captcha')); // send back the input (not the password) so that we can repopulate the form
            } else {

                $adminuser = DB::table('admins')
                        ->where('username', $username)
                        ->where('password', $password)
                        ->first();

               

                if (empty($adminuser)) {
                    // create our user data for the authentication
                    $userData = DB::table('users')
                            ->where('email_address', $username)
                            ->orwhere('username', $username)
                            ->where('password', md5($password))
                            ->first();
                    
                    if (!empty($userData)) {
                        // check activation status
                        if ($userData->status == 0) {
                            Session::put('error_message', "Your account might have been temporarily disabled.");
                            return Redirect::to('/admin');
                        }

                        if (isset($input['remember'])) {
                            Session::put('email_address', $email_address); // 30 days
                            Session::put('planPass', $planPass); // 30 days
                            Session::put('remember', '1'); // 30 days
                        } else {
                            Session::put('email_address', ''); // 30 days
                            Session::put('password', ''); // 30 days
                            Session::put('remember', ''); // 30 days
                        }
                        // return to dashboard page
                        Session::put('user_id', $userData->id);
                        Session::forget('captcha');
                        return Redirect::to('/user/myaccount');
                    } else {
                        // return error message
                        //Session::put('captcha', 1);
                        Session::put('error_message', "Invalid username or password");
                        return Redirect::to('/admin');
                    }
                } else {
                    // destroy captcha from login
                    Session::forget('captcha');
                    // return to dashboard page
                    Session::put('adminid', $adminuser->id);
                    return Redirect::to('/admin/admindashboard');
                }
            }
        }
        $this->layout->title = TITLE_FOR_PAGES . 'Sign In';
        $this->layout->content = View::make('home.index');
    }

    // forgotpassword page
    public function showForgotpassword() {

        $this->layout = View::make('layouts.forget');
        $input = Input::all();
        if (!empty($input)) {
            $email = $input['email'];
            $rules = array(
                'email' => 'required'
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                $errors->first('email');
                return Redirect::to('/user/forgotpassword')->with('error_message', "Email Address doesn't found.");
            } else {

                $adminuser = DB::table('admins')
                        ->where('email', $email)
                        ->first();

                // echo '<pre>';print_r($adminuser);die;

                if (empty($adminuser)) {

                    // create our user data for the authentication
                    $users = DB::table('users')
                            ->where('email_address', $email)
                            ->first();

                    if (!empty($users)) {
                        // generate random password
                        $password = rand(18973824, 989721389);

                        // send email to administrator
                        $mail_data = array(
                            'text' => 'Your password has been retrived successfully.',
                            'email' => $users->email_address,
                            'new_password' => $password,
                            'firstname' => $users->first_name,
                        );

                        // update admin password
                        DB::table('users')
                                ->where('id', $users->id)
                                ->update(array('password' => md5($password)));





//                return View::make('emails.template')->with($mail_data); // to check mail template data to view

                        Mail::send('emails.template', $mail_data, function($message) use ($users) {
                            $message->setSender(array(MAIL_FROM => SITE_TITLE));
                            $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                            $message->to($users->email_address, 'User')->subject('Forgot Password');
                        });

                        if (count(Mail::failures()) > 0) {

                            echo $errors = 'Failed to send password reset email, please try again.';
                            foreach (Mail::failures() as $email_address) {
                                echo " - $email_address <br />";
                            }
                        }
                        // end mail script
                        return Redirect::to('/')->with('success_message', 'Your password has been sent on your email id.');
                    } else {
                        // return error message
                        return Redirect::to('/user/forgotpassword')->with('error_message', "Email Address doesn't found.");
                    }
                } else {

                    $password = rand(18973824, 989721389);
                    $Newpassword = md5($password);

                    // send email to administrator
                    $mail_data = array(
                        'text' => 'Your password has been retrived successfully',
                        'email' => $adminuser->email,
                        'username' => $adminuser->username,
                        'new_password' => $password,
                        'firstname' => 'Admin',
                    );


                    DB::table('admins')
                            ->where('id', 1)
                            ->update(array('password' => $Newpassword));

//                      echo $password; 
//                      print_r($Newpassword); exit;
//                return View::make('emails.template')->with($mail_data); // to check mail template data to view

                    Mail::send('emails.template', $mail_data, function($message) use ($adminuser) {
                        $message->setSender(array(MAIL_FROM => SITE_TITLE));
                        $message->setFrom(array(MAIL_FROM => SITE_TITLE));
                        $message->to($adminuser->email, 'Admin')->subject('Forgot Password');
                    });

                    if (count(Mail::failures()) > 0) {
                        echo $errors = 'Failed to send password reset email, please try again.';
                        foreach (Mail::failures() as $email_address) {
                            echo " - $email_address <br />";
                        }
                    }
                    // end mail script
                    return Redirect::to('/')->with('success_message', 'Your password has been sent on your email id.');
                }
            }
        }
        $this->layout->title = TITLE_FOR_PAGES . 'Forget';
        $this->layout->content = View::make('home.forget');
    }
       public function numberformat($price, $coun = 0) {
        return CURR . ' ' . number_format($price, $coun);
    }

}
