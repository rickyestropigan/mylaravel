<?php

class BaseController extends Controller {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    public function createUniqueSlug($string = null, $model = null) {

        $string = substr(strtolower($string), 0, 35);
//        if ($string == 0) {
//            $string = "zero";
//        }
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $string = strtolower(preg_replace($old_pattern, $new_pattern, $string));
        $findData = DB::table($model)
                ->where('slug', $string)
                ->orderBy('id', 'desc')
                ->first();
        if (!empty($findData)) {
            $uniqueSlug = $string . '-' . time();
        } else {
            $uniqueSlug = $string;
        }


        return $uniqueSlug;
    }

    function userLoggedinCheck() {
        $user_id = Session::get('user_id');

        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        if (!empty($userData)) {
            return Redirect::to('/user/myaccount');
        } else {
            return Redirect::to('/user/login');
        }
    }

    function chkUserType($valid_user_type = null) {

        $user_id = Session::get('user_id');
        $userData = DB::table('users')
                ->where('id', $user_id)
                ->first();
        $user_type = $userData->user_type;
        if ($user_type != $valid_user_type) {
            return false;
        } else {
            return true;
        }
    }

    function getlatlong() {
        $key = "9dcde915a1a065fbaf14165f00fcc0461b8d0a6b43889614e8acdb8343e2cf15";
        $ip = $_SERVER["REMOTE_ADDR"];
        $url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=xml";
// load xml file
        $xml = simplexml_load_file($url);
// create a loop to print the element name and data for each node
        $array = array(
            'lat' => 0,
            'long' => 0
        );
        foreach ($xml->children() as $child) {
            if ($child->getName() == 'latitude') {
                $array['lat'] = $child;
            }if ($child->getName() == 'longitude') {
                $array['long'] = $child;
            }
        }
        return $array;
    }

    function humanTiming($time) {

        $time = time() - $time; // to get the time since that moment
        $time = ($time < 1) ? 1 : $time;
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
    }

    function checkAPI($api_key = NULL) {
        $ak = 'FOOD2AMhgHbyVwOijJGJguIsrTbyBHUVAN784vnBYBBgUYGB';
        if ($ak != $api_key) {
            echo $this->errorOutput('Wrong URL access');
            exit;
        }
    }

    function errorOutput($errormsg = null) {
        return '{"response_status":"error","response_msg":"' . $errormsg . '","response_data":""}';
    }

    function deActivateOutput($errormsg = null) {
        return '{"response_status":"Deactivated","response_msg":"' . $errormsg . '","response_data":""}';
    }

    function successOutput($successmsg = null) {
        return '{"response_status":"success","response_msg":"' . $successmsg . '","response_data":""}';
    }

    function output($output = null) {
        return '{"response_status":"success","response_data":' . $output . ',"response_msg":""}';
    }

    function outputresult($output = null,$errormsg= null) {
        return '{"response_status":"success","response_data":' . $output . ',"response_msg":"' . $errormsg . '"}';
    }

    function outputs($output = null, $total_data = null) {
        return '{"response_status":"success","response_data":' . $output . ',"total_data":' . $total_data . ',"response_msg":""}';
    }

    function outputwithError($output = null) {
        return '{"response_status":"error","response_msg":' . $output . ',"response_data":""}';
    }

}
