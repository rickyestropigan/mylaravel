@extends('layouts.default')
@section('content')
<script src="{{ URL::asset('public/js/front/sweet-alert.min.js') }}"></script>
{{ HTML::style('public/css/front/sweet-alert.css'); }}
<div class="botm_wraper">
    @include('elements/left_menu')
    <div class="right_wrap">
        <div class="right_wrap_inner">
            <div class="informetion informetion_new">
                {{ View::make('elements.actionMessage')->render() }}  
                <div class="informetion_top">
                    <div class="tatils">
                        <div class="link-button"> 
                            <?php
                            echo html_entity_decode(HTML::link('offer/addoffer', '<i class="fa  fa-plus"></i> Add Offer', array('title' => 'Add Offer', 'class' => 'btn btn-primary ', 'escape' => false)));
                            ?>
                        </div>
                    </div>
                    <div class="search-area">
                        <div class="pery">
                            <?php
                            if (!$records->isEmpty()) {
                                ?>
                                <div class="table_scroll user_newlist">
                                    <?php
                                    $i = 1;
                                    $cuisine_array = array(
                                        '' => 'Please Select',
                                        'all_menu' => 'On all menu',
                                        'all_menu_above' => 'On all menu on orders above ',
                                        'all_item' => 'On selected items',
                                        'all_item_above' => 'On selected items on orders above '
                                    );
                                    foreach ($records as $data) {
//                                        echo'<pre>';print_r($data);die;
                                        if ($i % 2 == 0) {
                                            $class = 'colr1';
                                        } else {
                                            $class = '';
                                        }
                                        ?>
                                        <div class="informetion_bxes new_table">
                                            <div class="table_dcf">
                                                <div class="tr_tables flip " >
                                                    <div class="td_tables wdth_title left_align "> <i class="fa fa-sort-down" data-offer_id="{{$data->id}}" aria-hidden="true"></i> Offer Duration</div>
                                                    <div class="td_tables wdth_title left_align">{{date('h:i A',strtotime($data->start_time)).'-'.date('h:i A',strtotime($data->end_time))}}</div>
                                                    <div class="td_tables wdth_title left_align big_width">From {{date('m/d/Y',strtotime($data->start_date)).' to '.date('m/d/Y',strtotime($data->end_date))}}</div>
                                                    <div class="td_tables wdth_title">
                                                        <span class="full">{{$data->status?'Online':'Offline'}}</span>  
                                                        <label class="switch">
                                                            {{ Form::checkbox('visibility', $data->status, $data->status ? TRUE:FALSE,['id' => '', 'class'=>'checkbox','data-offer_id' => $data->id,'data-type'=>'offer','data-href' => HTTP_PATH.'offer/changevisiblity']) }}
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                    <div class="td_tables">
                                                        {{ html_entity_decode(HTML::link(HTTP_PATH.'offer/deleteoffer/'.$data->slug, '<i class="fa fa-trash-o" aria-hidden="true"></i>', array('class' => 'delete samee','title'=>'Delete Offer','onclick'=>"return confirm('Are you sure want to delete?')"), true)) }}                                                   
                                                        {{ html_entity_decode(HTML::link(HTTP_PATH.'offer/editoffer/'.$data->slug, '<i class="fa fa-pencil" aria-hidden="true"></i>', array('class' => 'edit samee ','title'=>'Edit Offer'), true)) }}                                                   
                                                    </div>

                                                </div> 
<!--                                                <div id="offer_table_{{$data->id}}">-->
                                                    <?php
                                                    $records = DB::table('offers_slot')->select('offers_slot.*')
                                                                    ->where('offers_slot.offer_id', $data->id)->orderBy('offers_slot.id', 'asc')->get();

                                                    if ($records) {

                                                        $i = 1;
                                                        foreach ($records as $data1) {
                                                            if ($i % 2 == 0) {
                                                                $class = 'colr1';
                                                            } else {
                                                                $class = '';
                                                            }
                                                            ?>
                                                            <div class="tr_tables2 panel_{{$data->id}}" style="display:none">
                                                                <div class="td_tables2 wdth_title left_align">
                                                                    <span>
                                                                        {{$data1->discount}} {{$data1->type == 'percentage' ? '%':CURR}} Off  
                                                                    </span>
                                                                    <div class="detaill_food">
                                                                        <?php
                                                                        echo $cuisine_array[$data1->offer_name];
                                                                        if ($data1->offer_name == 'all_menu_above' || $data1->offer_name == 'all_item_above') {
                                                                            echo CURR . $data1->above_price;
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <div class="td_tables2 wdth_title">
                                                                    <span>
                                                                        {{date('h:i A',strtotime($data1->start_time)).'-'.date('h:i A',strtotime($data1->end_time))}}
                                                                    </span>
                                                                    <div class="detaill_food">Offer Hours</div>
                                                                </div>

                                                                <div class="td_tables2 wdth_title big_width">
                                                                    <span>
                                                                        From {{date('m/d/Y',strtotime($data->start_date)).' to '.date('m/d/Y',strtotime($data->end_date))}}
                                                                    </span> 
                                                                    <div class="detaill_foodleft">Offer Date</div>
                                                                  <span>  {{$data1->allocate ? $data1->allocate:'0' }}</span> 
                                                                    <div class="detaill_foodright">Offer allocate</div>

                                                                </div>
                                                                <div class="td_tables2 wdth_title"> 
                                                                    <label class="switch">
                                                                        {{ Form::checkbox('visibility', $data1->status, $data1->status ? TRUE:FALSE, ['id' => '', 'class'=>'checkbox','data-offer_id' => $data1->id,'data-type'=>'offer_slot','data-href' => HTTP_PATH.'offer/changevisiblity']) }}
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                                <div class="td_tables2"> 
                                                                    {{ html_entity_decode(HTML::link(HTTP_PATH.'offer/editofferslot/'.$data1->slug, '<i class="fa fa-pencil" aria-hidden="true"></i>', array('class' => 'edit samee ','title'=>'Edit Offer Slot'), true)) }}
                                                                </div>

                                                            </div>
                                                            <?php
                                                            $i++;
                                                        }
                                                    }
                                                    ?>
                                                <!--</div>-->
                                            </div>

                                        </div>    
                                        <?php
                                        $i++;
                                    }
                                } else {
                                    ?>
                                    <div class="no-record">
                                        No records available
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
        <style>
            .switch {
                position: relative;
                display: inline-block;
                width: 60px;
                height: 34px;
            }

            .switch input {display:none;}

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #CC202E;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 26px;
                width: 26px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked + .slider {
                background-color: #259E39;
            }

            input:focus + .slider {
                box-shadow: 0 0 1px #259E39;
            }

            input:checked + .slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
            }

            /* Rounded sliders */
            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }
        </style>

        <script>
