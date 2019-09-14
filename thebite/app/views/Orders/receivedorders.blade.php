@extends('layouts.default')
@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
{{ HTML::script('public/js/front/jquery.bpopup.min.js'); }}
{{ HTML::style('public/assets/bootstrap/css/bootstrap.min.css') }}
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
$(function () {
$("#searchByDateFrom").datepicker({
defaultDate: "+1w",
        changeMonth: true, dateFormat: 'yy-mm-dd',
        numberOfMonths: 1, minDate: 0,
        onClose: function (selectedDate) {
        $("#searchByDateTo").datepicker("option", "minDate", selectedDate);
        }
});
$("#searchByDateTo").datepicker({
defaultDate: "+1w", minDate: 0,
        changeMonth: true, dateFormat: 'yy-mm-dd',
        numberOfMonths: 1,
        onClose: function (selectedDate) {
        $("#searchByDateFrom").datepicker("option", "maxDate", selectedDate);
        }
});
});</script>
<?php
// get current user details
$user_id = Session::get('user_id');

$all_query = DB::table('orders')->where('orders.caterer_id', $user_id)->where('delivery_date', "<", date('Y-m-d'))->get();

$pending_query = DB::table('orders')->where('orders.caterer_id', $user_id)->where('delivery_date', "=", date('Y-m-d'))->where('status', "=", 'Pending')->get();

$confirm_query = DB::table('orders')->where('orders.caterer_id', $user_id)->where('delivery_date', "<", date('Y-m-d'))->where('status', 'Confirm')->get();

