@section('content')
<section class="slider_top my-5">
            <div class="container">
                <div class="row">
                    <div id="auto_width" class="owl-carousel owl-theme">
                        <div class="item" style="width:250px"><div class="single">
                            {{ HTML::image("public/frontimg/first_slide.png") }}
                        </div></div>
                        <div class="item" style="width:250px">
                        <div class="two">
                         {{ HTML::image("public/frontimg/second_slide.png") }}
                        </div>
                        <div class="two">
                            {{ HTML::image("public/frontimg/third_slide.png") }}
                        </div>
                        </div>
                        <div class="item" style="width:250px"><div class="single">{{ HTML::image("public/frontimg/fourth_slide.png") }}</div></div>
                          <div class="item" style="width:250px">
                        <div class="two">{{ HTML::image("public/frontimg/fifth_slide.png") }}</div>
                        <div class="two">{{ HTML::image("public/frontimg/six_slide.png") }}</div>
                      </div>
                     
                        <div class="item" style="width:250px"><div class="single">{{ HTML::image("public/frontimg/seven_slide.png") }}</div></div>
 
</div>
                </div>    
            </div>
            
        </section>
        <section class="deatils_page">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page_title text-center">
                          
                            <h3 class="d-inline-block">{{$data->first_name }}<button type="button" class="btn rounded-btn">$$$</button></h3> 
                            <a href="#" class="d-inline-block"><i class="fa fa-heart-o"></i></a> 
                            <?php $crusines = str_replace(',', ' | ', $data->cuisines); ?>
                            <div class="tag">{{ $crusines }}</div>
                            
                        </div>   
                    </div>   
                    <div class="col-12 mt-2 mb-2  mt-sm-2 mb-sm-2 mt-lg-4 mb-lg-3">
                        <ul class="list-unstyled text-center types">
                             <li class="nav-item d-inline-block">Open until <?php if(!empty($data->end_time)) {?> {{$data->end_time}} PM <?php }else {?>10:45 PM<?php }?></li>   
                             <li class="n@stopav-item d-inline-block">140-26-4732</li>   
                             <li class="nav-item d-inline-block">15 W 56th St, New York</li>  
                        </ul>    
                    </div>
                    
                </div>
                
                
                
            </div>   
            
            
            
            <div class="container">
                <div class="row">
                    <div class="col-12">
           
                        <ul class="nav nav-tabs list-unstyled tab_design text-center pick_tab" id="pickup_tab" role="tablist">
                            <li class="nav-item d-inline-block">
                                <a class="nav-link active" id="best-dishes-tab" data-toggle="tab" href="#best-dishes" role="tab" aria-controls="best-dishes" aria-selected="true">BEST DISHES</a>
                            </li>   
                            <li class="nav-item d-inline-block">
                                <a class="nav-link" id="COMBOS-tab" data-toggle="tab" href="#COMBOS" role="tab" aria-controls="COMBOS" aria-selected="false">COMBOS</a>
                            </li>   
                           <li class="nav-item d-inline-block">
                                <a class="nav-link" id="SPECIALS-tab" data-toggle="tab" href="#SPECIALS" role="tab" aria-controls="SPECIALS" aria-selected="false">SPECIALS</a>
                            </li>   
                             <li class="nav-item d-inline-block">
                              <a class="nav-link" id="SALADS-tab" data-toggle="tab" href="#SALADS" role="tab" aria-controls="SALADS" aria-selected="false">SALADS</a>
                            </li>   
                             <li class="nav-item d-inline-block">
                                <a class="nav-link" id="ROLLS-tab" data-toggle="tab" href="#ROLLS" role="tab" aria-controls="ROLLS" aria-selected="false">ROLLS</a>
                            </li>   
                             <li class="nav-item d-inline-block">
                                  <a class="nav-link" id="BURGERS-tab" data-toggle="tab" href="#BURGERS" role="tab" aria-controls="BURGERS" aria-selected="false">BURGERS</a>
                            </li>   
                             <li class="nav-item d-inline-block">
                                  <a class="nav-link" id="PIZZAS-tab" data-toggle="tab" href="#PIZZAS" role="tab" aria-controls="PIZZAS" aria-selected="false">PIZZAS</a>
                            </li>   
                             <li class="nav-item d-inline-block">
                                 <a class="nav-link" id="SANDWICHES-tab" data-toggle="tab" href="#SANDWICHES" role="tab" aria-controls="SANDWICHES" aria-selected="false">SANDWICHES</a>
                            </li>   
                             <li class="nav-item d-inline-block">
                             <a class="nav-link" id="SOUPS-tab" data-toggle="tab" href="#SOUPS" role="tab" aria-controls="SOUPS" aria-selected="false">SOUPS</a>
                            </li>   
                             <li class="nav-item d-inline-block">
                            <a class="nav-link" id="SHAKES-tab" data-toggle="tab" href="#SHAKES" role="tab" aria-controls="SHAKES" aria-selected="false">SHAKES</a>
                            </li>   
                        </ul>    
                    </div>   
                    
                    
                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                        <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="best-dishes" role="tabpanel" aria-labelledby="best-dishes-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">BEST DISHES</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">COMBOS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="COMBOS" role="tabpanel" aria-labelledby="COMBOS-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">COMBOS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">COMBOS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="SPECIALS" role="tabpanel" aria-labelledby="SPECIALS-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SPECIALS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SPECIALS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="SALADS" role="tabpanel" aria-labelledby="SALADS-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SALADS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SALADS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="ROLLS" role="tabpanel" aria-labelledby="ROLLS-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">ROLLS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">ROLLS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="BURGERS" role="tabpanel" aria-labelledby="BURGERS-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">BURGERS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">BURGERS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="PIZZAS" role="tabpanel" aria-labelledby="PIZZAS-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">PIZZAS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">PIZZAS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="SANDWICHES" role="tabpanel" aria-labelledby="SANDWICHES-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SANDWICHES</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SANDWICHES</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="SOUPS" role="tabpanel" aria-labelledby="SOUPS-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SOUPS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SOUPS</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                              <div class="tab-pane fade" id="SHAKES" role="tabpanel" aria-labelledby="SHAKES-tab">    
                        <div class="tab_wrap">
                        <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SHAKES</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                        </div>
                             <div class="dishes_wrap mb-3">
                            <div class="titl text-center py-5">SHAKES</div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                               <div class="hlaf_dish">
                                <div class="hlaf_dish_inner  mb-4">
                                    <a href="#"> Treasures of the Sea Congee</a>  
                                    <p> Prawns, scallops and fish slices</p>
                                    <div class="float-left">
                                        <span class="demo_rate">$15.12</span> <span class="actual_rate">$12.12 </span>
                                    </div>
                                    <div class="btn_add float-right"><a href="#" class="btn">+ Add</a></div>
                                    
                                </div>   
                                
                            </div>
                            
                             </div></div></div>
                            
                        </div>
                    </div> 
                    
                    
                    
                    
                    
                    
                    
                    
                    
                      <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                          <div class="side_bar">
                               <div class="cart_box_wrap">
                                    <ul class="nav list-unstyled cart_tab text-center" id="cart_tab" role="tablist">
                          <li class="nav-item d-inline-block">
                                <a class="" id="Delivery-tab" data-toggle="tab" href="#Delivery-cart" role="tab" aria-controls="Delivery-cart" aria-selected="true">Delivery</a>
                            </li>   
                          <li class="nav-item d-inline-block">
                                <a class="" id="reservation-tab" data-toggle="tab" href="#reservation-cart" role="tab" aria-controls="reservation-cart" aria-selected="true">Reservation</a>
                            </li>   
                          <li class="nav-item d-inline-block">
                                <a class="active" id="Pickup-tab" data-toggle="tab" href="#Pickup-cart" role="tab" aria-controls="Pickup-cart" aria-selected="true">Pickup</a>
                            </li>   
                                    
                                    </ul> 
                                   <div class="tab-content" id="cart_tab_content">
                                         <div class="tab-pane fade show active" id="Delivery-cart" role="tabpanel" aria-labelledby="Delivery-tab"> 
                              <div class="calendar same_field">
                                  <span class="calendar_icon ico_de"><i class="fa fa-calendar"></i> Date</span>
                                  <input class="form-control border-0" type="text" placeholder="Jan 12, 2018" value="Jan 12, 2018">
                                  <i class="fa fa-caret-up arrow"></i>
                              </div>   
                                          <div class="time same_field">
                                  <span class="calendar_icon ico_de"><i class="fa fa-clock-o"></i> Time</span>
                                  <input class="form-control border-0" type="text" placeholder="7:30 PM" value="7:30 PM">
                                  <i class="fa fa-caret-up arrow" id="time_drop"></i>
                                  
                                  <ul class="list-unstyled time-toolbar ">
         <li class="nav-item d-inline-block">
            
             <input type="radio" id="discount1" name="radioFruit" value="discount1" checked="">
             <label for="discount1"><span>ASAP</span>
                 <b>30% off</b>
             </label>
        </li>
         <li class="nav-item d-inline-block">
            <input type="radio" id="discount2" name="radioFruit" value="discount2">
    <label for="discount2"><span>9:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="discount3" name="radioFruit" value="discount3">
    <label for="discount3"><span>10:00 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timea" name="radioFruit" value="timea">
    <label for="timea"><span>10:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeb" name="radioFruit" value="timeb">
    <label for="timeb"><span>11:00 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timec" name="radioFruit" value="timec">
    <label for="timec"><span>11:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timed" name="radioFruit" value="timed">
    <label for="timed"><span>12:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timee" name="radioFruit" value="timee">
    <label for="timee"><span>6:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timee" name="radioFruit" value="timee">
    <label for="timee"><span>7:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timef" name="radioFruit" value="timef">
    <label for="timee"><span>7:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeg" name="radioFruit" value="timeg">
    <label for="timeg"><span>8:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeh" name="radioFruit" value="timeh">
    <label for="timeh"><span>8:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timei" name="radioFruit" value="timei">
    <label for="timei"><span>9:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timej" name="radioFruit" value="timej">
    <label for="timej"><span>9:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timek" name="radioFruit" value="timek">
    <label for="timek"><span>10:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timel" name="radioFruit" value="timel">
    <label for="timel"><span>10:30 PM</span>
                 <b>30% off</b></label></li>
    </ul>
                                 </div>
                                              <div class="discount_btn">
                                  <a href="#" class="btn"><span><i>{{ HTML::image("public/frontimg/tag.png") }}
                                  </i> Offer</span>   20% off on all menu</a>
                              </div>
                                         </div>
                                     
                                   
                                  <div class="tab-pane fade" id="Pickup-cart" role="tabpanel" aria-labelledby="Pickup-tab"> 
                             <div class="calendar same_field">
                                  <span class="calendar_icon ico_de"><i class="fa fa-calendar"></i> Date</span>
                                  <input class="form-control border-0" type="text" placeholder="Jan 12, 2018" value="Jan 12, 2018">
                                  <i class="fa fa-caret-up arrow"></i>
                              </div>   
                                          <div class="time same_field">
                                  <span class="calendar_icon ico_de"><i class="fa fa-clock-o"></i> Time</span>
                                  <input class="form-control border-0" type="text" placeholder="7:30 PM" value="7:30 PM">
                                  <i class="fa fa-caret-up arrow" id="time_dropp"></i>
                                  
                                  <ul class="list-unstyled time-toolbar ">
         <li class="nav-item d-inline-block">
            
             <input type="radio" id="discount1" name="radioFruit" value="discount1" checked="">
             <label for="discount1"><span>ASAP</span>
                 <b>30% off</b>
             </label>
        </li>
         <li class="nav-item d-inline-block">
            <input type="radio" id="discount2" name="radioFruit" value="discount2">
    <label for="discount2"><span>9:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="discount3" name="radioFruit" value="discount3">
    <label for="discount3"><span>10:00 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timea" name="radioFruit" value="timea">
    <label for="timea"><span>10:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeb" name="radioFruit" value="timeb">
    <label for="timeb"><span>11:00 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timec" name="radioFruit" value="timec">
    <label for="timec"><span>11:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timed" name="radioFruit" value="timed">
    <label for="timed"><span>12:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timee" name="radioFruit" value="timee">
    <label for="timee"><span>6:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timee" name="radioFruit" value="timee">
    <label for="timee"><span>7:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timef" name="radioFruit" value="timef">
    <label for="timee"><span>7:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeg" name="radioFruit" value="timeg">
    <label for="timeg"><span>8:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeh" name="radioFruit" value="timeh">
    <label for="timeh"><span>8:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timei" name="radioFruit" value="timei">
    <label for="timei"><span>9:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timej" name="radioFruit" value="timej">
    <label for="timej"><span>9:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timek" name="radioFruit" value="timek">
    <label for="timek"><span>10:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timel" name="radioFruit" value="timel">
    <label for="timel"><span>10:30 PM</span>
                 <b>30% off</b></label></li>
    </ul>
                                 </div>
                                              <div class="discount_btn">
                                  <a href="#" class="btn"><span><i>{{ HTML::image("public/frontimg/tag.png") }}</i> Offer</span>   20% off on all menu</a>
                              </div>
                                  </div>
                                           <div class="tab-pane fade" id="reservation-cart" role="tabpanel" aria-labelledby="reservation-tab"> 
                                   
                                   <div class="calendar same_field">
                                  <span class="calendar_icon ico_de"><i class="fa fa-calendar"></i> Date</span>
                                  <input class="form-control border-0" type="text" placeholder="Jan 12, 2018" value="Jan 12, 2018">
                                  <i class="fa fa-caret-up arrow"></i>
                              </div>   
                                          <div class="time same_field">
                                  <span class="calendar_icon ico_de"><i class="fa fa-clock-o"></i> Time</span>
                                  <input class="form-control border-0" type="text" placeholder="7:30 PM" value="7:30 PM">
                                  <i class="fa fa-caret-up arrow" id="time_droppp"></i>
                                  
                                  <ul class="list-unstyled time-toolbar ">
         <li class="nav-item d-inline-block">
            
             <input type="radio" id="discount1" name="radioFruit" value="discount1" checked="">
             <label for="discount1"><span>ASAP</span>
                 <b>30% off</b>
             </label>
        </li>
         <li class="nav-item d-inline-block">
            <input type="radio" id="discount2" name="radioFruit" value="discount2">
    <label for="discount2"><span>9:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="discount3" name="radioFruit" value="discount3">
    <label for="discount3"><span>10:00 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timea" name="radioFruit" value="timea">
    <label for="timea"><span>10:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeb" name="radioFruit" value="timeb">
    <label for="timeb"><span>11:00 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timec" name="radioFruit" value="timec">
    <label for="timec"><span>11:30 AM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timed" name="radioFruit" value="timed">
    <label for="timed"><span>12:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timee" name="radioFruit" value="timee">
    <label for="timee"><span>6:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timee" name="radioFruit" value="timee">
    <label for="timee"><span>7:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timef" name="radioFruit" value="timef">
    <label for="timee"><span>7:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeg" name="radioFruit" value="timeg">
    <label for="timeg"><span>8:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timeh" name="radioFruit" value="timeh">
    <label for="timeh"><span>8:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timei" name="radioFruit" value="timei">
    <label for="timei"><span>9:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timej" name="radioFruit" value="timej">
    <label for="timej"><span>9:30 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timek" name="radioFruit" value="timek">
    <label for="timek"><span>10:00 PM</span>
                 <b>30% off</b></label></li>
         <li class="nav-item d-inline-block"> <input type="radio" id="timel" name="radioFruit" value="timel">
    <label for="timel"><span>10:30 PM</span>
                 <b>30% off</b></label></li>
    </ul>
                                 </div>
                                              <div class="discount_btn">
                                  <a href="#" class="btn"><span><i>{{ HTML::image("public/frontimg/tag.png") }}</i> Offer</span>   20% off on all menu</a>
                              </div>
                                   
                                   </div>
                                  </div>
                               
                               </div>
                              <div class="cart_box_wrap">
                              <div class="cart_box">
                                  <div class="cart_title text-center">
                                      CART
                                  </div> 
                                  
                                  <div class="cart_row">
                                      <div class="top_details">
                                      <a href="#">Treasures of the Sea Congee</a>  
                                      <ul class="list-unstyled">
                                          <li>Tomatoes <span>$0.50</span></li>    
                                          <li>Olives <span>$0.50</span></li>    
                                      </ul></div>
                                      <div class="product_add">
                                          <div id="field1" class="d-inline-block">
    <button type="button" id="sub" class="sub ">-</button>
    <input type="text" id="1" value="1" min="1" max="3" />
    <button type="button" id="add" class="add">+</button>
    <i>{{ HTML::image("public/frontimg/trash.png") }}</i>
