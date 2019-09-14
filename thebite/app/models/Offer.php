<?php
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Offer extends Eloquent {

    //use UserTrait,
    use RemindableTrait,SortableTrait;
     protected $table = 'offers';
    

}
