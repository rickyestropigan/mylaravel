
<div class="modal-dialog">
    <?php
    $qur = DB::table('offers_slot');
    $offer_slots = $qur->where('offer_id', $record->id)->select('offers_slot.*')->orderBy('offers_slot.start_time', 'ASC')->get();
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
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-body">
            <div class="pop_form" style="text-align: center;">
                <div class="center_txt">Edit <?php echo $offerslotdata->discount; ?> % OFF on Time <?php echo date('g:i A', strtotime($offerslotdata->start_time)); ?> - <?php echo date('g:i A', strtotime($offerslotdata->end_time)); ?></div>
                <span data-color="#4AD57A"  data-slot="<?php echo $offerslotdata->id; ?>" data-offer="<?php echo $record->discount; ?>" data-obj="" class="changecolor" style="background:#4AD57A; "></span><div class="textt" style="margin-right: 20px;"><?php echo $record->discount; ?>% Off</div>
                <?php
                foreach ($color as $colorAtr) {
                    ?>
                    <span data-color="<?php echo $colorAtr['color']; ?>" data-slot="<?php echo $offerslotdata->id; ?>" data-offer="<?php echo $colorAtr['offer']; ?>" data-obj="" class="changecolor" style="background:<?php echo $colorAtr['color']; ?>"></span>   
                    <div class="textt" style="margin-right: 20px;"><?php echo $colorAtr['offer']; ?>% Off</div>
                <?php } ?>
                <input type="hidden" name="tmslot" id="tmslot" value="">
                <input type="hidden" name="upslcolr" id="upslcolr" value="">
            </div>

            {{ Form::open(array('url' => '', 'method' => 'post', 'id' => 'offerslotedit', 'files' => true,'class'=>"cmxform form-horizontal tasi-form form")) }}
            <div class="pop_form">
                <div class="pop_field">
                    <label>offer</label> 
                    <div class="two_wrap">
                        <div class="input_filed">{{  Form::text('offer', $offerslotdata->discount,  array('class' => 'required form-control positiveNumber number offervalid','id'=>"offer"))}}
                            <span class="sett" style="float: right;">%</span>
                        </div>
                    </div>
                    <span class="note">Offers should be 10% and above in order to be active.</span>
                </div>
                <div class="pop_field">
                    <label>Menu Item</label> 
                    <div class="two_wrap">
                        <div class="input_filed selcetdrop drop_down"><span>
                                <?php
                                $records = DB::table('menu_item')->select('menu_item.*')
                                                ->where('menu_item.user_id', Session::get("user_id"))->orderBy('menu_item.id', 'desc')->lists('item_name', 'id');

                                $cuisine_array = array();
                                $records = $cuisine_array + $records;
