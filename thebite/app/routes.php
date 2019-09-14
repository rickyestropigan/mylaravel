<?php
date_default_timezone_set('Asia/Kolkata');
/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */

Route::group(array('prefix' => 'admin'), function() {
    $adminId = Session::get('adminid');
    
    Route::get('/admin/login', 'AdminController@showAdminlogin');
});



Route::get('/admin/user/admin_index', array('as' => 'admin.admin_index', 'uses' => 'UserController@all'));

/* * ** Admin route ** */
Route::get('/admin/logout', 'AdminController@showAdminlogout');
Route::get('/admin/admindashboard', 'AdminController@showAdmindashboard');

/**** Restaurants ***********/
Route::get('/admin/restaurants/admin_add', 'UserController@showAdmin_add');
Route::post('/admin/restaurants/admin_add', 'UserController@showAdmin_add');
Route::get('/admin/restaurants/Admin_activeuser/{id}', 'UserController@showAdmin_activeuser');
Route::get('/admin/restaurants/Admin_deactiveuser/{id}', 'UserController@showAdmin_deactiveuser');
Route::get('/admin/restaurants/Admin_deleteuser/{id}', 'UserController@showAdmin_deleteuser');
Route::get('/admin/restaurants/Admin_edituser/{id}', 'UserController@showAdmin_edituser');
Route::post('/admin/restaurants/Admin_edituser/{id}', 'UserController@showAdmin_edituser');
Route::get('/admin/restaurants/admindeleteuser/{id}', 'UserController@showAdmindeleteuser');
Route::get('/admin/restaurants/admindeleteuser/{id}', 'UserController@showAdmindeleteuser');
Route::any('/admin/restaurants/Admin_menulist/{id}', 'UserController@showAdminmenulists');
Route::get('/admin/restaurants/admin_index', array('as' => 'admin.users', 'uses' => 'UserController@showAdmin_index'));
Route::post('/admin/restaurants/admin_index', array('as' => 'admin.users', 'uses' => 'UserController@showAdmin_index'));
Route::get('/admin/restaurants/Admin_menulist/{id}', array('as' => 'admin.menus', 'uses' => 'UserController@showAdminMenu'));
Route::post('/admin/restaurants/Admin_menulist/{id}', array('as' => 'admin.menus', 'uses' => 'UserController@showAdminMenu'));
Route::post('/admin/restaurants/Admin_openinghours/{id}', 'UserController@showAdminopeninghours');
Route::get('/admin/restaurants/Admin_openinghours/{id}', 'UserController@showAdminopeninghours');
/**** Slot management ******/
Route::get('/admin/restaurants/Admin_slotmanagement/{id}', 'UserController@showAdminSlot');
Route::post('/admin/restaurants/Admin_slotmanagement/{id}', 'UserController@showAdminSlot');

/************* - Bank Management ************/
Route::get('/admin/restaurants/banking/{id}', 'UserController@showRestBank');
        
/* * ** pages route ** */
Route::get('/admin/page/admin_index', 'PageController@showAdmin_index');
Route::get('/admin/page/admin_add', 'PageController@showAdmin_add');
Route::post('/admin/page/admin_add', 'PageController@showAdmin_add');
Route::post('/admin/page/admin_index', 'PageController@showAdmin_index');
Route::get('/admin/page/Admin_activepage/{id}', 'PageController@showAdmin_activepage');
Route::get('/admin/page/Admin_deactivepage/{id}', 'PageController@showAdmin_deactivepage');
Route::get('/admin/page/Admin_deletepage/{id}', 'PageController@showAdmin_deletepage');
Route::get('/admin/page/Admin_editpage/{id}', 'PageController@showAdmin_editpage');
Route::post('/admin/page/Admin_editpage/{id}', 'PageController@showAdmin_editpage');
Route::get('/admin/page/admindeletepage/{id}', 'PageController@showAdmindeletepage');

