<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <button class="btn btn-info btn-md" id="openPopupBtn"><?= _l('select_filter_to_apply') ?></button>
                            <div id="popupListContainer" class="popup-list-container">
                                <ul class="popup-list">
                                    <li><a class="filter_name_202" onclick="applyFilter(0)"><?= _l('remove_all_filters') ?></a></li>
                                    <?php foreach ($all_filters as $filter){ ?>
                                        <li id="li_<?= $filter['id'] ?>"><a onclick="applyFilter(<?= $filter['id'] ?>)" class="filter_name_202" style="width:80% !important;"><?= $filter['filter_name'] ?></a>
                                            <a href="<?php echo admin_url('prospects/downloadCsv/'.$filter['id']); ?>" class="filter_name_202" style="text-align: right; width:10%!important; color: #dc3545;"><div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?= _l('download_csv') ?>"><i class="fa fa-download"></i></div></a>
                                            <a onclick="deleteProspectFilter(<?= $filter['id'] ?>)" class="filter_name_202" style="text-align: right; width:10%!important; color: #dc3545;"><div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?= _l('delete') ?>"><i class="fa fa-trash"></i></div></a>
                                            <a onclick="showProspectFilterDetails(<?= $filter['id'] ?>)" class="filter_name_202" style="text-align: right; width:10%!important;"><div class="btn-group pull-right mleft4 btn-with-tooltip-group" data-toggle="tooltip" data-title="<?= _l('view_selections') ?>"><i class="fa fa-eye"></i></div></a></li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <a href="#" data-toggle="modal" class="btn btn-info pull-right display-block" data-target="#add-edit-filter"><i class="fa fa-plus"></i> <?php echo _l('new_filter'); ?></a>
                            <!--                            <a href="" class="btn btn-info pull-left display-block">--><?php //echo _l('new_filter'); ?><!--</a>-->
                        </div>
                        <input type="hidden" id="filter_id" name="filter_id" value="0">
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <table class="table table-prospects table-responsive" id="user_table">
                            <thead>
                                <th><?= _l('name') ?></th>
                                <th><?= _l('email') ?></th>
                                <th><?= _l('education') ?></th>
                                <th><?= _l('linked_in') ?></th>
                                <th><?= _l('religion') ?></th>
                                <th><?= _l('address') ?></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add-edit-filter" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
<!--        --><?php //echo form_open(admin_url('prospects/add_edit_members/'.$project->id)); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('filter'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="filter_name"><?= _l('filter_name'); ?> </label>
                        <input type="text" name="filter_name" id="filter_name" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="operator"><?= _l('operator'); ?> </label>
                        <select name="operator" id="operator" class="selectpicker form-control">
                            <option value="or" selected>or</option>
                            <option value="and">and</option>
                        </select>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="col-md-2">
                        <input type="checkbox" name="has_email" id="has_email" class="form-group" value="1"> <label for="has_email"><?= _l('has_email'); ?></label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" name="has_phone" id="has_phone" class="form-group" value="1"> <label for="has_phone"><?= _l('has_phone'); ?></label>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" name="has_address" id="has_address" class="form-group" value="1"> <label for="has_address"><?= _l('has_address'); ?></label>
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" name="has_linkedin" id="has_linkedin" class="form-group" value="1"> <label for="has_linkedin"><?= _l('has_linkedin'); ?></label>
                    </div>
                    <div class="col-md-3">
                        <input type="checkbox" name="part_of_company" id="part_of_company" class="form-group" value="1"> <label for="part_of_company"><?= _l('part_of_company'); ?></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 pt-12">
                        <label for="gender"><?= _l('gender'); ?> </label>
                        <select name="gender" id="gender" class="selectpicker form-control" data-live-search="true" data-none-selected-text="<?= _l('select_gender'); ?>">
                            <option value=""></option>
                            <option value="male"><?= _l('male'); ?></option>
                            <option value="female"><?= _l('female'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-4 pt-12">
                        <label for="political_affiliation"><?= _l('political_affiliation'); ?> </label>
                        <select name="political_affiliation" id="political_affiliation" class="selectpicker form-control" data-live-search="true" data-none-selected-text="<?= _l('select_political_affiliation'); ?>">
                            <option value=""></option>
                            <option value="republication"><?= _l('republication'); ?></option>
                            <option value="democrat"><?= _l('democrat'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-4 pt-12">
                        <label for="country"><?= _l('country'); ?> </label>
                        <select name="country" id="country" class="selectpicker form-control" data-live-search="true" data-none-selected-text="<?= _l('select_country'); ?>">
                            <option value=""></option>
                            <?php foreach ($countries as $country){ ?>
                                <option value="<?= strtolower($country['short_name']) ?>"><?= $country['short_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 pt-12">
                        <label for="state"><?= _l('state'); ?> </label>
                        <input type="text" name="state" id="state" class="form-control">
                    </div>

                    <div class="col-md-4 pt-12">
                        <label for="zip_code"><?= _l('zip_code'); ?> </label>
                        <input type="text" name="zip_code" id="zip_code" class="form-control">
                    </div>

                    <div class="col-md-4 pt-12">
                        <label for="job_position"><?= _l('job_position'); ?> </label>
                        <input type="text" name="job_position" id="job_position" class="form-control">
                    </div>

                    <div class="col-md-4 pt-12">
                        <label for="industry"><?= _l('industry'); ?> </label>
                        <input type="text" name="industry" id="industry" class="form-control">
                    </div>

                    <div class="col-md-4 pt-12">
                        <label for="salary"><?= _l('salary'); ?> </label>
                        <input type="number" name="salary" id="salary" class="form-control">
                    </div>

                </div>

                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" onclick="saveFilter()"><?php echo _l('apply_&_save'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
<!--        --><?php //echo form_close(); ?>
    </div>
</div>

<div class="modal fade" id="view-filter-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="selectedFilterTitle"></h4>
            </div>
            <div class="modal-body">
                <table class="table table-responsive table-bordered appendRow">
                        <tr id="operatorTr">
                            <td><b>operator</b></td>
                            <td id="operatorValue"></td>
                        </tr>
                    </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<style>
    .pt-12{
        padding-top: 12px;
    }


    .popup-list-container {
        display: none;
        position: absolute;
        background-color: white;
        border: 1px solid #ccc;
        padding: 10px;
        width: 20%;
        z-index: 9999; /* Ensure it appears over other content */
    }

    .popup-list {
        list-style-type: none;
        padding: 0;
    }

    .popup-list li {
        padding: 5px 10px;
    }

    .popup-list li:hover {
        background-color: #f0f0f0;
    }

    .filter_name_202{
        cursor: pointer;
        color: black;
    }
</style>

<?php init_tail(); ?>

<!-- Include jQuery and DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    var table = null;
    $(document).ready(function() {
        tableInitialization();
});
    function tableInitialization() {
        table = $('#user_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ordering": false,
            "ajax": {
                "url": "<?php echo admin_url('prospects/table'); ?>",
                "type": "GET",
                "data": function (d) {
                    d.filterId = $("#filter_id").val();
                }
            },
            "pageLength": 10,
            "language": {
                "emptyTable": "No records found"
            },
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    orderable: false
                },
                {
                    data: 'email',
                    name: 'email',
                    orderable: false
                },
                {
                    data: 'education_level',
                    name: 'education_level',
                    orderable: false
                },
                {
                    data: 'li_profile',
                    name: 'li_profile',
                    orderable: false
                },
                {
                    data: 'religion',
                    name: 'religion',
                    orderable: false
                },
                {
                    data: 'address',
                    name: 'address',
                    orderable: false
                },
            ],
        });
    }

    function saveFilter() {
        if ($("#filter_name").val() == ''){
            alert('filter name is required');
            return;
        }
        var filterData = {
            filter_name : $("#filter_name").val(),
            operator : $("#operator").val(),
            has_email : $("#has_email").is(':checked') ? 1 : 0,
            has_phone : $("#has_phone").is(':checked') ? 1 : 0,
            has_address : $("#has_address").is(':checked') ? 1 : 0,
            has_linkedin : $("#has_linkedin").is(':checked') ? 1 : 0,
            part_of_company : $("#part_of_company").is(':checked') ? 1 : 0,
            gender : $("#gender").val(),
            political_affiliation : $("#political_affiliation").val(),
            country : $("#country").val(),
            state : $("#state").val(),
            zip_code : $("#zip_code").val(),
            job_position : $("#job_position").val(),
            industry : $("#industry").val(),
            salary : $("#salary").val(),
        }
        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('prospects/saveFilters'); ?>',
            data: filterData,
            dataType: 'json',
            success: function(response) {
                // Handle response
                if (response.status === 'success') {
                    $("#filter_id").val(response.inserted_id);
                    $('#add-edit-filter').modal('hide');
                    table.ajax.reload();
                } else {
                    alert(response.message);

                }
            },
            error: function() {
                alert('Error: Failed to send AJAX request.');
            }
        });
    }
    function applyFilter(filterId) {
        $("#filter_id").val(filterId);
        table.ajax.reload();
    }

    document.getElementById('openPopupBtn').addEventListener('click', function() {
        var buttonRect = this.getBoundingClientRect();
        var popupListContainer = document.getElementById('popupListContainer');

        popupListContainer.style.display = 'block';
        // popupListContainer.style.top = (buttonRect.bottom + window.scrollY) + 'px';
        // popupListContainer.style.left = buttonRect.left + 'px';
    });

    // Close the popup when clicking outside of it
    document.addEventListener('click', function(event) {
        closePopup();
    });

    function closePopup(){
        var popupListContainer = document.getElementById('popupListContainer');
        if (!popupListContainer.contains(event.target) && event.target !== document.getElementById('openPopupBtn')) {
            popupListContainer.style.display = 'none';
        }
    }

    function deleteProspectFilter(filterId) {
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: '<?php echo admin_url('prospects/deleteFilter'); ?>',
                type: 'POST',
                data: {filter_id: filterId},
                success: function(response) {
                    // Handle success response
                    $("#li_"+filterId).hide();
                    var selectedFilter = $("#filter_id").val();
                    if (selectedFilter == filterId){
                        $("#filter_id").val(0);
                        table.ajax.reload();
                    }
                    // You can also reload the page or update the UI accordingly
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                }
            });
        }
    }

    function showProspectFilterDetails(filterId) {
        closePopup();

        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('prospects/getFilterDetails'); ?>',
            data: {filter_id: filterId},
            dataType: 'json',
            success: function(response) {
                // Handle response
                if (response.status === 'success') {
                    $("#selectedFilterTitle").html(response.data.filter_name);

                    $("#operatorTr").nextUntil("#operatorTr", "tr").remove();

                    $("#operatorValue").html(response.data.operator);

                    if (response.data.has_email == 1){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('has_email') ?></b></td>'+
                            '<td>true</td>'+
                            '</tr>');
                    }

                    if (response.data.has_phone == 1){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('has_phone') ?></b></td>'+
                            '<td>true</td>'+
                            '</tr>');
                    }

                    if (response.data.has_address == 1){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('has_address') ?></b></td>'+
                            '<td>true</td>'+
                            '</tr>');
                    }

                    if (response.data.has_linkedin == 1){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('has_linkedin') ?></b></td>'+
                            '<td>true</td>'+
                            '</tr>');
                    }

                    if (response.data.part_of_company == 1){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('part_of_company') ?></b></td>'+
                            '<td>true</td>'+
                            '</tr>');
                    }

                    if (response.data.gender !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('gender') ?></b></td>'+
                            '<td>'+response.data.gender+'</td>'+
                            '</tr>');
                    }

                    if (response.data.country !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('country') ?></b></td>'+
                            '<td>'+response.data.country+'</td>'+
                            '</tr>');
                    }

                    if (response.data.state !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('state') ?></b></td>'+
                            '<td>'+response.data.state+'</td>'+
                            '</tr>');
                    }

                    if (response.data.political_affiliation !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('political_affiliation') ?></b></td>'+
                            '<td>'+response.data.political_affiliation+'</td>'+
                            '</tr>');
                    }

                    if (response.data.zip_code !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('zip_code') ?></b></td>'+
                            '<td>'+response.data.zip_code+'</td>'+
                            '</tr>');
                    }

                    if (response.data.job_position !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('job_position') ?></b></td>'+
                            '<td>'+response.data.job_position+'</td>'+
                            '</tr>');
                    }

                    if (response.data.industry !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('industry') ?></b></td>'+
                            '<td>'+response.data.industry+'</td>'+
                            '</tr>');
                    }

                    if (response.data.salary !== ''){
                        $(".appendRow").append('<tr>'+
                            '<td><b><?= _l('salary') ?></b></td>'+
                            '<td>'+response.data.salary+'</td>'+
                            '</tr>');
                    }


                    $("#view-filter-details").modal('show');
                } else {
                    alert(response.message);

                }
            },
            error: function() {
                alert('Error: Failed to send AJAX request.');
            }
        });
    }
</script>
</body>
</html>