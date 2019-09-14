@extends('layouts.default')
@section('content')
{{ HTML::style('public/css/front2/slick.css') }}
{{ HTML::style('public/css/front2/slick-theme.css') }}
{{ HTML::style('public/css/front2/jquery.range.css') }}
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
<?php
$days_in_month = date('t');
$current_month = date('m');
$current_short_month = date('M');
$current_date = date('d');
$current_year = date('Y');
$initial_slide = 0;
//echo '<pre>';
//print_r($records);
//exit;
?>

<div id="right_content">
    <div class="right_content">
        <div class="content_nav">
            <section class="center slider" id="sldr" style="display:none;">
                <?php
                for ($i = 1; $i <= $days_in_month; $i++) {
                    if ($i == $current_date) {
                        $initial_slide = $i - 1;
                    }
//                    str_pad($i, 2, "0", STR_PAD_LEFT)
                    ?>
                    <div data-date="<?php echo $i; ?>" data-month="<?php echo $current_month; ?>" data-year="<?php echo $current_year; ?>">
                        <span class="digit"><?php echo $i; ?></span>
                        <span class="day"><?php echo date('D', strtotime($current_year . '-' . $current_month . '-' . $i)); ?></span>
                    </div>
                    <?php
                }
                ?>
            </section>
        </div>
        <input type="hidden" id="slected_date" value="<?php echo $current_year . '-' . $current_month . '-' . $current_date ?>" >

        <div class="search_bar calendar_searchbar">
            <div class="calendarfield">
                <i><img src="{{ URL::asset('public/img/front') }}/calendar_xs.png"></i>  
                <input type="text" name="daterange" id="daterange" placeholder="<?php echo $current_date . ' ' . $current_short_month; ?> - <?php echo $current_date . ' ' . $current_short_month; ?>">
                <input type="hidden" name="altrange" id="altrange" value="<?php echo $current_year . '/' . $current_month . '/' . $current_date . ' - ' . $current_year . '/' . $current_month . '/' . $current_date; ?>"/>
            </div>
            <div class="add-offer"><a data-toggle="modal" data-target="#myModal">+  Add Offer</a></div>   
        </div>
        <div class="menu_box" id="mnbx">
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
                                    echo 'All menu items with orders above $ ' . $record->above_price ;
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
                            <span class="nwspan">Offered on:</span><!-- Offered on: -->
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
                            //for($j=0; $j<=$days;$j++){
                            ?>
                            <div class="time_calendar_inner">
                                <div class="single_row">
                                    <?php foreach ($offer_slots as $offerslot) { ?>
                                        <?php //echo $offerslot->offer_id;
                                        $tmck = explode(':', $offerslot->start_time);
                                        if ($tmck[1] == '00') {
                                            ?>
                                            <a  href="javascript:void(0);" data-status="<?php echo $offerslot->status; ?>"  data-target="#editslotModal" data-toggle="modal" data-id="<?php echo $offerslot->id; ?>" data-offerid="<?php echo $offerslot->offer_id; ?>"  class="halfhour offerslotclick def gren" style="<?php
                                            if ($offerslot->status == 0) {
                                                echo 'background-color:#cdcdcd !important;';
                                            } elseif ($offerslot->status == 1) {
                                                ?>background-color:<?php echo $offerslot->color; ?> !important<?php } ?>"  title="<?php echo date('g:i A', strtotime($offerslot->start_time)); ?>"><?php echo date('g A', strtotime($offerslot->start_time)); ?>
                                                <div class="boxx"> 
                                                    <div class="top_row"><label><?php echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php echo date('g:i A', strtotime($offerslot->end_time)); ?></label>
<!--                                                        <i class="fa fa-pencil edit_slot" data-id="<?php //echo $offerslot->id; ?>" data-tex="Edit <?php //echo $offerslot->discount; ?> % OFF on Time <?php //echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php //echo date('g:i A', strtotime($offerslot->end_time)); ?>" data-toggle="modal" data-target="#editOfferSlotModal"></i>-->
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
                                                        //echo '<pre>';print_r($offerslot->id);
                                                        $queryoff = DB::table('offers');
                                                        $itemoffs = $queryoff->where(array('id'=>$offerslot->offer_id))
                                                                ->select('offers.item_name','offers.all_menu')
                                                                ->first();
                                                        $querycol = DB::table('offercolors');
                                                        $itemcols = $querycol->where(array('color'=>$offerslot->color,'offer_id'=>$offerslot->offer_id))
                                                                ->select('offercolors.item_name','offercolors.all_menu')
                                                                ->first();
                                                        //echo '<pre>';print_r($itemoffs);
                                                        if(!empty($itemcols) && !empty($offerslot->color)){
                                                            
                                                            if($itemcols->all_menu == 1){
                                                         ?>
                                                        All Menu <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?> <br>
                                                        <?php
                                                            }
                                                            else
                                                            {
                                                                ?>
                                                        On Select Items with Orders <?php if($offerslot->above_price>0){ echo "Above $$offerslot->above_price"; } ?><br>
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
                                                        All Menu $ <?php echo $offerslot->above_price; ?> <br>
                                                        <?php
//                                                            $menus = explode(",", $offerslot->item_name);
//                                                            foreach ($menus as $menuAtr) {
//                                                                $query = DB::table('menu_item');
//                                                                $items = $query->where('id', "=", $menuAtr)
//                                                                        ->select('menu_item.item_name')
//                                                                        ->first();
//                                                                if (!empty($items->item_name)) {
//                                                                    echo $items->item_name . ';';
//                                                                }
//                                                            }
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
                                            <a  href="javascript:void(0);" data-status="<?php echo $offerslot->status; ?>" data-target="#editslotModal" data-toggle="modal" data-id="<?php echo $offerslot->id; ?>"  data-offerid="<?php echo $offerslot->offer_id; ?>" data-offerid="<?php echo $offerslot->offer_id; ?>" class="offerslotclick def gren halfhour" style="<?php
                                            if ($offerslot->status == 0) {
                                                echo 'background-color:#cdcdcd !important;';
                                            } elseif ($offerslot->status == 1) {
                                                ?>background-color:<?php echo $offerslot->color; ?> !important<?php } ?>" title="<?php echo date('g:i A', strtotime($offerslot->start_time)); ?>">
                                                <div class="boxx"> 
                                                    <div class="top_row"><label><?php echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php echo date('g:i A', strtotime($offerslot->end_time)); ?></label>
<!--                                                        <i class="fa fa-pencil edit_slot" data-id="<?php //echo $offerslot->id; ?>" data-tex="Edit <?php //echo $offerslot->discount; ?> % OFF on Time <?php //echo date('g:i A', strtotime($offerslot->start_time)); ?> - <?php //echo date('g:i A', strtotime($offerslot->end_time)); ?>" data-toggle="modal" data-target="#editOfferSlotModal"></i>-->
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
                                                            if($itemcols->all_menu==1){
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
                                                        All Menu $ <?php echo $offerslot->above_price; ?> <br>
                                                        <?php
//                                                            $menus = explode(",", $offerslot->item_name);
//                                                            foreach ($menus as $menuAtr) {
//                                                                $query = DB::table('menu_item');
//                                                                $items = $query->where('id', "=", $menuAtr)
//                                                                        ->select('menu_item.item_name')
//                                                                        ->first();
//                                                                if (!empty($items->item_name)) {
//                                                                    echo $items->item_name . ';';
//                                                                }
//                                                            }
                                                        } else {
                                                            ?>
                                                        On Select  Items with Orders Above $ <?php echo $offerslot->above_price; ?> <br>
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

                        <div class="simple_btn ">
                            <span id="onoffstatus<?php echo $record->id; ?>">
                                <?php if ($record->status == 1) { ?>
                                    <a class="green_btn onoff" data-detail="offline" data-id="<?php echo $record->id; ?>" href="javascript:void(0)">online</a>
                                <?php } elseif ($record->status == 0) { ?>
                                    <a class="simple_btn_menu onoff" data-detail="online" data-id="<?php echo $record->id; ?>" href="javascript:void(0)">offline</a>
                                <?php } ?>
                            </span>
                            <a class="simple_btn_menu edit_offer" data-slug="<?php echo $record->slug; ?>" data-id="<?php echo $record->id; ?>" href="javascript:void(0);" data-toggle="modal" data-target="#editOfferModal">edit</a>
                            <a class="simple_btn_menu delete_offer" style="width:66px;" data-slug="<?php echo $record->slug; ?>" data-id="<?php echo $record->id; ?>" href="javascript:void(0);" ><i class="fa fa-trash"></i></a>
                            <div class="drop_arrow" data-dvid="<?php echo $record->id; ?>"><a id="more" href="javascript:void(0);"><span id="<? ?>" class="mr glyphicon glyphicon-chevron-down"></span></a></div>
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
                <?php
            }
            ?>
        </div>
    </div>
</div>


<div id="editOfferModal" class="modal fade editscreen" role="dialog">

</div>

<div id="editslotModal" class="modal fade editscreen" role="dialog">

</div>


<div id="myModal" class="modal fade editscreen" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                {{ Form::open(array('url' => '', 'method' => 'post', 'id' => 'offeradd', 'files' => true, 'autocomplete'=>'off','class'=>"cmxform form-horizontal tasi-form form")) }}
                <div class="pop_form">
                    <div class="center_txt">Add Offer</div>

                    <div class="pop_field">
                        <label>offer(%)</label> 
                        <div class="two_wrap">
                            <div class="input_filed">{{  Form::text('offer', Input::old("offer"),  array('autocomplete'=>'off','class' => 'required form-control positiveNumber number offervalid','id'=>"offer"))}}
                                <span class="sett" style="float: right;">%</span>
                            </div>

                        </div>
                        <span class="note">Offers should be 10% and above in order to be active.</span>
                    </div>
                    <div class="pop_field" style="height: 97px;">
                        <label>Menu Item</label> 
                        <div class="two_wrap">
                            <div class="input_filed selcetdrop drop_down">
                                <?php
                                $records = DB::table('menu_item')->select('menu_item.*')
                                                ->where('menu_item.user_id', Session::get("user_id"))->orderBy('menu_item.id', 'desc')->lists('item_name', 'id');
//dd(DB::getQueryLog());
                                $cuisine_array = array();
                                $records = $cuisine_array + $records;
//echo'<pre>'; print_r($records);
                                ?>
                                {{ Form::select('menu_items[]', $records, Input::old("menu_items"), array('multiple'=>'multiple', 'onclick'=>"$('#seldt').removeClass('active')",'id'=>'menus','class' => 'required','style'=>"height: 97px;")) }}

                            </div></div>
                        <div class="small_box" id="seldt"><a class="menu" id="select_all" href="javascript:void('0')">All Menu</a></div>
                        <input type="hidden" name="menu" value="" id="menu">
                    </div>
                    <div class="pop_field">
                        <label>Above Price</label> 
                        <div class="two_wrap">
                            <div class="input_filed">{{  Form::text('above_price', Input::old("above_price"),  array('class' => 'number','autocomplete'=>'off','id'=>"above_price"))}}<span class="sett" style="float: right;">$</span></div></div>
                    </div>

                    <div class="pop_field">
                        <label>Date</label> 
                        <div class="two_wrap">
                            <div class="input_filed half half_input calendar">{{ Form::text('startdate', '', array('class' => 'required','id'=>'startdate','placeholder'=>"","data-date-format"=>"mm/dd")) }}<img onclick='$("#startdate").focus();' src="{{ URL::asset('public/img/front') }}/calendar_xs.png"></div>
                            <div class="input_filed half half_input calendar">{{ Form::text('enddate', '', array('class' => 'required','id'=>'enddate','placeholder'=>"","data-date-format"=>"mm/dd")) }}<img onclick='$("#enddate").focus();' src="{{ URL::asset('public/img/front') }}/calendar_xs.png"></div></div>
                    </div>
                    <div class="pop_field">
                        <label>Time</label> 
                        <div class="slider_wrap">
                            <span id="time_show">
                                <div class="start_time">12:00 AM</div>
                                <div class="end_time">12:00 AM</div>
                            </span>
                            <div class="demo-output">
                                <input class="range-slider" name="timegap" id="range_tm" type="hidden" value="0,23"/>
                            </div>   
                        </div>
                        <div class="small_box"><a class="days" id="all_days" href="javascript:void('0')">All Day</a></div>
                        <input type="hidden" name="days" value="" id="days">
                    </div>


                    <div class="pop_field">
                        <label>No. of Coupons</label> 
                        <div class="two_wrap">
                            <div class="input_filed">{{ Form::text('coupon', '', array('minlength'=>'1','maxlength'=>'2','class' => 'number','id'=>'coupons','autocomplete'=>'off','placeholder'=>"")) }}</div>
                        </div>
                        <div class="small_box"><a data-title="unlimited" class="unlimited" href="javascript:void('0')">Unlimited</a></div>
                        <input type="hidden" name="unlimited" value="" id="unlimited">
                    </div>



                    <div class="pop_field three_box">
                        <label>Offered on</label> 
                        <?php
                        $offerded = explode(',', $userData->service_offered);
                        ?>
                        <?php if (in_array('Delivery', $offerded)) { ?>
                            <div class="input_filed thirhalf offerd_on"> <a data-title="delivery" class="options" href="javascript:void('0')">Delivery</a></div>
                        <?php } ?>
                        <?php if (in_array('Table reservations', $offerded)) { ?>
                            <div class="input_filed thirhalf margin_left offerd_on"><a data-title="reservation" class="options" href="javascript:void('0')">reservation</a></div>
                        <?php } ?>
                        <?php if (in_array('Pickup', $offerded)) { ?>
                            <div class="input_filed thirhalf margin_left offerd_on"><a data-title="pickup" class="options" href="javascript:void('0')">Pickup</a></div>
                        <?php } ?>
                        <input type="hidden" id="service_visibility" name="service_visibility[]">
                    </div>
                    <div class="pop_field">
                        <label></label> 
                        <div class="two_wrap">
                            <div class="input_filed textarea">
                                {{  Form::textarea('note', Input::old("note"),  array('class' => 'form-control','id'=>"note" ,"maxlength"=>"250"))}}
                                Disclaimer 
                            </div>
                        </div>
                    </div>

                    <div class="pop_btn full_btmn">
                        <input type="submit" class="same_btn" id="submit_btn" onclick="var offerd_on = $('#service_visibility').val();
                                if (offerd_on == '') {
                                    $('.offerd_on').css('border', '1px solid red')
                                }" value="Confirm">

                        <a class="same_btn default_btn" id="alctl" href="#" data-dismiss="modal">Cancel</a>    

                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script>
    $('#select_all').click(function () {
        $(this).addClass('active');
        $('#menus option').prop('selected', true);
    });
</script>
<script>
    $('#all_days').click(function () { 
        $('#range_tm').val('0,24');
        var data = {
            alltime: '0,24'
        }
        $.ajax({
            url: '<?php echo HTTP_PATH . 'offer/updatetime'; ?>',
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function (data, textStatus, XMLHttpRequest)
            {
                if (data.valid)
                {
                    $('#loading-image').hide();
                    $('#time_show').html(data.tmup);

                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                $('#loading-image').hide();
                // Message
                alert('An unexpected error occured, please try again');

            }
        });
    });
</script>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src="<?php echo HTTP_PATH; ?>/public/js/front2/slick.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="{{ URL::asset('public/js/front2/sweetalert.min.js') }}"></script>

<script type="text/javascript">
    $(function () {
        $('#daterange').daterangepicker(
                {
                    locale: {

                        format: 'DD MMM',
                        altFormat: "YYYY-MM-DD",
                        altField: "#altrange"
                    },

                }, function (start, end, label) {
//            $("#altrange").val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
//            var selected_date = $('#altrange').val();
//            var data = {
//                current_dat: selected_date,
//            }
//            $.ajax({
//                url: '<?php echo HTTP_PATH . 'searchoffer'; ?>',
//                type: 'POST',
//                data: data,
//                dataType: 'html',
//                success: function (result) {
//                    $('#mnbx').html(result);
//                }
//            });
        }
            
        );
    
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $("#altrange").val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            var selected_date = $('#altrange').val();
            var data = {
                current_dat: selected_date,
            }
            $.ajax({
                url: '<?php echo HTTP_PATH . 'searchoffer'; ?>',
                type: 'POST',
                data: data,
                dataType: 'html',
                success: function (result) {
                    $('#mnbx').html(result);
                }
            });
      });
    });
    $(function () {
        $("#startdate").datepicker({
            changeMonth: true, altFormat: "mm/dd",
            numberOfMonths: 1, minDate: 0,
            onClose: function (selectedDate) {
                $("#enddate").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#enddate").datepicker({
            minDate: 0,
            changeMonth: true, altFormat: "mm/dd",
            numberOfMonths: 1,
            onClose: function (selectedDate) {
                $("#startdate").datepicker("option", "maxDate", selectedDate);
            }
        });
    });</script>
<script src="<?php echo HTTP_PATH; ?>public/js/front2/jquery.range.js"></script>
<script type="text/javascript">
    $(document).on('ready', function () {
        $('#sldr').show();
        $(".center").slick({
            dots: true,
            infinite: true,
            centerMode: true,
            slidesToShow: 7,
            focusOnSelect: true,
            initialSlide: <?php echo $initial_slide; ?>,
            slidesToScroll: 3,
            responsive: [
                {
                    breakpoint: 1025,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '45px',
                        slidesToShow: 6
                    }
                },
                {
                    breakpoint: 991,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: 5
                    }
                },
                {
                    breakpoint: 670,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '40px',
                        slidesToShow: 5
                    }
                },
                {
                    breakpoint: 580,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '20px',
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        arrows: true,
                        centerMode: true,
                        centerPadding: '0px',
                        slidesToShow: 3
                    }
                }


                // You can unslick at a given breakpoint now by adding:
                // settings: "unslick"
                // instead of a settings object
            ]
        });
    });
    $('.center').on('afterChange', function (event, slick, currentSlide, nextSlide) {

        var datamonth = $(slick.$slides[currentSlide]).data('month');
        var datadate = $(slick.$slides[currentSlide]).data('date');
        var datayear = $(slick.$slides[currentSlide]).data('year');
        $('#slected_date').val(datayear + '-' + datamonth + '-' + datadate);

        $('.all_bg_ldr').hide();
        var data = {
            current_dat: datayear + '-' + datamonth + '-' + datadate,
        }
        $.ajax(
                {
                    url: "<?php echo HTTP_PATH; ?>/nextoffer",
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function (result) {
                        //console.log(result);
                        if (result.trim() == 'errorlogin') {
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        } else
                        {
                            $('#mnbx').html(result);
                        }
                        //$('.all_bg_ldr').show();
                    }
                });

    });</script>



