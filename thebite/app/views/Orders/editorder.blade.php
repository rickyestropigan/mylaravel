
<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-body"> 
            <form action="" method="post" id="editorder" autocomplete="off" >
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
                                <div class="non_edit">
                                    <?php echo $order->first_name; ?> <?php echo substr($order->last_name, 0, 1); ?>.
                                </div>    
                                <div class="non_edit right_side"><?php echo $order->contact; ?></div> 
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
                                    <div class="non_edit" style="width:100%;"><?php echo $order->discount; ?>% off on pizzas</div>

                                </div>
                            </div> 
                        <?php } ?>
                        <?php
                        $get_minuts = explode(' ', $userData->estimated_time);
                        $gettime = date('h:i', strtotime($order->delivery_date . " +" . $get_minuts[0] . " minutes"));
                        $grAM = date('A', strtotime($order->delivery_date . " +" . $get_minuts[0] . " minutes"));
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
                                    <div class="non_edit bg_field btn_pop "><a href="#">Pickup</a></div> 
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
                                </div>    </div>
                        </div>

<!--                        <div class="pop_field" id="remact">
                            <label>Order Status</label> 
                            <?php if ($order->status == 'Pending') { ?>

                                <div class="input_filed thirhalf margin_left offer_on">
                                    <a href="javascript:void(0)" class="offerd_on">Confirm</a>
                                </div>

                                <div style="margin-top:4px;" class="input_filed thirhalf offer_on">
                                    <a href="javascript:void(0)" class="offerd_on">Cancel</a>
                                </div>
                            <?php } else if ($order->status == 'Confirm') {
                                ?>
                                <div class = "input_filed thirhalf margin_left offer_on">
                                    <a href = "javascript:void(0)" class = "offerd_on">Complete</a>
                                </div>
                                <div style="margin-top:4px;" class="input_filed thirhalf offer_on">
                                    <a href="javascript:void(0)" class="offerd_on">Cancel</a>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div style="margin-top:4px;" class="input_filed thirhalf offer_on active">
                                    <a href="javascript:void(0)" class="offerd_on"><?php
                                        $newstatus = '';
                                        
                                        if ($order->status == 'Complete') {
                                            $newstatus = 'Completed';
                                        } else if ($order->status == 'Cancel') {
                                            $newstatus = 'Cancelled';
                                        }
                                        echo $newstatus;
                                        ?>
                                    </a>
                                </div>-->
                            <?php }
                            ?>
                            <input type = "hidden" class = "status" name = "status" id = "statusup" value = "<?php echo $order->status; ?>">
                        <!--</div>-->

                    </div>
                    <?php
                    $items = explode(',', $order->order_item_id);
                    $total_item = count($items);
                    $itmidarr = array();
                    ?>
                    <input type="hidden" value="<?php echo $total_item; ?>" name="totalcount" id="totalcount" />
                    <div class="right_popdiv">
                        <div class="order_height special_design">
                            <div id="menuitems">
                                <?php
                                $i = 0;
                                foreach ($items as $item) {
                                    ?>
                                    <?php
                                    $tot_price = '';
                                    $orderitem_detail = DB::table('order_item')->where('id', "=", $item)->select('order_item.*')->first();
                                    $item_detail = DB::table('menu_item')->where('id', $orderitem_detail->menu_id)->select('menu_item.*')->first();
                                    $itmidarr[] = $item_detail->id;
                                    $tot_price = $tot_price + number_format($item_detail->price, 2);
                                    ?>
                                    <div class="food_wrap" id="complete_item<?php echo $item_detail->id; ?>">
                                        <div class="food_detail_wrap first_bold">
                                            <div class="food_no"><?php echo $total_item; ?></div>
                                            <input type="hidden" name="order_item[<?php echo $i ?>][item_id]" value='<?php echo $item_detail->id; ?>' />
                                            <div class="food_detail"><?php echo $item_detail->item_name; ?></div> 
                                            <div class="food_rate">$<?php echo number_format($orderitem_detail->base_price, 2); ?></div>   
                                            <a class="close_icon" data-closeid="<?php echo $item_detail->id; ?>" href="javascript:void(0)" data-classid="restaurant_cat"><i class="fa fa-times"></i></a>
                                        </div>  
                                        <div id="modifier_data<?php echo $item_detail->id; ?>">
                                            <?php
                                            if (!empty($orderitem_detail->addon_id)) {
                                                $modifiers = explode(',', $orderitem_detail->addon_id);

                                                foreach ($modifiers as $modifier) {
                                                    $modifier_detail = DB::table('item_modifier')->where('id', $modifier)->select('item_modifier.*')->first();
                                                    $tot_price = $tot_price + number_format($modifier_detail->price, 2);
                                                    ?>

                                                    <div class="food_detail_wrap blank" id="modsec<?php echo $modifier_detail->id; ?>">
                                                        <div class="food_no"></div>    
                                                        <div class="food_detail">+<?php echo $modifier_detail->name; ?></div> 
                                                        <div class="food_rate">$<?php echo number_format($modifier_detail->price, 2); ?></div>  
                                                        <a class="close_icon1" href="javascript:void(0)" data-parentid="<?php echo $modifier_detail->item_id; ?>" data-closeid="<?php echo $modifier_detail->id; ?>"><i class="fa fa-times"></i></a>
                                                        <input type="hidden" id="hmodifierpr<?php echo $modifier_detail->id; ?>" value="<?php echo number_format($modifier_detail->price, 2); ?>" />
                                                        <input type="hidden" name="order_item[<?php echo $i ?>][addon_id][]" value='<?php echo $modifier_detail->id; ?>' />
                                                    </div> 

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="food_detail_wrap ">
                                            <div class="add_field">
                                                <input type="text" data-itemid="<?php echo $item_detail->id; ?>" class="modifier" placeholder="+ add modifier">
                                                <div id="modiftext<?php echo $item_detail->id; ?>"></div>
                                            </div>
                                        </div>

                                    </div> 
                                    <input type="hidden" id="hsectiontotal<?php echo $item_detail->id; ?>" name="sectiontotal" value="<?php echo $tot_price; ?>" />
                                    <?php
                                    $i++;
                                    $total_item--;
                                }
                                ?>

                            </div>
                            <div class="food_wrap field_design">
                                <div class="food_detail_wrap ">
                                    <div class="food_no">0</div>  
                                    <div class="add_field">
                                        <input type="text" data-not_id="<?php echo implode(',', $itmidarr); ?>" id="addmenuitem" autocomplete="off" placeholder="+ add menu item">
                                        <div id="autoload"></div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="food_wrap">
                            <div class="food_detail_wrap first_bold">
                                <div class="food_no"></div>    
                                <div class="food_detail">Subtotal</div> 
                                <div class="food_rate" id="subtotal">$<?php echo number_format($order->item_total, 2); ?></div>   
                                <input type="hidden" id="hsubtotal" name="subtotal" value="<?php echo number_format($order->item_total, 2); ?>">
                            </div>  
                            <div class="food_detail_wrap first_bold">
                                <div class="food_no"></div>    
                                <div class="food_detail" style="color:#F87421"><i>Discount</i></div> 
                                <?php
                                if ($order->discount > 0 && $order->discount) {
                                    $discount = ($order->item_total * $order->discount) / 100;
                                }
                                ?>
                                <input type="hidden" name="hordiscount" id="hordiscount" value="<?php echo $order->discount; ?>" />
                                <div class="food_rate" id="discount_calculate" style="color:#F87421"><i>$<?php echo number_format($discount, 2); ?></i></div>   
                                <input type="hidden" id="hdiscount_calculate" name="discounted_price" value="<?php echo number_format($discount, 2); ?>">
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
                                <input type="hidden" name="htax" id="htax" value="<?php echo $userData->sales_tax; ?>" />
                                <div class="food_rate" id="tax_calculate"><i>$<?php echo number_format($tax, 2); ?></i></div>   
                                <input type="hidden" id="htax_calculate" name="taxt_calculate" value="<?php echo number_format($tax, 2); ?>">
                            </div>  


                        </div> 
                        <input type="hidden" name="order_id" id="horderid" value="<?php echo $order->id; ?>" />
                        <div class="food_wrap">
                            <div class="food_detail_wrap first_bold">
                                <div class="food_no"></div>    
                                <div class="food_detail">Total</div> 
                                <div class="food_rate" id="total_amount">$<?php echo $order->total; ?></div>   
                                <input type="hidden" id="htotal_amount" name="total_amount" value="<?php echo number_format($order->total, 2); ?>">
                            </div>  


                        </div>
                    </div> 

                    <div class="bottm_btn">
                        <label></label>    
                        <div class="btnm_btnn">
                            <input type="submit" class='same_btn same_space' value='Update' />
                            <a class="same_btn defaut_btn" id='icancel' href="#" data-dismiss="modal">cancel</a>
                            <a class="print" href="#"><img src="{{ URL::asset('public/img/front') }}/blueprint.png"></a>
                            <div class="simple_txt"> #<?php echo $order->order_number; ?></div>
                        </div>

                    </div>

                </div>
            </form>
        </div>

    </div>
</div>

<script>
    $('.offerd_on').on('click', function () {
        $('#remact .offer_on').removeClass('active');
        $(this).parent().addClass('active');
        var service_vis = $(this).text();
        $('#statusup').val(service_vis);
    });

    $("#addmenuitem").on('keypress', function () {
        var data = $(this).val();
        var not_id = $(this).data('not_id');
        $.ajax({
            url: '<?php echo HTTP_PATH; ?>owner/menulist',
            type: 'POST',
            data: {data: data, not_id: not_id}
        }).done(function (response) { // c
            $("#autoload").html(response);
        });
    });

    $('#autoload').on('click', 'ul li', function () {
        var cusine_id = $(this).data('cusine_id');
        var price = $(this).data('price');
        var name = $(this).data('name');
        var id = $(this).data('id');
        var item_id = $(this).data('item_id');
        var tot = $('#totalcount').val();
        tot = parseInt(tot) + 1;
        var html = '<div class="food_wrap" id="complete_item' + id + '"><div class="food_detail_wrap first_bold">';
        html += '<div class="food_no">' + tot + '</div>';
        html += '<div class="food_detail">' + name + '</div> ';
        html += '<input type="hidden" name="order_item[' + tot + '][item_id]" value="' + id + '" />';
        html += '<div class="food_rate">$' + price + '</div>';
        html += '<a class="close_icon" data-closeid="' + id + '" data-parentid="' + item_id + '" href="javascript:void(0)" data-classid="restaurant_cat"><i class="fa fa-times"></i></a></div>';
        html += '<div id="modifier_data' + id + '"></div>'
        html += '<div class="food_detail_wrap "><div class="add_field"><input type="text" data-itemid="' + id + '" class="modifier" placeholder="+ add modifier"><div id="modiftext' + id + '"></div></div></div>';
        html += '<input type="hidden" value=' + price + ' id="hsectiontotal' + id + '" /></div>'
        $('#menuitems').append(html);
        $("#autoload").html('');
        $("#addmenuitem").val('');
        $('#totalcount').val(tot);

        var subtotal = $('#hsubtotal').val();
        var ordiscount = $('#hordiscount').val();
        var tax = $('#htax').val();

        var newsubtotal = parseFloat(subtotal) + parseFloat(price);
        var newdiscount = parseFloat((newsubtotal * parseFloat(ordiscount)) / 100);
        var newsaletax = (parseFloat(newsubtotal) - parseFloat(newdiscount)) * (parseFloat(tax) / 100);
        var newtotal = parseFloat(newsubtotal) + parseFloat(newsaletax);

        $("#total_amount").text('$' + newtotal.toFixed(2));
        $("#tax_calculate").text('$' + newsaletax.toFixed(2));
        $("#discount_calculate").text('$' + newdiscount.toFixed(2));
        $("#subtotal").text('$' + newsubtotal.toFixed(2));

        $('#hsubtotal').val(newsubtotal.toFixed(2));
        $('#hdiscount_calculate').val(newdiscount.toFixed(2));
        $('#htax_calculate').val(newsaletax.toFixed(2));
        $('#htotal_amount').val(newtotal.toFixed(2));
    });

    $('#menuitems').on('click', '.close_icon', function () {
        var closesection = $(this).data('closeid');
        var hsectiontotal = $('#hsectiontotal' + closesection).val();
        var subtotal = $('#hsubtotal').val();
        var ordiscount = $('#hordiscount').val();
        var tax = $('#htax').val();
        var tot = $('#totalcount').val();
        tot = parseInt(tot) - 1;
        var newsubtotal = parseFloat(parseFloat(subtotal) - parseFloat(hsectiontotal));

        var newdiscount = parseFloat((newsubtotal * parseFloat(ordiscount)) / 100);
        var newsaletax = (parseFloat(newsubtotal) - parseFloat(newdiscount)) * (parseFloat(tax) / 100);
        var newtotal = parseFloat(newsubtotal) + parseFloat(newsaletax);

        $("#total_amount").text('$' + newtotal.toFixed(2));
        $("#tax_calculate").text('$' + newsaletax.toFixed(2));
        $("#discount_calculate").text('$' + newdiscount.toFixed(2));
        $("#subtotal").text('$' + newsubtotal.toFixed(2));
        $('#hsubtotal').val(newsubtotal.toFixed(2));
        $('#complete_item' + closesection).remove();
        $('#hdiscount_calculate').val(newdiscount.toFixed(2));
        $('#htax_calculate').val(newsaletax.toFixed(2));
        $('#htotal_amount').val(newtotal.toFixed(2));
        $('#totalcount').val(tot);
    });
    $('#menuitems').on('click', '.close_icon1', function () {
        var closesection = $(this).data('closeid');
        var parentid = $(this).data('parentid');
        var hsectiontotal = $('#hsectiontotal' + parentid).val();
        var hmodifierpr = $('#hmodifierpr' + closesection).val();
        var subtotal = $('#hsubtotal').val();
        var ordiscount = $('#hordiscount').val();
        var tax = $('#htax').val();
        var newhsection = parseFloat(hsectiontotal) - parseFloat(hmodifierpr);
        var newsubtotal = parseFloat(parseFloat(subtotal) - parseFloat(hmodifierpr));

        var newdiscount = parseFloat((newsubtotal * parseFloat(ordiscount)) / 100);
        var newsaletax = (parseFloat(newsubtotal) - parseFloat(newdiscount)) * (parseFloat(tax) / 100);
        var newtotal = parseFloat(newsubtotal) + parseFloat(newsaletax);
        $("#total_amount").text('$' + newtotal.toFixed(2));
        $("#tax_calculate").text('$' + newsaletax.toFixed(2));
        $("#discount_calculate").text('$' + newdiscount.toFixed(2));
        $("#subtotal").text('$' + newsubtotal.toFixed(2));
        $('#hsubtotal').val(newsubtotal.toFixed(2));
        $('#hsectiontotal' + parentid).val(newhsection.toFixed(2));
        $('#modsec' + closesection).remove();
        $('#hdiscount_calculate').val(newdiscount.toFixed(2));
        $('#htax_calculate').val(newsaletax.toFixed(2));
        $('#htotal_amount').val(newtotal.toFixed(2));
    });
    $('#menuitems').on('keypress', '.modifier', function () {
        var data = $(this).val();
        var menuitem = $(this).data('itemid');
        $.ajax({
            url: '<?php echo HTTP_PATH; ?>owner/modifierlist',
            type: 'POST',
            data: {data: data, menuitem: menuitem}
        }).done(function (response) { // c
            $("#modiftext" + menuitem).html(response);
        });
    });
    $('#menuitems').on('click', 'ul li.modif', function () {
        var price = $(this).data('price');
        var name = $(this).data('name');
        var id = $(this).data('id');
        var updated = $(this).data('updated');
        var tot = $('#totalcount').val();
        var html = '<div class="food_detail_wrap blank" id="modsec' + id + '">';
        html += '<div class="food_no"></div>  ';
        html += '<div class="food_detail">+' + name + '</div>  ';
        html += '<input type="hidden" name="order_item[' + tot + '][addon_id][]" value="' + id + '" />';
        html += '<div class="food_rate">$' + price + '</div>  ';
        html += '<a class="close_icon1" href="javascript:void(0)" data-closeid="' + id + '"><i class="fa fa-times"></i></a>';
        html += '<input type="hidden" value=' + price + ' id="hmodifierpr' + id + '" /></div>';
        $('#modifier_data' + updated).append(html);
        $("#modiftext" + updated).html('');
        $(".modifier").val('');

        var subtotal = $('#hsubtotal').val();
        var ordiscount = $('#hordiscount').val();
        var tax = $('#htax').val();
        var newsubtotal = parseFloat(subtotal) + parseFloat(price);
        var newdiscount = parseFloat((newsubtotal * parseFloat(ordiscount)) / 100);
        var newsaletax = (parseFloat(newsubtotal) - parseFloat(newdiscount)) * (parseFloat(tax) / 100);
        var newtotal = parseFloat(newsubtotal) + parseFloat(newsaletax);
        $("#total_amount").text('$' + newtotal.toFixed(2));
        $("#tax_calculate").text('$' + newsaletax.toFixed(2));
        $("#discount_calculate").text('$' + newdiscount.toFixed(2));
        $("#subtotal").text('$' + newsubtotal.toFixed(2));

        $('#hsubtotal').val(newsubtotal.toFixed(2));
        $('#hdiscount_calculate').val(newdiscount.toFixed(2));
        $('#htax_calculate').val(newsaletax.toFixed(2));
        $('#htotal_amount').val(newtotal.toFixed(2));
    });
</script>

<script>
    $("#editorder").validate({
        submitHandler: function (form) { //prevent default action 
            var form_data = $('#editorder').serialize();
            var post_url = '<?php echo HTTP_PATH . 'order/subeditorder'; ?>';
            if (this.valid()) {
                $.ajax({
                    url: post_url,
                    type: 'POST',
                    data: form_data
                }).done(function (response) { // c
                    if (response == 'success') {
                        $('.modal-backdrop').remove();
                        var data = {
                            current_dat: $('#slected_date').val(),
                        }
                        $.ajax(
                                {
                                    url: "<?php echo HTTP_PATH; ?>/nextorder",
                                    dataType: 'html',
                                    type: 'POST',
                                    data: data,
                                    success: function (result) {
                                        $("#icancel").trigger("click");
                                        $('.modal-backdrop').remove();
                                        //console.log(result);
                                        $('#menubx').html(result);
                                        //$('.all_bg_ldr').show();
                                        $.ajax(
                                                {
                                                    url: "<?php echo HTTP_PATH; ?>/scheduleorder",
                                                    dataType: 'html',
                                                    type: 'POST',
                                                    data: data,
                                                    success: function (result) {
                                                        $('#sched').html(result);
                                                    }
                                                });
                                    }
                                });
                    }
                });
            }
        }
    });
</script>