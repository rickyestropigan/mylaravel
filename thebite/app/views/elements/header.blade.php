
<?php
  
use Moltin\Cart\Cart;
use Moltin\Cart\Storage\CartSession;
use Moltin\Cart\Identifier\Cookie;

$cart = new Cart(new CartSession, new Cookie);
$adminuser = DB::table('site_settings')->first();
?>

<div id="toTop">TOP</div>
<div class="header">
    <div class="logo">
        <?php
        if (file_exists(UPLOAD_LOGO_IMAGE_PATH . SITE_LOGO)) {
            ?>
            <a class="" href="<?php echo HTTP_PATH; ?>">{{ HTML::image(DISPLAY_LOGO_IMAGE_PATH.SITE_LOGO, '', array()) }}</a>

            <?php
        } else {
            ?>
            <a href="<?php echo HTTP_PATH; ?>"><img src="{{ URL::asset('public/img/front') }}/logo.png" alt="logo" /></a>

            <?php
        }
        ?>
    </div>
    <div class="top_right">
        <div class="header_buitton">
            <div class="header_button_option order_fixeds"><a href="<?php echo HTTP_PATH . 'order/todayorders/all'; ?>">No New orders<!-- <i class="fa fa-bell" aria-hidden="true"></i>  --></a></div>
            <div class="header_button_option active_btn"><a href="<?php echo HTTP_PATH . 'reservation/todayorders/all'; ?>"> <i class="fa fa-bell" aria-hidden="true"></i> 2 New Reservations</a></div>

        </div>
        <div class="top_profile profle"><a href="#"><i class="fa fa-user" aria-hidden="true"></i> </a>
            <div class="opetion_setting profle_show">
                <div class="sound"><a href="#"><i class="fa fa-bell" aria-hidden="true"></i> Notification</a></div>   

                <div class="sound">
                    <a href="{{HTTP_PATH.'user/openinghours'}}"><i class="fa fa-clock-o" aria-hidden="true"></i>Opening Hours</a>
                </div>
<!--                <div class="sound">
                    <a href="{{HTTP_PATH.'user/slot'}}"><i class="fa fa-times-circle-o" aria-hidden="true"></i>Slots</a>
                </div>-->
                <div class="sound"><a href="<?php echo HTTP_PATH . 'user/logout'; ?>"><i class="fa fa-sign-out" aria-hidden="true"></i> Log Out</a></div>  

            </div> 
        </div>
    </div>
</div>