<script type="text/javascript">
    $(document).ready(function () {
        $(".slide-toggle").click(function () {
            $(".slide-toggle").toggleClass('show');
            $(".box").animate({
                width: "toggle"
            });
        });
    });
</script>

<script>
    $(document).ready(function () {

        $('#mnbx').on('click', '.drop_arrow', function () {
            if ($('a span.mr').hasClass('glyphicon-chevron-down'))
            {
                $(this).find('#more').html('<span id="first" class="mr glyphicon glyphicon-chevron-up"></span>');
            } else
            {
                $(this).find('#more').html('<span id="first" class="mr glyphicon glyphicon-chevron-down"></span>');
            }
        });

        $('#mnbx').on('click', '.drop_arrow', function () {
            var dvid = $(this).data('dvid');
            $(".time_calendar" + dvid).slideToggle("menuicon");
        });
    });</script>
<script>
    $('.range-slider').jRange({
        from: 0,
        to: 24,
        step: 1,
        scale: [12, 2, 4, 6, 8, 10, 12, 2, 4, 6, 8, 10, 12],
        format: '%s',
        width: 320,
        showLabels: true,
        isRange: true,
        ondragend: function (value, data) {
            var data = {
                alltime: value
            }
            $.ajax({
                url: '<?php echo HTTP_PATH . 'offer/updatetime'; ?>',
                dataType: 'json',
                type: 'POST',
                data: data,
                success: function (data, textStatus, XMLHttpRequest)
                {
                    if (data.valid)
                    {
                        $('#loading-image').hide();
                        $('#time_show').html(data.tmup);

                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    $('#loading-image').hide();
                    // Message
                    alert('An unexpected error occured, please try again');

                }
            });
        }
    });
</script>
<script>
    $('.offerd_on').on('click', function () {
        // alert('hi');
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            var service_vis = $('#service_visibility').val();
            var sel = $(this).text();
            var result = removeValue(service_vis, sel, ",");
            $(this).css('border', '');
            $('#service_visibility').val(result);
        } else
        {
            $(this).addClass('active');
            var service_vis = $('#service_visibility').val();
            var sel = $(this).text();
            if (service_vis == '') {
                service_vis = sel;
            } else
            {
                service_vis = service_vis + ',' + sel;
            }
            $('#service_visibility').val(service_vis);
        }
    });


    var removeValue = function (list, value, separator) {
        separator = separator || ",";
        var values = list.split(separator);
        for (var i = 0; i < values.length; i++) {
            if (values[i] == value) {
                values.splice(i, 1);
                return values.join(separator);
            }
        }
        return list;
    }

