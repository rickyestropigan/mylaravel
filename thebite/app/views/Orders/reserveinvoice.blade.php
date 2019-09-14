<div class="transication_header">
    <h3>Total Covers</h3>   

    <div class="rate_header">
        <span class="left-title"><?php echo ucfirst($userData->first_name); ?></span> 
        <span class="right-title"> <?php echo count($records); ?></span> 
    </div>  

</div>


<?php if (count($records) > 0) { ?>
    <div class="detail_wrap">
        <div class="title_Row">
            <h3>Cover Details</h3>      
            <a href="javascript:void(0);" onclick="return printReserve();"><i><img src="{{ URL::asset('public/img/front') }}/printicon.png"></i> Print Invoice</a>
        </div>   
        <div class="table_box">
            <div class="table_disting">
                <div class="table_head">
                    <div class="divth"><a href="javascript:void(0)">id <i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                    <div class="divth big_width"><a href="javascript:void(0)">Name <i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                    <div class="divth"><a href="javascript:void(0)">Covers<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                    <div class="divth"><a href="javascript:void(0)">Time & date<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>
                    <div class="divth"><a href="javascript:void(0)">Status<i class="fa fa-unsorted" aria-hidden="true"></i></a></div>

                </div>   
                <?php foreach ($records as $record) { ?>
                    <div class="table_colm">
                        <div class="divtd "><?php echo $record->reservation_number; ?></div>
                        <div class="divtd"><?php echo $record->first_name; ?> <?php echo substr($record->last_name, 0, 1); ?>.</div>
                        <div class="divtd"><?php echo $record->size; ?></div>
                        <div class="divtd"><?php echo date('m/d/y | h:i A', strtotime($record->reservation_date)); ?></div>
                        <?php
                        if ($record->reservation_status == 'Pending') {
                            $newstatus = 'Confirm';
                        } else if ($record->reservation_status == 'Confirm') {
                            $newstatus = 'Confirmed';
                        } else if ($record->reservation_status == 'Complete') {
                            $newstatus = 'Completed';
                        } else if ($record->reservation_status == 'Cancel') {
                            $newstatus = 'Cancelled';
                        } else {
                            $newstatus = $record->reservation_status;
                        }
                        ?>
                        <div class="divtd"><?php echo $newstatus; ?></div>
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
