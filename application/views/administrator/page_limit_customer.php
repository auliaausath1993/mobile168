<?php include 'includes/header.php';
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
?>

<section id="content" >
	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">
						Keep Limit <small> untuk mengubah batas limit keep customer</small>
					</h1>
					<ol class="breadcrumb">
						<li class="active">
							<i class="fa fa-dashboard"></i> Limit Keep
						</li>
					</ol>
				</div>
			</div>
			<div class="panel">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="search-wrapper" style="margin-bottom:10px;">
								<div class="col-sm-12">
									<form method="post" action="<?=base_url()?>administrator/main/search_limit_customer_session">
										<div class="search-wrapper " style="margin-bottom:10px;">
											<div class="radio" style="display: none" >
												<label>
													<input type="radio" name="radio_customer" id="radio_customer_name" value="customer" <?php if($arr['radio']=="customer") { ?> checked="checked" <?php } ?> checked>
													Pelanggan
												</label>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<input name="customer_name" id="customer_name" class="customer_name form-control span4" type="text" style="margin-left: 15px; display: none;" value="<?php if(!empty($arr['customer_name'])){echo $arr['customer_name'];}else{echo '';}?>" placeholder="Nama Pelanggan" >
												<input type="hidden" class="form-control" id="customer_id" name="customer_id" value="<?php if(!empty($arr['customer_id'])){echo $arr['customer_id'];}else{echo '';}?>" >
											</div>
										<br>
										<div class="search-wrapper col-sm-3" style="margin-top:-18px; margin-bottom:10px;">
											<button class="btn btn-primary"  style="margin-bottom:10px;"><i class="fa fa-fw fa-search"> </i>CARI DATA </button>
											<a href="<?=base_url() ?>administrator/main/limit_customer" class="btn btn-danger" style="margin-bottom:10px;">RESET</a>
										</div>
									</form>
								</div>
								<div class="col-sm-12 table-responsive">
									<form onsubmit="return confirm('Update Keep Limit ?');" action="<?= base_url('administrator/main/limit_customer_update') ?>" method="post">
									<?=$this->session->flashdata('message') ?>
									<table id="table-exsport" class="table table-bordered table-striped">
										<thead>
											<tr>
												<th width="3%">No</th>
												<!-- <th width="3%">Id Pelanggan</th> -->
												<th width="14%">Nama Pelanggan </th>
												<th width="14%">Tipe Customer </th>
												<th width="14%">Keep Limit </th>
											</tr>
										</thead>
										<tbody>
											<?php $i = 1 * ($offset + 1);
											foreach ($customer as $customers) { ?>
												<tr>
													<td><?=$i?></td>
													<!-- <td>
														<?= $customers->id_customer ?>
													</td> -->
													<td>
														<?= $customers->nama_customer ?>
													</td>
													<td>
														<?= $customers->tipe_customer ?>
													</td>
													<td class="form-inline">
														<input type="hidden" value="<?= $customers->id_customer ?>" name="item_id[]">

														<input type="text" class="form-control" name="keep_limit[]" id="keep_limit_<?= $customers->id_customer ?>" value ="<?= $customers->keep_limit ?>" style="text-transform: uppercase; width: 100%" />
													</td>
												</tr>
												<?php
												$i++;
											} ?>
										</tbody>
									</table>
									<?=$this->pagination->create_links(); ?>
									<p><button type="submit" name="go" class="btn btn-success" >UPDATE KEEP LIMIT</button></p>
								</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script>

<script type="text/javascript">
	var site = '<?= site_url() ?>';
	$(function() {
		$('#customer_name').autocomplete({
			serviceUrl: site + 'administrator/main/search_id_customer',
			onSelect: function(suggestion) {
				$('#customer_id').val(suggestion.data);
			}
		});
	});

	if ($('input:checked').val() == 'customer') {
		$('#customer_name').show();
	}

	if ($('input:checked').val() == 'tamu') {
		$('#tamu_name').show();
	}

	$('#radio_tamu_name').click(function() {
		$('#customer_name').hide();
		$('#tamu_name').show();
		$('#customer_id').prop('disabled', true);
		$('#tamu_name').prop('disabled', false);
	});

	$('#radio_customer_name').click(function() {
		$('#customer_name').show();
		$('#tamu_name').hide();
		$('#customer_id').prop('disabled', false);
		$('#tamu_name').prop('disabled', true);
	});
function validate(form) {

			if(!valid) {
				alert('Please correct the errors in the form!');
				return false;
			}
			else {
				return confirm('Do you really want to submit the form?');
			}
		}
</script>
	<form onsubmit="return validate(this);">
<?php include 'includes/footer.php'; ?>
