<?php
if($view==0){
?>
<a class="simple_btn_menu onoff" data-detail="online" data-id="<?php echo $id; ?>" href="javascript:void(0)">offline</a>
<?php
}
elseif($view==1)
{
?>
<a class="green_btn onoff" data-detail="offline" data-id="<?php echo $id; ?>" href="javascript:void(0)">online</a>
<?php
}
?>