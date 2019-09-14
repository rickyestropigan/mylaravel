<?php
if(!function_exists('weekdays')) {
  function weekdays() {
    return array(
      'Mon' => 'Monday',
      'Tus' => 'Tuesday',
      'Wed' => 'Wednesday',
      'Thu' => 'Thursday',
      'Fri' => 'Friday',
      'Sat' => 'Saturday',
      'Sun' => 'Sunday'
    );
  }
}

if(!function_exists('getMiles')) {
  function getMiles($user_id = NULL, $res_id = NULL){
      $result = array();
        $user_Data = DB::table('users')
                ->select('users.id', 'users.address', 'users.latitude', 'users.longitude')
                ->where('users.id', $user_id)
                ->get();

        $lat = $user_Data[0]->latitude;
        $lng = $user_Data[0]->longitude;
        $result = DB::select("SELECT tbl_users.id as userid,((ACOS(SIN('$lat' * PI() / 180) * SIN(latitude * PI() / 180) + COS('$lat' * PI() / 180) * COS(latitude * PI() / 180) * COS(('$lng' - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance FROM tbl_users where latitude != '' AND longitude != '' AND user_type = 'Restaurant' AND tbl_users.status = '1' AND id = '$res_id'");
        if($result){
            return number_format($result[0]->distance,1);
        } else {
            return "N/A";
        }
  }
}
?>