<?php

class FrontController extends BaseController {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected $layout = 'frontlayout.frontdefault';
	public function showHome()
	{
			
		//$this->layout = View::make('layouts.landing');
   		 $this->layout->title = 'Welcome :: Bitebargain '.TITLE_FOR_PAGES;
    	 $this->layout->content = View::make('front.index');

	}

}
