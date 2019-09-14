<?php
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Reservation extends Eloquent {

    //use UserTrait,
    use RemindableTrait,SortableTrait;
     protected $table = 'reservations';
    

}
