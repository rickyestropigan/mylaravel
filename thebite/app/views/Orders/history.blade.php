@extends('layouts.default')
@section('content')

{{ HTML::style('public/css/front2/slick.css') }}
{{ HTML::style('public/css/front2/slick-theme.css') }}


<?php
$days_in_month = date('t');
$current_month = date('m');
$current_short_month = date('M');
$current_date = date('d');
$current_year = date('Y');
$initial_slide = 0;
//    echo '<pre>';
//    print_r($orders);
//    exit;
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
            <form action="" method="post" id="searchorder">
                <div class="search_field">
                    <i class="fa fa-search"></i>
                    <input type="text" placeholder="Search" id="searchkey" name="search"> 

                </div>  

                <div class="calendarfield">
                    <i><img src="{{ URL::asset('public/img/front') }}/calendar_xs.png"></i>  
                    <input type="text" name="daterange" id="daterange" placeholder="<?php echo $current_date . ' ' . $current_short_month; ?> - <?php echo $current_date . ' ' . $current_short_month; ?>">
                    <input type="hidden" name="altrange" id="altrange" value="<?php echo $current_year . '/' . $current_month . '/' . $current_date . ' - ' . $current_year . '/' . $current_month . '/' . $current_date; ?>"/>
                </div>
                <div class="search_btn toggle_btn">
                    <div class="btn-group" id="status" data-toggle="buttons">
                        <label for="selecorder" class="btn btn-default btn-on btn-xs active">
                            <input type="radio" id="selecorder" value="order" name="select_tabs"  checked="">Orders</label>
                        <label for="seleres" class="btn btn-default btn-off btn-xs ">
                            <input type="radio" id="seleres"  value="reserve"  name="select_tabs" >Reservations</label>
                    </div>
                </div>
                <div class="col-md-5">


                </div>
                <input type="submit" style="display:none">
            </form>
        </div>


        <!--        <div class="divider">
                    <span>21st jan, 2018</span>
                </div>-->
        <div id="menubx">
            <div id="tabbx">
                <div class="menu_box">

                    <?php
                    $i = 1;
                    $new = 0;
                    $confirm = 0;
                    $completed = 0;
                    $cancelled = 0;

                    if (!empty($orders) && count($orders) > 0) {
                        foreach ($orders as $order) {
                            if ($i % 2 == 0) {
                                $class = 'pull-right';
                            } else {
                                $class = '';
                            }
                            if ($order->status == 'Pending') {
                                $new = $new + 1;
                                $color = 'blue';
                                $but = 'blue_btn';
                                $btn_color = 'blue_btn';
                            }
                            if ($order->status == 'Confirm') {
                                $confirm = $confirm + 1;
                                $color = 'green';
                                $but = 'green_btn';
                                $btn_color = 'green_btn';
                            }
                            if ($order->status == 'Complete') {
                                $completed = $completed + 1;
                                $color = 'orange';
                                $but = 'orange_btn';
                                $btn_color = 'orange_btn';
                            }
                            if ($order->status == 'Cancel') {
                                $cancelled = $cancelled + 1;
                                $color = 'default';
                                $but = 'simple_btn_menu ';
                                $btn_color = '';
                            }

                            $RestaurantData = DB::table('users')
                                    ->select("users.*")
                                    ->where('users.id', $order->caterer_id)
                                    ->first(); // get cateter details
                            $userData = DB::table('users')
                                    ->select("users.*")
                                    ->where('users.id', $order->user_id)
                                    ->first(); // get user details
//print_r($cartItems);
                            $cartItems = DB::table('order_item')
                                    ->whereIn('menu_id', explode(',', $order->order_item_id))
                                    ->where('order_id', $order->id)
                                    ->get();
                            ?>
                            <div class="menu_block <?php echo $class . ' ' . $color; ?>  order_box">
                                <div class="menu_top_title">
                                    <span class="pull-left"><?php echo $order->first_name; ?> <?php echo substr($order->last_name, 0, 1); ?>.</span> <?php if ($order->status == 'Pending') { ?><a class="circle_btn" href="#">New</a><?php } ?>
                                    <span class="pull-right">$ <?php echo number_format((float) $order->total, 2, '.', ''); ?></span>
                                </div>
                                <?php
                                if ($order->address_id > 0) {
                                    $address = DB::table('addresses')
                                            ->select('addresses.*', 'areas.name as area_name', 'cities.name')
                                            ->leftjoin('cities', 'cities.id', '=', 'addresses.city')
                                            ->leftjoin('areas', 'areas.id', '=', 'addresses.area')
                                            ->where('addresses.id', $order->address_id)
                                            ->first();
                                }
                                if (!empty($address)) {
                                    ?>
                                    <div class="address"><?php echo $address->building . ' ' . $address->apartment . ' ' . $address->street_name . ', ' . $address->area_name . ' ' . $address->name ?></div>
                                <?php } else { ?>
                                    <div class="address">&nbsp;</div>
                                <?php } ?>
                                <?php if ($order->discount > 0) { ?>
                                    <div class="discnt"><?php echo $order->discount; ?>% off on this order</div>
                                <?php } else { ?>
                                    <div class="discnt">&nbsp;</div>
                                <?php } ?>

                                <div class="tabb tabb_width history_width">
                                    <div class="offer_tabb">
                                        <span>Promised by:</span>
                                        <?php
                                        if ($color == 'bluee') {
                                            ?>
                                            <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/blueclock.png"></i><?php echo date('h:i A', strtotime($order->delivery_date . " +30 minutes")); ?></a>
                                            <?php
                                        } elseif ($color == 'green') {
                                            ?>
                                            <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/greenclock.png"></i><?php
                                                if (!empty($order->pickup_time)) {
                                                    echo date('h:i A', strtotime($order->pickup_time));
                                                } else {
                                                    echo 'Not Set';
                                                }
                                                ?></a>
                                                <?php
                                            } elseif ($color == 'orange') {
                                                ?>
                                            <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/orangeclock.png"></i><?php
                                                if (!empty($order->pickup_time)) {
                                                    echo date('h:i A', strtotime($order->pickup_time));
                                                } else {
                                                    echo 'Not Set';
                                                }
                                                ?></a>
                                            <?php } elseif ($color == 'default') { ?>
                                            <a class="tab_btn" href="#"><i><img src="{{ URL::asset('public/img/front') }}/gray_clcock.png"></i><?php
                                                if (!empty($order->gray_clcock)) {
                                                    echo date('h:i A', strtotime($order->pickup_time));
                                                } else {
                                                    echo 'Not Set';
                                                }
                                                ?></a>
                                            <?php } ?>
                                    </div> 
                                    <div class="offer_tabbright">


                                        <div class="offer_tabb">
                                            <span>Order Type:</span>
                                            <?php
                                            if ($color == 'bluee') {
                                                ?>
                                                <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/blue_delivery.png"></i><?php echo $order->delivery_type; ?></a>
                                                <?php
                                            } elseif ($color == 'orange') {
                                                ?>
                                                <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/orangedeleiber.png"></i><?php echo $order->delivery_type; ?></a>
                                                <?php
                                            } elseif ($color == 'green') {
                                                ?>
                                                <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/greenhouse.png"></i><?php echo $order->delivery_type; ?></a>
                                            <?php } elseif ($color == 'default') { ?>
                                                <a class="tab_btn" href="javascript:void(0);"><i><img src="{{ URL::asset('public/img/front') }}/deliver_icon.png"></i><?php echo $order->delivery_type; ?></a>
                                            <?php } ?>
                                        </div>


                                    </div>

                                </div>

                                <?php
                                $newstatus = '';
                                if ($order->status == 'Pending') {
                                    $newstatus = 'Confirm';
                                } else if ($order->status == 'Confirm') {
                                    $newstatus = 'Confirmed';
                                } else if ($order->status == 'Complete') {
                                    $newstatus = 'Completed';
                                } else if ($order->status == 'Cancel') {
                                    $newstatus = 'Cancelled';
                                } else {
                                    $newstatus = $order->status;
                                }
                                ?> 

                                <div class="simple_btn "><a class="<?php echo $but; ?>" href="#"><?php echo $newstatus; ?></a>
                                    <a class="simple_btn_menu pop detail_ord" data-id="<?php echo $order->id; ?>" data-order="<?php echo $order->slug; ?>" href="#" data-keyboard="true" data-backdrop="true" data-controls-modal="leave_modal" data-toggle="modal" data-target="#editreserModal_<?php echo $order->id; ?>">details</a><!--orderview_bpop_<?php //echo $order->id;     ?>-->
                                    <div class="timediv">Received at <?php echo date('h:i A', strtotime($order->created)); ?></div>
                                </div>
                                <div></div>

                            </div>

                            <div id="editreserModal_<?php echo $order->id; ?>" class="modal fade editscreen addmenu view_order" role="dialog">

                            </div>
                            <?php
                            $i++;
                        }
                        ?>

                        <?php
                    } else {
                        ?>
                        <div class="no_record">
                            <div>No Record Found on that date.</div>
                        </div>
                    <?php } ?>

                </div>
            </div>

            <div class="tab_bottom">
                <ul>
                    <li class="active"><a href="javascript:void(0)" class="alldata" data-cat="all">all(<?php echo count($orders); ?>)</a></li> 
                    <li><a href="javascript:void(0)" class="alldata" data-cat="complete">completed(<?php echo $completed; ?>)</a></li>
                    <li><a href="javascript:void(0)" class="alldata" data-cat="cancel">cancelled(<?php echo $cancelled; ?>)</a></li>

                </ul>   

            </div> 
        </div>

    </div>
