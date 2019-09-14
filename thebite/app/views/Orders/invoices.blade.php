@extends('layouts.default')
@section('content')
{{ HTML::style('public/css/front2/slick.css') }}
{{ HTML::style('public/css/front2/slick-theme.css') }}

<?php
    $days_in_month =  date('t');
    $current_month = date('m');
    $current_short_month = date('M');
    $current_date = date('d');
    $current_year = date('Y');
    $initial_slide=0;
    
//    echo '<pre>';
//    print_r($orders);
//    exit;
?>

<div id="right_content">
    <div class="right_content">

        <div class="content_nav history_page">
            <section class="center big_slider slider" id="sldr" style="display:none;" >
                <?php
                    for($i=1;$i<=$days_in_month;$i++){
                        if($i==$current_date){
                            $initial_slide = $i-1;
                        }
                ?>
                <div data-date="<?php echo $i; ?>" data-month="<?php echo $current_month; ?>" data-year="<?php echo $current_year; ?>">
                    <span class="digit"><?php echo $i; ?></span>
                    <span class="day"><?php echo date('D',strtotime($current_year.'-'.$current_month.'-'.$i)); ?></span>
                </div>
                <?php
                    }
                ?>
            </section>
        </div> 
        <input type="hidden" id="slected_date" value="<?php echo $current_year.'-'.$current_month.'-'.$current_date ?>" >
        <div class="search_bar calendar_searchbar">
            <form action="" method="post" id="searchorder">
            <div class="search_field">
                <i class="fa fa-search"></i>
                <input type="text" id="searchkey" name="search" placeholder="Search"> 

            </div>  

            <div class="calendarfield">
                <i><img src="{{ URL::asset('public/img/front') }}/calendar_xs.png"></i>  
                <input type="text" name="daterange" id="daterange" placeholder="<?php echo $current_date.' '.$current_short_month; ?> - <?php echo $current_date.' '.$current_short_month; ?>">
                <input type="hidden" name="altrange" id="altrange" value="<?php echo $current_year.'/'.$current_month.'/'.$current_date.' - '.$current_year.'/'.$current_month.'/'.$current_date; ?>"/>
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
        <div id="mnbx">
        <div class="transication_header">
            <h3>Total Transactions</h3>   

            <div class="rate_header">
                <span class="left-title"><?php echo ucfirst($userData->first_name); ?></span> 
                <span class="right-title">$ <?php echo number_format($totalsales[0]->totalsale,2); ?></span> 
            </div>  

        </div>

        <div class="detail_wrap">
            <div class="title_Row">
                <h3>Transaction details</h3>      
                <a href="javascript:void(0);" onclick="return printTransaction();"><i><img src="{{ URL::asset('public/img/front') }}/printicon.png"></i> Print Invoice</a>
            </div>   
            <?php if(!empty($orders)){ ?>
            <div class="table_box">
                <div class="table_disting">
                    <div class="table_head">
                        <div class="divth"><a href="javascript:void(0)">id <i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth"><a href="javascript:void(0)">Name <i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth date_width"><a href="javascript:void(0)">Time & date<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth payment_width"><a href="javascript:void(0)">payment Type <i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth subtotal_width"><a href="javascript:void(0)">Subtotal<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth"><a href="javascript:void(0)">Delivery<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth"><a href="javascript:void(0)">tax<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth"><a href="javascript:void(0)">tip<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                        <div class="divth"><a href="javascript:void(0)">total<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                    </div>   
                    <?php 
                        foreach($orders as $order){
                    ?>
                    <div class="table_colm">
                        <div class="divtd"><?php echo $order->order_number; ?></div>
                        <div class="divtd"><?php echo $order->first_name; ?> <?php echo substr($order->last_name,0,1); ?>.</div>
                        <div class="divtd"><?php echo date('m/d/y | h:i A',strtotime($order->delivery_date)); ?></div>
                        <div class="divtd"><?php echo $userData->payment_options ?></div>
                        <div class="divtd">$<?php echo number_format($order->item_total,0) ?></div>
                        <div class="divtd">$<?php echo number_format($order->delivery_charge,0) ?></div>
                        <div class="divtd">$<?php echo number_format($order->tax,0) ?></div>
                        <div class="divtd">--</div>
                        <div class="divtd">$<?php echo number_format($order->total,0) ?></div>
                    </div> 
                    <?php } ?>

                </div> 
            </div>
            <?php } else { ?>
                <div class="no_record">
                    <div>No Record Found on that date.</div>
                </div>
            <?php } ?>
        </div> 
        </div>
    </div> 
