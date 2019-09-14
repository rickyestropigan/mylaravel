
{{ HTML::style('public/css/front/popup_list.css') }}
<script>
    function showpop(salonid)
    {
        document.getElementById('light' + salonid).style.display = 'block';
    }
    function closepop(salonid)
    {
        document.getElementById('light' + salonid).style.display = 'none';
    }
</script>
<?php
if (!$mainorders->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/order/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Orders List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <th>{{ SortableTrait::link_to_sorting_action('order_number', 'Order Number') }}</th>
                                    <th>{{ "Customer Name" }}</th>

                                    <th>{{ SortableTrait::link_to_sorting_action('created', 'Created') }}</th>
                                    <th class="bjhuh">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($mainorders as $order) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Order Number">
                                            {{ $order->order_number; }}
                                        </td>
                                        <td data-title="Customer Name">
                                            <?php
                                            $userData = DB::table('users')
                                                    ->where('id', $order->user_id)
                                                    ->first();
                                            if (empty($userData)) {
                                                echo "Not Available";
                                            } else {
                                                echo ucwords($userData->first_name . ' ' . $userData->last_name);
                                            }
                                            ?>
                                        </td>

                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($order->created)) }}
                                        </td>
                                        <td data-title="Action">
        <?php
        echo html_entity_decode(HTML::link('admin/order/view/' . $order->slug, '<i class="fa fa-search"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'View Details')));
        ?>
                                        </td>	
                                    </tr>
        <?php
        $i++;
    }
    ?>
                            </tbody>
                        </table>
                    </section>
                </div>
            </section>
        </div>
    </div>
  <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body border-bottom">
                    <div class="dataTables_paginate paging_bootstrap pagination">
                        {{ $mainorders->appends(Input::except('page'))->links() }}
                    </div>
                </div>
                
            </section>
        </div>
    </div>
    {{ Form::close() }} 

<?php } else {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Orders List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no Order added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>