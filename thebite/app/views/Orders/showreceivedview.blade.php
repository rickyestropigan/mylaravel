@extends('layouts.default')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    $("#myform").validate({
        submitHandler: function (form) {
            this.checkForm();

            if (this.valid()) { // checks form for validity
                //                    $('#formloader').show();
                if (confirm("Are you sure want to change order status?")) {
                    this.submit();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    });

    $.validator.addMethod('number1',
            function (value) {
                return Number(value) >= 0 && (value) < 60;
            },
            'Enter number between 1 to 60 only.');
});</script>
<script type="text/javascript">

    function chkCancel() {
        var r = confirm("Are you sure want to cancel your order?");
        if (r == true) {
            window.location.href = "<?php echo HTTP_PATH . "order/cancelOrder/" . $orderData->slug; ?>";
        } else {
            return false;
        }
    }

    function submitchk() {
        var r = confirm("Are you sure want to change order status?");
        if (r == true) {
            return true;
        } else {
            return false;
        }
    }

</script>

<?php
$adminData = DB::table('admins')
        ->where('id', '1')
        ->first();

$RestaurantData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $orderData->caterer_id)
        ->first(); // get cateter details

$customerData = DB::table('users')
        ->select("users.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('areas', 'areas.id', '=', 'users.area')
        ->leftjoin('cities', 'cities.id', '=', 'users.city')
        ->where('users.id', $orderData->user_id)
        ->first(); // get cateter details

$deliveryAddress = DB::table('addresses')
        ->select("addresses.*", "areas.name as area_name", "cities.name as city_name")
        ->leftjoin('cities', 'cities.id', '=', 'addresses.city')
        ->leftjoin('areas', 'areas.id', '=', 'addresses.area')
        ->where('addresses.id', $orderData->address_id)
        ->first(); // get cateter details

$cartItems = DB::table('order_item')
        ->whereIn('menu_id', explode(',', $orderData->order_item_id))
        ->where('order_id', $orderData->id)
        ->get(); // get cart menu of this order

$adminuser = DB::table('admins')
        ->where('id', '1')
        ->first();
$getAdminTimeDiffMinutes = $adminuser->caterer_time;
$orderCreatedTime = strtotime($orderData->created);
$currDatetime = strtotime(date('Y-m-d H:i:s'));
$adminDiffTime = strtotime(date("Y-m-d H:i:s", strtotime("+$getAdminTimeDiffMinutes minutes", $orderCreatedTime)));
?>
<div class="botm_wraper">
    @include('elements/left_menu')
    <div class="right_wrap">
        <div class="right_wrap_inner">
            <div class="informetion informetion_new">
                {{ View::make('elements.actionMessage')->render() }}
                <div class="informetion_top">
                    <div class="tatils"><span class="personal">Order Details</span>
                        <!--                        <div class="link-button edit_pro">
                        <?php // echo html_entity_decode(HTML::link('user/managemenuitem', '<i class="fa fa-arrow-left"></i> Back', array('class' => 'icon-3', 'title' => 'Back to menu list'))); ?>
                                                </div>-->
                    </div>
                    <div class="informetion_bx">
                        <div class="panel-body panel-body_ful">


                            <div class="user-sec">


                                <div class="informetion_bx_left">
                                    <label>Customer Name</label>
                                    <div class="im_txt"><?php echo $customerData->first_name . ' ' . $customerData->last_name; ?></div>
                                </div>
                                <div class="informetion_bx_left">
                                    <label>Contact Number</label>
                                    <div class="im_txt"><?php echo $customerData->contact?$customerData->contact:'N/A'; ?></div>
                                </div>
                                <div class="informetion_bx_left">
                                    <label>Order Number</label>
                                    <div class="im_txt"><?php echo $orderData->order_number; ?></div>
                                </div>
                                <div class="informetion_bx_left">
                                    <label>Status</label>
                                    <div class="im_txt"><?php echo $orderData->status; ?></div>
                                </div>
                                <div class="informetion_bx_left">
                                    <label>Placed Date/Time</label>
                                    <div class="im_txt"><?php echo date('d M Y h:i A', strtotime($orderData->created)); ?></div>
                                </div>
                                <?php if($orderData->delivery_date>0){ ?>
                                <div class="informetion_bx_left">
                                    <label>Delivery Date/Time</label>
                                    <div class="im_txt"><?php echo date('d M Y h:i A', strtotime($orderData->delivery_date)); ?></div>
                                </div>
                                <?php
                                }
                                ?>
                                <?php
                                if ($orderData->pickup_ready == 1) {
                                    ?>
                                    <div class="informetion_bx_left">
                                        <label>Delivery type</label>
                                        <div class="im_txt">Pickup

                                        </div>
                                    </div>    
                                    <div class="informetion_bx_left">
                                        <label>Delivery Mode</label>
                                        <div class="im_txt"><?php
                                            if ($orderData->pickup_now == 1) {
                                                echo "Pickup Now";
                                            } else {
                                                echo "Pickup Later (" . $orderData->pickup_time . ')';
                                            }
                                            if ($adminDiffTime >= $currDatetime) {
                                                if ($orderData->status == "Confirm") {
                                                    ?>{{ html_entity_decode(HTML::link(HTTP_PATH.'user/notify/'.$orderData->slug," Notify to customer", ['class'=>'link-menu_mcbbb'])); }}<?php
                                                }
                                            }
                                            ?></div>
                                    </div>    
                                    <div class="informetion_bx_left">
                                        <label>Pickup address</label>
                                        <div class="im_txt">{{$RestaurantData->address}}, {{$RestaurantData->area_name}}</div>
                                    </div>    
                                    <?php
                                }
                                ?>

                            </div>




                            <?php
                            // echo'<pre>'; print_r($deliveryAddress);die;

                            if (!empty($deliveryAddress)) {
                                ?>

                                <div class="user-sec">
                                    <div class="user-title">Delivery Address Details</div>    

                                    <div class="informetion_bx_left">
                                        <label>Address Title</label>
                                        <div class="im_txt">{{ $deliveryAddress->address_title ? $deliveryAddress->address_title:"N/A"; }}</div>
                                    </div>
                                    <div class="informetion_bx_left">
                                        <label>Address Type</label>
                                        <div class="im_txt">{{ $deliveryAddress->address_type ? $deliveryAddress->address_type:"N/A"; }}</div>
                                    </div>
                                    <div class="informetion_bx_left">
                                        <label>Floor</label>
                                        <div class="im_txt">{{ $deliveryAddress->floor ? $deliveryAddress->floor:"N/A"; }}</div>
                                    </div>
                                    <div class="informetion_bx_left">
                                        <label>Apartment</label>
                                        <div class="im_txt">{{ $deliveryAddress->apartment ? $deliveryAddress->apartment:"N/A"; }}</div>
                                    </div>
                                    <div class="informetion_bx_left">
                                        <label>Building</label>
                                        <div class="im_txt">{{ $deliveryAddress->building ? $deliveryAddress->building:"N/A"; }}</div>
                                    </div>
                                    <div class="informetion_bx_left">
                                        <label>Street Name</label>
                                        <div class="im_txt">{{ $deliveryAddress->street_name ? $deliveryAddress->street_name:"N/A"; }}</div>
                                    </div>
                                    <div class="informetion_bx_left">
                                        <label>Area</label>
                                        <div class="im_txt">{{ $deliveryAddress->area_name ? $deliveryAddress->area_name:"N/A"; }}</div>
                                    </div>
                                    <div class="informetion_bx_left">
                                        <label>City</label>
                                        <div class="im_txt">{{ $deliveryAddress->city_name ? $deliveryAddress->city_name:"N/A"; }}</div>
                                    </div>

                                    <div class="informetion_bx_left">
                                        <label>Phone Number</label>
                                        <div class="im_txt">{{ $deliveryAddress->phone_number ? $deliveryAddress->phone_number:"N/A"; }}</div>
                                    </div>



                                </div>

                            <?php } ?> 
                            <?php 
                            if ($cartItems) {
                                $total = array();
                                ?>

                                <div class="user-sec">
                                    <div class="order-title user-title saw">Items Details</div>
                                    <div class="ored_ur">
                                        <div class="order-table-sec">
                                            <div class="order-table-head">
                                                <div class="order-table-head-in">Item</div>
                                                <div class="order-table-head-in">Base Price</div>
                                                <div class="order-table-head-in">Quantity</div>
                                                <div class="order-table-head-in">Sub Total</div>
                                            </div>
                                            <?php 
                                            foreach ($cartItems as $cartData) {

                                                $menuData = DB::table('menu_item')
                                                                ->where('id', $cartData->menu_id)->first();  // get menu data from menu table
//                                                    $sub_total = $cartData->base_price * $cartData->quantity;
//                                                    $total[] = $sub_total;
//        print_r($menuData);die;
                                                ?>
                                                <div class="order-table-middel">
                                                    <div class="order-table-middel-in">
                                                        <div class="menucmtilet">
                                                            <?php echo $menuData->item_name; ?>
                                                        </div>
                                                        <?php if (!empty($cartData->submenus)) { ?>
                                                            <div class="menucmt">
                                                                <span class="texlbl"> Sub Menu:</span> <span class="texval"><?php echo $cartData->submenus; ?></span>
                                                            </div>
                                                            <?php
                                                        }
                                                        // print_r($cartData);
                                                        $addonprice = 0;
                                                        
                                                       
                                                        $addonprice =$menuData->price;
//                                                        $addonprice = $addonprice * $cartData->quantity;
                                                        $total[] = $addonprice;
                                                        ?>
                                                        <?php /* if (!empty($menuData->preparation_time)) { ?>
                                                          <div class="menucmtrpepar">
                                                          <span class="texlbl"> Preparation Time:</span> <span class="texval"><?php echo $menuData->preparation_time . ' Hours'; ?></span>
                                                          </div>
                                                          <?php } */ ?>

                                                        <?php if (!empty($cartData->comment)) { ?>
                                                            <div class="menucmt">
                                                                <span class="texlbl"> Comment:</span> <span class="texval"><?php echo $cartData->comment; ?></span>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($addonprice, 2); ?></div>
                                                    <div class="order-table-middel-in"><?php echo $cartData->quantity; ?></div>
                                                    <div class="order-table-middel-in"><?php echo App::make("HomeController")->numberformat($addonprice, 2); ?></div>
                                                </div>
                                                <?php
                                            }
                                            $gTotal = array_sum($total);
                                            ?>
                                            <div class="order-table-end">
                                                <div class="order-table-end-in" style="border-right:0px;">Total</div>
                                                <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                <div class="order-table-end-in">&nbsp;</div>
                                                <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($gTotal, 2); ?></div>
                                            </div>
                                            <?php
                                            if (!empty($orderData->discount)) {
                                                //$ordersc = explode(',',$orderData->order_id);
                                                $minus = $orderData->discount;
                                                // if(count($ordersc) == 1){
                                                $gTotal = $gTotal - $minus;
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Discount (-)</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"> <?php echo App::make("HomeController")->numberformat($minus, 2); ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php
                                            if (!empty($orderData->tax)) {
                                                //  $ordersc = explode(',',$orderData->order_id);
                                                $tax = $orderData->tax;
                                                $gTotal = $gTotal + $tax;
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Tax</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($tax, 2); ?></div>
                                                </div>
                                            <?php } ?>

                                            <?php
                                            if (!empty($orderData->delivery_charge)) {
                                                $gTotal = $gTotal + $orderData->delivery_charge;
                                                ?>
                                                <div class="order-table-end">
                                                    <div class="order-table-end-in" style="border-right:0px;">Delivery Charge (<?php echo $orderData->delivery_type; ?>)</div>
                                                    <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                    <div class="order-table-end-in">&nbsp;</div>
                                                    <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($orderData->delivery_charge, 2); ?></div>
                                                </div>
                                            <?php } ?>
                                            <?php /*
                                              if ($adminData->is_commission == 1) {
                                              $comm_per = $adminData->commission;
                                              $tax_amount = $comm_per * $gTotal / 100;
                                              $gTotal = $gTotal - $tax_amount;
                                              ?>
                                              <div class="order-table-end">
                                              <div class="order-table-end-in" style="border-right:0px;">Admin Commission (-)</div>
                                              <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                              <div class="order-table-end-in">&nbsp;</div>
                                              <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($tax_amount, 2) ; ?></div>
                                              </div>
                                              <?php } */ ?>
                                            <?php $gTotal = $gTotal; ?>
                                            <div class="order-table-end">
                                                <div class="order-table-end-in" style="border-right:0px;">Grand Total</div>
                                                <div class="order-table-end-in" style="border-right:0px;">&nbsp;</div>
                                                <div class="order-table-end-in">&nbsp;</div>
                                                <div class="order-table-middel-in-g"><?php echo App::make("HomeController")->numberformat($gTotal, 2); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php 
//  if ($orderData->status != "Confirm") {


//                            if ($adminDiffTime >= $currDatetime) {
                                ?>
                                <div class="form-group">

                                    <div class="user-sec">
                                        <div class="user-title">Status</div> 
                                        <?php if (($orderData->status == "Pending" || $orderData->status == "Confirm" || $orderData->status == "Paid") || ($orderData->status != "Cancel" && $orderData->status != "Delivered")) { ?>
                                            <div class="informetion_bx_left">
                                                <label>Current Status</label>

                                                <div class="im_txt">
                                                    <?php echo $orderData->status; ?>
                                                </div>
                                                <?php
                                                if (!empty($orderData->kitchen_staff_id)) {
                                                    $kitchenStaffInfo = DB::table('users')
                                                            ->where('id', $orderData->kitchen_staff_id)
                                                            ->first();
                                                    //print_r($kitchenStaffInfo);
                                                    ?>
                                                    <div class="user-sec-left">Kitchen Staff</div>
                                                    <div class="user-sec-right in_upt">
                                                        <?php echo ucfirst($kitchenStaffInfo->first_name . " " . $kitchenStaffInfo->last_name); ?>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                                if (!empty($orderData->delivery_person_id)) {
                                                    $deliveryPersonInfo = DB::table('users')
                                                            ->where('id', $orderData->delivery_person_id)
                                                            ->first();
                                                    //print_r($kitchenStaffInfo);
                                                    ?>
                                                    <div class="user-sec-left">Delivery Person</div>
                                                    <div class="user-sec-right in_upt">
                                                        <?php echo ucfirst($deliveryPersonInfo->first_name . " " . $deliveryPersonInfo->last_name); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <!--                                            <div class="informetion_bx_left">
                                                                                        <div class="user-sec-left">Order status</div>
                                                                                        
                                                                                        <div class="user-sec-right in_upt">
                                        <?php //echo $orderData->paid?"Paid":"Payment is not successfull.";    ?>
                                                                                        </div>
                                                                                    </div>-->
                                         {{ View::make('elements.actionMessage')->render() }}
                                            {{ Form::model($userData, array('url' => '/order/receivedview/'.$orderData->slug.'/'.$type, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}       	
                                          
                                        <div class="informetion_bx_left label_special">




                                             <!--                                            <div class="user-sec-left">Next Status</div>-->

                                            
                                                <?php 
                                                //if (($orderData->paid == 1) && (($orderData->status == "Pending" || $orderData->status == "Confirm" || $orderData->status == "Paid") || ($orderData->status != "Cancel" && $orderData->status != "Delivered"))) {
                                                if ((( $orderData->status == "Confirm" || $orderData->status == "Paid") || ($orderData->status != "Cancel" && $orderData->status != "Delivered"))) {
                                                    ?>

                                                    <?php
                                                    $kitchStaffArray = array(
                                                        '' => 'Please Select'
                                                    );
                                                    $kitchenStaffs = User::orderBy('first_name', 'asc')->where('status', "=", "1")->where('user_type', "=", 'KitchenStaff')->where('id', "=", $userData->id)->get();
                                                    if (!empty($kitchenStaffs)) {
                                                        foreach ($kitchenStaffs as $kitchenStaff)
                                                            $kitchStaffArray[$kitchenStaff->id] = ucfirst($kitchenStaff->first_name . " " . $kitchenStaff->last_name);
                                                    }

                                                    //echo '<pre>';print_r($kitchenStaffs);
                                                    $totalKS = sizeof($kitchenStaffs);


                                                    $deliveryPersonArray = array(
                                                        '' => 'Please Select'
                                                    );
                                                    $deliveryPersons = User::orderBy('first_name', 'asc')->where('status', "=", "1")->where('is_busy', "=", "0")->where('user_type', "=", 'DeliveryPerson')->where('id', "=", $userData->id)->get();
                                                    if (!empty($deliveryPersons)) {
                                                        foreach ($deliveryPersons as $deliveryPerson)
                                                            $deliveryPersonArray[$deliveryPerson->id] = ucfirst($deliveryPerson->first_name . " " . $deliveryPerson->last_name);
                                                    }

                                                    $totalDP = sizeof($deliveryPersons);
                                                    ?>

                                                    <?php
                                                    global $adminStatus;
                                                    $statusArray = array(
                                                        '' => 'Please Select'
                                                    );
// new status created                               
//                                                    if ($orderData->status == "Confirm") {
//                                                        $statusArray['Out for delivery'] = 'Out for delivery';
//                                                        $statusArray['Preparing'] = 'Preparing';
//                                                        $statusArray['Delivered'] = 'Delivered';
//                                                    } else {
//                                                        $statusArray['Confirm'] = 'Confirm';
//                                                        $statusArray['cancel'] = 'Cancel';
//                                                    }

                                                    $orderstatus = Orderstatus::orderBy('status_name', 'asc')->where('status', "=", "1")->where('user_id', "=", $userData->id)->lists('status_name', 'id');
                                                    $orderstatus = array_combine($orderstatus, $orderstatus);
                                                    if (!empty($orderstatus)) {
                                                        foreach ($orderstatus as $key => $val)
                                                            $statusArray[$key] = ucfirst($val);
                                                    }
//
//
                                                    if (!empty($adminStatus)) {
                                                        foreach ($adminStatus as $key => $val)
                                                            $statusArray[$key] = $val;
                                                    }
//
//                                                    // echo '<pre>';print_r($statusArray);
//
//
                                                    if (in_array($orderData->status, $statusArray)) {
                                                        unset($statusArray[$orderData->status]);
                                                    }
                                                    if ($orderData->status != "Pending" && $orderData->status != "Paid") {
                                                        unset($statusArray['Confirm']);
                                                    }
                                                    if ($orderData->status != " On Delivery " ) {
                                                        unset($statusArray['Prepared']);
                                                    }
//
                                                    if ($orderData->delivery_type == "Pickup") {
                                                        unset($statusArray['Assign To Delivery']);
                                                        unset($statusArray['On Delivery']);
                                                    }
                                                    ?>
                                                    
                                                        {{ HTML::decode(Form::label('status', "Next Status<span class='require'>*</span>",array('class'=>""))) }}

                                                        <div class="in_upt ">
                                                            <span class="dropp drop_short">{{ Form::select('status',$statusArray, $orderData->status, array('class' => 'required form-control','id'=>"selectop")) }}</span>
                                                        </div>
                                                  </div>
                                                    <div class="clear"></div>
                                                    <?php if( $orderData->status == "Pending"){ ?>
                                                    <div class="informetion_bx_left label_special">
                                                        {{ HTML::decode(Form::label('preparation_time', "Preparation Time<span class='require'>*</span>",array('class'=>""))) }}

                                                        <div class="im_txt_filed">
                                                            {{  Form::text('preparation_time', '',  array('class' => 'required form-control number1','id'=>"name"))}}
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                    <div class="form_group none_label left_btn">
                                                        <label>&nbsp;</label>
                                                    <?php if($type=='today'){
                                                        $url='order/todayorders';
                                                    }elseif($type=='schedule'){
                                                          $url='order/scheduleorders';
                                                    }elseif($type=='receive'){
                                                          $url='order/receivedorders';
                                                    }else{
                                                          $url='order/receivedpayment';
                                                    } ?>
                                                    <div class="in_upt in_upt_res center-element ">
                                                        {{ Form::submit('Submit', array('class' => "btn btn-primary")) }}
                                                        {{ html_entity_decode(HTML::link(HTTP_PATH.$url, "Cancel", array('class' => 'btn btn-default'), true)) }}
                                                    
                                                    </div>
                                                        </div>
                                                <?php } else { ?>
                                                    <div class='stawe'>{{ $orderData->status }}</div>
                                                <?php } ?>
                                          

                                            <?php
                                            echo Form::close();
                                            ?>
                                      
                                    </div>
                                </div>
                                <?php
//                            }
                            //  }
                            ?>



                            <div class="print_btn">
                                <form>     

                                 
                                           <a title="Print This Order" class="icon-5 print btn btn-primary" href="javascript:void(0)">Print Order</a> 
                                        
                                </form>              
                            </div>



                            <div class="chan_pich" style="width: 20%">
                                <!--                                <a title="Print This Order" class="icon-5 print" href="javascript:void(0)">Print Order</a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="{{ URL::asset('public/js/front/jQuery.print.js') }}"></script>
<script type='text/javascript'>

    $(function () {

        $(".print").on('click', function () {
            //Print ele4 with custom options
            $(".informetion_top").print({
                //Use Global styles
                globalStyles: false,
                //Add link with attrbute media=print
                mediaPrint: false,
                //Custom stylesheet
                stylesheet: "<?php echo HTTP_PATH . "public/css/front/style.css" ?>",
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