</div>
<input type="hidden" id="rangesearch" name="" value="0" />
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<script src="<?php echo HTTP_PATH; ?>/public/js/front2/slick.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    $(function() {
        $('#daterange').daterangepicker(
            {
                locale: {
                  
                  format: 'DD MMM',
                  altFormat: "YYYY-MM-DD",
                  altField: "#altrange"
                },
                
            }, function(start, end, label) {
                $("#altrange").val(start.format('MM/DD/YYYY')+' - '+end.format('MM/DD/YYYY'));
                $("#rangesearch").val('1');
                var tabs_select = $('input[name=select_tabs]:checked').val();
                var keyword = $('#searchkey').val();
                var selected_date = $('#altrange').val();
                if(tabs_select=='order'){
                    var data = {
                    current_dat: selected_date,
                    keyword: keyword,
                    serch_mt:'range',
                }
                $.ajax(
                    {
                    url: "<?php echo HTTP_PATH; ?>/order/searchinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        //console.log(result);
                        if(result.trim()=='errorlogin'){
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        }
                        else
                        {
                            $('#mnbx').html(result);
                        }
                        //$('.all_bg_ldr').show();
                    }
                });
                } else if(tabs_select=='reserve'){
                    var data = {
                        current_dat: selected_date,
                        keyword: keyword,
                        serch_mt:'range',
                    }
                    $.ajax(
                    {
                        url: "<?php echo HTTP_PATH; ?>/order/reserveinvoice", 
                        dataType: 'html',
                        type: 'POST',
                        data: data,
                        success: function(result){
                            //console.log(result);
                            if(result.trim()=='errorlogin'){
                                window.location.href = "<?php echo HTTP_PATH; ?>";
                            }
                            else
                            {
                                $('#mnbx').html(result);
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
            focusOnSelect:true,
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
        
        $('.center').on('afterChange', function(event, slick, currentSlide, nextSlide){
            var datamonth = $(slick.$slides[currentSlide]).data('month');
            var datadate = $(slick.$slides[currentSlide]).data('date');
            var datayear = $(slick.$slides[currentSlide]).data('year');
            var tabs_select = $('input[name=select_tabs]:checked').val();
            var keyword = $('#searchkey').val();
            var range = $('#altrange').val();
            $('#slected_date').val(datayear+'-'+datamonth+'-'+datadate);
            $('.all_bg_ldr').hide();
            $("#rangesearch").val('0');
            if(tabs_select=='order'){
                var data = {
                    current_dat: datayear+'-'+datamonth+'-'+datadate,
                    keyword: keyword,
                    range: range
                }
                $.ajax(
                    {
                    url: "<?php echo HTTP_PATH; ?>/order/searchinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        //console.log(result);
                        if(result.trim()=='errorlogin'){
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        }
                        else
                        {
                            $('#mnbx').html(result);
                        }
                        //$('.all_bg_ldr').show();
                    }
                });
            } else if(tabs_select=='reserve'){
                var data = {
                    current_dat: datayear+'-'+datamonth+'-'+datadate,
                    keyword: keyword,
                    range: range
                }
                $.ajax(
                    {
                    url: "<?php echo HTTP_PATH; ?>/order/reserveinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        //console.log(result);
                        if(result.trim()=='errorlogin'){
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        }
                        else
                        {
                            $('#mnbx').html(result);
                        }                        
                    }
                });
            }
        });
        
        
        $("#searchorder").submit(function (e) {
                var keyword = $('#searchkey').val();
                var tabs_select = $('input[name=select_tabs]:checked').val();
                var range = $('#altrange').val();
                
                if(tabs_select=='order'){
                    
                    var data = {
                        current_dat: $('#slected_date').val(),
                        keyword: keyword,
                        range: range
                    }
                    $.ajax(
                    {
                    url: "<?php echo HTTP_PATH; ?>/order/searchinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        //console.log(result);
                        if(result.trim()=='errorlogin'){
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        }
                        else
                        {
                            $('#mnbx').html(result);
                        }
                    }
                });
                } else if(tabs_select=='reserve')
                {
                    var data = {
                        current_dat: $('#slected_date').val(),
                        keyword: keyword,
                        range: range
                    }
                    $.ajax(
                    {
                    url: "<?php echo HTTP_PATH; ?>/order/reserveinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        //console.log(result);
                        if(result.trim()=='errorlogin'){
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        }
                        else
                        {
                            $('#mnbx').html(result);
                        }
                    }
                });
                }
                e.preventDefault();
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
    
//Tab Change 

$('input[type=radio]').change( function() {
        var select_tab = $(this).val();
        var keyword = $('#searchkey').val();
        var range = $('#altrange').val();
        
        if(select_tab=='reserve'){
            var data = {
                    current_dat: $('#slected_date').val(),
                    keyword: keyword,
                    range: range
                }
            $.ajax({
                    url: "<?php echo HTTP_PATH; ?>/order/reserveinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        if(result.trim()=='errorlogin'){
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        }
                        else
                        {
                            $('#mnbx').html(result);
                        }
                    }
                });
        } else if(select_tab=='order') {
        
            var data = {
                        current_dat: $('#slected_date').val(),
                        keyword: keyword,
                        range: range
                    }
                    $.ajax(
                    {
                    url: "<?php echo HTTP_PATH; ?>/order/searchinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        //console.log(result);
                        if(result.trim()=='errorlogin'){
                            window.location.href = "<?php echo HTTP_PATH; ?>";
                        }
                        else
                        {
                            $('#mnbx').html(result);
                        }
                    }
                });
        }
     });
    
    
     function printTransaction() {
        var keyword = $('#searchkey').val();
        var range = $('#altrange').val();
        var rng = $("#rangesearch").val();
        if(rng == '0'){
            var serch_mt = '';
            range = $('#slected_date').val();
        }
        else if(rng=='1'){
            var serch_mt = 'range';
            range = $('#altrange').val();
        }
            var data = {
                        current_dat: range,
                        keyword: keyword,
                        range: range,
                        serch_mt: serch_mt
                    }
                    $.ajax(
                    {
                    url: "<?php echo HTTP_PATH; ?>/order/printordinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        //console.log(result);
                        var printscreen = window.open('', '', 'left=1,top=1,width=550,height=650,toolbar=0,scrollbars=1,status=0​');
                        printscreen.document.write(result);
                        printscreen.document.close();
                        printscreen.focus();
                        var is_chrome = Boolean(window.chrome);
                        //alert(is_chrome);
                        if (is_chrome) {
                            setTimeout(function () {
                                printscreen.print();
                            }, 2000);
                            printscreen.onload = function() {
                                printscreen.print();
                            };
                        } else {
                            printscreen.print();
                        }
                    }
                });
            
        //printscreen.close();
        return false;

    }
    
    function printReserve() {
        
        var keyword = $('#searchkey').val();
        var range = $('#altrange').val();
        var data = {
                    current_dat: $('#slected_date').val(),
                    keyword: keyword,
                    range: range
                }
            $.ajax({
                    url: "<?php echo HTTP_PATH; ?>/order/printresinvoice", 
                    dataType: 'html',
                    type: 'POST',
                    data: data,
                    success: function(result){
                        var printscreen = window.open('', '', 'left=1,top=1,width=550,height=650,toolbar=0,scrollbars=1,status=0​');
                        printscreen.document.write(result);
                        printscreen.document.close();
                        printscreen.focus();
                        var is_chrome = Boolean(window.chrome);
                        //alert(is_chrome);
                        if (is_chrome) {
                            setTimeout(function () {
                                //printscreen.print();
                            }, 2000);
                            printscreen.onload = function() {
                                printscreen.print();
                            };
                        } else {
                            printscreen.print();
                        }
                    }
                });
        
        return false;

    }
    
</script>
<script type="text/javascript">
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

});


</script>

@stop