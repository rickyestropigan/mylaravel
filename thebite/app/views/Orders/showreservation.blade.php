<?php
//// get current user details
$user_id = Session::get('user_id');

//$all_query = DB::table('reservations')->where('reservations.caterer_id', $user_id)->where('reservation_date', "<", date('Y-m-d'))->get();
//
//$pending_query = DB::table('reservations')->where('reservations.caterer_id', $user_id)->where('reservation_date', "<", date('Y-m-d'))->where('reservation_status', "=", 'pending')->get();
//
//$confirm_query = DB::table('reservations')->where('reservations.caterer_id', $user_id)->where('reservation_date', "<", date('Y-m-d'))->where('reservation_status', 'Confirm')->get();
//
//$noshow_query = DB::table('reservations')->where('reservations.caterer_id', $user_id)->where('reservation_date', "<", date('Y-m-d'))->where('reservation_status', 'No Show')->get();
//
//$cancel_query = DB::table('reservations')->where('reservations.caterer_id', $user_id)->where('reservation_date', "<", date('Y-m-d'))->where('reservation_status', 'Cancel')->get();
?>
<div id="tabbx">
    <div class="menu_box">

        <?php
        $i = 1;
        $new = 0;
        $confirm = 0;
        $completed = 0;
        $cancelled = 0;
        $noshow = 0;
        $color = 'default';
        $but = 'simple_btn_menu';
        if (!empty($records) && count($records) > 0) {
            $cuisine_array = array(
                '' => 'Please Select',
                'all_menu' => 'On all menu',
                'all_menu_above' => 'On all menu on orders above ',
                'all_item' => 'On selected items',
                'all_item_above' => 'On selected items on orders above '
            );
            foreach ($records as $order) {
                if ($i % 2 == 0) {
                    $class = 'pull-right';
                } else {
                    $class = '';
                }

                if ($order->reservation_status == 'Pending') {
                    $new = $new + 1;
                    $color = 'blue';
                    $but = 'blue_btn';
                    $btn_color = 'blue_btn';
                }
                if ($order->reservation_status == 'Confirm') {
                    $confirm = $confirm + 1;
                    $color = 'orange';
                    $but = 'orange_btn';
                    $btn_color = 'orange_btn';
                }

                if ($order->reservation_status == 'Complete') {
                    $completed = $completed + 1;
                    $color = 'green';
                    $but = 'green_btn';
                    $btn_color = 'green_btn';
                }

                if ($order->reservation_status == 'No Show') {
                    $noshow = $noshow + 1;
                    $color = 'red';
                    $but = 'simple_btn_menu';
                    $btn_color = '';
                }
                if ($order->reservation_status == 'Cancel') {
                    $cancelled = $cancelled + 1;
                    $color = 'default';
                    $but = 'simple_btn_menu ';
                    $btn_color = '';
                }
                ?>
                <div class="menu_block <?php echo $class . ' ' . $color; ?>  order_box">
                    <div class="menu_top_title">
                        <span class="pull-left"><?php echo $order->first_name; ?> <?php echo substr($order->last_name, 0, 1); ?>. </span> <?php if ($order->status == 'Pending') { ?><a class="circle_btn" href="#">New</a><?php } ?>
                        <span class="pull-right"> <?php echo $order->size . ' People'; ?></span>
                    </div>
                    <?php
                    if ($order->address_id > 0) {
                        $address = DB::table('addresses')
                                ->select('addresses.*', 'areas.name as area_name', 'cities.name')
                                ->leftjoin('cities', 'cities.id', '=', 'addresses.city')
                                ->leftjoin('areas', 'areas.id', '=', 'addresses.area')
                                ->where('addresses.id', $order->address_id)
                                ->first();
                    }
                    if (!empty($address)) {
                        ?>
                        <div class="address"><?php echo $address->building . ' ' . $address->apartment . ' ' . $address->street_name . ', ' . $address->area_name . ' ' . $address->name ?></div>
                    <?php } else { ?>
                        <div class="address">&nbsp;</div>
                    <?php } ?>
                    <?php if ($order->offer_id > 0) { ?>
                        <div class="discnt">
                            <?php
                            $offers = DB::table('offers')->where('offers.id', $order->offer_id)->first();
                            $text1 = '0';
                            if ($offers && $order->offer_id) {
                                $text = '';
                                $prefix = '';
                                $postfix = '';
                                $text .= $cuisine_array[$offers->offer_name];
                                if ($offers->type == 'percentage') {

                                    $postfix = '%';
                                } else {
                                    $prefix = CURR;
                                }
                                if ($offers->offer_name == 'all_menu_above' || $offers->offer_name == 'all_item_above') {
                                    $text .= CURR . $offers->above_price;
                                }
                                $text1 = $prefix . $offers->discount . $postfix . ' Off ' . $text;
                            }
                            echo $text1;
                            ?>

                        </div>
                    <?php } else { ?>
                        <div class="discnt">&nbsp;</div>
                    <?php } ?>

                    <div class="tabb tabb_width history_width">
                        <div class="offer_tabb">
                            <span>Promised by:</span>
                            <?php
                            if ($color == 'bluee') {
                                ?>
                                <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/blueclock.png"></i><?php echo date('h:i A', strtotime($order->delivery_date . " +30 minutes")); ?></a>
                                <?php
                            } elseif ($color == 'green') {
                                ?>
                                <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/greenclock.png"></i><?php
                                    if (!empty($order->pickup_time)) {
                                        echo date('h:i A', strtotime($order->pickup_time));
                                    } else {
                                        echo 'Not Set';
                                    }
                                    ?></a>
                                    <?php
                                } elseif ($color == 'orange') {
                                    ?>
                                <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/orangeclock.png"></i><?php
                                    if (!empty($order->pickup_time)) {
                                        echo date('h:i A', strtotime($order->pickup_time));
                                    } else {
                                        echo 'Not Set';
                                    }
                                    ?></a>
                                    <?php
                                } elseif ($color == 'red') {
                                    ?>
                                <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/redclock.png"></i><?php
                                    if (!empty($order->pickup_time)) {
                                        echo date('h:i A', strtotime($order->pickup_time));
                                    } else {
                                        echo 'Not Set';
                                    }
                                    ?></a>
                                <?php } elseif ($color == 'default') { ?>
                                <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/gray_clcock.png"></i><?php
                                    if (!empty($order->gray_clcock)) {
                                        echo date('h:i A', strtotime($order->pickup_time));
                                    } else {
                                        echo 'Not Set';
                                    }
                                    ?></a>
                                <?php } ?>
                        </div> 
                        <div class="offer_tabbright">


                            <div class="offer_tabb">
                                <span>Order By:</span>
                                <?php if ($color == 'blue') { ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/blucall.png"></i><?php echo $order->contact; ?></a>
                                <?php } elseif ($color == 'orange') {
                                    ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/orangecall.png"></i><?php echo $order->contact; ?></a>
                                <?php } elseif ($color == 'green') {
                                    ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/grencall.png"></i><?php echo $order->contact; ?></a>
                                <?php } elseif ($color == 'red') {
                                    ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/redcall.png"></i><?php echo $order->contact; ?></a>
                                <?php } elseif ($color == 'default') { ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/grey_call.png"></i><?php echo $order->contact; ?></a>
                                <?php } ?>
                            </div>


                        </div>

                    </div>

                    <div class="simple_btn ">
                        <?php
                        $newstatus = '';
                        if ($order->reservation_status == 'Pending') {
                            $newstatus = 'Confirm';
                        } else if ($order->reservation_status == 'Confirm') {
                            $newstatus = 'Confirmed';
                        } else if ($order->reservation_status == 'Complete') {
                            $newstatus = 'Completed';
                        } else if ($order->reservation_status == 'Cancel') {
                            $newstatus = 'Cancelled';
                        } else {
                            $newstatus = $order->reservation_status;
                        }
                        ?> 
                        <a class="<?php echo $but; ?>" href="#"><?php echo $newstatus; ?></a>
                        <a class="simple_btn_menu detail_reser" data-id="<?php echo $order->id; ?>" data-order="<?php echo $order->id; ?>" href="javascript:void(0);" data-toggle="modal" data-target="#editresModal<?php echo $order->id; ?>">details</a><!--orderview_bpop_<?php //echo $order->id;     ?>-->
                        <div class="timediv">Received at <?php echo date('h:i A', strtotime($order->created)); ?></div>
                    </div>
                    <div></div>

                </div>
                <div id="editresModal<?php echo $order->id; ?>" class="modal fade reservationscreen" role="dialog">

                </div>
                <?php
                $i++;
            }
            ?>

            <?php
        } else {
            ?>
            <div class="no_record">
                <div>No Record Found on that date.</div>
            </div>
        <?php } ?>

    </div>
</div>
<div class="tab_bottom ">
    <ul>
        <li class="active">
            <a href="javascript:void(0)" class="alldata" data-cat="all">All(<?php echo count($records); ?>)</a>
        </li> 
        <li>
            <a href="javascript:void(0)" class="alldata" data-cat="complete">Complete(<?php echo $completed; ?>)</a>
        </li>
        <!--        <li>
                    <a href="javascript:void(0)" class="alldata" data-cat="noshow">No Show(<?php echo $noshow; ?>)</a>
                </li>-->
        <li>
            <a href="javascript:void(0)" class="alldata" data-cat="cancel">Canceled(<?php echo $cancelled; ?>)</a>
        </li>

    </ul>   

</div>