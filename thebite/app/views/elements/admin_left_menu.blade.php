<aside>
    <div id="sidebar"  class="nav-collapse ">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">
            <li>
                <a class="{{ Request::is('admin/admindashboard*') ? 'active' : '' }}" href="{{ URL::to( 'admin/admindashboard') }}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sub-menu">
                <a href="javascript:;" class="{{( Request::is('admin/editprofile*') or  Request::is('admin/changepassword*')  or  Request::is('admin/admintax*')  or  Request::is('admin/admindeliverycharge*') or  Request::is('admin/timeSettings*')   or  Request::is('admin/sitesetting*') or  Request::is('admin/changelogo*') or  Request::is('admin/admincommission*') ) ? 'active' : '' }}">
                    <i class="fa fa-cogs"></i>
                    <span>Configuration</span>
                </a>
                <ul class="sub" style="{{( Request::is('admin/editprofile*') or  Request::is('admin/changepassword*') or  Request::is('admin/timesettings*')  or  Request::is('admin/admindeliverycharge*')  )? 'display: block;' : '' }}">
                    <li class="{{ Request::is('admin/changepassword*') ? 'active' : '' }}">
                        <a class="{{ Request::is('admin/changepassword*') ? 'active' : '' }}" href="{{ URL::to( 'admin/changepassword') }}">
                            Change Password
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/editprofile*') ? 'active' : '' }}"> 
                        <a class="{{ Request::is('admin/editprofile*') ? 'active' : '' }}" href="{{ URL::to( 'admin/editprofile') }}">
                            Edit Profile
                        </a>
                    </li>

                    <li class="{{ Request::is('admin/sitesetting*') ? 'active' : '' }}"> 
                        <a class="{{ Request::is('admin/sitesetting*') ? 'active' : '' }}" href="{{ URL::to( 'admin/sitesetting') }}">
                            Site Configuration
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/changelogo*') ? 'active' : '' }}"> 
                        <a class="{{ Request::is('admin/changelogo*') ? 'active' : '' }}" href="{{ URL::to( 'admin/changelogo') }}">
                            Site Logo
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sub-menu">
                <a href="javascript:;" class="{{Request::is('admin/restaurants*') ? 'active' : '' }}" >
                    <i class="fa fa-cutlery"></i>
                    <span>Restaurants</span>
                </a>
                <ul class="sub" style="{{Request::is('admin/restaurants*') ? 'display: block;' : '' }}">
                    <li class="{{ (Request::is('admin/restaurants/admin_index') OR Request::is('admin/restaurants/Admin_edituser*') OR Request::is('admin/restaurants/Admin_slotmanagemnt*')) ? 'active' : '' }}">{{ link_to('/admin/restaurants/admin_index', 'Restaurants List', ['escape' => false]) }}</li>
                    <li class="{{ Request::is('admin/restaurants/admin_add') ? 'active' : '' }}">{{ link_to('/admin/restaurants/admin_add', 'Add Restaurant', ['escape' => false]) }}</li>
                </ul>
            </li>

            <li class="sub-menu">
                <a href="javascript:;" class="{{Request::is('admin/customer*') ? 'active' : '' }}" >
                    <i class="fa fa-users"></i>
                    <span>Customers</span>
                </a>
                <ul class="sub" style="{{Request::is('admin/customer*') ? 'display: block;' : '' }}">
                    <li class="{{ (Request::is('admin/customer/admin_index') OR Request::is('admin/customer/Admin_edituser*')) ? 'active' : '' }}">{{ link_to('/admin/customer/admin_index', 'Customers List', ['escape' => false]) }}</li>
                    <li class="{{ Request::is('admin/customer/admin_add') ? 'active' : '' }}">{{ link_to('/admin/customer/admin_add', 'Add Customer', ['escape' => false]) }}</li>
                </ul>
            </li>
            <li class="sub-menu">
                <a href="javascript:;" class="{{Request::is('admin/order*') ? 'active' : '' }}" >
                    <i class="fa fa-tasks"></i>
                    <span>Orders</span>
                </a>
                <ul class="sub" style="{{Request::is('admin/order*') ? 'display: block;' : '' }}">
                    <li class="{{ Request::is('admin/order/admin_index') ? 'active' : '' }}">{{ link_to('/admin/order/admin_index', 'Orders List', ['escape' => false]); }}</li>
                </ul>
            </li>
            <li class="sub-menu">
                <a href="javascript:;" class="{{Request::is('admin/payments*') ? 'active' : '' }}" >
                    <i class="fa fa-money"></i>
                    <span>Payments</span>
                </a>
                <ul class="sub" style="{{Request::is('admin/payments*') ? 'display: block;' : '' }}">
                    <li class="{{ (Request::is('admin/payments')) ? 'active' : '' }}">{{ link_to('/admin/payments', 'Payment History', ['escape' => false]) }}</li>
                </ul>
            </li>
            <li class="sub-menu">
                <a href="javascript:;" class="{{Request::is('admin/reservations*') ? 'active' : '' }}" >
                    <i class="fa fa-ticket"></i>
                    <span>Reservations</span>
                </a>
                <ul class="sub" style="{{Request::is('admin/reservations*') ? 'display: block;' : '' }}">
                    <li class="{{ (Request::is('admin/reservations/admin_index')) ? 'active' : '' }}">{{ link_to('/admin/reservations/admin_index', 'Reservations List', ['escape' => false]) }}</li>
                </ul>
            </li>
            <li class="sub-menu">
                <a href="javascript:;"   class="{{Request::is('admin/page*') ? 'active' : '' }}">
                    <i class="fa  fa-files-o"></i>
                    <span>Pages</span>
                </a>
                <ul class="sub" style="{{Request::is('admin/page*') ? 'display: block;' : '' }}">
                    <li class="{{ (Request::is('admin/page/admin_index') OR Request::is('admin/page/Admin_editpage*')) ? 'active' : '' }}">{{ link_to('/admin/page/admin_index', 'Pages List', ['escape' => false]) }}</li>
                </ul>
            </li>
        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>