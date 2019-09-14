<div class="transication_header">
            <h3>Total Transactions</h3>   

            <div class="rate_header">
                <span class="left-title"><?php echo ucfirst($userData->first_name); ?></span> 
                <span class="right-title">$ <?php echo number_format($totalsales[0]->totalsale,2); ?></span> 
            </div>  

        </div>
<?php if(!empty($orders)){ ?>
        <div class="detail_wrap">
            <div class="title_Row">
                <h3>Transaction details</h3>      
                <a href="javascript:void(0);" onclick="return printTransaction();"><i><img src="{{ URL::asset('public/img/front') }}/printicon.png"></i> Print Invoice</a>
            </div>   
            
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
            
        </div>
<?php } else { ?>
                <div class="no_record">
                    <div>No Record Found on that date.</div>
                </div>
            <?php } ?>