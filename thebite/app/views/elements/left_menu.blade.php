<?php
$user_id = Session::get('user_id');
$userData = DB::table('users')
        ->select('users.address', 'users.*', "opening_hours.open_close", "opening_hours.start_time", "opening_hours.end_time", "opening_hours.minimum_order", "opening_hours.open_days")
        ->leftjoin('opening_hours', 'opening_hours.user_id', '=', 'users.id')
        ->where('users.id', $user_id)
        ->where('users.status', "=", "1")
        ->first();

?>

<!-- Sidebar Holder -->
<nav id="sidebar">
    <div class="nav_wrap">
        <div class="sidebar-header">
            <div class="logo_top">
                <div class="logo"><a href="#"><img src="{{ URL::asset('public/img/front') }}/site_logo.jpg"></a></div> 
                <?php
                    if(empty($userData->profile_image) && $userData->profile_image==''){
                ?>
                    <div class="logo big_logo"><a href="#"><img src="{{ URL::asset('public/img/front') }}/noimage.png"></a></div>    
                <?php
                    }
                    else
                    {
                ?>
                    <div class="logo big_logo"><a href="#"><img src="{{ URL::asset(DISPLAY_FULL_PROFILE_IMAGE_PATH.$userData->profile_image) }}"></a></div>    
                <?php
                    }
                ?>
                
            </div>  
        </div>

        <ul class="list-unstyled components">
            <p><?php echo $userData->first_name; ?></p>
            <li class="{{ (Request::is('user/myaccount*') or  Request::is('user/editProfile') or Request::is('user/bankaccount') or Request::is('user/editAccount*') or Request::is('user/openinghours'))  ? 'active' : '' }}">
                <a href="<?php echo HTTP_PATH.'user/myaccount'; ?>" >profile</a>
            </li>
            <li class="{{ Request::is('reservation/dashboard') ? 'active' : '' }}">
                <a href="<?php echo HTTP_PATH.'reservation/dashboard'; ?>">dashboard</a>
            </li>
            <li class="{{ Request::is('order/history') ? 'active' : '' }}">
                <a href="<? echo HTTP_PATH.'order/history' ?>">history</a>
            </li>
            <li class="{{ Request::is('user/managemenu') ? 'active' : '' }}">
                <a href="<?php echo HTTP_PATH.'user/managemenu'; ?>">menu</a>
            </li>
            <li  class="{{ Request::is('offer/manageoffer') ? 'active' : '' }}">
                <a href="<?php echo HTTP_PATH.'offer/manageoffer'; ?>">offers</a>
            </li>
            <li class="{{ Request::is('offer/invoices') ? 'active' : '' }}">
                <a href="<?php echo HTTP_PATH.'order/invoices'; ?>">invoice</a>
            </li>
        </ul>

        <ul class="list-unstyled CTAs">
            <li><a href="<?php echo HTTP_PATH.'/user/logout'; ?>" class="download">LOGOUT</a></li>

        </ul>
    </div>

</nav>
<div class="responsive_btn">
    <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn calendarnav">
        <i class="iccon"></i>
        <span>Toggle Sidebar</span>
    </button>
</div>



