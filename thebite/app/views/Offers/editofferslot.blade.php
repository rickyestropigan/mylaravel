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
                    <div class="tatils"> <span class="personal">Edit {{$offers_slot->discount}} {{$offers_slot->type == 'percentage' ? '%':CURR}} OFF on Time {{date('h:i:s A',strtotime($offers_slot->start_time))}} - {{date('h:i:s A',strtotime($offers_slot->end_time))}}</span></div>
                    <div class="pery">
                        <div id="formloader" class="formloader" style="display: none;">
                        </div>
                        {{ View::make('elements.frontEndActionMessage')->render() }}
                        {{ Form::model($offers_slot,array('url' => '/offer/editofferslot/'.$offers_slot->slug, 'method' => 'post', 'id' => 'myform', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
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
                                    {{ Form::select('type', $cuisine_array,$offers_slot->type, array('class' => 'form-control required', 'id'=>'type')) }}
                                </div>
                            </div>
                        </div>
                        <div class="form_group">    
                            {{ HTML::decode(Form::label('offer', "Offer <span class='require'>*</span> ",array('class'=>"control-label col-lg-2"))) }}
                            <?php if ($offers_slot->type == 'currency') { ?>
                                <div id="items" class="in_upt">
                                    {{  Form::text('offer', $offers_slot->discount,  array('class' => 'required form-control positiveNumber','id'=>"offer"))}}
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
                                    {{ Form::select('offer', $start_time, $offers_slot->discount, array('class' => 'form-control required', 'id'=>'offer')) }}
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
                                    {{ Form::select('name', $cuisine_array,$offers_slot->offer_name, array('class' => 'form-control required', 'id'=>'offer_name')) }}
                                </div>
                            </div>
                        </div>
                        <div id="item_fieds_offer" style="display:{{$offers_slot->offer_name == 'all_item' || $offers_slot->offer_name == 'all_item_above'  ? 'block':'none'}}">
                            <div class="form_group">
                                {{ HTML::decode(Form::label('item_name_text', "Items <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::text('item_name_text', Input::old("item_name_text"),  array('readonly'=>'readonly','class' => 'required form-control','id'=>"item_name_text"))}}
                                </div>
                                {{ Form::hidden('item_name', '', array('id' => 'item_name')) }}
                            </div>
                        </div>
                        <div id="above_price" style="display: {{$offers_slot->offer_name == 'all_item' || $offers_slot->offer_name == 'all_item_above'||$offers_slot->offer_name =='all_menu_above'  ? 'block':'none'}}">
                            <div class="form_group">
                                {{ HTML::decode(Form::label('above_price', "Above Price(".CURR.")<span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                                <div class="in_upt">
                                    {{  Form::text('above_price', Input::old("above_price"),  array('class' => 'required positiveNumber number form-control','id'=>"above_price"))}}
                                </div>
                            </div>
                        </div>
                        
                        <div class="pop_field">
                                <label>Visibility</label> 
                                <ul class="vis_mn" style="display: initial;">
                                    <li class="input_filed thirhalf <?php if($offers_slot->status==1){ echo 'active'; } ?>">
                                        <a href="javascript:void(0)" class="">On</a>
                                    </li>
                                    <li class="input_filed thirhalf <?php if($offers_slot->status==0){ echo 'active'; } ?>">
                                        <a href="javascript:void(0)" class="">Off</a>
                                    </li>
                                </ul>
                                <input type="hidden" class="required" value="<?php echo $offers_slot->status; ?>" name="status" id="editvisibility" />
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
                                    {{ Form::select('allocate', $start_time, $offers_slot->allocate, array('class' => 'form-control required', 'id'=>'allocate')) }}
                                </div>
                            </div>
                        </div>



                        <div class="form_group input_bxxs">
                            <label>&nbsp;</label>
                            <div class="in_upt in_upt_res">
                                {{ html_entity_decode(HTML::link(HTTP_PATH.'offer/manageoffer', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                {{ Form::submit('Save', array('class' => "btn btn-primary",'id'=>'bubbb')) }}
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

                {{ Form::open(array('url'=>'#', 'method' => 'post', 'id' => 'menuitem', 'class'=>"cmxform form-horizontal tasi-form form")) }}
                <?php
                $records = DB::table('menu_item')->select('menu_item.*')
                                ->where('menu_item.user_id', Session::get("user_id"))->orderBy('menu_item.id', 'desc')->lists('item_name', 'id');
                $cuisine_array = array(
                    '' => 'Please Select Items',
                );

                $records = $cuisine_array + $records;
//                 echo'<pre>'; print_r($records);
                ?>
                <?php
                $array = explode(',', $offers_slot->item_name);
//                $aray_data = array_combine(explode(',', trim($offers_slot->item_name_text, ',')), explode(',', $offers_slot->item_name));
                ?>
                <div class="form_group selectt">
                    {{ HTML::decode(Form::label('menu_items', "Select Items <span class='require'>*</span>",array('class'=>"control-label col-lg-2"))) }}
                    <div class="in_upt">
                        {{ Form::select('menu_items', $records,$array,array('multiple'=>'multiple','class' => 'form-control required items_popup', 'id'=>'menu_items')) }}
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
    
    $('#editmenu').on('click','.pop_field ul.vis_mn li', function () {
    $(this).parent().find('li.active').removeClass('active');
    $(this).addClass('active');

            var act_val = $(this).parent().find('li.active').text();
            if ($.trim(act_val) == 'On') {
                $('#editvisibility').val('1');
            } else if ($.trim(act_val) == 'Off') {
                $('#editvisibility').val('0');
            }
        });
</script>
@stop

