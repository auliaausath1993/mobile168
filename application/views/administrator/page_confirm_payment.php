<?php include "includes/header.php"; ?>
<!-- Content
================================================== -->
<div id="page-wrapper">

    <div class="container-fluid">

	    	<!-- Page Heading -->
	    <div class="row">
	        <div class="col-lg-12">
	            <h1 class="page-header">
	                Konfirmasi Pembayaran
	            </h1>
	            <ol class="breadcrumb">
	                <li class="active">
	                    <i class="fa fa-fw fa-edit"></i> Konfirmasi Pembayaran
	                </li>
	            </ol>
	        </div>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>

    	<?php $status = $this->uri->segment(4) ?>

    	<div class="row">
    		<div class="col-md-12 form-inline">
    			<div class="form-group">
    				<label for="status">Status Pembayaran</label>
    				<select class="form-control" id="status">
    					<option <?= $status == 'all' || $status == '' ? 'selected' : '' ?> value="all">Semua Status</option>
    					<option <?= $status == 'Approve' ? 'selected' : '' ?> value="Approve">Approve</option>
    					<option <?= $status == 'Pending' ? 'selected' : '' ?> value="Pending">Pending</option>
    				</select>
    			</div>
    		</div>
    	</div>

		<?= $output->output; ?>
    </div>

 </div>

<?php include "includes/footer.php"; ?>
<?php if($output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
	<?php } ?>

<script>
function setBg() {
	$('.Approve').parent().parent().find('td').css('background-color', '#dff0d8');
}
$(document).ajaxSuccess(function(event, xhr, settings) {
	var url = settings.url.split('/');
	if (url.includes('confirm_payment')) {
		setBg();
	}
});

$(document).ready(function() {
	setBg();
});

$('#status').change(function() {
	var status = $(this).val();
	var base_url = '<?= base_url('administrator/main/confirm_payment')  ?>';
	window.location.href = base_url + '/' + status;
});
</script>