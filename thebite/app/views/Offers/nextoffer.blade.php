<?php
$i = 1;
if (!empty($records)) {
    foreach ($records as $record) {

        if ($i % 2 == 0) {
            $class = 'pull-right';
        } else {
            $class = '';
        }
        if ($i % 2 == 0) {
            
        } else {
            echo '<div class="compdiv">';
        }
        ?>
        <div class="menu_block  <?php echo $class; ?>  order_box">
            <div class="menu_top_title">
                <span class="pull-left"><?php echo $record->discount; ?>% off</span> 
                <span class="pull-right"><?php
                    if ($record->allocate == 0) {
                        echo 'Unlimited';
                    } else {
                        echo $record->allocate . ' people';
                    }
                    ?></span>
            </div>  
            <div class="address min_height"><?php
                if ($record->all_menu == 1) {
                    if($record->above_price>0){
                    echo 'All menu items with orders above $ ' . $record->above_price;
                    }
                    else
                    {
                        echo 'All menu items';
                    }
                } else {
                    if($record->above_price>0){
                        echo 'On Select Menu Items with Orders Above $ ' . $record->above_price . '<br> (';
                    }
                    else
                    {
                        echo 'On Select Menu Items <br> (';
                    }

                    $menus = explode(",", $record->item_name);
                    foreach ($menus as $menuAtr) {
                        $query = DB::table('menu_item');
                        $items = $query->where('id', "=", $menuAtr)
                                ->select('menu_item.item_name')
                                ->first();
                        if (!empty($items->item_name)) {
                            echo $items->item_name . ';';
                        }
                    }
                    echo ")";
                }
                ?></div>


            <div class="tabb tabb_width history_width big_width">
                <div class="offer_tabb">
                    <span>Offer Period:</span>
                    <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/gray_clcock.png"></i><?php echo date("g:i a", strtotime($record->start_time)) ?> - <?php echo date("g:i a", strtotime($record->end_time)) ?></a>
                </div> 

                <div class="offer_tabb">
                    <span></span>
                    <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/calendar.png"></i><?php echo date('m/d/y', strtotime($record->start_date)); ?> - <?php echo date('m/d/y', strtotime($record->end_date)); ?></a>
                </div>
            </div>

            <div class="offers_tab full_res" style="height: 67px;">

                <?php
                $offerson = explode(',', trim($record->service_visibility));
                ?>
                <span class="nwspan">Offered on:</span> <!-- Offered on: -->
                <?php if (in_array('Delivery', $offerson)) { ?>
                    <div class="offer_tabb">

                        <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/deliver_icon.png"></i>Delivery</a>

                    </div>
                <?php } ?>
                <?php if (in_array('Pickup', $offerson)) { ?>
                    <div class="offer_tabb smal_tab">

                        <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/pickup_icon.png"></i>Pickup</a>

                    </div>
                <?php } ?>
                <?php if (in_array('reservation', $offerson)) { ?>
                    <div class="offer_tabb">

                        <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/reservation_icon.png"></i>Reservations</a>

                    </div>
                <?php } ?>

            </div>

            <div class="time_calendar<?php echo $record->id; ?>" style="display:none;">
                <?php
                $qur = DB::table('offers_slot');
                $offer_slots = $qur->where('offer_id', $record->id)->select('offers_slot.*')->where('status',1)->orderBy('offers_slot.start_time', 'ASC')->get();
                $start_date = strtotime($record->start_date);
                $end_date = strtotime($record->end_date);
                $datediff = $end_date - $start_date;
                $days = round($datediff / (60 * 60 * 24));
                $color = array();
                $clrcheck = array();
                $k = 0;
                foreach ($offer_slots as $offerslot) {
                    if (!empty($offerslot->color) && $offerslot->color != '#4AD57A' && !in_array($offerslot->color, $clrcheck)) {
                        $color[$k]['offer'] = $offerslot->discount;
                        $color[$k]['color'] = $offerslot->color;
                        $clrcheck[] = $offerslot->color;
                        $k++;
                    }
                }
                ?>
                <div class="time_calendar_inner">
                    <div class="single_row">
                        <?php foreach ($offer_slots as $offerslot) { ?>
                            <?php
                            $tmck = explode(':', $offerslot->start_time);
                            if (isset($tmck[1]) && $tmck[1] == '00') {
                                ?>
                                <a  href="javascript:void(0);"  data-target="#editslotModal" data-toggle="modal" data-offerid="<?php echo $offerslot->offer_id; ?>" data-status="<?php echo $offerslot->status; ?>" data-id="<?php echo $offerslot->id; ?>"  class="halfhour offerslotclick def gren " style="<?php
                                if ($offerslot->status == 0) {
                                    echo 'background-color:#cdcdcd !important;';
                                } elseif ($offerslot->status == 1) {
                                    ?>background-color:<?php echo $offerslot->color; ?> !important<?php } ?>" title="<?php echo date('g:i A', strtotime($offerslot->start_time)); ?>"><?php echo date('g A', strtotime($offerslot->start_time)); ?>
                                    <div class="boxx">
                                        <div class="top_row"><label><?php echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php echo date('g:i A', strtotime($offerslot->end_time)); ?></label>
<!--                                            <i class="fa fa-pencil edit_slot" data-id="<?php //echo $offerslot->id; ?>" data-tex="Edit <?php //echo $offerslot->discount; ?> % OFF on Time <?php //echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php //echo date('g:i A', strtotime($offerslot->end_time)); ?>" data-toggle="modal" data-target="#editOfferSlotModal"></i>-->
                                        </div>   

                                        <div class="peop"><span><?php echo $offerslot->discount; ?>% off</span>
                                            <span class="right_txt"><?php
                                                if ($offerslot->allocate == 0) {
                                                    echo 'Unlimited';
                                                } else {
                                                    echo $offerslot->allocate . ' people';
                                                }
                                                ?></span>
                                        </div>

                                        <div class="simple_txt">
                                            
                                            <?php
                                            $queryoff = DB::table('offers');
                                                        $itemoffs = $queryoff->where(array('id'=>$offerslot->offer_id))
                                                                ->select('offers.item_name','offers.all_menu')
                                                                ->first();
                                                        $querycol = DB::table('offercolors');
                                                        $itemcols = $querycol->where(array('color'=>$offerslot->color,'offer_id'=>$offerslot->offer_id))
                                                                ->select('offercolors.item_name','offercolors.all_menu')
                                                                ->first();
                                                        if(!empty($itemcols) && !empty($offerslot->color)){
                                                            if($itemcols->all_menu == 1){
                                                         ?>
                                                        All Menu <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                        <?php
                                                            }
                                                            else
                                                            {
                                                                ?>
                                                        On Select  Items with Orders <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                        <?php
                                                            $menus = explode(",", $itemcols->item_name);
                                                            foreach ($menus as $menuAtr) {
                                                                $query = DB::table('menu_item');
                                                                $items = $query->where('id', "=", $menuAtr)
                                                                        ->select('menu_item.item_name')
                                                                        ->first();
                                                                if (!empty($items->item_name)) {
                                                                    echo $items->item_name . ';';
                                                                }
                                                            }
                                                            }
                                                        }
                                                        else
                                                        {
                                            if ($itemoffs->all_menu == 1) {
                                                 ?>
                                                All Menu <?php if($offerslot->above_price>0){ echo '$'.$offerslot->above_price; } ?> <br>
                                                <?php
//                                                $menus = explode(",", $offerslot->item_name);
//                                                foreach ($menus as $menuAtr) {
//                                                    $query = DB::table('menu_item');
//                                                    $items = $query->where('id', "=", $menuAtr)
//                                                            ->select('menu_item.item_name')
//                                                            ->first();
//                                                    if (!empty($items->item_name)) {
//                                                        echo $items->item_name . ';';
//                                                    }
//                                                }
                                            } else {
                                                ?>
                                                        On Select  Items with Orders <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                        <?php
                                                $menus = explode(",", $offerslot->item_name);
                                                foreach ($menus as $menuAtr) {
                                                    $query = DB::table('menu_item');
                                                    $items = $query->where('id', "=", $menuAtr)
                                                            ->select('menu_item.item_name')
                                                            ->first();
                                                    if (!empty($items->item_name)) {
                                                        echo $items->item_name . ';';
                                                    }
                                                }
                                            }
                                                        }
                                            ?>
                                        </div>


                                    </div>
                                </a>
                            <?php } else { ?>
                                <a  href="javascript:void(0);" data-target="#editslotModal" data-toggle="modal" data-status="<?php echo $offerslot->status; ?>" data-offerid="<?php echo $offerslot->offer_id; ?>"  data-id="<?php echo $offerslot->id; ?>"  class="offerslotclick def gren halfhour" style="<?php
                                if ($offerslot->status == 0) {
                                    echo 'background-color:#cdcdcd !important;';
                                } elseif ($offerslot->status == 1) {
                                    ?>background-color:<?php echo $offerslot->color; ?> !important<?php } ?>" title="<?php echo date('g:i A', strtotime($offerslot->start_time)); ?>">
                                    <div class="boxx">
                                        <div class="top_row"><label><?php echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php echo date('g:i A', strtotime($offerslot->end_time)); ?></label>
                                            <i class="fa fa-pencil edit_slot" data-id="<?php echo $offerslot->id; ?>" data-tex="Edit <?php echo $offerslot->discount; ?> % OFF on Time <?php echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php echo date('g:i A', strtotime($offerslot->end_time)); ?>" data-toggle="modal" data-target="#editOfferSlotModal"></i>
                                        </div>   

                                        <div class="peop"><span><?php echo $offerslot->discount; ?>% off</span>
                                            <span class="right_txt"><?php
                                                if ($offerslot->allocate == 0) {
                                                    echo 'Unlimited';
                                                } else {
                                                    echo $offerslot->allocate . ' people';
                                                }
                                                ?></span>
                                        </div>

                                        <div class="simple_txt">
                                            
                                            <?php
                                            $queryoff = DB::table('offers');
                                                        $itemoffs = $queryoff->where(array('id'=>$offerslot->offer_id))
                                                                ->select('offers.item_name','offers.all_menu')
                                                                ->first();
                                                        $querycol = DB::table('offercolors');
                                                        $itemcols = $querycol->where(array('color'=>$offerslot->color,'offer_id'=>$offerslot->offer_id))
                                                                ->select('offercolors.item_name','offercolors.all_menu')
                                                                ->first();
                                                        if(!empty($itemcols) && !empty($offerslot->color)){
                                                            if($itemcols->all_menu == 1){
                                                         ?>
                                                        All Menu <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                        <?php
                                                            }
                                                            else
                                                            {
                                                                ?>
                                                        On Select  Items with Orders <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                        <?php
                                                            $menus = explode(",", $itemcols->item_name);
                                                            foreach ($menus as $menuAtr) {
                                                                $query = DB::table('menu_item');
                                                                $items = $query->where('id', "=", $menuAtr)
                                                                        ->select('menu_item.item_name')
                                                                        ->first();
                                                                if (!empty($items->item_name)) {
                                                                    echo $items->item_name . ';';
                                                                }
                                                            }
                                                            }
                                                        }
                                                        else
                                                        {
                                            if ($itemoffs->all_menu == 1) {
                                                ?>
                                                All Menu <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                <?php
//                                                $menus = explode(",", $offerslot->item_name);
//                                                foreach ($menus as $menuAtr) {
//                                                    $query = DB::table('menu_item');
//                                                    $items = $query->where('id', "=", $menuAtr)
//                                                            ->select('menu_item.item_name')
//                                                            ->first();
//                                                    if (!empty($items->item_name)) {
//                                                        echo $items->item_name . ';';
//                                                    }
//                                                }
                                            } else {
                                                ?>
                                                On Select  Items with Orders <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                <?php
                                                $menus = explode(",", $offerslot->item_name);
                                                foreach ($menus as $menuAtr) {
                                                    $query = DB::table('menu_item');
                                                    $items = $query->where('id', "=", $menuAtr)
                                                            ->select('menu_item.item_name')
                                                            ->first();
                                                    if (!empty($items->item_name)) {
                                                        echo $items->item_name . ';';
                                                    }
                                                }
                                            }
                                                        }
                                            ?>
                                        </div>


                                    </div>
                                </a>
                            <?php } ?>
                        <?php } ?>

                    </div>
                </div> 

                <div class="seat">
                    <!--                    <div class="seat_book grenn">
                                            <span></span>   
                                            <div class="textt">Current Offer</div>
                    
                                        </div> -->

                    <div class="seat_book grenn">
                        <span></span>   
                        <div class="textt"><?php echo $record->discount; ?>% Off</div>
                    </div> 
                    <?php foreach ($color as $colorAtr) { ?>
                        <div class="seat_book grenn" >
                            <span style="background:<?php echo $colorAtr['color']; ?>"></span>   
                            <div class="textt"><?php echo $colorAtr['offer']; ?>% Off</div>
                        </div> 
                    <?php } ?>
                    <div class="seat_book defaut">
                        <span></span>   
                        <div class="textt">Disabled</div>

                    </div> 

                </div>
            </div>

            <!--data-toggle="modal" data-target="#editOfferSlotModal"-->
            <div id="editcolorSlotModal<?php echo $record->id; ?>" class="modal fade editscreen" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body" >
                            <div class="pop_form" style="text-align: center;">
                                <div class="center_txt">Edit <?php echo $record->discount; ?> % OFF on Time <?php echo date('g:i A', strtotime($record->start_time)); ?> - <?php echo date('g:i A', strtotime($record->end_time)); ?></div>
                                <span data-color="#369F5C" data-offer="<?php echo $record->discount; ?>" data-obj="" class="changecolor" style="background:#369F5C; "></span><div class="textt" style="margin-right: 20px;"><?php echo $record->discount; ?>% Off</div>
                                <?php foreach ($color as $colorAtr) { ?>
                                    <span data-color="<?php echo $colorAtr['color']; ?>" data-offer="<?php echo $colorAtr['offer']; ?>" data-obj="" class="changecolor" style="background:<?php echo $colorAtr['color']; ?>"></span>   
                                    <div class="textt" style="margin-right: 20px;"><?php echo $colorAtr['offer']; ?>% Off</div>
                                <?php } ?>
                                <input type="hidden" name="tmslot" id="tmslot" value="">
                                <input type="hidden" name="upslcolr" id="upslcolr" value="">

                            </div>
                            <span class="nwclick">&nbsp;</span>
                            <div class="pop_field">
                                <label>Slot Color</label> 
                                <div class="two_wrap">
                                    {{ Form::text('color', '#369F5C', array('class' => '','id'=>'colorslott'.$record->id,'placeholder'=>"")) }}
                                </div>
                            </div>

                            <div class="pop_field">
                                <label>offer</label> 
                                <div class="two_wrap">
                                    <div class="input_filed">{{  Form::text('offer',$record->discount,  array('class' => 'required form-control positiveNumber offervalid number ','id'=>"offerchange"))}}
                                        <span class="sett" style="float: right;">%</span>
                                    </div>

                                    <span class="note">Offers should be 10% and above in order to be active.</span>

                                </div>
                            </div>

                            <div class="pop_btn full_btmn">

                                <input type="submit" class="same_btn" id="offchange" data-obj="" value="Update">


                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="simple_btn ">
                <span id="onoffstatus<?php echo $record->id; ?>">
                    <?php
                    if ($record->status == 1) {
                        ?>
                        <a class="green_btn onoff" data-detail="offline" data-id="<?php echo $record->id; ?>" href="javascript:void(0)">online</a>
                    <?php } elseif ($record->status == 0) { ?>
                        <a class="simple_btn_menu onoff" data-detail="online" data-id="<?php echo $record->id; ?>" href="javascript:void(0)">offline</a>
                    <?php } ?>

                </span>

                <a class="simple_btn_menu edit_offer" data-slug="<?php echo $record->slug; ?>" data-id="<?php echo $record->id; ?>" href="javascript:void(0)" data-toggle="modal" data-target="#editOfferModal">edit</a>
                <a class="simple_btn_menu delete_offer" style="width:66px;" data-slug="<?php echo $record->slug; ?>" data-id="<?php echo $record->id; ?>" href="javascript:void(0);" ><i class="fa fa-trash"></i></a>
                <div class="drop_arrow" data-dvid="<?php echo $record->id; ?>"><a id="more" href="javascript:void(0)"><span id="<? ?>" class="mr glyphicon glyphicon-chevron-down"></span></a></div>
            </div>
            <div>   


            </div>
        </div>  
        <?php
        if ($i % 2 == 0) {
            echo '</div>';
        } elseif ($i == count($records)) {
            echo '</div>';
        }
        $i++;
    }
} else {
    ?>
    <div class="no_record">
        <div>No Record Found on that date.</div>
    </div>
<?php } ?>

<script>
    $("#colorslott").spectrum({
        preferredFormat: "hex",
        // color: "#369F5C"
    });

    $.validator.addMethod("offervalid", function (value, element) {
        // allow any non-whitespace characters as the host part

        return this.optional(element) || value >= 10;
    }, 'Offers should be 10% and above in order to be active.');
</script>