</script>

<script>
    $('.unlimited').on('click', function () {
        if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
            $('#unlimited').val('0');
            $('#coupons').val('');
        } else
        {
            $(this).parent().addClass('active');
            $('#unlimited').val('1');
            $('#coupons').val('0');
        }
    });
</script>
<script>
    $('.days').on('click', function () {
        if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
            $('#days').val('0');

        } else
        {
            $(this).parent().addClass('active');
            $('#days').val('1');

        }
    });
</script>
<script>
    $('.menu').on('click', function () {
        if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
            $('#menu').val('0');
        } else
        {
            $(this).parent().addClass('active');
            $('#menu').val('1');
        }
    });
</script>


<script>
    $.validator.addMethod('positiveNumber',
            function (value) {
                return Number(value) >= 0 & Number(value) <= 100;
            }, 'Enter a positive number.');

    $.validator.addMethod("offervalid", function (value, element) {
//alert();
        // allow any non-whitespace characters as the host part
        return this.optional(element) || value >= 10;
    }, 'Offers should be 10% and above in order to be active.');


    $("#offeradd").validate({
        submitHandler: function (form) {

            this.checkForm();

            if (this.valid()) { // checks form for validity
                var offerd_on = $('#service_visibility').val();
                if (offerd_on == '') {
                    swal("Error", "Please Select Offered On it is Required!");
                } else
                {
                    var form_data = $(form).serialize();
                    var post_url = '<?php echo HTTP_PATH . 'offer/addoffer'; ?>';
                    $.ajax({
                        url: post_url,
                        type: 'POST',
                        data: form_data
                    }).done(function (response) {
                        var suc = JSON.parse(response.trim());

                        if (suc.message == 'success') {
                            document.getElementById("offeradd").reset();
                            var selected_date = $('#slected_date').val();
                            var data = {
                                current_dat: selected_date,
                            }
                            $.ajax({
                                url: '<?php echo HTTP_PATH . 'nextoffer'; ?>',
                                type: 'POST',
                                data: data,
                                dataType: 'html',
                                success: function (result) {
                                    //console.log(result);
                                    $('#mnbx').html(result);
                                    //$('.all_bg_ldr').show();
                                }
                            });
                            $('#alctl').trigger('click');
                        } else if (suc.message == 'alreadyadded') {
                            swal("Offer already added. please select other time slot. Period :- " + suc.period);
                        }
                    });
                    return false;
                }
            } else {


                return false;
            }
        }
    });

    $('#mnbx').on('click', '.onoff', function () {
        var confrm = '';
        var id = $(this).data('id');
        var onoroff = $(this).data('detail');


        var data = {
            id: id,
            status: onoroff
        }
        $.ajax({
            url: '<?php echo HTTP_PATH . '/offer/offerstatus'; ?>',
            dataType: 'html',
            type: 'POST',
            data: data,
            success: function (result) {
                $('#onoffstatus' + id).html(result);
            }
        });


    });

