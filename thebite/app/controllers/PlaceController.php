<?php

class PlaceController extends BaseController {
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

   protected $layout = 'layouts.homedefault';

    public function showPlaces() {
      
       $this->layout->content = View::make('place.index');




    }


  }
  ?>