<?php include "includes/header.php"; ?>

<div id="page-wrapper">

	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Settings <small> Edit profile</small>
				</h1>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-user"></i> Edit Profile
					</li>
				</ol>
			</div>
		</div>
		<!-- /.row -->
		<div class="row">
			<div class="col-lg-12">
				<?= $this->session->flashdata('message'); ?>
				<?php echo validation_errors(); ?>
				<ul id="myTab" class="nav nav-tabs">
					<li class="active"><a href="#general_settings" data-toggle="tab"><b>Update profile</b></a></li>

				</ul>
				<div class="panel">
					<div class="panel-body">
						<?=form_open('administrator/main/edit_profile_process');?>

						<div id="myTabContent" class="tab-content" >

							<!-- General Settings -->
							<div class="tab-pane fade active in" id="general_settings">
								<div class="form-horizontal">
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_name">Email</label>
										<div class="col-lg-6">
											<input type="text" class="form-control" id="user_email"  placeholder="user_email" name="user_email" value="<?= $data_user['user_email'] ?>">
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_email">Username</label>
										<div class="col-lg-6">
											<input type="text" class="form-control" id="user_name" placeholder="Nama" name="user_name" value="<?= $data_user['user_name'] ?>" />
										</div>
										<div class="clearfix"></div>
									</div>

									<h4>Set new password</h4>
									<div class="form-group">
										<label class="control-label col-lg-2" for="user_pass">Password</label>
										<div class="col-lg-6">

											<input type="password" class="form-control" id="user_pass" placeholder="password" name="user_pass" >
											<br/><br/>
											<small>Empty if you not change password</small>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2" for="pincode">Pincode</label>
										<div class="col-lg-6">
											<input type="password" class="form-control" id="pincode" placeholder="Pincode" name="pincode" >
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						</div>

						<button type="submit" name="update_settings" class="btn btn-success">Update Settings</button>
						<?=form_close()?>
					</div>
				</div>
			</div>
		</div>
		<!-- /.row -->
	</div>
</div>

<script src="<?= base_url() ?>application/views/administrator/assets/js/nicedit/nicEdit.js" type="text/javascript"></script>
<script type="text/javascript">
	bkLib.onDomLoaded(function() {
		new nicEditor().panelInstance('signature');
		new nicEditor().panelInstance('new_member_registration');
		new nicEditor().panelInstance('new_order_invoice');
		new nicEditor().panelInstance('order_invoice_paid');
		new nicEditor().panelInstance('order_invoice_cancel');
		new nicEditor().panelInstance('forgot_password');
		new nicEditor().panelInstance('newsletter_registration');
		new nicEditor().panelInstance('admin_new_order');
		new nicEditor().panelInstance('admin_new_payment_confirmation');
	});
</script>
<?php include "includes/footer.php"; ?>