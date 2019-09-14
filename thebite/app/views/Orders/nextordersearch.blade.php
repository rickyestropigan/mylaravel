<div id="tabbx">
    <div class="menu_box">

        <?php
        $i = 1;
        $new = 0;
        $confirm = 0;
        $completed = 0;
        $cancelled = 0;

        if (!empty($orders) && count($orders) > 0) {
            foreach ($orders as $order) {
                if ($i % 2 == 0) {
                    $class = 'pull-right';
                } else {
                    $class = '';
                }
                if ($order->status == 'Pending') {
                    $new = $new + 1;
                    $color = 'blue';
                    $but = 'blue_btn';
                    $btn_color = 'blue_btn';
                }
                if ($order->status == 'Confirm') {
                    $confirm = $confirm + 1;
                    $color = 'green';
                    $but = 'green_btn';
                    $btn_color = 'green_btn';
                }
                if ($order->status == 'Complete') {
                    $completed = $completed + 1;
                    $color = 'orange';
                    $but = 'orange_btn';
                    $btn_color = 'orange_btn';
                }
                if ($order->status == 'Cancel') {
                    $cancelled = $cancelled + 1;
                    $color = 'default';
                    $but = 'simple_btn_menu ';
                    $btn_color = '';
                }

                $RestaurantData = DB::table('users')
                        ->select("users.*")
                        ->where('users.id', $order->caterer_id)
                        ->first(); // get cateter details
                $userData = DB::table('users')
                        ->select("users.*")
                        ->where('users.id', $order->user_id)
                        ->first(); // get user details
//print_r($cartItems);
                $cartItems = DB::table('order_item')
                        ->whereIn('menu_id', explode(',', $order->order_item_id))
                        ->where('order_id', $order->id)
                        ->get();
                ?>
                <div class="menu_block <?php echo $class . ' ' . $color; ?>  order_box">
                    <div class="menu_top_title">
                        <span class="pull-left"><?php echo $order->first_name; ?> <?php echo substr($order->last_name, 0, 1); ?>.</span> <?php if ($order->status == 'Pending') { ?><a class="circle_btn" href="#">New</a><?php } ?>
                        <span class="pull-right">$ <?php echo number_format((float) $order->total, 2, '.', ''); ?></span>
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
                    <?php if ($order->discount > 0) { ?>
                        <div class="discnt"><?php echo $order->discount; ?>% off on this order</div>
                    <?php } else { ?>
                        <div class="discnt">&nbsp;</div>
                    <?php } ?>

                    <div class="tabb tabb_width history_width">
                        <div class="offer_tabb">
                            <span>Promised by:</span>
                            
                            <?php
                            
                            if ($color == 'blue') {
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
                                    ?>
                                </a>
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
                                <span>Order Type:</span>
                                <?php
                                if ($color == 'blue') {
                                    ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/blue_delivery.png"></i><?php echo $order->delivery_type; ?></a>
                                    <?php
                                } elseif ($color == 'green') {
                                    ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/greenhouse.png"></i><?php echo $order->delivery_type; ?></a>
                                    <?php
                                } elseif ($color == 'orange') {
                                    ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/orangedeleiber.png"></i><?php echo $order->delivery_type; ?></a>
                                <?php } elseif ($color == 'default') { ?>
                                    <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/deliver_icon.png"></i><?php echo $order->delivery_type; ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    $newstatus = '';
                    if ($order->status == 'Pending') {
                        $newstatus = 'Confirm';
                    } else if ($order->status == 'Confirm') {
                        $newstatus = 'Confirmed';
                    } else if ($order->status == 'Complete') {
                        $newstatus = 'Completed';
                    } else if ($order->status == 'Cancel') {
                        $newstatus = 'Cancelled';
                    } else {
                        $newstatus = $order->status;
                    }
                    ?> 
                    <div class="simple_btn ">
                        <a class="<?php echo $but; ?>" href="javascript:void(0)"><?php echo $newstatus; ?></a>
                        <a class="simple_btn_menu detail_ord" data-id="<?php echo $order->id; ?>" data-order="<?php echo $order->slug; ?>" href="javascript:void(0)" data-keyboard="true" data-backdrop="true" data-controls-modal="leave_modal" data-toggle="modal" data-target="#editreserModal_<?php echo $order->id; ?>">details</a><!--orderview_bpop_<?php //echo $order->id;                 ?>-->
                        <div class="timediv">Received at <?php echo date('h:i A', strtotime($order->created)); ?></div>
                    </div>
                    <div></div>

                </div>
                <div id="editreserModal_<?php echo $order->id; ?>" class="modal fade editscreen addmenu view_order" role="dialog">

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
<div class="tab_bottom">
    <ul>
        <li class="active"><a href="javascript:void(0)" class="alldata" data-cat="all">all(<?php echo count($orders); ?>)</a></li> 
        <li><a href="javascript:void(0)" class="alldata" data-cat="complete">completed(<?php echo $completed; ?>)</a></li>
        <li><a href="javascript:void(0)" class="alldata" data-cat="cancel">cancelled(<?php echo $cancelled; ?>)</a></li>

    </ul>   

</div>