/***** ADMIN ROUTE ******/
Route::get('/captcha', 'AdminHomeController@showCapcha');
Route::post('/admin/forgotpassword', 'AdminController@showForgotpassword');
Route::get('/admin/changepassword', 'AdminController@showChangepassword');
Route::post('/admin/changepassword', 'AdminController@showChangepassword');
Route::get('/admin/editprofile', 'AdminController@showEditprofile');
Route::post('/admin/editprofile', 'AdminController@showEditprofile');
Route::any('/admin/timeSettings', 'AdminController@showTimesettings');
Route::any('/admin/sitesetting', 'AdminController@sitesetting');
Route::any('/admin/changelogo', 'AdminController@changelogo');
Route::get('/adminpage', 'AdminController@showAdmindashboard');

/* * * customer routes * * */
Route::get('/admin/customer/admin_add', 'CustomerController@showAdmin_add');
Route::post('/admin/customer/admin_add', 'CustomerController@showAdmin_add');
Route::get('/admin/customer/Admin_activeuser/{id}', 'CustomerController@showAdmin_activeuser');
Route::get('/admin/customer/Admin_deactiveuser/{id}', 'CustomerController@showAdmin_deactiveuser');
Route::get('/admin/customer/Admin_deleteuser/{id}', 'CustomerController@showAdmin_deleteuser');
Route::get('/admin/customer/Admin_edituser/{id}', 'CustomerController@showAdmin_edituser');
Route::post('/admin/customer/Admin_edituser/{id}', 'CustomerController@showAdmin_edituser');
Route::get('/admin/customer/admindeleteuser/{id}', 'CustomerController@showAdmindeleteuser');
Route::get('/admin/customer/admin_index', array('as' => 'admin.customers', 'uses' => 'CustomerController@showAdmin_index'));
Route::post('/admin/customer/admin_index', array('as' => 'admin.customers', 'uses' => 'CustomerController@showAdmin_index'));

/* * * set front end function  ** */
Route::get('/', 'HomeController@showHome');
Route::get('/admin', 'AdminHomeController@showWelcome');
//Route::get('/home', 'FrontController@showHome');
Route::post('/signup', 'HomeController@signup');
Route::post('/userlogin', 'HomeController@logincheck');
Route::post('/forgotpassword', 'HomeController@showForgotpassword');
Route::get('/listing', 'ListingController@showListing');
Route::post('/getdata', 'ListingController@showSearch');
Route::post('/getlocation', 'ListingController@showLocation');
Route::get('/logout', 'ListingController@logout');
//Route::get('/deliverydetails/{parameter}', 'DeliveryController@showpage');
Route::get('/restaurantdetail/{parameter}', 'RestaurantdetailController@showpage');
Route::post('/getdatares', 'ListingController@showSearchreservation');
Route::post('/getdatapickup', 'ListingController@showSearchpickup');
Route::post('/getfilterdata', 'ListingController@showFilterdata');
Route::post('/getresfilterdata', 'ListingController@showresFilterdata');
Route::post('/getpickfilterdata', 'ListingController@showpickFilterdata');
Route::post('/profile', 'ListingController@changeProfiledata');
Route::get('/place', 'PlaceController@showPlaces');
Route::get('/terms_conditions', 'HomeController@termsconditions');
Route::post('/contact_us', 'HomeController@contactus');
Route::any('/getbest', 'ListingController@showBest');
Route::any('/getprice', 'ListingController@showPrice');
Route::any('/getdiscount', 'ListingController@showDiscount');
Route::any('/getresbest', 'ListingController@showResbest');
Route::any('/getresprice', 'ListingController@showResprice');
Route::any('/getresdiscount', 'ListingController@showResdiscount');
Route::any('/getpickbest', 'ListingController@showPickbest');
Route::any('/getpickprice', 'ListingController@showPickprice');
Route::any('/getpickdiscount', 'ListingController@showPickdiscount');
Route::any('/restaurantdetail/getmenu', 'RestaurantdetailController@showMenu');
Route::any('/restaurantdetail/getfavourite', 'RestaurantdetailController@showFavourite');
Route::any('/restaurantdetail/getreviews', 'RestaurantdetailController@showReviews');
Route::any('/slotdetails', 'SlotController@showSlot');
Route::get('/home/resetPassword/{value1}/{value2}', 'HomeController@showResetPassword');
Route::post('/home/resetPassword/{value1}/{value2}', 'HomeController@showResetPassword');
Route::any('/discountdetails', 'DiscountController@showDiscount');