//                 echo'<pre>'; print_r($records);
                                $exp = explode(',', $offerslotdata->item_name);
                                if (count($exp) == count($records)) {
                                    $men_all = 1;
                                } else {
                                    $men_all = '';
                                }
                                ?>
                                {{ Form::select('menu_items[]', $records, $exp, array('multiple'=>'multiple','id'=>'menu','class' => 'required')) }}

                            </span></div></div>
                    <div class="small_box"><a class="menu" id="select_all" href="javascript:void('0')">All Menu</a></div>
                    <input type="hidden" name="menu" value="<?php echo $men_all; ?>" id="menual">
                </div>
                <script>
                    $('#select_all').click(function () {
                        $('#menus option').prop('selected', true);
                    });
                </script>

                <div class="pop_field">
                    <label>Above Price</label> 
                    <div class="two_wrap">
                        <div class="input_filed">{{  Form::text('above_price', $offerslotdata->above_price,  array('class' => 'number','id'=>"above_price"))}}<span class="sett" style="float: right;">$</span></div></div>
                </div>
                <?php
                $start_date = date('d/m/Y', strtotime($offerslotdata->start_date));
                $end_date = date('d/m/Y', strtotime($offerslotdata->end_date));
                ?>

                <div class="pop_field">
                    <label>No. of Coupons</label> 
                    <div class="two_wrap">
                        <div class="input_filed">{{ Form::text('coupon', $offerslotdata->allocate, array('minlength'=>'1','maxlength'=>'2','class' => 'required number','id'=>'coupon','placeholder'=>"")) }}</div>
                    </div>
                    <div class="small_box"><a data-title="unlimited" class="unlimited" href="javascript:void('0')">Unlimited</a></div>
                    <?php
                    if ($offerslotdata->allocate == 0) {
                        $unlimit = 1;
                    } else {
                        $unlimit = 0;
                    }
                    ?>
                    <input type="hidden" name="unlimited" value="<?php echo $unlimit; ?>" id="unlimit">
                </div>
                <div class="pop_field">
                    <label>Visibility</label> 
                    <ul class="vis_mn" style="display: initial;">
                        <li class="input_filed thirhalf <?php
                        if ($offerslotdata->status == 1) {
                            echo 'active';
                        }
                        ?>">
                            <a href="javascript:void(0)" class="">On</a>
                        </li>
                        <li class="input_filed thirhalf <?php
                        if ($offerslotdata->status == 0) {
                            echo 'active';
                        }
                        ?>">
                            <a href="javascript:void(0)" class="">Off</a>
                        </li>
                    </ul>
                    <input type="hidden" class="required" value="<?php echo $offerslotdata->status; ?>" name="status" id="editvisibility" />
                </div>
                <div class="pop_field">
                    <label>Slot Color</label> 
                    <div class="two_wrap">
                        <?php if (!empty($offerslotdata->color)) { ?>
                            {{ Form::text('color', $offerslotdata->color, array('class' => '','id'=>'colorslot','placeholder'=>"")) }}
                        <?php } else { ?>
                            {{ Form::text('color', '#369F5C', array('class' => '','id'=>'colorslot','placeholder'=>"")) }}
                        <?php } ?>
                    </div>
                </div>

                <div class="pop_field">
                    <label></label> 
                    <div class="two_wrap">
                        <div class="input_filed textarea">
                            {{  Form::textarea('disclaimer', $offerslotdata->disclaimer,  array('class' => 'form-control','id'=>"offerslotdatadiscla" ,"maxlength"=>"250"))}}
                            Disclaimer 
                        </div>
                    </div>
                </div>

                <div class="pop_btn full_btmn">
                    <input type="hidden" value="<?php echo $offerslotdata->slug ?>" name="slug"/>
                    <input type="submit" id="offchange" data-obj="" class="same_btn" value="Update">

                    <a class="same_btn default_btn" id="altcan" href="#" data-dismiss="modal">Cancel</a>    

                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<script src="{{ URL::asset('public/js/spectrum.js') }}"></script>
{{ HTML::style('public/css/front2/spectrum.css') }}
<script>
                    $('#editcolorSlotModal').hide();
                    $("#colorslot").spectrum({
                        preferredFormat: "hex",
                        // color: "#369F5C"
                    });
                    $("#offerslotedit").validate({
                        submitHandler: function (form) {



                            this.checkForm();
                            //consol.log($(form).serialize());
//                            alert($(form).serialize());


                            if (this.valid()) { // checks form for validity
                                var form_data = $(form).serialize();
                                var post_url = '<?php echo HTTP_PATH . 'offer/editofferslot'; ?>';
                                $.ajax({
                                    url: post_url,
                                    type: 'POST',
                                    data: form_data
                                }).done(function (response) {
                                    //alert(response);
                                    var suc = response.trim();
                                    if (suc == 'success') {
                                        document.getElementById("offerslotedit").reset();
                                        var selected_date = $('#slected_date').val();
                                        var data = {
                                            current_dat: selected_date,
                                        }
                                        $.ajax({
                                            //editOfferSlotModal
                                            url: '<?php echo HTTP_PATH . 'nextoffer'; ?>',
                                            type: 'POST',
                                            data: data,
                                            dataType: 'html',
                                            success: function (result) {
//                                                console.log(result);
                                                $('#mnbx').html(result);
                                                $('.modal-backdrop').hide();
//                                                $('.all_bg_ldr').show();
                                            }
                                        });
                                        $('#altcan').trigger('click');
                                        $('#editOfferModal').hide();
                                    }
                                });
                                return false;
                            } else {
                                return false;
                            }
                        }

                    });
                    $('#offerslotedit').on('click', '#select_all', function () {
                        var almen = $('#menual').val();
                        if (almen == 1) {
                            $('#menu option').prop('selected', false);
                            $('#menual').val('0');
                        } else
                        {
                            $('#menu option').prop('selected', true);
                            $('#menual').val('1');
                        }
                    });
                    $('#offerslotedit').on('click', '.unlimited', function () {
                        if ($(this).parent().hasClass('active')) {
                            $(this).parent().removeClass('active');
                            $('#unlimit').val('0');
                            $('#coupon').val('');
                        } else
                        {
                            $(this).parent().addClass('active');
                            $('#unlimit').val('1');
                            $('#coupon').val('0');
                        }
                    });

                    $('#offerslotedit').on('click', '.pop_field ul.vis_mn li', function () {
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