<?php include "includes/header.php"; ?>
<!-- Content
================================================== -->
<div id="page-wrapper">

    <div class="container-fluid">

	    	<!-- Page Heading -->
	    <div class="row">
	        <div class="col-lg-12">
	            <h1 class="page-header">
	              	Data User
	            </h1>
	            <ol class="breadcrumb">
	                <li class="active">
	                    <i class="fa fa-dashboard"></i> Data User
	                </li>
	            </ol>
	        </div>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>
		<div class="text-right m-b">
			<button title="form-add" class="btn btn-success btn-add">Tambah User</button>
		</div>
		<?= $output->output; ?>
    </div>
    <form method="post" id="form-delete" action="<?= base_url('administrator/main/delete_user') ?>">
    	<input type="hidden" name="id" class="form-id">
    </form>
    <form method="post" id="form-edit" action="<?= base_url('administrator/main/edit_user') ?>">
    	<input type="hidden" name="id" class="form-id">
    </form>
    <form method="post" id="form-add" action="<?= base_url('administrator/main/add_new_user') ?>">
    </form>
 </div>
<?php if($output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
	<?php } ?>
<?php include "includes/footer.php"; ?>

<script>
	$(document).on('click', '.btn-edit, .btn-delete, .btn-add', function(e) {
		e.preventDefault();
		var id = $(this).attr('no');
		$('#modal-pincode').modal('show');
		var title = $(this).attr('title');
		$('#title-pincode').val(title);
		$('#var-no').val(id);
    });
	$('#submit-pincode').click(function() {
		var code = $('#pincode').val();
		var id = $('#var-no').val();
		var title = $('#title-pincode').val();
		$.post(base_url + 'administrator/main/check_pincode', { code: code }, function(data) {
			$('.form-pincode').show();
			$('.loading-pincode').hide();
			if (data.status == 'Success') {
				$('.form-id').val(id);
				$('#' + title).submit();
	    	} else {
	    		alert('Pincode salah');
	    	}
	    }, 'json');
	});
</script>