Route::any('/map_show', 'ListingController@restaurent_map');
Route::any('/saveLocation', 'ListingController@saveLocation');
Route::any('/updateaddress', 'ListingController@updateaddress');
Route::any('/updateLocation', 'ListingController@updateLocation');
Route::any('/getdistance', 'ListingController@showDistance');
Route::any('/getresdistance', 'ListingController@showresDistance');
Route::any('/getpickdistance', 'ListingController@showpickDistance');
Route::any('/getmoreslot', 'SlotController@getslot');
Route::any('/gettimedata', 'ListingController@restaurant_time');
Route::any('/getdatedata', 'ListingController@restaurant_date');



/***Login*********/

Route::get('/login', 'AdminHomeController@showLogin');
Route::post('/login', 'AdminHomeController@showLogin');
Route::get('/captcha', 'AdminHomeController@showCapcha');
Route::any('/user/forgotpassword', 'AdminHomeController@showForgotpassword');

//Account page 

Route::get('/user/myaccount', 'UserController@showMyaccount');
Route::get('/user/bankaccount', 'UserController@showBankaccount');
Route::get('/user/logout', 'UserController@showLogout');
Route::get('/user/editAccount/{id}', 'UserController@showEditbank');
Route::post('/user/editAccount/{id}', 'UserController@showEditbank');
Route::get('/user/editProfile', 'UserController@showEditProfile');
Route::post('/user/editProfile', 'UserController@showEditProfile');

Route::get('/user/changePicture', 'UserController@showChangePicture');
Route::post('/user/changePicture', 'UserController@showChangePicture');
Route::get('/user/deleteUserImage', 'UserController@showDeleteUserImage');

Route::get('/user/openinghours', 'UserController@showOpeninghours');
Route::post('/user/openinghours', 'UserController@showOpeninghours');

Route::post('/user/updateOpeninghours', 'UserController@showOpeningupdatehours');

Route::post('/user/openingstatus', 'UserController@showOpeningstatus');
Route::post('/user/openingdays', 'UserController@showOpeningdays');

Route::get('/user/changePassword', 'UserController@showChangePassword');
Route::post('/user/changePassword', 'UserController@showChangePassword');

/* * * set page route *** */
Route::get('/{id}', 'PageController@showindex');
Route::get('page/{id}', 'PageController@showIndexNew');
Route::get('/data/{id}', 'PageController@showdata');

App::missing(function($exception) {
    return Response::view('errors.missing', array(), 404);
});

/*
 * Manage menu routing
 */ 
Route::any('/user/managemenu', 'UserController@showManagemenu');
//Route::post('/user/managemenu', 'UserController@showManagemenu');
Route::get('/user/addmenu', 'UserController@showAddmenu');
Route::post('/user/addmenu', 'UserController@showAddmenu');
Route::get('/user/editmenu', 'UserController@showEditmenu');
Route::post('/user/editmenu', 'UserController@showEditmenu');
Route::get('/user/deletemenu/{id}', 'UserController@showDeletemenu');
Route::get('/user/viewmenu/{id}', 'UserController@showViewmenu');
Route::post('/user/viewmenu/{id}', 'UserController@showViewmenu');


/*
 * Manage menu item routing
 */      
Route::get('/user/managemenuitem', 'UserController@showManagemenuItem');
Route::post('/user/managemenuitem', 'UserController@showManagemenuItem');
Route::get('/user/addmenuitem/{id}', 'UserController@showAddmenuItem');
Route::post('/user/addmenuitem/{id}', 'UserController@showAddmenuItem');
Route::get('/user/editmenuitem', 'UserController@showEditmenuitem');
Route::post('/user/editmenuitem', 'UserController@showEditmenuitem');
Route::any('/user/subeditmenuitem', 'UserController@showEditmenuitemsub');
Route::get('/user/deleteActionMenuItem/{value1}/{value2}', 'UserController@deleteActionMenu');
Route::get('/user/getCuisineDiscount/{id}', 'UserController@getCuisineDiscount');
Route::get('/user/viewmenuitem/{id}', 'UserController@showViewmenuitem');
Route::post('/user/viewmenuitem/{id}', 'UserController@showViewmenuitem');

