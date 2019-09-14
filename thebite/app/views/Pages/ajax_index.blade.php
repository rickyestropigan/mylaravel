<?php
if (!$pages->isEmpty()) {
    ?>

    {{ Form::open(array('url' => 'admin/page/admin_index', 'method' => 'post', 'id' => 'adminAdd', 'files' => true,'class'=>"form-inline form")) }}
    <div class="row">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Pages List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">
                        <table class="table table-bordered table-striped table-condensed cf">
                            <thead class="cf">
                                <tr>
                                    <!--<th></th>-->
                                    <th class=" enable-sort" sort_type="" field="tbl_pages.first_name">Name</th>
                                    <th class="enable-sort" sort_type=""  field="tbl_pages.created">Created</th>
                                    <th class="bjhuh">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($pages as $page) {
                                    if ($i % 2 == 0) {
                                        $class = 'colr1';
                                    } else {
                                        $class = '';
                                    }
                                    ?>
                                    <tr>
                                        <td data-title="Name">
                                            {{ ucwords($page->name); }}
                                        </td>
                                        <td data-title="Created">
                                            {{  date("d M, Y h:i A", strtotime($page->created)) }}</td>

                                        <td data-title="Action">
                                            <?php
                                            echo html_entity_decode(HTML::link('admin/page/Admin_editpage/' . $page->slug, '<i class="fa fa-pencil"></i>', array('class' => 'btn btn-primary btn-xs', 'title' => 'Edit')));
                                            echo html_entity_decode(HTML::link('admin/page/Admin_deletepage/' . $page->slug, '<i class="fa fa-trash-o"></i>', array('title' => 'Delete', 'class' => 'btn btn-danger btn-xs action-list delete-list', 'escape' => false, 'onclick' => "return confirmAction('delete');")));
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

            <!--                    Number of pages <span class="badge-gray"> </span> - <span class="badge-gray"> </span> out of <span class="badge-gray"></span>-->

                    <div class="dataTables_paginate paging_bootstrap pagination">
                        {{ $pages->appends(Request::only('search','from_date','to_date'))->links() }}
                    </div>
                </div>
                <!--                <div class="panel-body">
                                    <button type="button" name="chkRecordId" onclick="checkAll(true);"  class="btn btn-success">Select All</button>
                                    <button type="button" name="chkRecordId" onclick="checkAll(false);" class="btn btn-success">Unselect All</button>
                <?php
                $arr = array(
                    "" => "Action for selected...",
                    'Activate' => "Activate",
                    'Deactivate' => "Deactivate",
                    'Delete' => "Delete",
                );
                //  echo form_dropdown("action", $arr, '', "class='small form-control' id='table-action'");
                ?>
                                    {{ Form::select('action', $arr, null, array('class'=>"small form-control",'id'=>'action')) }}
                
                                    <button type="submit" class="small btn btn-success btn-cons" onclick=" return isAnySelect();" id="submit_action">Ok</button>
                                </div>-->
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
                    pages List
                </header>
                <div class="panel-body">
                    <section id="no-more-tables">There are no page added on site yet.</section>
                </div>
            </section>
        </div>
    </div>  
<?php }
?>