</script>

<script>
    $(document).on('ready', function () {
        $("#mnbx").on('click', '.edit_offer', function () {
            var upid = $(this).data('id');
            var upslug = $(this).data('slug');
            var data = {
                offer_id: upid,
                offer_slug: upslug
            }
            $.ajax({
                url: "<?php echo HTTP_PATH; ?>offer/editofferpage",
                dataType: 'html',
                type: 'POST',
                cache: false,
                data: data,
                success: function (result) {
                    $('#editOfferModal').html(result);
                }
            });
        });

        $("#mnbx").on('click', '.delete_offer', function () {

            swal({
                title: "Delete",
                text: "Are you sure to delete this Offer?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                    .then((willDelete) => {
                        if (willDelete) {

                            var upid = $(this).data('id');
                            var upslug = $(this).data('slug');
                            var data = {
                                offer_id: upid,
                                offer_slug: upslug
                            }
                            $.ajax({
                                url: "<?php echo HTTP_PATH; ?>offer/deleteoffer",
                                dataType: 'html',
                                type: 'POST',
                                data: data,
                                success: function (result) {
                                    var res = result.trim();
                                    if (res == 'success') {
                                        var selected_date = $('#slected_date').val();
                                        var data = {
                                            current_dat: selected_date,
                                        }
                                        $.ajax({
                                            url: '<?php echo HTTP_PATH . 'nextoffer'; ?>',
                                            type: 'POST',
                                            data: data,
                                            dataType: 'html',
                                            success: function (result) {
                                                //console.log(result);
                                                $('#mnbx').html(result);
                                                //$('.all_bg_ldr').show();
                                            }
                                        });
                                    }

                                }
                            });
                        }
                    });
        });

        $('#mnbx').on('click', '.offerslotclick', function (e) {
            var upid = $(this).data('id');
            var data = {
                slot_id: upid
            };

            var that = $(this);

//            console.log(that);

            $.ajax({
                url: "<?php echo HTTP_PATH; ?>offer/sloteditpage",
                dataType: 'html',
                type: 'POST',
                data: data,
                success: function (result) {
                    $('#editslotModal').html(result);
                    $("#editslotModal" + " .changecolor").data('obj', that);
                    $("#editslotModal" + " #offchange").data('obj', that);
                }
            });
        });

        $(document).on('click', '.changecolor', function () {
            var color = $(this).data('color');
            var upid = $(this).data('slot');
            var offer = $(this).data('offer');
            var that = $(this).data('obj');

            console.log(that);

            var data = {
                slot_id: upid,
                color: color,
                offer: offer,
                same: 0
            }

//            console.log(data);
            $.ajax({
                url: "<?php echo HTTP_PATH; ?>offer/updateofferslot",
                dataType: 'html',
                type: 'POST',
                data: data,
                success: function (result) {
                    if (result.trim() != 'notchange') {
                        $(that).attr('style', 'background-color:' + result.trim() + ' !important');
                        $('#editOfferSlotModal').modal('hide');
                        $('#editslotModal').modal('hide');
                        $(that).find('div.peop span:first-child').text(offer + '% off');
                        var selected_date = $('#slected_date').val();
                        var data = {
                            current_dat: selected_date,
                        }
                        $.ajax({
                            url: '<?php echo HTTP_PATH . 'nextoffer'; ?>',
                            type: 'POST',
                            data: data,
                            dataType: 'html',
                            success: function (result) {
                                //console.log(result);
                                $('#mnbx').html(result);
                                //$('.all_bg_ldr').show();
                            }
                        });
                    } else
                    {
                        alert('Offer color or percentage already added!');
                    }
                }
            });
        });

        $('option').mousedown(function (e) {
            e.preventDefault();
            var originalScrollTop = $(this).parent().scrollTop();
            console.log(originalScrollTop);
            $(this).prop('selected', $(this).prop('selected') ? false : true);
            var self = this;
            $(this).parent().focus();
            setTimeout(function () {
                $(self).parent().scrollTop(originalScrollTop);
            }, 0);

            return false;
        });

        $('.pop_field ul.vis_mn li').on('click', function () {
            $(this).parent().find('li.active').removeClass('active');
            $(this).addClass('active');

            var act_val = $(this).parent().find('li.active').text();
            if ($.trim(act_val) == 'On') {
                $('#visibility').val('1');
            } else if ($.trim(act_val) == 'Off') {
                $('#visibility').val('0');
            }
        });

    });
</script>
<script src="{{ URL::asset('public/js/spectrum.js') }}"></script>
{{ HTML::style('public/css/front2/spectrum.css') }}
<script>
    $(".colorslott").spectrum({
        preferredFormat: "hex",
    });
</script>
@stop


