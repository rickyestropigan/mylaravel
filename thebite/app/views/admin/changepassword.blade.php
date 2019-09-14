@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Change Password')
@extends('layouts/adminlayout')
@section('content')

<script src="{{ URL::asset('public/js/jquery.validate.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod("contact", function(value, element) {
            return  this.optional(element) || (/^[0-9-]+$/.test(value));
        }, "Contact Number is not valid.");
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
                    <li class="active"> Change Password </li>
                </ul>
                <section class="panel">
                    <header class="panel-heading">
                        Change Administrator Password
                    </header>
                    <div class="panel-body"> 

                        {{ View::make('elements.actionMessage')->render() }}
                        <span class="require_sign">Please note that all fields that have an asterisk (*) are required. </span>
                        <div class=" form">
                            <?php echo Form::open(array('url' => 'admin/changepassword', 'method' => 'post', 'id' => 'myform', 'class' => 'cmxform form-horizontal tasi-form form')); ?>

                            <div class="form-group ">
                                <label for="opassword" class="control-label col-lg-2">Old Password  <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::password('opassword', array('id' => 'opassword', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Old Password'));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="password" class="control-label col-lg-2">Password <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::password('password', array('id' => 'password','minlength' => 8, 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Password'));
                                    ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label for="cpassword" class="control-label col-lg-2">Confirm Password <span class="require">*</span></label>
                                <div class="col-lg-10">
                                    <?php
                                    echo Form::password('cpassword', array('id' => 'cpassword','minlength' => 8, 'equalTo' => '#password', 'autofocus' => true, 'class' => "required form-control", 'placeholder' => 'Confirm Password'));
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button class="btn btn-danger" type="submit">Change Password</button>
                                    <a class="btn btn-default" href="<?php echo HTTP_PATH . "adminpage" ?>">Cancel</a>
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
