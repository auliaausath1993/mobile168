<?php include "includes/header.php"; ?>
<!-- Content
================================================== -->
<div id="page-wrapper">


    <div class="container-fluid">

	    	<!-- Page Heading -->
	    <div class="row">
	        <div class="col-lg-12">
	            <h1 class="page-header">
	                Data Pelanggan
	            </h1>
	            <ol class="breadcrumb">
	                <li class="active">
	                    <i class="fa fa-fw fa-list"></i> Data Pelanggan
	                </li>
	            </ol>
	        </div>
	    </div>
	    <!-- /.row -->

    	<?=$this->session->flashdata('message') ?>
		<div class="pull-right" style="margin-bottom:15px;">
			<a href="<?=base_url()?>administrator/main/add_new_customer" class="btn btn-success">Tambah Customer</a>
		</div>
		<div id="list_ubah_status">
		<?= $output->output; ?>
		</div>
		<br/>
		<?php if($this->session->userdata('webadmin_user_level') != 'Staf_admin') { ?>
			 <div>
				<div class="col-sm-2 select-menu">
					<select class="select_action form-control">
						<option value="">-Pilih Action-</option>
						<?php if($this->total_available_space_customer != 0 || $this->total_max_customer == 'Unlimited')  { ?>
							<option value="active">Active</option>
							<option value="inactive">Inactive</option>
						<?php } ?>
						<option value="delete">Delete</option>
					</select>
				</div>
					<input type="button" class="btn btn-md btn-success" id="btn_check_lunas" value="SUBMIT" />
				<?php if($this->total_available_space_customer != 0 || $this->total_max_customer == 'Unlimited')  { ?>
				<div class="pull-right">
					<form method="post" action="<?=base_url() ?>administrator/main/update_status_customer_all">
						<input type="submit" class="btn btn-success btn-sm" name="status" value="ACTIVE ALL" onClick="return confirm('Anda yakin ingin mengaktivkan semua member ?');">
						<input type="submit" class="btn btn-danger btn-sm" name="status" value="INACTIVE ALL" onClick="return confirm('Anda yakin ingin menginaktivkan semua member ?');">
					</form>
				</div>
				<?php } ?>
			</div>
		<?php } ?>

    </div>

 </div>

<?php include "includes/footer.php"; ?>
<?php if($output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
	<?php } ?>
<script type="text/javascript">
$("#btn_check_lunas").click(function(){
	var list = "";
	var base_url = "<?=base_url() ?>";

	var ch = new Array();

	$('#list_ubah_status input:checkbox:checked').each(function() {

		ch.push($(this).val());

	});

	var nilai = "";

	$('.select_action option:selected').each(function() {
		nilai += $( this ).val();
	});


	var list_status = "["+ch+"]";

	if(nilai == 'active'){

		$.post( base_url+"administrator/main/update_status_customer_active", { list_status : list_status}, function( data ) {

			if(data.pesan == 'Success')
			{
				window.location.reload(true);
			}
			else
			{
				alert("Data pesanan gagal diubah");
			}


		}, "json");
	}
	if(nilai == 'inactive'){

		$.post( base_url+"administrator/main/update_status_customer_inactive", { list_status : list_status}, function( data ) {

			if(data.pesan == 'Success')
			{
				window.location.reload(true);
			}
			else
			{
				alert("Data pesanan gagal diubah");
			}


		}, "json");
	}

	if(nilai == 'delete'){

		$.post( base_url+"administrator/main/update_status_customer_delete", { list_status : list_status}, function( data ) {

			if(data.pesan == 'Success')
			{
				window.location.reload(true);
			}
			else
			{
				alert("Data pesanan gagal diubah");
			}


		}, "json");
	}

});
</script>



