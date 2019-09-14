  
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
                            }
                            if (!empty($address)) {
                                ?>
                                <div class="non_edit edit_full "><?php echo $address->building . ' ' . $address->apartment . ' ' . $address->street_name . ', ' . $address->area_name . ' ' . $address->name ?></div>  
                                <?php
                            }
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
                                <div class="non_edit right_side bg_field btn_pop default_pop"><a href="#">Pickup</a></div> 
                            <?php } ?>
                        </div>
                    </div>
                    <div class="fieldd">
                        <label>Payment Type</label>
                        <div class="file_wrap">
                            <div class="non_edit edit_full " style="margin-top: 0px;">Credit Card</div>    </div>
                    </div>
                    <div class="fieldd">
                        <label>Extra Note</label>
                        <div class="file_wrap">
                            <div class="non_edit edit_full " style="margin-top: 0px;">Please arrange a '76 Lafite before we arrive.</div>    </div>
                    </div>
                    <div class="fieldd">
                        <label>Order Status</label>
                        <div class="file_wrap">

                            <div class="non_edit bg_field btn_pop">
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
                                <a href="javascript:void(0);"><?php echo $newstatus; ?></a>
                            </div>    

                        </div>
                    </div> 


                </div>

                <div class="right_popdiv">
                    <div class="order_height">
                        <div class="food_wrap">
                            <div class="food_detail_wrap first_bold">
                                <div class="food_no">2</div>    
                                <div class="food_detail">Large Pepperoni Pizza</div> 
                                <div class="food_rate">$3.50</div>   

                            </div>  
                            <div class="food_detail_wrap ">
                                <div class="food_no"></div>    
                                <div class="food_detail">+extra cheese</div> 
                                <div class="food_rate">$0.50</div>   

                            </div>  
                            <div class="food_detail_wrap ">
                                <div class="food_no"></div>    
                                <div class="food_detail">+cheese crust</div> 
                                <div class="food_rate">$0.25</div>   

                            </div>

                        </div>    

                        <div class="food_wrap">
                            <div class="food_detail_wrap first_bold">
                                <div class="food_no">1</div>    
                                <div class="food_detail">Medium Pepperoni Pizza</div> 
                                <div class="food_rate">$3.50</div>   

                            </div>  


                        </div> </div>



                    <div class="food_wrap">
                        <div class="food_detail_wrap first_bold">
                            <div class="food_no"></div>    
                            <div class="food_detail">Subtotal</div> 
                            <div class="food_rate">$11.00</div>   

                        </div>  
                        <div class="food_detail_wrap first_bold">
                            <div class="food_no"></div>    
                            <div class="food_detail" style="color:#F87421"><i>Discount</i></div> 
                            <div class="food_rate" style="color:#F87421"><i>$2.50</i></div>   

                        </div>  




                        <div class="food_detail_wrap first_bold">
                            <div class="food_no"></div>    
                            <div class="food_detail"><i>Taxes</i></div> 
                            <div class="food_rate"><i>$0.50</i></div>   

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