Route::get('/user/changevisiblity', 'UserController@showChangeVisiblity');
Route::post('/user/changevisiblity', 'UserController@showChangeVisiblity');



/* * ** orders route ** */

Route::any('/admin/order/admin_index', array('as' => 'admin.orders', 'uses' => 'OrderController@showAdmin_index'));
Route::any('/admin/order/suborders/{slug}', array('as' => 'admin.innerorder', 'uses' => 'OrderController@showAdminSub_view'));

Route::any('/admin/order/view/{id}', 'OrderController@showAdmin_view');
Route::any('/order/receivedorders/{id}', 'OrderController@showreceivedorders');
Route::any('/order/receivedview/{id}/{type}', 'OrderController@showreceivedview');
Route::any('/order/printorder/{id}', 'OrderController@showPrint');
Route::any('/order/todayorders/{id}', 'OrderController@showtoday');
Route::any('/order/scheduleorders/{id}', 'OrderController@showschedule');

Route::get('/order/cancelorder', 'OrderController@showcancelorder');
Route::post('/order/cancelorder', 'OrderController@showcancelorder');

Route::get('/order/updatetime', 'OrderController@showupdatetime');
Route::post('/order/updatetime', 'OrderController@showupdatetime');

Route::any('/admin/payments', array('as' => 'admin.payments', 'uses' => 'PaymentController@showAdmin_payment_index'));
Route::any('/admin/payment/deletepayment/{slug}', array('as' => 'admin.paymentsc', 'uses' => 'PaymentController@showAdmin_deletepayment'));

Route::any('/user/paymenthistory/{type}', 'UserController@paymenthistory');

Route::any('/user/receivedpayment',array('as' => 'user.payments', 'uses' => 'UserController@receivedpayment'));


/**** Slot management ******/
Route::get('/user/slot', 'UserController@showSlot');
Route::post('/user/slot', 'UserController@showSlot');

Route::get('/admin/restaurants/Admin_slotmanagemnt/{id}', 'UserController@showAdminSlot');
Route::post('/admin/restaurants/Admin_slotmanagemnt/{id}', 'UserController@showAdminSlot');

/*
 * reservations routes
 */
Route::any('/admin/reservations/admin_index', array('as' => 'admin.reservations', 'uses' => 'ReservationController@showAdmin_index'));
Route::get('/admin/reservations/Admin_reservation', 'ReservationController@showAdmin_reservation');
Route::post('/admin/reservations/Admin_reservation', 'ReservationController@showAdmin_reservation');
Route::post('/admin/restaurants/Admin_slotmanagemnt/{id}', 'UserController@showAdminSlot');
/*
 * Alok New Routes
 */
Route::any('/reservation/dashboard', 'ReservationController@showdashboard');
Route::any('/nextorder', 'ReservationController@shownextorder');
Route::any('/searchorder', 'ReservationController@showsearchorder');
Route::any('/scheduleorder', 'ReservationController@showscheduleorder');
Route::any('/taborder', 'ReservationController@showtaborder');
Route::any('/reserveorder', 'ReservationController@showtabreservation');
Route::any('/tabreserve', 'ReservationController@showtabreserve');
Route::any('/nextreservation', 'ReservationController@shownextreservation');
Route::any('/newreservation', 'ReservationController@showafterchangeres');
Route::any('/schedulereservation', 'ReservationController@showschedulereserve');
Route::any('/order/editorder', 'OrderController@showeditorder');
Route::any('/order/orderdetail', 'OrderController@showvieworder');
Route::any('/reservation/reservedetail', 'ReservationController@showviewreserve');
Route::any('/reservatoin/editreservation', 'ReservationController@showeditreserve');
Route::any('/searchmenuitem', 'UserController@showsearchmenuitem');
Route::any('/user/subeditmenu', 'UserController@showsubeditmenu');

/*
 * Alok New Routes End
 */

Route::any('/reservation/todayorders/{id}', 'ReservationController@showtoday');
Route::any('/reservation/scheduleorders/{id}', 'ReservationController@showschedule');
Route::any('/reservation/receivedorders/{id}', 'ReservationController@showreceivedorders');
Route::any('/reservation/reservationstatus/{id}', 'ReservationController@showreservationstatus');

