@extends('layouts.default')
@section('content')
{{ HTML::style('public/css/front/print.css') }}
<?php 
$RestaurantData = DB::table('users')
                                                    ->select("users.*")
                                                    ->where('users.id', $orderData->caterer_id)
                                                    ->first(); // get cateter details
//                                            print_r($customerData);

//echo '<pre>';
//print_r($orderData);
//exit;
?>
<div class="printcontainer">
    <div class="print_wrap">
        <div class="print_wrap_inner">
            <div class="top_header">
                <div class="print_logo">bitebargain.com</div>
                <div class="print_detail">
                    <ul>
                        <li>order#: <b><?php echo $orderData->order_number; ?></b></li>    
                        <li>customer service <span>{{ $RestaurantData->contact ? $RestaurantData->contact:""; }}</span></li>  
                        <li>automated self-service <span>{{ $RestaurantData->contact ? $RestaurantData->contact:""; }}</span></li>  

                    </ul>    

                </div></div>

            <div class="left_detail">
                <div class="titledetail"><b>{{ $RestaurantData->first_name ? $RestaurantData->first_name.' '.$RestaurantData->last_name:"N/A"; }}</b>{{ $RestaurantData->contact ? $RestaurantData->contact:""; }}</div>  
                <div class="simple_detail">CONFIRMATION CODE:</div>
                <div class="simple_detail">Order Type:</div>
                <div class="simple_detail">Request Time:</div>
                <div class="simple_detail">Order Placed Time:</div>

            </div>
            <div class="right_detail">
                <div class="borderr">86</div><br> 
                <div class="borderr"> <?php
                    if ($orderData->pickup_ready == 1) {
                        echo "Pick up";
                    } else {
                        echo "Home Deliver";
                    }
                    ?></div>
                <div class="borderrfull"><?php echo date('d/m/Y h:i A', strtotime($orderData->delivery_date)); ?>(ASAP)</div>
                <div class="borderrfull"><?php echo date('d/m/Y h:i A', strtotime($orderData->created)); ?>(ASAP)</div>

            </div>
            <div class="left_detail none_border">
                <div class="titledetail"><b>Customer info :</b></div>  
                <div class="simplee">{{ $customerData->first_name ? $customerData->first_name.' '.$customerData->last_name:"N/A"; }}:</div>
                <div class="simplee">{{ $customerData->address ? $customerData->address:"N/A"; }}</div>
                <div class="simplee">{{ $customerData->city ? $customerData->city:""; }}, {{ $customerData->zipcode ? $customerData->zipcode:""; }}</div>
                <div class="simplee">{{ $customerData->state ? $customerData->state:""; }}</div>

            </div>
            <div class="right_detail none_border">
                <div class="simple"></div> 
                <div class="simple">Phone:{{ $customerData->contact ? $customerData->contact:"N/A"; }}</div>
                <div class="simple">{{ $customerData->contact ? $customerData->contact:"N/A"; }}</div>
                <div class="simple">Distance: 5.08ml</div>

            </div>
            <?php
            echo $orderData->id;
            $cartItems = DB::table('order_item')
                    ->whereIn('id', explode(',', $orderData->order_item_id))
                    ->where('order_id', $orderData->id)
                    ->get(); // get cart menu of this order
            
            //echo '<pre>';print_r($cartItems);exit;
            
            ?>
            <div class="table_row">
                <div class="table_dcf_wrap">
                    <div class="td_tabllle">Qty</div>
                    <div class="td_tabllle">Menu item</div>
                    <div class="td_tabllle">Sub</div>
                </div> 


                <?php
//                                                            echo '<prE>'; print_r($data);
                if ($cartItems) {
                    $total = array();

                    foreach ($cartItems as $cartData) {

                        $menuData = DB::table('menu_item')
                                        ->where('id', $cartData->menu_id)->first();  // get menu data from menu table
                        ?>
                        <div class="tr_tabless">
                            <div class="td_tablee">{{ $cartData->quantity ? $cartData->quantity:''}}</div>
                            <div class="td_tablee">{{$menuData->item_name ? $menuData->item_name :''}}
                            </div>
                            <div class="td_tablee">{{CURR}}{{$cartData->base_price ? number_format($cartData->base_price,2) :''}}</div>

                        </div>
                        <?php
                        $price = $cartData->base_price * $cartData->quantity;
                        $total[] = $price;
                    }
                }
                ?>


            </div> 

            <div class="discount_wrap">
                <!--<div class="discount_wrap_left">DISCOUNT:31% Off w Purchase of $1 or More</div>-->
                <div class="discount_wrapright"><ul>
                        <li><b>subtotal:</b> <span>{{CURR}}{{ isset($total) ? number_format(array_sum($total),2):''}}</span></li>
<!--                        <li><b>Discount:</b><span>$20.70</span></li>
                        <li><b>Tax(8%):</b> <span>$20.70</span></li>
                        <li><b>Tip</b><span>$20.70</span></li>
                        <li><b>Delivery Charge:</b> <span>$20.70</span></li>-->
                        <li><b>TOTAL:</b><span class="boldd">{{CURR}}{{ isset($total) ? number_format(array_sum($total),2):''}}</span></li>

                    </ul></div>
            </div>
            <div class="prepaid">
                <div class="bold_txt">Prepaid Do NOT Charge</div>  
                <div class="orderr">
                    <span>order #<?php echo $orderData->order_number; ?></span>   
                    <span><?php echo date('d/m/Y', strtotime($orderData->created)); ?></span>   
                </div>

                <div class="signaturee">
                    <div class="signature">Customer Signature:</div>
                    <div class="signaturesize"></div>
                    <div class="text_wrap">
                        <div class="textt"><b>CARD HOLDER:</b> Laurie M Spicuzza. </div>
                        <div class="textt"><b>VISA:</b> XXXX XXXX XXXX 4572</div></div>

                </div>

            </div>

        </div>  
    </div></div>
<div class="print_btn">
    <form>     


        <a title="Print This Order" class="icon-5 print btn btn-primary"  href="javascript:void(0)">Print Order</a> 

    </form>              
</div>

<script src="{{ URL::asset('public/js/front/jQuery.print.js') }}"></script>
<script type='text/javascript'>

$(function () {

    $(".print").on('click', function () {
        //Print ele4 with custom options
        $(".printcontainer").print({
            //Use Global styles
            globalStyles: false,
            //Add link with attrbute media=print
            mediaPrint: false,
            //Custom stylesheet
            stylesheet: "<?php echo HTTP_PATH . "public/css/front/print.css" ?>",
            //Print in a hidden iframe
            iframe: false,
            //Don't print this
            noPrintSelector: ".avoid-this",
        });
    });
    // Fork https://github.com/sathvikp/jQuery.print for the full list of options
});
</script>
@stop