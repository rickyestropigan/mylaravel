@extends('layouts.default')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js" ></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{{ HTML::script('public/js/front/jquery.bpopup.min.js'); }}
<div class="botm_wraper">
    @include('elements/left_menu')
    <div class="right_wrap">
        <div class="right_wrap_inner">
            <div class="informetion informetion_new">
                {{ View::make('elements.actionMessage')->render() }}  
                <div class="informetion_top">
                    <div class="tatils"> <span class="personal">Orders History</span></div>
                    <div class="search-area">
                        {{ Form::open(array('url' => '/order/receivedorders', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>'form-inline')) }}
                        <div class="form-group align_box">
                            <label class="sr-only" for="search">Your Keyword</label>
                            {{ Form::text('search', $search_keyword, array('class' => 'required search_fields form-control','placeholder'=>"Your Keyword")) }}
                        </div>
                        <div class="form-group align_box">
                            <?php
                            $statusArray = array(
                                '' => 'Please Select'
                            );
//                                    $statusArray['Pending'] = 'Pending';
//                                    $statusArray['Paid'] = 'Paid';
                            global $adminStatus;
                            if (!empty($adminStatus)) {
                                foreach ($adminStatus as $key => $val)
                                    $statusArray[$key] = $val;
                            }
                            ?>
                            <!--{{ Form::select('status', $statusArray, $orderstatus, array('class' => 'form-control search_fields required', 'id'=>'status')) }}-->
                            <!--<span class="subb">{{ Form::submit('Search', array('class' => "btn btn-primary")) }}  </span>-->
                        </div>

                        <div class="search_btn">{{ Form::submit('Search', array('class' => "btn btn-success")) }}</div>
                        <span class="hint" style="margin:5px 0">Search Order by typing their Order number</span>
                        {{ Form::close() }}
                    </div>
                    <div class="pery">
                        <div class="table_scroll">
                            <div class="informetion_bxes">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables">Order Number</div>
                                            <div class="td_tables">Status</div>
                                            <div class="td_tables">Placed Date/Time</div>
                                            <div class="td_tables">Order Type</div>
                                            <div class="td_tables">Action</div>
                                        </div>
                                        <?php
                                        $i = 1;
                                        foreach ($records as $data) {
                                            if ($i % 2 == 0) {
                                                $class = 'colr1';
                                            } else {
                                                $class = '';
                                            }

                                            $RestaurantData = DB::table('users')
                                                    ->select("users.*")
                                                    ->where('users.id', $data->caterer_id)
                                                    ->first(); // get cateter details


                                            $cartItems = DB::table('order_item')
                                                    ->whereIn('menu_id', explode(',', $data->order_item_id))
                                                    ->where('order_id', $data->id)
                                                    ->get(); // get cart menu of this order
                                            ?>
                                            <div class="tr_tables2">
                                                <div data-title="Address Title" class="td_tables2">
                                                    {{ $data->order_number }}
                                                </div>
                                                <div data-title="Address Title" class="td_tables2">
                                                    {{ ucwords($data->status); }}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M Y h:i A", strtotime($data->created)) }}
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    <?php
                                                    // print_r($data); exit;
                                                    if ($data->pickup_ready == 1) {
                                                        echo "Pick up";
                                                    } else {
                                                        echo "Home Deliver";
                                                    }
                                                    ?>
                                                </div>
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <?php
                                                        echo html_entity_decode(HTML::link('order/receivedview/' . $data->slug . '/receive', '<i class="fa fa-search"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'View Order Details')));
                                                        ?>
                                                        <a href="javascript:void(0)" class="orderview_bpop_<?php echo $data->id; ?>">View</a>

                                                    </div>

                                                </div>	
                                            </div>

                                            <div class="popup fixed-width orderview-window_<?php echo $data->id; ?>" style="display:none">
                                                <div class="ligd">   
                                                    <div class="wrapper_login">
                                                        <span class="button b-close">
                                                            <span>X</span>        
                                                        </span> 
                                                        <div class="order_pop">
                                                            <div class="order_pop_inner">
                                                                <div class="left_side"><b>{{ $RestaurantData->first_name ? $RestaurantData->first_name.' '.$RestaurantData->last_name:"N/A"; }}</b>
                                                                    <address>{{ $RestaurantData->address ? $RestaurantData->address:"N/A"; }} <i class="fa fa-plus" aria-hidden="true"></i></address>
                                                                    <div class="call">{{ $RestaurantData->contact ? $RestaurantData->contact:"N/A"; }}</div>
                                                                </div>
                                                                <div class="right_side">
                                                                    <div class="deliverd">delivery</div> 
                                                                    <div class="recived">recived at 1:15 PM</div> 

                                                                </div>   


                                                            </div>   
                                                            <div class="btn_gropu">
                                                                <a href="#"> <i class="fa fa-usd" aria-hidden="true"></i> adjut</a> 
                                                                <a href="#"><i class="fa fa-times" aria-hidden="true"></i> cancel</a> 
                                                                <a href="#"> <i class="fa fa-print" aria-hidden="true"></i> Reprint</a> 

                                                            </div>
                                                            <div class="order_div">
                                                                <div class="order">order #: {{$data->order_number  ? $data->order_number:'N/A'}}</div>
                                                                <div class="orderr">Prepaid order</div>
                                                            </div>
                                                            <?php
                                                            if ($cartItems) {
                                                                $total = array();

                                                                foreach ($cartItems as $cartData) {

                                                                    $menuData = DB::table('menu_item')
                                                                                    ->where('id', $cartData->menu_id)->first();  // get menu data from menu table
                                                                    ?>
                                                                    <div class="roww"><span class="numb">{{ $cartData->quantity ? $cartData->quantity:''}}</span>
                                                                        <span class="namee">{{$menuData->item_name ? $menuData->item_name :''}}</span>
                                                                        <span class="ratee">{{CURR}}{{$cartData->base_price ? number_format($cartData->base_price,2) :''}}</span></div>


                                                                    <div class="roww white_colo"><span class="numb">1</span>
                                                                        <span class="namee"><b>chips and guacamole</b>
                                                                            <span class="tagg">small<br> leave out the onions and garlic, and <br> extra time, please</span>
                                                                        </span>
                                                                        <span class="ratee">$7.00</span></div>


                                                                    <div class="roww"><span class="numb">1</span>
                                                                        <span class="namee"><b>Enchiladas Suizas</b>
                                                                            <span class="tagg">substitute corn tortilla for flour</span>
                                                                        </span>
                                                                        <span class="ratee">$13.00</span></div>
                                                                    <div class="subtotlall"><div class="left_totla"><i class="fa fa-plus" aria-hidden="true"></i>Subtotal</div> 
                                                                        <div class="right_totla"><i class="fa fa-credit-card" aria-hidden="true"></i> $26.50</div>

                                                                    </div>
                                                                <?php }
                                                            } ?>
                                                            <div class="attention"><span class="atent"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> special order instructions</span>
                                                                <div class="atgg_linne">please deliver to the rear of the building. i live on the third floor doen the hall</div>
                                                            </div>


                                                            <div class="increasee">
                                                                <div class="deliveryy">delivery time</div>
                                                                <div class="numberr">
                                                                    <div class="value-button" id="decrease" onclick="decreaseValue()" value="Decrease Value">-</div>
                                                                    <input type="number" id="number" value="0" />
                                                                    <div class="value-button" id="increase" onclick="increaseValue()" value="Increase Value">+</div>
                                                                </div>

                                                            </div>

                                                            <div class="btnn"><a href="#">confirm</a></div>

                                                        </div>
                                                    </div>
                                                    <!--</div>-->
                                                </div>
                                            </div>
                                            <script>
                                                $(document).on("click", ".b-close", function () {
                                                    $('.orderview-window_<?php echo $data->id; ?>').bPopup().close();
                                                });

                                                $(document).on("click", ".orderview_bpop_<?php echo $data->id; ?>", function () {
                                                    $('.orderview-window_<?php echo $data->id; ?>').bPopup({
                                                        easing: 'easeOutBack', //uses jQuery easing plugin
                                                        speed: 700,
                                                        modalClose: false,
                                                        transition: 'slideBack',
                                                        transitionClose: "slideIn",
                                                        modalColor: false,
                                                    });
                                                });
                                            </script>

                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    <?php } else {
                                        ?>
                                        <div class="no-record">
                                            No records available
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div class="dataTables_paginate paging_bootstrap pagination">
                                    {{ $records->appends(Request::only('search','from_date','to_date'))->links() }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function increaseValue() {
        var value = parseInt(document.getElementById('number').value, 10);
        value = isNaN(value) ? 0 : value;
        value++;
        document.getElementById('number').value = value;
    }

    function decreaseValue() {
        var value = parseInt(document.getElementById('number').value, 10);
        value = isNaN(value) ? 0 : value;
        value < 1 ? value = 1 : '';
        value--;
        document.getElementById('number').value = value;
    }

</script>


@stop


