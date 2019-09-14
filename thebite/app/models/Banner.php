<?php


use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class Banner extends Eloquent implements RemindableInterface {

	use  RemindableTrait, SortableTrait;
         

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'banner';


}