$(document).ready(function () {
    $('.checkbox').click(function (event) {

        var offer_id = $(this).data('offer_id');
        var type = $(this).data('type');
        var visible = '';
        if ($(this).prop("checked") == true) {
            visible = '1';
        } else if ($(this).prop("checked") == false) {
            visible = '0';
        }

//                    event.preventDefault();
        // Target url
        var target = $(this).data('href');

        if (target) {

//                        event.preventDefault();

            if (!target || target == '')
            {
                // Page url without hash
                target = document.location.href.match(/^([^#]+)/)[1];
            }

            var data = {
                target: target,
                offer_id: offer_id,
                visible: visible,
                type: type
            };

            // Send
            $.ajax({
                url: target,
                dataType: 'json',
                type: 'POST',
                data: data,
                success: function (data, textStatus, XMLHttpRequest)
                {
                    //  alert(data);
                    if (data.valid)
                    {
                        swal({
                            title: "Success",
                            text: data.smessage,
                            type: "success",
                            html: true
                        }, function () {
                            window.location.reload();
                        });

                        $(".all_bg").hide();

                    } else
                    {
                        // Message
                        swal({
                            title: "Sorry!",
                            text: data.message,
                            type: "error",
                            html: true
                        });
                        $(".all_bg").hide();
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    // Message
                    swal({
                        title: "Sorry!",
                        text: "Error while contacting server, please try again",
                        type: "error",
                        html: true
                    });
                    $(".all_bg").hide();
                }
            });
            // Message
            $(".all_bg").show();
        } else {
//                        event.preventDefault();
        }
    });
});
        </script>

        <script>
            $(document).ready(function () {
                $(".fa-sort-down").click(function () {
                    var offerid = $(this).data('offer_id');
//                    alert(offerid);
                    $(".panel_"+ offerid).toggle();
                });
            });
        </script>



        @stop


