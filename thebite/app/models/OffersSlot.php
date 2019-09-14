<?php


use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class OffersSlot extends Eloquent implements RemindableInterface {

	use RemindableTrait, SortableTrait;
         

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'offers_slot';


}
