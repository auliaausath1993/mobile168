<?php include "includes/header.php";
$origin_city_id = $this->main_model->get_detail('content',array('name' => 'origin_city_id'));
$satuan_berat = $this->main_model->get_detail('content',array('name' => 'satuan_berat'));
$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
$jne = $this->main_model->get_detail('content',array('name' => 'jne_status'));
$tiki = $this->main_model->get_detail('content',array('name' => 'tiki_status'));
$pos = $this->main_model->get_detail('content',array('name' => 'pos_status'));
$wahana = $this->main_model->get_detail('content',array('name' => 'wahana_status'));
$jnt = $this->main_model->get_detail('content',array('name' => 'jnt_status'));
$sicepat = $this->main_model->get_detail('content',array('name' => 'sicepat_status'));
$lion = $this->main_model->get_detail('content',array('name' => 'lion_status'));
?>

<style type="text/css">
	.autocomplete-suggestions {
		width: 220px !important;
	}
</style>

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Pesanan <small> Detail</small></h1>
				<div class="message"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<?=$this->session->flashdata('message'); ?>
				<div class="panel">
					<div class="panel-body">
						<div class="insert-box border-bottom m-b">
							<div class="form-horizontal row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-xs-6">ID PESANAN</label>
										<div class="col-xs-6">
											: #<?=$order['id'] ?>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-xs-6">TANGGAL PESANAN</label>
										<div class="col-xs-6">
											: <?=date("D, d-M-Y, H:i",strtotime($order['order_datetime'])); ?>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-xs-6">CUSTOMER / PELANGGAN</label>
										<div class="col-xs-6">
											<?php if ($order['customer_id'] == 0) { ?>
											: <span style="color:#e23427;"><?=$order['name_customer']?> <strong>(Guest)</strong></span><br>
											<?php } else { ?>
											: <?=$customer['name']?> (<?=$customer['id']?>)<br>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-xs-6">JENIS PESANAN</label>
										<div class="col-xs-6">
											: <?=get_order_status($order['order_status']) ?>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<?php if ($order['customer_id'] != 0) { ?>
										<label class="col-xs-6">EMAIL</label>
										<div class="col-xs-6">
											: <?= $customer['email'] ?>
										</div>
									<?php } ?>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-xs-6">STATUS PEMBAYARAN</label>
										<div class="col-xs-6">
											: <?=get_order_payment_label($order['order_payment']) ?>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-md-offset-6">
									<div class="form-group">
										<label class="col-md-6">METODE PEMBAYARAN</label>
										<div class="col-md-6">
											<form role="form" id="payment_methode_update" style="display: inline;" action="<?= base_url() ?>administrator/main/payment_methode_process" method="post" enctype="multipart/form-data" class="form-inline">
													<select  class="payment-methode-select form-control" name="methode_pembayaran" title="insert methode pembayaran" style="padding: 7px;">
														<?php if ($order['payment_method_id'] != 0) { ?>
														<option value="">Pilih Metode pembayaran</option>
														<?php foreach($payment_method->result() as $payment_method) { ?>
															<option value="<?= $payment_method->id ?>"<?php if ($payment_method->id  == $order['payment_method_id']) { echo 'selected="selected" '; } ?>><?= $payment_method->name ?></option>
														<?php } ?>
														<?php } else { ?>
															<option value="">Pilih Metode pembayaran</option>
														<?php foreach($payment_method->result() as $payment_method) { ?>
															<option value="<?= $payment_method->id ?>"><?= $payment_method->name ?></option>
														<?php } ?>
														<?php } ?>
													</select>
													<input type="hidden" class="order_id" name="$order_id" value="<?=$order['id']?>">
												</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="insert-box">
					<div class="well">
						<div class="row">
							<?=form_open('administrator/main/order_detail_change_status', array('class' => 'col-md-4 row')) ?>
							<input type="hidden" name="order_id" value="<?=$order['id'] ?>" >
							<div class="col-md-6 m-b-mini">
								<select name="order_payment" class="form-control">
									<option value="Unpaid" <?= $order['order_payment'] == 'unpaid' ? 'selected' : '' ?>>Belum Lunas</option>
									<option value="Paid" <?= $order['order_payment'] == 'Paid' ? 'selected' : '' ?>>Lunas</option>
								</select>
								<?php if ($order['print_nota'] == 1) {  ?>
								<span class="label label-success">Sudah Cetak Nota</span><br>
								<?php } if ($order['print_ekspedisi'] == 1) { ?>
								<span class="label label-info">Sudah Cetak Data Ekspedisi</span>
								<?php } ?>
							</div>
							<div class="col-md-6">
								<button class="btn btn-success btn-sm"><i class="fa fa-edit"></i> UBAH STATUS</button>
							</div>
							<?= form_close() ?>
							<div class="col-md-8">
								<p>
									<a href="<?=base_url()?>administrator/main/nota_detail_print/<?=$order['id'] ?>" class="btn btn-success btn-sm m-b-mini" ><i class="fa fa-print"></i> CETAK NOTA</a>
									<a href="<?=base_url()?>administrator/main/ekspedisi_print_a4/<?=$order['id'] ?>" class="btn btn-info btn-sm m-b-mini" ><i class="fa fa-print"></i> CETAK DATA EKSPEDISI</a>
									<a href="<?=base_url()?>administrator/main/nota_detail_save/<?=$order['id'] ?>" class="btn btn-success btn-sm m-b-mini" id="export-pdf"><i class="fa fa-file-pdf-o"></i> Export PDF</a>
									<button type="button" link="<?= 'cancel_order/' . $order['id'] ?>" id="btn-cancel-order" class="btn btn-danger btn-sm m-b-mini" ><i class="fa fa-times"></i> BATALKAN PESANAN</button>
								</p>
							</div>
						</div>
					</div>
					<hr>
					<?php if ($order['customer_id'] != 0) { ?>
					<input type="hidden" name="customer_id" value="<?=$customer['id']?>" >
					<?php } ?>
					<?php if ($direct == 'kembali') { ?>
					<a href="<?=base_url() ?>administrator/main/last_order_process" class="btn btn-default" >Kembali</a>
					<?php } else { ?>
					<button class="btn btn-default" onclick="history.back(-1)">Kembali</button>
					<?php } ?>
					<?php if ($order['order_status'] == 'Dropship') { ?>
						<div>
							<h4 style="display: inline-block;">PENGIRIMAN (Shipping)</h4>
							<span class="pull-right"><a href="<?=base_url()?>administrator/main/create_order_dropship/<?=$order['id']?>/<?=$order['order_payment']?>" class="btn"><strong>Edit Pengiriman</strong></a></span>
						</div>
						<div class="table-responsive">
							<table class="table table-striped table-bordered">
								<thead>
									<tr class="btn-primary">
										<th width="10%">DARI</th>
										<th width="10%">DIKIRIM KE</th>
										<th width="10%">Telpon Penerima</th>
										<th width="20%">Alamat Penerima</th>
										<th width="10%">Kode Pos</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?=$order['shipping_from'] ?></td>
										<td><?=$order['shipping_to'] ?></td>
										<td><?=$order['phone_recipient'] ?></td>
										<td><?=$order['address_recipient'] ?>
											<?php if ($order['kecamatan_id'] != '') {
												$tujuan = $kec.' - '.$kota_tujuan;
												$ekspedisi = $order['ekspedisi'].' - '.$order['tarif_tipe'];
											} else {
												$tujuan = $tarif['kecamatan'].' - '.$tarif['kota_kabupaten'];
												$ekspedisi = 'JNE - '.$order['tarif_tipe'];
											} ?>
											<br><strong>Ekspedisi ke</strong> : <?= $tujuan?>
											<br><strong>Layanan</strong> : <?=strtoupper($ekspedisi) ?>
										</td>
										<td><?=$order['postal_code'] ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					<?php } ?>
					<div class="view-nota"></div>
					<h4>ITEM PESANAN</h4>
					<form method="POST" action="<?=base_url() ?>administrator/main/order_detail_update_qty">
						<div class="table-responsive">
							<input type="hidden" name="order_id" value="<?=$order['id'] ?>" >
							<table class="table table-striped table-bordered">
								<thead>
									<tr class="btn-primary">
										<th>#</th>
										<th width="30%">Item</th>
										<th width="20%">Deskripsi</th>
										<th width="15%">Harga</th>
										<th width="8%">Berat</th>
										<th>QTY</th>
										<th width="20%">Subtotal</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$i = 1;
									$total = 0;
									$total_items = $order_item->num_rows();
									foreach ($order_item->result() as $orders) {
										$data_product = $this->main_model->get_detail('product',array('id' => $orders->prod_id));
										$data_color = $this->main_model->get_detail('product_variant',array('id' => $orders->variant_id));
										$total = $total + $orders->subtotal; ?>
									<tr>
										<td><?=$i?><input type="hidden" name="order_item_id[]" value="<?=$orders->id?>" ></td>
										<td><?=$data_product['name_item'] ?></td>
										<td>
											<?=$data_color['variant'] ?> <br>
											<?= $orders->notes ? 'Catatan : ' . $orders->notes : '' ?>
										</td>
										<td>Rp.<?=numberformat($orders->price)?></td>
										<td><?=$data_product['weight'] ?> Kg</td>
										<td><input type="number" min="1" name="order_item_qty[]" class="form-control" value="<?=$orders->qty ?>" ></td>
										<td>Rp.<?=numberformat($orders->subtotal) ?></td>
										<td>
											<?php if($total_items > 1) { ?>
											<button link="<?= 'cancel_order_item/' . $orders->id ?>" type="button" class="btn-sm btn btn-danger btn-cancel-item">Cancel</button>
											<?php } ?>
										</td>
									</tr>
									<?php
									$i++;
									} ?>
									<!-- Button trigger modal -->

									<tr><td colspan="5"><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#myModal" id="form_add_item"><strong>[+] Tambah Item Pesanan</strong></a></td><td><button type="submit" class="btn btn-info btn-sm" name="update_order">SIMPAN</button></td><td colspan="2"></td></tr>
									<?php if ($order['order_status'] == 'Dropship') { ?>
									<tr>
										<td></td>
										<td>Ongkos Pengiriman</td>
										<td>Ke <strong><?=$kec ?></strong></td>
										<td>Rp.<?=numberformat($order['shipping_fee'])?></td>
										<td colspan="2"><?=$order['shipping_weight'] ?> Kgs</td>
										<td>Rp.<?=numberformat($order['shipping_fee']) ?></td>
										<td></td>
									</tr>
									<?php } if ($order['diskon'] - $order['program_diskon'] > 0) { ?>
									<tr>
										<td></td>
										<td colspan="5">Jumlah Diskon</td>
										<td colspan="2">
											Rp.<?=numberformat($order['diskon'] - $order['program_diskon']) ?>
											<a class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#modalDiskon"><strong>Ubah Diskon</strong></a>
										</td>
									</tr>
									<?php } if ($order['nominal_point'] > 0) { ?>
									<tr>
										<td></td>
										<td colspan="5">Jumlah Diskon Point</td>
										<td colspan="2">
											Rp.<?=numberformat($order['nominal_point']) ?>
										</td>
									</tr>
									<?php } if ($order['program_diskon'] > 0) { ?>
									<tr>
										<td></td>
										<td colspan="5">Jumlah Program Diskon</td>
										<td colspan="2">
											Rp.<?=numberformat($order['program_diskon']) ?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<table class="table table-striped table-bordered">
								<thead>
									<tr class="btn-inverse">
										<th><big>TOTAL</big></th>
										<th width="26%"><big>Rp.<?=numberformat($order['total'])?></big></th>
									</tr>
								</thead>
							</table>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="<?=base_url()?>administrator/main/order_detail_add_product" method="POST" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Tambahkan Item ke Pesanan <strong>#<?=$order['id'] ?></strong></h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="add_form_order_id" value="<?=$order['id'] ?>" >
					<div class="row">
						<div class="col-md-5">
							Produk <br>
							<input type="text" id="add_form_product_name" class="form-control">
							<input type="hidden" name="add_form_product" id="add_form_product">
						</div>
						<div class="col-md-5">
							Variant <br>
							<select name="add_form_variant" id="add_form_variant" class="form-control">
								<option> - </option>
							</select>
							<span id="notif_stock"></span>
						</div>
						<div class="col-md-2">
							QTY <br>
							<input type="number" min="1" name="add_form_qty" id="add_form_qty" class="form-control" >
						</div>
						<input type="hidden" name="berat_produk" id="berat_produk">
						<input type="hidden" name="total_berat" id="total_berat" value="<?=$order['shipping_weight'] ?>">
						<input type="hidden" name="berat_before" id="berat_before" value="<?=$order['shipping_weight'] ?>">
						<?php if($order['order_status'] == 'Dropship'){?>
						<div class="col-md-5">
							Pilih Ekspedisi<br>
							<select name="add_ekspedisi" id="add_ekspedisi" class="form-control" id="total_berat" value="<?=$order['shipping_weight'] ?>">
								<option> - </option>
								<?php if ($jne['value'] == 'available') { ?>
								<option value="jne">JNE</option>
								<?php } ?>
								<?php if ($tiki['value'] == 'available') { ?>
								<option value="tiki">TIKI</option>
								<?php } ?>
								<?php if ($pos['value'] == 'available') { ?>
								<option value="pos">POS</option>
								<?php } ?>
								<?php if ($wahana['value'] == 'available') { ?>
								<option value="wahana">WAHANA</option>
								<?php } ?>
								<?php if ($jnt['value'] == 'available') { ?>
								<option value="jnt">JNT</option>
								<?php } ?>
								<?php if ($sicepat['value'] == 'available') { ?>
								<option value="sicepat">Sicepat</option>
								<?php } ?>
								<?php if ($lion['value'] == 'available') { ?>
								<option value="lion">Lion</option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-5">
							Pilih Tarif Tipe<br>
							<select name="add_tarif_tipe" id="add_tarif_tipe" class="form-control">
								<option> - </option>
							</select>
						</div>
						<?php } ?>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="customer_id" value="<?=$customer['id']?>">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Tambahkan</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="modalDiskon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form action="<?=base_url()?>administrator/main/order_detail_edit_diskon" method="POST" >
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Ubah Diskon</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="order_id" value="<?=$order['id'] ?>" >
					<div class="row">
						<div class="col-md-12">
							Diskon Baru <br>
							<input type="number" min="1" name="diskon" id="diskon" class="form-control">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary">Simpan</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script type='text/javascript' src='<?php echo base_url();?>assets/js/jquery.autocomplete.js'></script>
<script type="text/javascript">
	var base_url_api = "http://api.tokomobile.co.id/ongkir/development/api";
	var token_api = "<?=$this->config->item('tokomobile_token')?>";
	var domain_api = "<?=$this->config->item('tokomobile_domain')?>";
	var origin_city_id = "<?=$origin_city_id['value']?>";
	var base_url = "<?=base_url()?>";
	$("#export-pdf").click(function(){
		window.location = base_url+"administrator/main/nota_detail_save/<?=$order['id'] ?>";
		$('#view-nota').tableExport({type:'png',escape:'false'});
	});

	$(function() {
		$('#add_form_product_name').autocomplete({
			serviceUrl: base_url+'administrator/main/search_id_produk_item',
			onSelect: function (suggestion) {
				$('#add_form_product').val(suggestion.data);
				get_variant(suggestion.data);
			}
		});
	});

	$("#add_ekspedisi").change(function(){
		var ekspedisi = $(this).val();
		var kecamatan_id = "<?=$order['kecamatan_id']?>";
		var satuan_berat = "<?=$satuan_berat['value']?>";
		var weight = $("#total_berat").val();
		var destination_type = 'subdistrict';
		$("#add_tarif_tipe").html("<option value=''>Loading ...</option>");
		//GET TIPE TARIF
		$.get(base_url_api+"/cost", { domain: domain_api, token: token_api, origin_city_id:origin_city_id,weight:weight,destination_type:destination_type, destination_id : kecamatan_id, courier:ekspedisi, satuan:satuan_berat },
			function(data) {
				if (data.status == 'Success'){
					$("#add_tarif_tipe").html("<option value=''>- Pilih Tarif Tipe -</option>");

					var result = data.result;
					var result_length = data.result.length;
					for (var i = 0 ; i < result_length; i++){
						if (data.status == 'Success') {
							if (result[i].code == ekspedisi) {
								var nilai = data.result[i].costs;
								var nilai_length = data.result[i].costs.length;
								for (var a = 0 ; a < nilai_length; a++) {
									var html = "<option value='"+nilai[a].service+"'>"+nilai[a].service+"</option>";
									$("#add_tarif_tipe").append(html);
								}
							}

							if (result[i].code == 'J&T') {
								var nilai = data.result[i].costs;
								var nilai_length = data.result[i].costs.length;
								for (var a = 0 ; a < nilai_length; a++) {

									var html = "<option value='"+nilai[a].service+"'>"+nilai[a].service+"</option>";
									$("#add_tarif_tipe").append(html);
								}
							}
						}
					}
				}
			}, 'json');
	});

	$("#add_form_qty").change(function(){
		var berat_produk = $("#berat_produk").val();
		var berat_before = $("#berat_before").val();
		var qty = $(this).val();
		var new_berat = berat_produk * qty;
		var berat_total = parseFloat(new_berat)+parseFloat(berat_before);
		$("#total_berat").val(berat_total);
		$("#add_tarif_tipe").html("<option value=''>-</option>");
	});

	$('.payment-methode-select').change(function(){
		var base_url = "<?=base_url()?>";
		var methode_pembayaran =  $('.payment-methode-select :selected').val();
		var order_id = $('.order_id').val();
		$.post(base_url+"administrator/main/order_payment_methode_process",{order_id: order_id, methode_pembayaran: methode_pembayaran }, function(data) {
			if (data.status == 'Success') {
				$('.message').html('<div class=\"alert alert-success\">Methode Pembayaran telah berhasil dirubah</div>');
			}
		}, "json");
	});

	$("#form_add_item").click(function(){
		$("#add_form_product_name").val("");
		$("#add_form_product").val("");
		$("#add_form_qty").val("0");
		// $.post(base_url+"administrator/main/order_detail_get_publish_product", function(data) {
		// 	for (var i = 0; i < data.length; i++) {
		// 		$("#add_form_product").append("<option value='"+data[i].prod_id+"'>"+data[i].name_item+"</option>");
		// 	}
		// }, "json");
		$("#add_form_variant").html("<option>- Pilih Variant -</option>");
		$("#add_form_variant").attr("disabled","disabled");
	});

	function get_variant(prod_id){
		$("#add_form_variant").removeAttr("disabled");
		$("#add_form_variant").html("<option> - Pilih Variant -</option>");

		$.post(base_url+"administrator/main/order_detail_get_variant",{prod_id: prod_id},
			function(data) {
				for (var i = 0; i < data.length; i++) {
					$("#add_form_variant").append("<option value='"+data[i].variant_id+"'>"+data[i].variant+"</option>");
				}
			}, "json");
	}

	$("#add_form_variant").change(function(){
		var variant_id  = $(this).val();
		var stock_value = '<?=$data_value_stock["value"] ?>';
		$("#add_form_qty").val("");
		$.post(base_url+"administrator/main/order_detail_get_variant_detail",{variant_id: variant_id},
			function(data) {
				$("#add_form_qty").attr("max",data.stock);
				$("#berat_produk").val(data.weight);
				var berat_total = parseFloat($("#berat_before").val())+parseFloat(data.weight);
				$("#total_berat").val(berat_total);
				if (stock_value != 3) {
					$("#notif_stock").html("<font color='red'>Sisa Stock : <strong>"+data.stock+"</strong></font>");
				};
			}, "json");
	});

	$('#btn-cancel-order, .btn-cancel-item').click(function(e) {
		e.preventDefault();
		$('#modal-pincode').modal('show');
		var form = $('#modal-cancel-order form');
		var action_link = $(this).attr('link');
		form.attr('action', base_url + 'administrator/main/' + action_link);
	});
	$(document).on('click', '#submit-pincode', function() {
		var code = $('#pincode').val();
		$('.loading-pincode').show();
		$('.form-pincode').hide();
		$.post(base_url + 'administrator/main/check_pincode', { code: code }, function(data) {
			$('.form-pincode').show();
			$('.loading-pincode').hide();
			if (data.status == 'Success') {
				$('#modal-cancel-order').modal('show');
				$('#modal-pincode').modal('hide');
			} else {
				alert('Pincode salah');
			}
			$('#pincode').val('');
		}, 'json');
	});
</script>
<?php include "includes/footer.php"; ?>