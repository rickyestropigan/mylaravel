@extends('layouts.default')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
{{ HTML::style('public/js/chosen/chosen.css'); }}
<script type="text/javascript">
$(document).ready(function () {
    $.validator.addMethod('positiveNumber',
            function (value) {
                return Number(value) > 0;
            }, 'Enter a positive number.');
    $.validator.addMethod("pass", function (value, element) {
        return  this.optional(element) || (/.{8,}/.test(value) && /((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,20})/.test(value));
    }, "Password minimum length must be 8 characters and combination of 1 special character, 1 lowercase character, 1 uppercase character and 1 number.");
    $("#myform").validate({
        submitHandler: function (form) {
            this.checkForm();
            if (this.valid()) { // checks form for validity
//                    $('#formloader').show();
                this.submit();
            } else {
                return false;
            }
        }
    });
    $("#menuitem").validate({
        submitHandler: function (form) {
            this.checkForm();
            if (this.valid()) { // checks form for validity
                var item = $("#menu_items").val();
                var text = '';
                $("#menu_items :selected").each(function () {
                    text += $(this).text() + ',';
                });
                if (item) {
                    $("#item_name_text").val(text);
                    $("#item_name").val(item);
                    $('.orderview-window').bPopup().close();
                }
            } else {
                return false;
            }
        }
    });
    $("#days").change(function () {
        if (this.checked) {
            $(".offer_time_slot").hide();
        } else {
            $(".offer_time_slot").show();
        }
    });
});</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js" ></script>
{{ HTML::script('public/js/front/jquery.bpopup.min.js'); }}
{{ HTML::style('public/assets/bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('public/assets/bootstrap/css/bootstrap-datetimepicker.min.css') }}
<script src="{{ URL::asset('public/assets/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('public/assets/bootstrap/js/bootstrap-datetimepicker.js') }}"></script>

<script type="text/javascript">
$(function () {
    $('.searchDate').datetimepicker({
        language: 'en',
        weekStart: 1,
        todayBtn: 1,
        endDate: new Date(),
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });
});

</script>
<div class="botm_wraper">
    @include('elements/left_menu')
    <div class="right_wrap">
        <div class="right_wrap_inner">
            <div class="informetion informetion_new">
                <div class="informetion_top">
                    <div class="tatils"> <span class="personal">Edit Offer</span></div>
                    <div class="pery">
                        <div id="formloader" class="formloader" style="display: none;">
                            {{ HTML::image('public/img/loader_large_blue.gif','', array()) }}
                        </div>
                        {{ View::make('elements.frontEndActionMessage')->render() }}
                        {{ Form::open(array('url' => '/offer/editoffer/'.$offerdata->slug, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
                        <span class="require redd" style="float: left;width: 100%;">* Please note that all fields that have an asterisk (*) are required. </span>
                        <div class="form_group selectt">
                            {{ HTML::decode(Form::label('type', "Type <span class='require'>*</span> ",array('class'=>"control-label col-lg-2"))) }}
                            <div class="in_upt">
                                <div class="dropp">
                                    <?php
                                    $cuisine_array = array(
                                        '' => 'Please Select',
                                        'percentage' => 'Percentage',
                                        'currency' => 'Currency'
                                    );
                                    ?>
                                    {{ Form::select('type', $cuisine_array, $offerdata->type, array('class' => 'form-control required', 'id'=>'type')) }}
                                </div>
                            </div>
                        </div>
                        <div class="form_group">    
                            {{ HTML::decode(Form::label('offer', "Offer <span class='require'>*</span> ",array('class'=>"control-label col-lg-2"))) }}
                            <?php if ($offerdata->type == 'currency') { ?>
                                <div id="items" class="in_upt">
                                    {{  Form::text('offer', $offerdata->discount,  array('class' => 'required form-control positiveNumber','id'=>"offer"))}}
                                </div>
                            <?php } else { ?>
                            <div class="in_upt"  id="items">
                                <div class="dropp">
                                    <?php
                                    $start_time = array('' => 'Please Select');
                                    for ($i = 10; $i < 100; ($i = $i + 10)) {
                                        $start_time[$i] = $i . '%';
                                    }
                                    ?>
                                    {{ Form::select('offer', $start_time, $offerdata->discount, array('class' => 'form-control required', 'id'=>'offer')) }}
                                </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="form_group selectt">
                            {{ HTML::decode(Form::label('name', "Offer Name<span class='require'>*</span> ",array('class'=>"control-label col-lg-2"))) }}
                            <div class="in_upt">
                                <div class="dropp">
                                    <?php
                                    $cuisine_array = array(
                                        '' => 'Please Select',
                                        'all_menu' => 'On all menu',
                                        'all_menu_above' => 'On all menu on orders above ' . CURR,
                                        'all_item' => 'On selected items',
                                        'all_item_above' => 'On selected items on orders above ' . CURR
                                    );
                                    ?>
                                    {{ Form::select('name', $cuisine_array, $offerdata->offer_name, array('class' => 'form-control required', 'id'=>'offer_name')) }}
                                </div>
                            </div>
                        </div>

                        <div id="item_fieds_offer" style="display: {{$offerdata->offer_name == 'all_item' || $offerdata->offer_name == 'all_item_above'  ? 'block':'none'}}">
                            <div class="form_group">
                                {{ HTML::decode(Form::label('item_name_text', "Items <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::text('item_name_text', $offerdata->item_name_text,  array('readonly'=>'readonly','class' => 'required form-control','id'=>"item_name_text"))}}
                                </div>
                                {{ Form::hidden('item_name', '', array('id' => 'item_name')) }}
                            </div>
                        </div>
                        <div id="above_price" style="display: {{$offerdata->offer_name == 'all_item' || $offerdata->offer_name == 'all_item_above'||$offerdata->offer_name =='all_menu_above'  ? 'block':'none'}}">
                            <div class="form_group">
                                {{ HTML::decode(Form::label('above_price', "Above Price(".CURR.")<span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::text('above_price', $offerdata->above_price,  array('class' => 'required positiveNumber number form-control','id'=>"above_price"))}}
                                </div>

                            </div>
                        </div>


                        <div class="form_group">
                            {{ HTML::decode(Form::label('note', "Note <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                            <div class="in_upt">
                                {{  Form::textarea('note', $offerdata->note,  array('readonly'=>'readonly','class' => 'form-control required','id'=>"note" ,"maxlength"=>"250"))}}
                            </div>
                        </div>
                        <div class="calendar_row">
                            <div class="form_group">



                                <div class="radio_div">
                                    {{ Form::checkbox('days', 'All Day', $offerdata->days?true:false, ['disabled'=>'disabled','id' => 'days', 'class'=>'first-choice']) }}
                                    <label for="days">All Day</label>
                                </div>



                            </div>
                            <div class="form_group">
                                <label class="sr-only" for="search_to">Offer Period</label>
                                {{ Form::text('start_date', $offerdata->start_date, array('disabled'=>'disabled','class' => 'required search_fields form-control searchDate','placeholder'=>"Start Date",'id'=>'searchByDateFrom',"data-date"=>"", "data-date-format"=>"yyyy-mm-dd", "data-link-field"=>"dtp_input2", "data-link-format"=>"yyyy-mm-dd",'onkeypress'=>"return false")) }}
                            </div>
                            <div class="form_group selectt offer_time_slot" style="display:{{$offerdata->days?'none':'block'}}">

                                <div class="dropp">
                                    <?php
                                    $start_time = array();
                                    $enddate = strtotime(date('Y-m-d') . '24:00');
                                    $startdate = strtotime(date('Y-m-d') . '00:00');
                                    $min = strtotime(30);
                                    for ($i = $startdate; $i < $enddate; ($i = strtotime('+30 minutes', $i))) {
                                        $time = date('H:i', $i);
                                        $start_time[$time] = $time;
                                    }
                                    ?>
                                    {{ Form::select('start_time', $start_time, $offerdata->start_time, array('disabled'=>'disabled','class' => 'form-control required', 'id'=>'start_time')) }}
                                </div>

                            </div>
                            <div class="form_group">
                                <label class="sr-only" for="search_end">End Date</label>
                                {{ Form::text('end_date', $offerdata->end_date, array('disabled'=>'disabled','class' => 'required search_fields form-control searchDate','placeholder'=>"End Date",'id'=>'searchByDateTo',"data-date"=>"", "data-date-format"=>"yyyy-mm-dd", "data-link-field"=>"dtp_input2", "data-link-format"=>"yyyy-mm-dd",'onkeypress'=>"return false")) }}
                            </div>

                            <div class="form_group selectt offer_time_slot" style="display:{{$offerdata->days ? 'none':'block'}}">

                                <div class="dropp">

                                    {{ Form::select('end_time', $start_time, $offerdata->end_time, array('disabled'=>'disabled','class' => 'form-control required', 'id'=>'end_time')) }}
                                </div>

                            </div></div>



                        <div class="form_group">
                            <label class="">Offer Food Service<span class='require'>*</span></label>     
                            <div class="in_upt">
                                 <?php
                                $offer_service = '';
                                $offer_service = explode(',', $offerdata->service_visibility);
                                $ij=1;
                                foreach ($offer_service as $offer_services) {
                                    ?>

                                    <div class="radio">
                                        {{ Form::checkbox('service_visibility[]',$offer_services,TRUE, ['id' => 'first-choice'.$ij, 'class'=>'first-choice required','disabled' => 'disabled']) }}
                                        <label for="first-choice{{$ij}}">{{$offer_services ? ucfirst($offer_services):''}}</label>
                                    </div> 
                                    
                                
                                    <?php
                                    $ij++;
                                }
                                ?>
                                
<!--                                <div class="radio">
                                    <input type="radio" name="radio-group" id="first-choice" value="First Choice" />
                                    {{ Form::checkbox('service_visibility[]', 'Reservations', $offerdata->days?true:false, ['disabled'=>'disabled','id' => 'first-choice', 'class'=>'first-choice required']) }}
                                    <input name="service_visibility[]" <?php echo in_array('Reservations', explode(',', $offerdata->service_visibility)) ? 'checked="checked"' : ''; ?> id="first-choice" type="checkbox" value="Reservations" class="required" disabled="disabled">
                                    <label for="first-choice">Reservations</label>
                                </div>

                                <div class="radio">
                                    <input type="radio" name="radio-group" id="second-choice" value="Second Choice" />
                                    {{ Form::checkbox('service_visibility[]', 'Delivery & Pickup', $offerdata->days?true:false, ['disabled'=>'disabled','id' => 'second-choice', 'class'=>'first-choice required']) }}
                                    <input name="service_visibility[]" <?php echo in_array('Delivery', explode(',', $offerdata->service_visibility)) ? 'checked="checked"' : ''; ?> id="second-choice" type="checkbox" value="Delivery & Pickup" class="required" disabled="disabled">
                                    <label for="second-choice">Delivery</label>
                                </div>-->


                            </div>  
                        </div>

                        <div class="form_group selectt">
                            {{ HTML::decode(Form::label('allocate', "Offer Allocated<span class='require'>*</span> ",array('class'=>"control-label col-lg-2"))) }}
                            <div class="in_upt">
                                <div class="dropp">
                                    <?php
                                    $start_time = array('' => 'Please Select');
                                    for ($i = 1; $i <= 20; $i++) {
                                        $start_time[$i] = $i;
                                    }
                                    ?>
                                    {{ Form::select('allocate', $start_time, $offerdata->allocate, array('class' => 'form-control required', 'id'=>'allocate')) }}
                                </div>
                            </div>
                        </div>

                        <div class="form_group input_bxxs">
                            <label>&nbsp;</label>
                            <div class="in_upt in_upt_res">
                                {{ html_entity_decode(HTML::link(HTTP_PATH.'offer/manageoffer', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                {{ Form::submit('Submit', array('class' => "btn btn-primary",'id'=>'bubbb')) }}
                                
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="popup fixed-width orderview-window" style="display:none">
    <div class="ligd">   
        <div class="wrapper_login">

            <div class="order_pop">
                <span class="button b-close">
                    <span>X</span>        
                </span> 
                <!--<form id="menuitem" method="post">-->
                {{ Form::open(array('url'=>'#', 'method' => 'post', 'id' => 'menuitem', 'class'=>"cmxform form-horizontal tasi-form form")) }}
                <?php
                $records = DB::table('menu_item')->select('menu_item.*')
                                ->where('menu_item.user_id', Session::get("user_id"))->orderBy('menu_item.id', 'desc')->lists('item_name', 'id');
                $cuisine_array = array(
                    '' => 'Please Select Items',
                );

                $records = $cuisine_array + $records;
                $array = explode(',', $offerdata->item_name);
//                 echo'<pre>'; print_r($records);
                ?>

                <div class="form_group selectt">
                    {{ HTML::decode(Form::label('menu_items', "Select Items <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                    <div class="in_upt">
                        {{ Form::select('menu_items', $records, $array, array('multiple'=>'multiple','class' => 'form-control required items_popup', 'id'=>'menu_items')) }}
                    </div>
                </div>
                <div class="form_group input_bxxs">
                    <label>&nbsp;</label>
                    <div class="in_upt in_upt_res">
                        {{ Form::submit('Submit', array('class' => "btn btn-primary",'id'=>'bubbb')) }}
                        {{ Form::reset('Cancel', array('class' => "btn btn-default b-close")) }}
                    </div>
                </div>
                <!--</form>-->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#type").change(function () {
            var selection = $("#type").val();
            if (selection == 'currency') {
                $("#items").html('<input type="text" name="offer" id="offer" class="required form-control positiveNumber">');
            } else {
                var select = '<div class="dropp"><select name="offer" id="type" class="form-control "><option selected="selected" value="">Please Select</option>';
                for (var i = 10; i < 100; (i = i + 10)) {
                    select += '<option  value="' + i + '">' + i + '%</option>';
                }
                select += '</select><div>';
                $("#items").html(select);
            }
        });
    });
</script>
<script>
    $(document).on("click", ".b-close", function () {
        $('.orderview-window').bPopup().close();
    });

    $(document).on("change", "#offer_name", function () {
        $("#above_price").hide();
        $("#item_fieds_offer").hide();
        var offer_name = $("#offer_name").val();
        if (offer_name == 'all_item' || offer_name == 'all_item_above') {
            $("#item_fieds_offer").show();
            $('.orderview-window').bPopup({
                easing: 'easeOutBack', //uses jQuery easing plugin
                speed: 700,
                modalClose: false,
                transition: 'slideBack',
                transitionClose: "slideIn",
                modalColor: false,
            });
        }
        if (offer_name == 'all_menu_above' || offer_name == 'all_item_above') {
            $("#above_price").show();
        }
    });
</script>
@stop

