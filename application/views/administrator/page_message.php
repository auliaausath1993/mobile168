<?php 

include "includes/header.php"; 

?>



<!-- Content

================================================== -->

<section id="content">



  <!-- Headings & Paragraph Copy -->

  <div class="row">
	

	<form action="<?= base_url() ?>administrator/crud/update_settings" method="post" enctype="multipart/form-data" >

	

    <div class="span12">

	

	<h3>Settings</h3>

	

	<?php echo $error;?>

	

	<?= $this->session->flashdata('setting_message'); ?>

	

	<ul id="myTab" class="nav nav-tabs">

              <li class="active"><a href="#general_settings" data-toggle="tab"><b>General settings</b></a></li>

              <li class=""><a href="#mail_settings" data-toggle="tab">Mail template settings</a></li>

			  <li class=""><a href="#seo_settings" data-toggle="tab">SEO settings</a></li>

             

            </ul>

            <div id="myTabContent" class="tab-content">

				

				<!-- General Settings -->

				<div class="tab-pane fade active in" id="general_settings">

					<div class="form-horizontal">

						<div class="control-group">

							<label class="control-label" for="store_name">Store name</label>

							<div class="controls">

							<input type="text" class="span8" id="store_name" placeholder="Store name" name="store_name" value="<?= $crud->settings_value('store_name') ?>">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="store_description">Store description</label>

							<div class="controls">

							<textarea class="span8" id="store_description" placeholder="Store description" name="store_description" ><?= $crud->settings_value('store_description') ?></textarea>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="site_url">Site URL</label>

							<div class="controls">

							<input type="text" class="span8" id="site_url" placeholder="http://youronlineshop.com" name="site_url" value="<?= $crud->settings_value('site_url') ?>">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="email_administrator">Email administrator</label>

							<div class="controls">

							<input type="text" class="span8" id="email_administrator" placeholder="admin@youronlineshop.com" name="" value="<?= $crud->settings_value('email_administrator') ?>">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="logo">Logo (URL)</label>

							<div class="controls">

							<input type="text" class="span6" id="logo" name="logo" value="<?= $crud->settings_value('logo') ?>"><br/><br/>

							<label class="checkbox"><input type="checkbox" name="upload_logo" value="Yes" /> Use upload</label><input type="file" id="file_logo" name="file_logo">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="logo_small">Logo small (URL)</label>

							<div class="controls">

							<input type="text" class="span6" id="logo_small" name="logo_small"value="<?= $crud->settings_value('logo_small') ?>"><br/><br/>

							<label class="checkbox"><input type="checkbox" name="upload_logo_small" value="Yes" /> Use upload</label><input type="file" id="file_logo_small" name="file_logo_small">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="icon">Icon (URL)</label>

							<div class="controls">

							<input type="text" class="span6" id="icon" name="icon" <?= $crud->settings_value('icon') ?>><br/><br/>

							<label class="checkbox"><input type="checkbox" name="upload_icon" value="Yes" /> Use upload</label><input type="file" id="file_icon" name="file_icon">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="store_description">Signature</label>

							<div class="controls">

							<textarea class="span8" id="signature" placeholder="signature" name="signature" ><?= $crud->settings_value('signature') ?></textarea>

							</div>

						</div>

					</div>

				</div>

				<div class="tab-pane fade" id="mail_settings">

					<div class="form-horizontal">

						<div class="control-group">

							<label class="control-label" for="new_member_registration">New member registration</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="new_member_registration" name="new_member_registration" ><?= $crud->settings_value('new_member_registration') ?></textarea>

							<br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="new_order_invoice">New order invoice</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="new_order_invoice" name="new_order_invoice" ><?= $crud->settings_value('new_order_invoice') ?></textarea>

                            <br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="order_invoice_paid">Order invoice paid</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="order_invoice_paid" name="order_invoice_paid" ><?= $crud->settings_value('order_invoice_paid') ?></textarea>

                            <br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="order_invoice_cancel">Order invoice cancel</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="order_invoice_cancel" name="order_invoice_cancel" ><?= $crud->settings_value('order_invoice_cancel') ?></textarea>

                            <br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="forgot_password">Forgot password</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="forgot_password" name="forgot_password" ><?= $crud->settings_value('forgot_password') ?></textarea>

                            <br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="newsletter_registration">Newsletter registration</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="newsletter_registration" name="newsletter_registration" ><?= $crud->settings_value('newsletter_registration') ?></textarea>

                            <br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="admin_new_order">Admin new order</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="admin_new_order" name="admin_new_order" ><?= $crud->settings_value('admin_new_order') ?></textarea>

                            <br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="admin_new_payment_confirmation">Admin new payment confirmation</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" id="admin_new_payment_confirmation" name="admin_new_payment_confirmation" ><?= $crud->settings_value('admin_new_payment_confirmation') ?></textarea>

                            <br/>

							<a href="#myModal" role="button" class="btn btn-info" data-toggle="modal">Lihat shortcode</a>

							</div>

						</div>

					</div>

				</div>

				<div class="tab-pane fade" id="seo_settings">

					<div class="form-horizontal">

						<div class="control-group">

							<label class="control-label" for="meta_title">Meta title</label>

							<div class="controls">

							<input type="text" class="span8" id="meta_title" name="meta_title" value="<?= $crud->settings_value('meta_title') ?>">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="meta_keyword">Meta keyword</label>

							<div class="controls">

							<input type="text" class="span8" id="meta_keyword" placeholder="Fashion wanita, t-shirt, Shoes" name="meta_keyword" value="<?= $crud->settings_value('meta_keyword') ?>">

							</div>

						</div>

						<div class="control-group">

							<label class="control-label" for="meta_description">Meta description</label>

							<div class="controls">

							<textarea style="height:200px;" class="span8" placeholder="Our store is the best of Clothing collections" id="meta_description" name="meta_description" ><?= $crud->settings_value('meta_description') ?></textarea>

							</div>

						</div>

					</div>

				</div>

            </div>

			

			<button type="submit" name="update_settings" class="btn btn-success">Update Settings</button>

			

	</div>

	

	</form>

	

  </div>

  

	

	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

		<div class="modal-header">

		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

		<h3 id="myModalLabel">Shortcode documentation</h3>

		</div>

		<div class="modal-body">

		

			<table class="table table-bordered">

				<thead>

					<tr class="alert alert-success">

						<th>Shortcode</th>

						<th>Fungsi</th>

					</tr>

				</thead>

				<tbody>

					<tr>

						<td><strong>{%customer_name%}</strong></td>

						<td>Nama customer</td>

					</tr>

                    <tr>

						<td><strong>{%customer_email%}</strong></td>

						<td>Email customer</td>

					</tr>

                    <tr>

						<td><strong>{%customer_password%}</strong></td>

						<td>Password customer</td>

					</tr>

					<tr>

						<td><strong>{%store_name%}</strong></td>

						<td>Nama online store</td>

					</tr>

					<tr>

						<td><strong>{%order_invoice%}</strong></td>

						<td>Nomor invoice / order pesanan</td>

					</tr>

                    <tr>

						<td><strong>{%order_date%}</strong></td>

						<td>Tanggal invoice / order pesanan</td>

					</tr>

					<tr>

						<td><strong>{%order_detail%}</strong></td>

						<td>Detail order / pesanan</td>

					</tr>

					<tr>

						<td><strong>{%order_ship_address%}</strong></td>

						<td>Menampilkan alamat pengiriman pesanan</td>

					</tr>

					<tr>

						<td><strong>{%billing_information%}</strong></td>

						<td>Menampilkan informasi billing / pembayaran</td>

					</tr>

					<tr>

						<td><strong>{%signature%}</strong></td>

						<td>Catatan kaki / signature email</td>

					</tr>

				</tbody>

			</table>

			

    </div>

		<div class="modal-footer">

		<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>

		</div>

	</div>

		

	

</section>

<script src="<?= base_url() ?>application/views/backend/assets/js/nicedit/nicEdit.js" type="text/javascript"></script>

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