</div>
                                          <span class="total float-right">$0.50</span>
                                      </div>
                                  </div>
                                  <div class="cart_row">
                                      <div class="top_details">
                                      <a href="#">Treasures of the Sea Congee</a>  
                                      <ul class="list-unstyled">
                                          <li>Tomatoes <span>$0.50</span></li>    
                                        
                                      </ul></div>
                                      <div class="product_add">
                                          <div id="field1" class="d-inline-block">
    <button type="button" id="sub" class="sub ">-</button>
    <input type="text" id="1" value="1" min="1" max="3" />
    <button type="button" id="add" class="add">+</button>
    <i>{{ HTML::image("public/frontimg/trash.png") }}</i>
</div>
                                          <span class="total float-right">$0.50</span>
                                      </div>
                                  </div>
                                  <div class="cart_row">
                                      <div class="top_details">
                                          <a href="#">Total <span class="total float-right">$40.50</span></a>  
                                      <ul class="list-unstyled">
                                          <li><i>extra charges may apply</i></li>    
                                        
                                      </ul></div></div>
                              </div></div>
                               <div class="d-block text-center tablebtn"><a href="#" class="btn btn-primary border-0">Checkout</a></div>
                          </div>
                    
                </div>   
                
                
            </div>
        </section>  
        @stop