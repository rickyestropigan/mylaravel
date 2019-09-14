<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-body">
            {{ Form::open(array('url' => '', 'method' => 'post', 'id' => 'offeredit', 'autocomplete'=>'off','files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
            <div class="pop_form">
                <div class="center_txt">Edit Offer</div>

                <div class="pop_field">
                    <label>offer(%)</label> 
                    <div class="two_wrap">
                        <div class="input_filed">
                            {{  Form::text('offer', $offerdata->discount,  array('class' => 'required form-control positiveNumber number offervalid','id'=>"offer"))}}
                            <span class="sett" style="float: right;">%</span>
                        </div>
                    </div>
                    <span class="note">Offers should be 10% and above in order to be active.</span>
                </div>

                <div class="pop_field" style="height: 97px;">
                    <label>Menu Item</label> 
                    <div class="two_wrap">
                        <div class="input_filed selcetdrop drop_down"><span>
                                <?php
                                $records = DB::table('menu_item')->select('menu_item.*')
                                                ->where('menu_item.user_id', Session::get("user_id"))->orderBy('menu_item.id', 'desc')->lists('item_name', 'id');

                                $cuisine_array = array();
                                $records = $cuisine_array + $records;
//                 echo'<pre>'; print_r($records);
                                $exp = explode(',', $offerdata->item_name);
                                if (count($exp) == count($records)) {
                                    $men_all = 1;
                                } else {
                                    $men_all = 0;
                                }
                                ?>
                                {{ Form::select('menu_items[]', $records, $exp, array('multiple'=>'multiple', 'onclick'=>'checkfunc()','id'=>'menus','class' => 'required','style'=>'height: 97px;', 'onclick'=>"$('#offeredit.menu').removeClass('active')")) }}

                            </span></div></div>
                    <div class="small_box "><a class="menu" id="select_all" href="javascript:void('0')">All Menu</a></div>
                    <input type="hidden" name="menu" value="<?php echo $men_all; ?>" id="menu">
                </div>
                <script>
                    $('#select_all').click(function () {
                        $(this).parent().addClass('active');

                        $('#menus option').prop('selected', true);
                    });
                </script>

                <div class="pop_field">
                    <label>Above Price</label> 
                    <div class="two_wrap">
                        <div class="input_filed">{{  Form::text('above_price', $offerdata->above_price,  array('class' => 'number','id'=>"above_price"))}}<span class="sett" style="float: right;">$</span></div></div>
                </div>
                <?php
                $start_date = date('m/d/Y', strtotime($offerdata->start_date));
                $end_date = date('m/d/Y', strtotime($offerdata->end_date));
                ?>
                <div class="pop_field">
                    <label>Date</label> 
                    <div class="two_wrap">
                        <div class="input_filed half half_input calendar">{{ Form::text('startdate', $start_date, array('class' => 'required','id'=>'startdateedit','readonly'=>true ,'placeholder'=>"","data-date-format"=>"mm/dd")) }}<img onclick='$("#startdateedit").focus();' src="{{ URL::asset('public/img/front') }}/calendar_xs.png"></div>
                        <div class="input_filed half half_input calendar">{{ Form::text('enddate', $end_date, array('class' => 'required','id'=>'enddateedit', 'readonly'=>true,'placeholder'=>"","data-date-format"=>"mm/dd")) }}<img onclick='$("#enddateedit").focus();' src="{{ URL::asset('public/img/front') }}/calendar_xs.png"></div></div>
                </div>

                <div class="pop_field">
                    <label>Time</label> 
                    <div class="slider_wrap">
                        <span id="time_show">
                            <div class="start_time"><?php echo date("g:i a", strtotime($offerdata->start_time)); ?></div>
                            <div class="end_time"><?php echo date("g:i a", strtotime($offerdata->end_time)); ?></div>
                        </span>
                        <div class="demo-output">
                            <?php 
                            $starttm = explode(':', $offerdata->start_time);
                            $endtm = explode(':', $offerdata->end_time);
                            ?>
                            <input class="range-slider" name="timegap" id="range_tm" type="hidden" value="<?php echo $starttm[0]; ?>,<?php echo $endtm[0]; ?>"/>
                        </div>   
                    </div>
                    <div class="small_box"><a class="days" id="all_days" href="javascript:void('0')">All Day</a></div>
                    <script>
                        $('#all_days').click(function () {
                            $(this).parent().addClass('active');
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
                    <input type="hidden" name="days" value="" id="days">
                </div>

                <div class="pop_field">
                    <label>No. of Coupons</label> 
                    <div class="two_wrap">
                        <div class="input_filed">{{ Form::text('coupon', $offerdata->allocate, array('minlength'=>'1','maxlength'=>'2','class' => 'number','id'=>'coupons','placeholder'=>"")) }}</div>
                    </div>
                    <div class="small_box"><a data-title="unlimited" class="unlimited" href="javascript:void('0')">Unlimited</a></div>
                    <?php
                    if ($offerdata->allocate == 0) {
                        $unlimit = 1;
                    } else {
                        $unlimit = 0;
                    }
                    ?>
                    <input type="hidden" name="unlimited" value="<?php echo $unlimit; ?>" id="unlimited">
                </div>



                <div class="pop_field three_box">
                    <label>Offered on</label> 
                    <?php $services = explode(",", trim($offerdata->service_visibility)); ?>
                    <?php
                    $offerded = explode(',', $userData->service_offered);
                    ?>
                    <?php if (in_array('Delivery', $offerded)) { ?>
                        <div class="input_filed thirhalf offerd_on <?php
                        if (in_array('Delivery', $services)) {
                            echo 'active';
                        }
                        ?>"> <a data-title="delivery" class="options" href="javascript:void('0')">Delivery</a></div>
                         <?php } ?>
                         <?php if (in_array('Table reservations', $offerded)) { ?>
                        <div class="input_filed thirhalf margin_left offerd_on <?php
                        if (in_array('reservation', $services)) {
                            echo 'active';
                        }
                        ?>"><a data-title="reservation" class="options" href="javascript:void('0')">reservation</a></div>
                         <?php } ?>
                         <?php if (in_array('Pickup', $offerded)) { ?>
                        <div class="input_filed thirhalf margin_left offerd_on <?php
                        if (in_array('Pickup', $services)) {
                            echo 'active';
                        }
                        ?>"><a data-title="pickup" class="options" href="javascript:void('0')">Pickup</a></div>
                         <?php } ?>
                    <input type="hidden" id="service_visibility" value="<?php echo trim($offerdata->service_visibility); ?>" name="service_visibility[]">
                </div>
                <div class="pop_field">
                    <label></label> 
                    <div class="two_wrap">
                        <div class="input_filed textarea">
                            {{  Form::textarea('note', $offerdata->note,  array('class' => 'form-control','id'=>"note" ,"maxlength"=>"250"))}}
                            Disclaimer 
                        </div>
                    </div>
                </div>

                <div class="pop_btn full_btmn">
                    <input type="hidden" value="<?php echo $offerdata->slug ?>" name="slug"/>
                    <input type="hidden" value="<?php echo $offerdata->id ?>" name="offerid"/>
                    <input type="submit" class="same_btn" id="" value="Update">

                    <a class="same_btn default_btn" id="alctl" href="#" data-dismiss="modal">Cancel</a>    

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

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

    $(document).ready(function () {
        $.validator.addMethod("offervalid", function (value, element) {
            // allow any non-whitespace characters as the host part
            return  (Number(value) >= 10);
        }, 'Offers should be 10% and above in order to be active.');
    });


function checkfunc(obj){
        var j;
        for (var i = 0; i < obj.options.length; i++) {
            if(obj.options[i].selected === true){
                j++;
            }
        }
        if(j==<?php echo count($records); ?>){
            $('#menual').val('1');
        }
        else
        {
            $('#menual').val('0');
        }
    }
    $("#offeredit").validate({
        submitHandler: function (form) {

            this.checkForm();



            if (this.valid()) { // checks form for validity



                var form_data = $(form).serialize();
                var post_url = '<?php echo HTTP_PATH . 'offer/editoffer'; ?>';
                $.ajax({
                    url: post_url,
                    type: 'POST',
                    data: form_data
                }).done(function (response) {
                    //alert(response);
                    var suc = response.trim();
                    if (suc == 'success') {
                        document.getElementById("offeredit").reset();
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
                    }
                });
                return false;

            } else {
                return false;
            }
        }
    });

    $('#select_all').click(function () {
        $('#menus option').prop('selected', true);
    });
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
    $('.offerd_on').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            var service_vis = $('#service_visibility').val();
            var sel = $(this).text();
            var result = removeValue(service_vis, sel, ",");
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
</script>