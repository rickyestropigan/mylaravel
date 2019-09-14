<?php
if ($order->status == 'Pending') {
    $but = 'blue_btn';
}
if ($order->status == 'Confirm') {
    $but = 'green_btn';
}
if ($order->status == 'Complete') {
    $but = 'red_btn';
}
if ($order->status == 'Cancel') {
    $but = 'default_btn';
}
?>


<?php if ($order->status == 'Pending') { ?>
    <a class="changeorstatus" data-status="Confirm" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Confirm</a>
    <a class="changeorstatus default" data-status="Cancel" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Cancel</a>
<?php } else if ($order->status == 'Confirm') { ?>
    <a class="changeorstatus" data-status="Complete" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Complete</a>
    <a class="changeorstatus" data-status="Cancel" data-id="<?php echo $order->id; ?>" href="javascript:void(0)">Cancel</a>
    <?php
} else {
    ?>
    <a class="active <?php echo $but;?>" href="javascript:void(0);"> <?php echo $order->status; ?></a>
<?php }
?>