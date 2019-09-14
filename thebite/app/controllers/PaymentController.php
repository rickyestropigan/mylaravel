<?php
class PaymentController extends BaseController {

    protected $layout = 'layouts.default';

    public function logincheck($url) {

        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        } else {

            $user_id = Session::get('user_id');
            $userData = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
            if (empty($userData)) {
                Session::forget('user_id');
                return Redirect::to('/');
            }
        }
    }

    public function showAdmin_index() {

        if (!Session::has('adminid')) {
            return Redirect::to('/');
        }
        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        $query = Listing::sortable()
                ->join('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', 'users.first_name', 'users.last_name')
                ->where(function ($query) use ($search_keyword) {
            $query->where('title', 'LIKE', '%' . $search_keyword . '%');
        });

        if (!empty($input['action'])) {

            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('payments')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    Session::put('success_message', 'Service Product(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('payments')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Service Product(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('payments')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Service Product(s) deleted successfully');
                    break;
            }
        }

        DB::table('payments')->where('type', '=', 'Purchase');

        $separator = implode("/", $separator);

        // Get all the users
        $payments = $query->orderBy('payments.id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Listings/adminindex', compact('payments'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_deleteuser($slug = null) {
        if (!empty($slug)) {
            DB::table('users')->where('slug', $slug)->delete();
            return Redirect::to('/admin/user/admin_index')->with('success_message', 'Service Provider deleted successfully');
        }
    }

    //In Admin payment listing
    public function showAdmin_payment_index() {
        if (!Session::has('adminid')) {
            return Redirect::to('/');
        }

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }

        $query = Payment::sortable()
                ->where(function ($query) use ($search_keyword) {
            $query->where('transaction_id', 'LIKE', '%' . $search_keyword . '%');
        });


        $separator = implode("/", $separator);

//        $query->where('type', '=', 'Purchase');
        // Get all the users
        $payments = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('payment/payment_index', compact('payments'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }
    
    //delete payment record by slug in admin
    public function showAdmin_deletepayment($slug = null) {
        // get menu item details
        $payments = DB::table('payments')
                ->where('slug', $slug)
                ->delete();
        return Redirect::back()->with('success_message', 'Payment record deleted successfully');
    }

}
?>

