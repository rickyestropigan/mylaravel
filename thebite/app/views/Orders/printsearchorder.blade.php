  <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"> 
    <title>Print Orders :: Bitebargain</title>
 
    <!-- Bootstrap -->
    
    {{ HTML::style('public/css/front2/bootstrap.min.css') }}

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
       <style>
     body{font-family: arial;     background: #fff;}    
        
        
    </style>
  
     
  </head>
  
     <!---------------------Header------------------>
 <body>
     <div style="max-width: 1170px; margin: 40px auto;">
         
         <table>
             <tbody>
                 <tr>
                     <td style="width: 20%;">
                         <?php
                            if(empty($userData->profile_image) && $userData->profile_image==''){
                        ?>
                            <img src="{{ URL::asset('public/img/front') }}/noimage.png">
                        <?php
                            }
                            else
                            {
                        ?>
                            <img src="{{ URL::asset(DISPLAY_FULL_PROFILE_IMAGE_PATH.$userData->profile_image) }}">
                        <?php
                            }
                        ?>
                     </td>   
                     <td style="width: 20%;">
                         <b style="font-size: 20px;">Email</b>
                         <p style="font-size: 20px;"><?php echo $userData->email_address; ?></p>
                     </td>
                      <td style="width: 20%;">
                         <b style="font-size: 20px;">Phone</b>
                         <p style="font-size: 20px;"><?php echo $userData->phone1; ?></p>
                     </td>
                      <td style="width: 20%;">
                         <b style="font-size: 20px;">Address</b>
                         <p style="font-size: 20px;"><?php echo $userData->address; ?> <br> <?php echo $userData->city; ?>, <?php echo $userData->state; ?>, <?php echo $userData->zipcode; ?></p>
                     </td>
                 </tr>    
             </tbody>    
             
             
         </table>    
         
         <table style="margin: 30px 0px 50px;">
             <tbody>
                 <tr>
                     <td style="font-size: 24px;"><?php echo $userData->first_name; ?></td>   
                 </tr>     
             </tbody>   
         </table>
         
          <table style="margin: 30px 0px 30px; width: 100%;">
             <tbody>
                 <tr>
                     <td style="font-size: 20px;     padding: 0px 0px 10px 0px;"><b>Total Transaction</b></td>   
                 </tr>   
                 <tr style="border: 1px solid #666;">
                     <td style="font-size: 24px;padding: 15px;"><?php echo $userData->first_name; ?></td>  
                     <td style="font-size: 30px;padding: 15px; float: right;"><b>$ <?php echo $totalsales[0]->totalsale ?></b></td>  
                     
                 </tr> 
             </tbody>   
         </table>
         
         <?php if(!empty($orders)){ ?>
          <table style="margin:0px; width: 100%;">
             <tbody>
                 <tr>
                     <td style="font-size: 20px;     padding: 0px 0px 10px 0px;"><b>Transaction Details</b></td>   
                     
                 </tr>   
                 
             </tbody>   
           
         </table>
         
          <table style="margin:0px; width: 100%;    border: 1px solid #666;     text-align: center;">
             <thead>
                 <tr style="   ">
                     <th style="font-size: 16px; padding: 20px 0px 20px 15px;     text-align: center;"><b>ID</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>NAME</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>DATE & TIME</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>PAYMENT TYPE</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>SUBTOTAL</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>DELIVERY</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>TAX</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>TIP</b></th>
                     <th style="font-size: 16px; padding: 20px 0px; text-align: center;"><b>TOTAL</b></th>
                 </tr>   
                 
             </thead>   
             
              <tbody>
                  <?php 
                        foreach($orders as $order){
                    ?>
                  <tr style="    border-bottom: 1px solid #666;">
                     <td style="font-size: 16px; padding: 10px 0px 10px 15px;"><?php echo $order->order_number; ?></td>
                     <td style="font-size: 16px; padding: 15px 0px;"><?php echo $order->first_name; ?> <?php echo substr($order->last_name,0,1); ?>.</td>
                     <td style="font-size: 16px; padding: 15px 0px;"><?php echo date('m/d/y | h:i A',strtotime($order->delivery_date)); ?></td>
                     <td style="font-size: 16px; padding: 15px 0px;"><?php echo $userData->payment_options ?></td>
                     <td style="font-size: 16px; padding: 15px 0px;">$ <?php echo number_format($order->item_total,0) ?></td>
                     <td style="font-size: 16px; padding: 15px 0px;">$ <?php echo number_format($order->delivery_charge,0) ?></td>
                     <td style="font-size: 16px; padding: 15px 0px;">$ <?php echo number_format($order->tax,0) ?></td>
                     <td style="font-size: 16px; padding: 15px 0px;"> --</td>
                     <td style="font-size: 16px; padding: 15px 0px;">$ <?php echo number_format($order->total,0) ?></td>
                 </tr>
                <?php } ?>
                 
             </tbody> 
           
         </table>
         <table style="    width: 100%;margin: 80px 0px 0px 0px;">
             <tbody>
                 <tr><td style="float: right;font-size: 16px;color: #000;font-weight: bold;">Date Created : <?php echo date('m/d/Y'); ?> </td>   </tr>  
                  
             </tbody>    
         </table> 
         <?php
         }
         else
         {
         ?>
         <table style="margin:0px; width: 100%;">
             <tbody>
                 <tr>
                     <td style="font-size: 20px;     padding: 0px 0px 10px 0px;"><b>Record Not Found!</b></td> 
                 </tr>   
                 
             </tbody>   
           
         </table>
         <?php
         }
         ?>
     </div>   
  
     
 </body>
</html>