</div>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<script src="<?php echo HTTP_PATH; ?>/public/js/front2/slick.js" type="text/javascript" charset="utf-8"></script>
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
        $("#altrange").val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        var selected_date = $('#altrange').val();
        var tabs_select = $('input[name=select_tabs]:checked').val();
        var keyword = $('#searchkey').val();
        var data = {
            current_dat: selected_date,
            serch_mt: 'range',
            keyword: keyword
        }

        if (tabs_select == 'order') {
            $.ajax({
                url: "<?php echo HTTP_PATH; ?>/order/hissearchorder",
                type: 'POST',
                data: data,
                dataType: 'html',
                success: function (result) {
                    //console.log(result);
                    $('#menubx').html(result);
                    //$('.all_bg_ldr').show();
                }
            });
        } else if (tabs_select == 'reserve') {
            $.ajax(
                    {
                        url: "<?php echo HTTP_PATH; ?>/order/hisreserveorder",
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        success: function (result) {
                            //console.log(result);
                            if (result.trim() == 'errorlogin') {
                                window.location.href = "<?php echo HTTP_PATH; ?>";
                            } else
                            {
                                $('#menubx').html(result);
                            }

                        }
                    });
        }
    }
    );
});

$(document).on('ready', function () {

    $('#sldr').show();

    $(".center").slick({
        dots: true,
        infinite: true,
        centerMode: true,
        slidesToShow: 7,
        slidesToScroll: 3,
        focusOnSelect: true,
        initialSlide: <?php echo $initial_slide; ?>,
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

        ]
    });
    $('.center').on('afterChange', function (event, slick, currentSlide, nextSlide) {
        var datamonth = $(slick.$slides[currentSlide]).data('month');
        var datadate = $(slick.$slides[currentSlide]).data('date');
        var datayear = $(slick.$slides[currentSlide]).data('year');
        var tabs_select = $('input[name=select_tabs]:checked').val();
        $('#slected_date').val(datayear + '-' + datamonth + '-' + datadate);
        $('.all_bg_ldr').hide();
        var keyword = $('#searchkey').val();
        if (tabs_select == 'order') {
            var data = {
                current_dat: datayear + '-' + datamonth + '-' + datadate,
                keyword: keyword
            }
            $.ajax(
                    {
                        url: "<?php echo HTTP_PATH; ?>/order/hissearchorder",
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        success: function (result) {
                            //console.log(result);
                            $('#menubx').html(result);
                            //$('.all_bg_ldr').show();

                        }
                    });
        } else if (tabs_select == 'reserve') {
            var data = {
                current_dat: datayear + '-' + datamonth + '-' + datadate,
                keyword: keyword
            }
            $.ajax(
                    {
                        url: "<?php echo HTTP_PATH; ?>/order/hisreserveorder",
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        success: function (result) {
                            //console.log(result);
                            if (result.trim() == 'errorlogin') {
                                window.location.href = "<?php echo HTTP_PATH; ?>";
                            } else
                            {
                                $('#menubx').html(result);
                            }

                        }
                    });
        }
    });

    $("#searchorder").submit(function (e) {
        var keyword = $('#searchkey').val();
        var tabs_select = $('input[name=select_tabs]:checked').val();
        if (tabs_select == 'order') {

            var data = {
                current_dat: $('#slected_date').val(),
                keyword: keyword
            }
            $.ajax(
                    {
                        url: "<?php echo HTTP_PATH; ?>/order/hissearchorder",
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        success: function (result) {
                            //console.log(result);
                            $('#menubx').html(result);
                        }
                    });
        } else if (tabs_select == 'reserve')
        {
            var data = {
                current_dat: $('#slected_date').val(),
                keyword: keyword
            }
            $.ajax(
                    {
                        url: "<?php echo HTTP_PATH; ?>/order/hisreserveorder",
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        success: function (result) {
                            //console.log(result);
                            if (result.trim() == 'errorlogin') {
                                window.location.href = "<?php echo HTTP_PATH; ?>";
                            } else
                            {
                                $('#menubx').html(result);
                            }
                        }
                    });
        }
        e.preventDefault();
    });

    $('#menubx').on('click', '.alldata', function () {
        var cat = $(this).data('cat');
        var keyword = $('#searchkey').val();
        var tabs_select = $('input[name=select_tabs]:checked').val();
        if (tabs_select == 'order') {

            var data = {
                current_dat: $('#slected_date').val(),
                keyword: keyword,
                cat: cat
            }
            $.ajax({
                url: "<?php echo HTTP_PATH; ?>/order/taborder",
                dataType: 'html',
                type: 'POST',
                data: data,
                success: function (result) {
                    $('#tabbx').html(result);
                }
            });
        } else if (tabs_select == 'reserve') {
            var data = {
                current_dat: $('#slected_date').val(),
                keyword: keyword,
                cat: cat
            }

            $.ajax({
                url: "<?php echo HTTP_PATH; ?>/order/tabreserve",
                dataType: 'html',
                type: 'POST',
                data: data,
                success: function (result) {
                    $('#tabbx').html(result);
                }
            });
        }
    });

    $('#menubx').on('click', '.detail_ord', function () {
        var order = $(this).data('order');
        var upid = $(this).data('id');
        var data = {
            order: order
        }

        $.ajax({
            url: "<?php echo HTTP_PATH; ?>/history/orderdetail",
            dataType: 'html',
            type: 'POST',
            data: data,
            success: function (result) {
                $('#editreserModal_' + upid).html(result);
            }
        });
    });

    $('#menubx').on('click', '.detail_reser', function () {
        var order = $(this).data('order');
        var upid = $(this).data('id');
        var data = {
            order: order
        }

        $.ajax({
            url: "<?php echo HTTP_PATH; ?>/reservation/reservedetail",
            dataType: 'html',
            type: 'POST',
            data: data,
            success: function (result) {
                $('#editresModal' + upid).html(result);
            }
        });
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>


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
        $("#sidebarCollapse").click(function () {
            $(".navbar-btn").toggleClass("menuicon");

        });
    });

    $('input[type=radio]').change(function () {
        var select_tab = $(this).val();

        var keyword = $('#searchkey').val();
        if (select_tab == 'reserve') {
            var data = {
                current_dat: $('#slected_date').val(),
                keyword: keyword
            }
            $.ajax({
                url: "<?php echo HTTP_PATH; ?>/order/hisreserveorder",
                dataType: 'html',
                type: 'POST',
                data: data,
                success: function (result) {
                    if (result.trim() == 'errorlogin') {
                        window.location.href = "<?php echo HTTP_PATH; ?>";
                    } else
                    {
                        $('#menubx').html(result);
                    }
                }
            });
        } else if (select_tab == 'order') {

            var data = {
                current_dat: $('#slected_date').val(),
                keyword: keyword
            }
            $.ajax(
                    {
                        url: "<?php echo HTTP_PATH; ?>/order/hissearchorder",
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        success: function (result) {
                            //console.log(result);
                            $('#menubx').html(result);
                        }
                    });
        }
    });

</script>

<script>
    $(window).scroll(function () {
        if ($(this).scrollTop() > 5) {
            $(".navbar_tab").addClass("fixed-me");
        } else {
            $(".navbar_tab").removeClass("fixed-me");
        }

        if ($(this).scrollTop() > 5) {
            $(".responsive_btn").addClass("fixed-icon");
        } else {
            $(".responsive_btn").removeClass("fixed-icon");
        }
    });
</script>
<script type="text/javascript">

    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });

    $('#menubx').on('click', '.tab_bottom ul li', function () {
        $(this).parent().find('li.active').removeClass('active');
        $(this).addClass('active');
    });

</script>

@stop