
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="reservatyion_pop">
                <div class="fieldd top_headd">
                    <div class="top_head">Recieved Time : <?php echo date('d/m/Y h:i A', strtotime($order->created)); ?></div>
                    <?php if ($order->status == 'Confirm') { ?>
                        <div class="top_head">Confirmed Time : <?php echo date('d/m/Y h:i A', strtotime($order->modified)); ?></div>
                    <?php } ?>
                </div>
                <div class="left_popdiv">

                    <div class="fieldd">
                        <label>Personal Info</label>
                        <div class="file_wrap">
                            <div class="non_edit"><?php echo $order->first_name; ?> <?php echo substr($order->last_name, 0, 1); ?>.</div>    
                            <div class="non_edit right_side"> <?php echo $order->contact; ?></div> 
                            <?php
                            if ($order->address_id > 0) {
                                $address = DB::table('addresses')
                                        ->select('addresses.*', 'areas.name as area_name', 'cities.name')
                                        ->leftjoin('cities', 'cities.id', '=', 'addresses.city')
                                        ->leftjoin('areas', 'areas.id', '=', 'addresses.area')
                                        ->where('addresses.id', $order->address_id)
                                        ->first();
                            } else {
                                $nwaddress = $order->address;
                            }
                            if (!empty($address)) {
                                ?>
                                <div class="non_edit edit_full "><?php echo $address->building . ' ' . $address->apartment . ' ' . $address->street_name . ', ' . $address->area_name . ' ' . $address->name ?></div>  
                                <?php
                            } elseif (isset($nwaddress) && !empty($nwaddress)) {
                                ?>

                                <div class="non_edit edit_full "><?php echo $nwaddress; ?></div>
                            <?php }
                            ?>
                        </div>
                    </div>  
                    <?php if ($order->discount > 0) { ?>
                        <div class="fieldd">
                            <label>Offer Applied</label>
                            <div class="file_wrap">
                                <div class="non_edit  fulll" style="width:100%;"><?php echo $order->discount; ?>% off on this order</div>

                            </div>
                        </div>
                    <?php } ?>
                    <?php
                    $gettime = date('h:i', strtotime($order->delivery_date . " +30 minutes"));
                    $grAM = date('A', strtotime($order->delivery_date . " +30 minutes"));
                    ?>
                    <div class="fieldd">
                        <label>Promise By</label>
                        <div class="file_wrap">
                            <div class="non_edit bg_field"><?php echo $gettime; ?></div>    
                            <div class="non_edit right_side bg_field"><?php echo $grAM; ?></div> 

                        </div>
                    </div> 
                    <div class="fieldd">
                        <label>Order Status</label>
                        <div class="file_wrap">
                            <?php
                            if ($order->delivery_type == 'Delivery') {
                                ?>
                                <div class="non_edit bg_field btn_pop"><a href="#">Delivery</a></div>   
                                <?php
                            }
                            ?>
                            <?php
                            if ($order->delivery_type == 'Pickup') {
                                ?>
                                <div class="non_edit bg_field btn_pop"><a href="#">Pickup</a></div> 
                            <?php } ?>
                        </div>
                    </div>
                    <div class="fieldd">
                        <label>Payment Type</label>
                        <div class="file_wrap">
                            <div class="non_edit edit_full " style="margin-top: 0px;"><?php echo $userData->payment_options; ?></div>    </div>
                    </div>
                    <div class="fieldd">
                        <label>Extra Note</label>
                        <div class="file_wrap">
                            <div class="non_edit edit_full " style="margin-top: 0px;"><?php
                                if (empty($order->comment)) {
                                    echo '&nbsp;';
                                } else {
                                    echo $order->comment;
                                }
                                ?>
                            </div> 
                        </div>
                    </div>

                    <div class="fieldd">
                        <label>Order Status</label>
                        <div class="file_wrap">
                            <div class="non_edit bg_field btn_pop reservation-new-bx">
                                <?php if ($order->status == 'Pending') { ?>
                                    <a class="changeorstatus" data-status="Confirm" data-currentdate ="<?php echo date('Y-m-d',strtotime($order->delivery_date)); ?>" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Confirm</a>
                                    <a class="changeorstatus" data-status="Cancel" data-currentdate ="<?php echo date('Y-m-d',strtotime($order->delivery_date)); ?>" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Cancel</a>
                                <?php } else if ($order->status == 'Confirm') { ?>
                                    <a class="changeorstatus" data-status="Complete" data-currentdate ="<?php echo date('Y-m-d',strtotime($order->delivery_date)); ?>" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Complete</a>
                                    <a class="changeorstatus" data-status="Cancel" data-currentdate ="<?php echo date('Y-m-d',strtotime($order->delivery_date)); ?>" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Cancel</a>
                                    <?php
                                } else if ($order->status == 'Complete') {
                                    ?>
                                    <a class="changeorstatus" data-status="Cancel" data-currentdate ="<?php echo date('Y-m-d',strtotime($order->delivery_date)); ?>" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Cancel</a>
                                <?php } else if ($order->status == 'Cancel') { ?>
                                    <a class="changeorstatus" data-status="Confirm" data-currentdate ="<?php echo date('Y-m-d',strtotime($order->delivery_date)); ?>" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Confirm</a>
                                    <a class="changeorstatus" data-status="Complete" data-currentdate ="<?php echo date('Y-m-d',strtotime($order->delivery_date)); ?>"  data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Complete</a> 
                                <?php } ?>
                            </div>
                        </div>
                        <input type="hidden" name="status" value="<?php echo $order->status; ?>" />
                    </div>

                </div>
                <?php
                $items = explode(',', $order->order_item_id);
                $total_item = count($items);
                $i = 1;
                ?>
                <div class="right_popdiv">
                    <div class="order_height">

                        <?php foreach ($items as $item) { ?>
                            <div class="food_wrap">
                                <div class="food_detail_wrap first_bold">
                                    <div class="food_no"><?php echo $total_item; ?></div>  

                                    <div class="food_detail">
                                        <?php
                                        $orderitem_detail = DB::table('order_item')->where('id', $item)->select('order_item.*')->first();
                                        if ($orderitem_detail) {
                                            $item_detail = DB::table('menu_item')->where('id', $orderitem_detail->menu_id)->select('menu_item.*')->first();
                                            echo $item_detail->item_name ? $item_detail->item_name : '';
                                        }
                                        ?>
                                    </div> 
                                    <div class="food_rate">$<?php echo number_format($orderitem_detail->base_price, 2); ?></div>   

                                </div> 
                                <?php
                                if (!empty($orderitem_detail->addon_id)) {
                                    $modifiers = explode(',', $orderitem_detail->addon_id);
                                    if ($modifiers) {
                                        foreach ($modifiers as $modifier) {
                                            $modifier_detail = DB::table('item_modifier')->where('id', $modifier)->select('item_modifier.*')->first();
                                            ?>
                                            <div class="food_detail_wrap ">
                                                <div class="food_no"></div>    
                                                <div class="food_detail">+<?php echo $modifier_detail->name; ?></div> 
                                                <div class="food_rate">$<?php echo number_format($modifier_detail->price, 2); ?></div>   

                                            </div> 
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <?php
                            $total_item--;
                        }
                        ?>


                    </div>



                    <div class="food_wrap">
                        <div class="food_detail_wrap first_bold">
                            <div class="food_no"></div>    
                            <div class="food_detail">Subtotal</div> 
                            <div class="food_rate">$<?php echo number_format($order->item_total, 2); ?></div>   

                        </div>  
                        <div class="food_detail_wrap first_bold">
                            <div class="food_no"></div>    
                            <div class="food_detail" style="color:#F87421"><i>Discount</i></div> 
                            <?php
                            $discount = 0;
                            if ($order->discount > 0 && $order->discount) {
                                $discount = ($order->item_total * $order->discount) / 100;
                            }
                            ?>
                            <div class="food_rate" style="color:#F87421"><i>$<?php echo number_format($discount, 2); ?></i></div>   

                        </div>  




                        <div class="food_detail_wrap first_bold">
                            <div class="food_no"></div>    
                            <div class="food_detail"><i>Taxes</i></div> 
                            <?php
                            if ($userData->sales_tax) {
                                $tax = ($order->item_total - $discount) * ($userData->sales_tax / 100);
                            } else {
                                $tax = '0.00';
                            }
                            ?>
                            <div class="food_rate"><i>$<?php echo number_format($tax, 2); ?></i></div>   

                        </div>  


                    </div> 

                    <div class="food_wrap">
                        <div class="food_detail_wrap first_bold">
                            <div class="food_no"></div>    
                            <div class="food_detail">Total</div> 
                            <div class="food_rate">$<?php echo $order->total; ?></div>   
                        </div>  

                    </div>
                </div> 
                <div class="bottm_btn">
                    <label></label>    
                    <div class="btnm_btnn">
                        <a class="edit_pen" href="javascript:void(0);" data-order="<?php echo $order->slug; ?>" data-id = "<?php echo $order->id; ?>"><img src="{{ URL::asset('public/img/front') }}/pencil_white.png"></a>
                        <a class=" same_space" href="javascript:void(0);">&nbsp</a><!--.same_btn-->
                        <a class="same_btn defaut_btn" href="javascript:void(0);" data-dismiss="modal">cancel</a>
                        <a class="print" target="_blank" href="<?php echo HTTP_PATH . 'order/printorder/' . $order->slug; ?>"><img src="{{ URL::asset('public/img/front') }}/blueprint.png"></a>
                        <div class="simple_txt"> #<?php echo $order->order_number; ?></div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>