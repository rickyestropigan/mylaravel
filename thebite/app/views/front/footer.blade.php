
    <div id="contactModal" class="modal fade registration_pop" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="pop_inner">
      <div class="modal-header">
          <h4 class="modal-title">Contact Us
          </h4>
       
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        
      </div>
         <br>
          <p>
            Please contact us by sending a message using the form below:
          </p>
      <div class="modal-body">

        
         {{ Form::open(['url' => 'contact_us', 'method' => 'post']) }}

          <div class="form-group">
              <input type="text" name="firstname" id="contfirstname" placeholder="First Name" class="form-control" required>  
          </div>
          <div class="error" id="err_firstname" style="color:red;"></div>
           <div class="form-group">
              <input type="text" name="lastname" id="contlastname" placeholder="Last Name" class="form-control" required>  
          </div>
            <div class="error" id="err_lastname" style="color:red;"></div>
          <div class="form-group">
              <input type="text" name="phone" id="contphone" placeholder="Phone" class="form-control" required>  
          </div>
            <div class="error" id="err_phone" style="color:red;" ></div>
          <div class="form-group">
              <input type="text" name="email" id="contemail" placeholder="Email" class="form-control" required>  
          </div>
           <div class="error" id="err_email" style="color:red;" ></div>
          
          <div class="form-group">
              <textarea name="message" id="contmessage" placeholder="Message" class="form-control"></textarea>
          </div>
           <div class="error" id="err_msg" style="color:red;" ></div>
      </div>
       
      <div class="modal-footer text-center">
          
        <input type="submit" class="btn btn-default m-auto contactus"  value="Submit">
      </div>
       {{ Form::close() }}
        </div>
    </div>

  </div>
</div>
  <footer>
            <div class="container">
                <div class="row mb-5">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="subscription">
                            <h2>Get Updates</h2>  
                            <div class="form-group subscription_form">
                                <input type="text" placeholder="Email Address" class="subscription" >
                                <button type="submit" class="btn btn-primary">GO</button>
                            </div>
                        </div>    
                    </div>    
                </div>  
                <div class="row mt-5">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <a href="{{ url('/listing') }}"> {{ HTML::image("public/frontimg/white_logo.png") }}</a>    
                    </div>  
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <ul class="list-unstyled foot_menu text-right d-inline-block float-right">
                            <li class="d-inline-block"><a href="" data-toggle="modal" data-target="#contactModal">Contact Us</a></li>
                            <li class="d-inline-block">&copy; <?php echo date("Y"); ?> Bitebargain Inc.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
