@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Site Configuration')
@extends('layouts/adminlayout')
@section('content')
<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>
{{ HTML::style('public/js/chosen/chosen.css'); }}
<script type="text/javascript">
$(document).ready(function () {
    $(".cb-enable").click(function () {
        $('#is_default_delivery').val('1');
        var parent = $(this).parents('.switch');
        $('.cb-disable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', true);
    });
    $(".cb-disable").click(function () {
        $('#is_default_delivery').val('0');
        var parent = $(this).parents('.switch');
        $('.cb-enable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox', parent).attr('checked', false);
    });
});
</script>
<style>
    .cb-enable, .cb-disable, .cb-enable span, .cb-disable span { background: url(<?php echo HTTP_PATH . "public/css/front/" ?>switch.gif) repeat-x; display: block; float: left; }
    .cb-enable span, .cb-disable span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
    .cb-enable span { background-position: left -90px; padding: 0 10px; }
    .cb-disable span { background-position: right -180px;padding: 0 10px; }
    .cb-disable.selected { background-position: 0 -30px; }
    .cb-disable.selected span { background-position: right -210px; color: #fff; }
    .cb-enable.selected { background-position: 0 -60px; }
    .cb-enable.selected span { background-position: left -150px; color: #fff; }
    .switch label { cursor: pointer; }
    .switch input { display: none; }

</style>
<script type="text/javascript">
    $(document).ready(function () {
        $("#myform").validate();
    });
</script>

<section id="main-content">
    <section class="wrapper">
        <!-- page start-->
        <div class="row">
            <div class="col-lg-12"> 
                <ul id="breadcrumb" class="breadcrumb">
                    <li>
                        {{ html_entity_decode(link_to('/admin/admindashboard', '<i class="fa fa-dashboard"></i> Dashboard', array('escape' => false))) }}
                    </li>
                    <li class="active"> Site Configuration </li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Site Configuration
                    </header>

                    <div class="panel-body">
                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        <div class=" form">
                            <?php echo Form::model($detail, ['url' => ['/admin/sitesetting'], 'id' => 'myform', 'class' => 'cmxform form-horizontal tasi-form form'], array('method' => 'post', 'id' => 'adminAdd')); ?>


                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Site Title <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('title', Input::old('title'), array('id' => 'title', 'autofocus' => true, 'class' => "required form-control name"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Site URL <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('url', Input::old('url'), array('id' => 'url', 'autofocus' => true, 'class' => "required form-control url"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Site Tagline <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('tagline', Input::old('tagline'), array('id' => 'tagline', 'autofocus' => true, 'class' => "required form-control"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">From Email Address <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('mail_from', Input::old('mail_from'), array('id' => 'mail_from', 'autofocus' => true, 'class' => "required form-control email"));
                                    ?>
                                </div>
                            </div>

                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Phone Number <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('phone', Input::old('phone'), array('id' => 'phone', 'autofocus' => true, 'class' => "required form-control number"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Facebook link <span class="require"></span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('facebook_link', Input::old('facebook_link'), array('id' => 'facebook_link', 'autofocus' => true, 'class' => " form-control url"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Twitter link<span class="require"></span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('twitter_link', Input::old('twitter_link'), array('id' => 'twitter_link', 'autofocus' => true, 'class' => " form-control url"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Instagram link<span class="require"></span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('instagram_link', Input::old('instagram_link'), array('id' => 'instagram_link', 'autofocus' => true, 'class' => " form-control url"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Paypal Email Address<span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('paypal_email_address', Input::old('paypal_email_address'), array('id' => 'paypal_email_address', 'autofocus' => true, 'class' => " form-control email"));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Paypal URL<span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('paypal_url', Input::old('paypal_url'), array('id' => 'paypal_url', 'autofocus' => true, 'class' => " form-control url"));
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group ">
                                <label for="name" class="control-label col-lg-2">Order Email<span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::text('send_order_email', Input::old('send_order_email'), array('id' => 'send_order_email', 'autofocus' => true, 'class' => " form-control email"));
                                    ?>
                                </div>
                            </div> 

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">

                                    <button class="btn btn-danger" type="submit">Update</button>
                                    {{ html_entity_decode(HTML::link(HTTP_PATH.'admin/admindashboard', "Cancel", array('class' => 'btn btn-default'), true)) }}
                                </div>
                            </div>
                            <?php echo Form::close(); ?>
                        </div>

                    </div>
                </section>
            </div>
        </div>
        <!-- page end-->
    </section>
</section>
@stop
