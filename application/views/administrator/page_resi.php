<?php include 'includes/header.php';
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
?>

<section id="content" >
	<div id="page-wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">
						Resi Pengiriman <small> Untuk memasukan no. resi pengiriman</small>
					</h1>
					<ol class="breadcrumb">
						<li class="active">
							<i class="fa fa-dashboard"></i> Resi Pengiriman
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
									<form method="post" action="<?=base_url()?>administrator/main/search_resi_session">
										<div class="search-wrapper " style="margin-bottom:10px;">
											<div class="radio">
												<label >
													<input type="radio" name="radio_customer" id="radio_customer_name" value="customer" <?php if($arr['radio']=="customer") { ?> checked="checked" <?php } ?> checked>
													Pelanggan
												</label>
												<label style="padding-left: 4%;" >

													<input type="radio" name="radio_customer" id="radio_tamu_name" value="tamu" <?php if($arr['radio']=="tamu") { ?> checked="checked" <?php } ?>>
													Non-Pelanggan
												</label>
											</div>
										</div>
										<div class="search-wrapper col-sm-3" style="margin-left:-15px; margin-bottom:10px;">
											<input name="customer_name" id="customer_name" class="customer_name form-control span4" type="text" style="display: none;" value="<?php if(!empty($arr['customer_name'])){echo $arr['customer_name'];}else{echo '';}?>" placeholder="Nama Pelanggan" >
											<input name="tamu_name" id="tamu_name" class="tamu_name form-control span4" type="text" style="display: none;" value="<?php if(!empty($arr['tamu_name'])){echo $arr['tamu_name'];}else{echo '';}?>" placeholder="Nama Tamu" >
											<input type="hidden" class="form-control" id="customer_id" name="customer_id" value="<?php if(!empty($arr['customer_id'])){echo $arr['customer_id'];}else{echo '';}?>" ><br/>
										</div>
										<div class="search-wrapper col-sm-3" style="margin-left:-15px; margin-bottom:10px;">
											<input name="no_nota" id="no_nota" class="no_nota form-control span4" type="text" value="<?php if(!empty($arr['no_nota'])){echo $arr['no_nota'];}else{echo '';}?>" placeholder="Nomer Nota" >
										</div>
										<div class="search-wrapper col-sm-3" style="margin-left:-15px; margin-bottom:10px;">
											<input name="date_payment" id="date_payment" class="form-control span4 datepicker" name="date" data-date-format="yyyy-mm-dd" value="<?php if(!empty($arr['date_payment'])){echo $arr['date_payment'];}else{echo '';}?>" placeholder="Tanggal Konfirmasi" >
										</div>
										<div class="search-wrapper col-sm-3" style="margin-left:-15px; margin-bottom:10px;">
											<button class="btn btn-primary"  style="margin-bottom:10px;"><i class="fa fa-fw fa-search"> </i>CARI DATA </button>
											<a href="<?=base_url() ?>administrator/main/resi_pengiriman" class="btn btn-danger" style="margin-bottom:10px;">RESET</a>
										</div>
									</form>
								</div>

								<div class="col-sm-12 table-responsive">
									<?=form_open('administrator/main/resi_update') ?>
									<?=$this->session->flashdata('message') ?>
									<table id="table-exsport" class="table table-bordered table-striped">
										<thead>
											<tr>
												<th width="3%">No</th>
												<th width="3%">Nota</th>
												<th width="10%">Tanggal Konfirmasi</th>
												<th width="14%">Nama Pelanggan </th>
												<th width="10%">TOTAL</th>
												<th>Data Pengiriman</th>
												<th width="20%">Status Pengiriman</th>
												<th width="10%">No Resi </th>
											</tr>
										</thead>
										<tbody>
											<?php $i = 1 * ($offset + 1);
											foreach ($orders as $order) { ?>
												<tr>
													<td><?=$i?></td>
													<td> #<?= $order->id ?></td>
													<td>
														<?= date('d-m-y', strtotime($order->date_payment)) ?>
													</td>
													<td>
														<?= $order->customer_id != 0 ? $order->name . ' (' . $order->customer_id . ')' : '<span style="color:#e23427;"><strong>' . $order->name_customer . ' (Guest)</strong>'; ?>
													</td>
													<td>Rp.<?=numberformat($order->total) ?></td>
													<td>
														<p>
															<?= $order->shipping_to ?> (<?= $order->phone_recipient ?>),
														</p>
														<?= $order->address_recipient ?>
													</td>
													<td>
														<select id="status-select"  class="form-control" name="status[]" title="pilih status product anda">
															<option value="">Pilih Status Pengiriman</option>
															<option value="Belum Dikirim" <?= $order->shipping_status == 'Belum Dikirim' ? 'selected' : '' ?>>Belum Dikirim</option>
															<option value="Dikirim" <?= $order->shipping_status == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
															<option value="Terkirim" <?= $order->shipping_status == 'Terkirim' ? 'selected' : '' ?>>Terkirim</option>
														</select>
													</td>
													<td class="form-inline">
														<input type="hidden" value="<?= $order->id ?>" name="item_id[]">

														<input type="text" class="form-control" name="no_resi[]" id="no_resi_<?= $order->id ?>" value ="<?= $order->resi ?>" style="text-transform: uppercase;" />
													</td>
												</tr>
												<?php
												$i++;
											} ?>
										</tbody>
									</table>
									<?=$this->pagination->create_links(); ?>
									<p><button type="submit" name="go" class="btn btn-success" >UPDATE RESI</button></p>
									<?=form_close()?>
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
</script>
<?php include 'includes/footer.php'; ?>