Route::get('/reservation/updatestatus', 'ReservationController@showupdatestatus');
Route::post('/reservation/updatestatus', 'ReservationController@showupdatestatus');

Route::any('/reservation/reservation', 'ReservationController@showreservation');
Route::get('/reservations/ajaxreservation', 'ReservationController@showajaxreservation');
Route::post('/reservations/ajaxreservation', 'ReservationController@showajaxreservation');
//Menu routs Start Alok
Route::post('/user/menuitemstatus', 'UserController@showstatusonoff');
Route::post('/user/nextmenuitem', 'UserController@shownextitems');


/*
 * Offer routing
 * 
 */
Route::any('/offer/manageoffer', 'OfferController@showmanageoffer');
Route::get('/offer/addoffer', 'OfferController@showAddoffer');
Route::post('/offer/addoffer', 'OfferController@showAddoffer');
Route::post('/offer/editoffer', 'OfferController@showEditOffer');
Route::get('/offer/editoffer', 'OfferController@showEditOffer');
Route::post('/offer/editofferslot', 'OfferController@showEditOfferSlot');
Route::get('/offer/editofferslot', 'OfferController@showEditOfferSlot');

Route::get('/offer/changevisiblity', 'OfferController@showChangeVisiblity');
Route::post('/offer/changevisiblity', 'OfferController@showChangeVisiblity');
Route::any('/offer/deleteoffer', 'OfferController@showDeleteoffer');

Route::any('/offer/sloteditpage', 'OfferController@showsloteditpage');



Route::any('/offer/updatetime','OfferController@showupdatetime');
Route::any('/offer/offerstatus','OfferController@showofferstatus');
Route::any('/nextoffer','OfferController@shownextoffer');
Route::any('/searchoffer','OfferController@showsearchoffer');
Route::any('/offer/editofferpage','OfferController@shownEditofferpage');
Route::any('/offer/editofferslotpage','OfferController@shownEditofferslotpage');

//Alok Invoices Page

Route::any('/order/invoices','OrderController@showInvoices');
Route::any('/order/reserveinvoice','OrderController@showReserveInvoices');
Route::any('/order/searchinvoice','OrderController@showSearchInvoices');
Route::any('/order/printordinvoice','OrderController@showPrintOrdInvoices');
Route::any('/order/printresinvoice','OrderController@showPrintResInvoices');
Route::any('/order/history','OrderController@showOrderHistory');
Route::any('/order/hisreserveorder','OrderController@showOrderHistorySearch');
Route::any('/order/hissearchorder','OrderController@showorHistorySearch');
Route::any('/order/taborder', 'OrderController@showtaborder');
Route::any('/order/tabreserve', 'OrderController@showtabreserve');
Route::any('/history/orderdetail', 'OrderController@showhisvieworder');
Route::any('/history/reservedetail', 'OrderController@showhisviewreserve');
Route::any('/owner/menulist', 'UserController@showmenulist');
Route::any('/owner/modifierlist', 'UserController@showmodifierlist');
Route::any('/order/subeditorder', 'OrderController@showsubeditorder');
Route::any('/order/subeditorderstatus', 'OrderController@showsubeditorderstatus');
Route::any('/order/subeditreserve', 'OrderController@showsubeditreser');
Route::any('/user/deletemenu', 'UserController@showdeletemenu');
Route::any('/user/deletemenuitem', 'UserController@showDeletemenuitem');
Route::any('/user/rearrangemenu', 'UserController@showRearrangemenu');
Route::any('/user/menuorderchange', 'UserController@showMenuorderchange');
Route::any('/user/rearrangemenuitem', 'UserController@showRearrangeitem');
Route::any('/user/menuitemorderchange', 'UserController@showMenuitemorderchange');
Route::any('/offer/updateofferslot', 'OfferController@showUpdateofferslotchange');
Route::any('/reservation/changeresstatus', 'ReservationController@showchangeresstatus');
Route::any('/order/changeorderstatusall', 'OrderController@showchangeorstatusall');



Route::any('/reservation/printreservation/{id}', 'ReservationController@showPrintres');
Route::any('/getrestaurants/', 'HomeController@showgetRestaurants');
Route::any('/listing/searchLocation/', 'ListingController@searchLocation');




