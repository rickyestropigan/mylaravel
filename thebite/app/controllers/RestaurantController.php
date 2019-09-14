<?php

class RestaurantController extends BaseController {
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

// --------------------------------------------------------------------
// Create slug for secure URL
    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

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
    
    public function showLoadarea($id, $area_id = 0) {
        $options = "<option value=''>Area</option>";
        $record = Area::where("city_id", $id)->orderBy('name', 'asc')->lists('name', 'id');
        if (!empty($record)) {
            foreach ($record as $key => $val)
                $options .= "<option value='$key' " . ($area_id == $key ? "selected='selected'" : "") . ">" . ucfirst($val) . "</option>";
        }
        return $options;
    }

    public function showLoadfromarea($id, $area_id = 0) {
        $options = "<option value=''>Area</option>";
        $record = Area::where("city_id", $id)->orderBy('name', 'asc')->lists('name', 'id');
        if (!empty($record)) {
            foreach ($record as $key => $val)
                $options .= "<option value='$key' " . ($area_id == $key ? "selected='selected'" : "") . ">" . ucfirst($val) . "</option>";
        }
        return $options;
    }

    public function showLoadtoarea($id, $area_id = 0) {
        $options = "<option value=''>Area</option>";
        $record = Area::where("city_id", $id)->orderBy('name', 'asc')->lists('name', 'id');
        if (!empty($record)) {
            foreach ($record as $key => $val)
                $options .= "<option value='$key' " . ($area_id == $key ? "selected='selected'" : "") . ">" . ucfirst($val) . "</option>";
        }
        return $options;
    }

}

?>