$cancel_query = DB::table('orders')->where('orders.caterer_id', $user_id)->where('delivery_date', "<", date('Y-m-d'))->where('status', 'Cancel')->get();
?>
<div class="botm_wraper">
    @include('elements/left_menu')
    <div class="right_wrap">
        <div class="right_wrap_inner">
            <div class="informetion informetion_new">
                {{ View::make('elements.actionMessage')->render() }}  
                <div class="informetion_top">
                    <div class="tatils"> <span class="personal">Orders History</span></div>
                    <div class="tabel_option">
                        <ul class="tabel_option_li">
                            <li class="{{Request::is('order/receivedorders/all') ? 'active' :''}}"><a href="{{HTTP_PATH.'order/receivedorders/all'}}">ALL ({{$all_query ? count($all_query) :'0'}})</a></li>
                            <li class="{{Request::is('order/receivedorders/new') ? 'active' :''}}"><a href="{{HTTP_PATH.'order/receivedorders/new'}}">NEW ({{$pending_query ? count($pending_query):'0'}})</a></li>
                            <li class="{{Request::is('order/receivedorders/confirm') ? 'active' :''}}"><a href="{{HTTP_PATH.'order/receivedorders/confirm'}}">CONFIRMED ({{$confirm_query ? count($confirm_query) :'0'}})</a></li>
                            <li class="{{Request::is('order/receivedorders/cancel') ? 'active' :''}}"><a href="{{HTTP_PATH.'order/receivedorders/cancel'}}">CANCELLED ({{$cancel_query ? count($cancel_query):'0'}})</a></li>
                        </ul>  
                    </div>
                    <div class="search-area">
                        {{ Form::open(array('url' => '/order/receivedorders/'.$type, 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>'form-inline')) }}
                        <div class="form-group">
                            <label class="sr-only" for="search">Your Keyword</label>
                            {{ Form::text('search', $search_keyword, array('class' => 'required search_fields form-control','placeholder'=>"Your Keyword")) }}
                        </div>
                        <?php // if($type=='all'){  ?>
                        <div class="form-group">
                            <label class="sr-only" for="search_to">Start Date</label>
                            {{ Form::text('search_to', $search_to, array('class' => 'required search_fields form-control searchDate','placeholder'=>"From Date",'id'=>'searchByDateFrom',"data-date"=>"", "data-date-format"=>"yyyy-mm-dd", "data-link-field"=>"dtp_input2", "data-link-format"=>"yyyy-mm-dd",'onkeypress'=>"return false")) }}
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="search_end">End Date</label>
                            {{ Form::text('search_end', $search_end, array('class' => 'required search_fields form-control searchDate','placeholder'=>"To Date",'id'=>'searchByDateTo',"data-date"=>"", "data-date-format"=>"yyyy-mm-dd", "data-link-field"=>"dtp_input2", "data-link-format"=>"yyyy-mm-dd",'onkeypress'=>"return false")) }}
                        </div>
                        <?php // }  ?>
                        <div class="search_btn search_btn_right">{{ Form::submit('Search', array('class' => "btn btn-success")) }}</div>
                        <span class="hint" style="margin:5px 0">Search Order by typing their Order number</span>
                        {{ Form::close() }}
                    </div>
                    <div class="pery">
                        <div class="table_scroll">
                            <div class="informetion_bxes  green-table">
                                <?php
                                if (!$records->isEmpty()) {
                                    ?>
                                    <div class="table_dcf">
                                        <div class="tr_tables">
                                            <div class="td_tables"><i class="fa fa-truck" aria-hidden="true"></i></div>
                                            <div class="td_tables"><i class="fa fa-user" aria-hidden="true"></i></div>
                                            <div class="td_tables"><i class="fa fa-money" aria-hidden="true"></i></div>
                                            <div class="td_tables"><i class="fa fa-tag" aria-hidden="true"></i></div>
                                            <div class="td_tables"> <i class="fa fa-clock-o" aria-hidden="true"></i></div>

<!--                                            <div class="td_tables"> Status</div>-->
                                            <div class="td_tables"> <i class="fa fa-question-circle-o" aria-hidden="true"></i> </div>
                                        </div>
                                        <?php
                                        $i = 1;
                                        foreach ($records as $data) {
//                                           echo'<pre>'; print_r($data);die;
                                            if ($i % 2 == 0) {
                                                $class = 'colr1';
                                            } else {
                                                $class = '';
                                            }

                                            $RestaurantData = DB::table('users')
                                                    ->select("users.*")
                                                    ->where('users.id', $data->caterer_id)
                                                    ->first(); // get cateter details
                                            $userData = DB::table('users')
                                                    ->select("users.*")
                                                    ->where('users.id', $data->user_id)
                                                    ->first(); // get user details
//print_r($cartItems);
                                            $cartItems = DB::table('order_item')
                                                    ->whereIn('menu_id', explode(',', $data->order_item_id))
                                                    ->where('order_id', $data->id)
                                                    ->get(); // get cart menu of this order
//                                            print_r($cartItems);die;
                                            ?>
                                            <div class="tr_tables2">
                                                <div data-title="Order Number" class="td_tables2">
                                                    {{ $data->order_number }}
                                                </div>
                                                <div data-title="Name" class="td_tables2">
                                                    {{ $userData->first_name ? $userData->first_name.' '.$userData->last_name:"N/A"; }}
                                                    <br/>{{ $userData->address ? $userData->address:"N/A"; }}
                                                </div>
                                                <div data-title="Total Cost" class="td_tables2">
                                                    {{ $data->total ? CURR.number_format($data->total,2):"N/A"; }}
                                                </div>
                                                <div data-title="Offer" class="td_tables2">
                                                   30% on all menu
                                                  
                                                </div>
                                                <div data-title="Created" class="td_tables2">
                                                    {{  date("d M Y h:i A", strtotime($data->created)) }}
                                                </div>


                                                <!--                                                <div data-title="Order Type" class="td_tables2">
                                                <?php
                                                if ($data->pickup_ready == 1) {
                                                    echo "Pick up";
                                                } else {
                                                    echo "Home Deliver";
                                                }
                                                ?>
                                                                                                </div>-->
<!--                                                <div data-title="Status" class="td_tables2">
                                                    {{ ucwords($data->status); }}
                                                </div>-->
                                                <div data-title="Action" class="td_tables2">
                                                    <div class="actions">
                                                        <a href="javascript:void(0)" class="orderview_bpop_<?php echo $data->id; ?>" title = "View Order Details"><i class="fa fa-search"></i></a>
                                                    </div>
                                                </div>	
                                            </div>

                                            <div class="popup fixed-width orderview-window_<?php echo $data->id; ?>" style="display:none">
                                                <div class="ligd">   
                                                    <div class="wrapper_login">

                                                        <div class="order_pop">
                                                            <span class="button b-close">
                                                                <span>X</span>        
                                                            </span> 
                                                            <div class="order_pop_inner">
                                                                <div class="left_side"><b>{{ $RestaurantData->first_name ? $RestaurantData->first_name.' '.$RestaurantData->last_name:"N/A"; }}</b>
                                                                    <address>{{ $RestaurantData->address ? $RestaurantData->address:"N/A"; }} <i class="fa fa-plus" aria-hidden="true"></i></address>
                                                                    <div class="call">{{ $RestaurantData->contact ? $RestaurantData->contact:"N/A"; }}</div>
                                                                </div>
                                                                <div class="right_side">
                                                                    <div class="deliverd">{{$data->status ? $data->status:'N/A'}}</div> 
                                                                    <div class="recived">Promised by {{$data->created  ? date("M d", strtotime($data->created)).','.date('h:i A',strtotime($data->created)):'N/A' }}</div> 
                                                                </div>   
                                                            </div>   
                                                            <div class="btn_gropu">
                                                                <?php if ($data->status == "Pending") { ?>
                                                                    <a href="#"> <i class="fa fa-usd" aria-hidden="true"></i> Adjust</a>
                                                                    <a href="#"> <i class="fa fa-times" aria-hidden="true"></i> Cancel</a> 
                                                                <?php } ?>
                                        <!--<a href="#"> <i class="fa fa-print" aria-hidden="true"></i> Reprint</a>--> 
                                                                <?php
                                                                echo html_entity_decode(HTML::link('order/printorder/' . $data->slug, '<i class="fa fa-print"></i>Reprint', array('class' => 'btn btn-primary btn-xs', 'title' => 'Reprint', 'target' => '_blank')));
                                                                ?>
                                                            </div>
                                                            <div class="order_div">
                                                                <div class="order">order #: {{$data->order_number  ? $data->order_number:'N/A'}}</div>
                                                                <div class="orderr">Prepaid Order</div>
                                                            </div>
                                                            <?php
                                                            $total = array();
                                                            ?>
                                                            <div class="subtotlall"><div class="left_totla"><i class="fa fa-plus" aria-hidden="true"></i>Subtotal</div> 
                                                                <div class="right_totla"><i class="fa fa-credit-card" aria-hidden="true"></i>{{ $data->total ? CURR.number_format($data->total,2):"N/A"; }}</div>
                                                            </div>

                                                            <div class="attention"><span class="atent"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> special order instructions</span>
                                                                <div class="atgg_linne">please deliver to the rear of the building. i live on the third floor doen the hall</div>
                                                            </div>
                                                            <?php if ($data->status == "Pending") { ?>

                                                                <div class="increasee">
                                                                    <div class="deliveryy">delivery time</div>
                                                                    <div class="numberr">
                                                                        <div class="value-button" id="decrease" onclick="decreaseValue('{{$data->id}}')" value="Decrease Value">-</div>
                                                                        <input type="text" id="number" class='number{{$data->id}}' value="0" />
                                                                        <div class="value-button" id="increase" onclick="increaseValue('{{$data->id}}')" value="Increase Value">+</div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ($data->status == "Pending") { ?>
                                                                <div class="btnn"><a href="javascript:void(0)" onclick="updatetime('{{$data->id}}')">confirm</a></div>
                                                            <?php } ?>
                                                            <?php if ($data->status == "Cancel") { ?>
                                                                <div class="attention"><span class="atent">Cancelled order at <?php echo date('H:i A', strtotime($data->modified)) ?></span><i class="fa fa-times" aria-hidden="true"></i>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ($data->status == "Confirm") { ?>
                                                                <div class="attention"><span class="atent">Successfully Confirmed order at <?php echo date('H:i A', strtotime($data->modified)) ?></span><i class="fa fa-check" aria-hidden="true"></i>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
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
    function increaseValue(id) {
    var value = $('.number' + id).val();
//        var value = parseInt(document.getElementById('number'+id).value, 10);

    value = isNaN(value) ? 0 : value;
    value++;
    $('.number' + id).val(value);
    }

    function decreaseValue(id) {
    var value = $('.number' + id).val();
//        var value = parseInt(document.getElementById('number'+id).value, 10);
//        alert(value);
    value = isNaN(value) ? 0 : value;
    value < 1 ? value = 1 : '';
    value--;
    $('.number' + id).val(value);
    }

</script>
<script>
    function cancel(slug) {

    var data = {slug: slug}
    $.ajax({
    url: "<?php echo HTTP_PATH . "order/cancelorder" ?>",
            type: 'POST',
            data: data,
            success: function(data, textStatus, XMLHttpRequest)
            {
            window.location.reload();
            }
    });
    }
    function updatetime(id) {

    var data = {id: id, preparation_time:$('.number' + id).val()}
    $.ajax({
    url: "<?php echo HTTP_PATH . "order/updatetime" ?>",
            type: 'POST',
            data: data,
            success: function(data, textStatus, XMLHttpRequest)
            {
            window.location.reload();
            }
    });
    }
</script>

@stop


