@extends('layouts/adminlayout')
@section('content')
@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Dashboard')
{{ HTML::style('public/assets/morris.js-0.4.3/morris.css'); }}
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--state overview start-->
        <div class="row state-overview">
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol terques">
                        <i class="fa fa-cutlery"></i>
                    </div>
                    <div class="value">
                        <h1 class="count">
                            {{ $user = DB::table('users')->where('user_type', "=", "Restaurant")->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/restaurants/admin_index', "Restaurants", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol gray">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="value">
                        <h1 class="count4">
                            {{ $customers = DB::table('users')->where('user_type', "=", "Customer")->count()  }}
                        </h1>
                        <p>{{ link_to('/admin/customer/admin_index', "Customers", array('escape' => false,'class'=>"")) }}</p>
                    </div>
                </section>
            </div>

        </div>

        <!--state overview end-->
        <div id="morris">
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Customers Registrations {{$last_seven_days1}} , Restaurants Registrations {{$last_seven_days}} (new Restaurants registered in the last 7 days )
                        </header>
                        <div class="panel-body">
                            <div id="hero-bar" class="graph"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</section>
<!--main content end-->


{{ HTML::script('public/js/count.js'); }}
<script>
    countUp({{$user}}, 'count');
    countUp({{$customers}}, 'count4');</script>

{{ HTML::script('public/assets/morris.js-0.4.3/morris.min.js'); }}
{{ HTML::script('public/assets/morris.js-0.4.3/raphael-min.js'); }}


<script>
    var Script = function () {

        //morris chart

        $(function () {
            // data stolen from http://howmanyleft.co.uk/vehicle/jaguar_'e'_type

            Morris.Bar({
                element: 'hero-bar',
                data: [
                    {device: 'Jan', geekbench: <?php echo isset($regular[0]) ? $regular[0] : 0 ?>, geekbenchs:<?php echo isset($merchant[0]) ? $merchant[0] : 0 ?>},
                    {device: 'Feb', geekbench: <?php echo isset($regular[1]) ? $regular[1] : 0 ?>, geekbenchs:<?php echo isset($merchant[1]) ? $merchant[1] : 0 ?>},
                    {device: 'Mar', geekbench: <?php echo isset($regular[2]) ? $regular[2] : 0 ?>, geekbenchs:<?php echo isset($merchant[2]) ? $merchant[2] : 0 ?>},
                    {device: 'Apr', geekbench: <?php echo isset($regular[3]) ? $regular[3] : 0 ?>, geekbenchs:<?php echo isset($merchant[3]) ? $merchant[3] : 0 ?>},
                    {device: 'May', geekbench: <?php echo isset($regular[4]) ? $regular[4] : 0 ?>, geekbenchs:<?php echo isset($merchant[4]) ? $merchant[4] : 0 ?>},
                    {device: 'Jun', geekbench: <?php echo isset($regular[5]) ? $regular[5] : 0 ?>, geekbenchs:<?php echo isset($merchant[5]) ? $merchant[5] : 0 ?>},
                    {device: 'July', geekbench: <?php echo isset($regular[6]) ? $regular[6] : 0 ?>, geekbenchs:<?php echo isset($merchant[6]) ? $merchant[6] : 0 ?>},
                    {device: 'Aug', geekbench: <?php echo isset($regular[7]) ? $regular[7] : 0 ?>, geekbenchs:<?php echo isset($merchant[7]) ? $merchant[7] : 0 ?>},
                    {device: 'Sep', geekbench: <?php echo isset($regular[8]) ? $regular[8] : 0 ?>, geekbenchs:<?php echo isset($merchant[8]) ? $merchant[8] : 0 ?>},
                    {device: 'Oct', geekbench: <?php echo isset($regular[9]) ? $regular[9] : 0 ?>, geekbenchs:<?php echo isset($merchant[9]) ? $merchant[9] : 0 ?>},
                    {device: 'Nov', geekbench: <?php echo isset($regular[10]) ? $regular[10] : 0 ?>, geekbenchs:<?php echo isset($merchant[10]) ? $merchant[10] : 0 ?>},
                    {device: 'Dec', geekbench: <?php echo isset($regular[11]) ? $regular[11] : 0 ?>, geekbenchs:<?php echo isset($merchant[11]) ? $merchant[11] : 0 ?>}
                ],

                xkey: 'device',
                ykeys: ['geekbench', 'geekbenchs'],
                labels: ['Restaurants', 'Customers'],
                barRatio: 0.4,
                xLabelAngle: 35,
                hideHover: 'auto',
                barColors: ['#6ccac9', '#646464']
            });
        });

    }();

</script>


@stop