  <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"> 
    <title>Bitebargain :: Print Reservation </title>
 
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
  
     <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
  </head>
  
     <!---------------------Header------------------>
 <body>     
     @yield('content')
 </body>
</html>
