<?php include "includes/header.php"; ?>
<div id="page-wrapper">
    <div class="container-fluid">
	    <div class="row">
	        <div class="col-lg-12">
	            <h1 class="page-header">
	               Edit Pengiriman Pesanan - <small>Dropship Detail</small>
	            </h1>
	        </div>
	    </div>

	    <div class="panel">
	    	<div class="panel-body">
    		<?=$this->session->flashdata('message') ?>

			<?php echo validation_errors(); ?>

			<?=form_open('administrator/main/create_order_dropship_process', array('class' => 'form-horizontal')) ?>

				<div class="form-group">
					<label class="col-md-offset-1 col-md-2">No ID Pesanan</label>
					<div class="col-md-8">
						<input type="hidden" name="order_id" id="order_id" value="<?=$orders['id'] ?>">
						<?=$orders['id'] ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-offset-1 col-md-2">Customer </label>
					<?php if ($orders['customer_id'] == 0) {?>
						<div class="col-md-8"><?=$orders['name_customer']?> <label class="col-md-offset-1 col-md-2">(Guest)</label>
					<?php } else { ?>
						<div class="col-md-8"><?=$customer['name'] ?> (<?=$customer['id'] ?>)</div>
					<?php } ?>
				</div>
				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Dikirim Dari</label>

					<div class="col-md-8">

					<span id="notif_from"><font color="red">Wajib diisi*</font></span><br/>

					<?php if ($orders['customer_id'] != 0) {?>

					<select name="alamat_status" id="alamat_status" class="form-control">

						<option value="">- Pilih Alamat -</option>

						<option value="sendiri">Alamat Sendiri</option>

						<option value="lain">Alamat Lain</option>

					</select>

					<?php }?>

					<br/>

					<textarea name="shipping_from" class="form-control" id="shipping_from"><?=set_value('shipping_from') ?><?=$orders['shipping_from'] ?></textarea></div>

				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Kepada</label>

					<div class="col-md-8">

					<span id="notif_to"><font color="red">Wajib diisi*</font></span><br/>

					<input name="shipping_to"  id="shipping_to" class="form-control" value="<?=set_value('shipping_to') ?><?=$orders['shipping_to'] ?>" required></div>

				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Alamat Penerima</label>

					<div class="col-md-8">

					<span id="notif_to"><font color="red">Wajib diisi*</font></span><br/>

					<div class="address-textarea">

						<textarea name="address_recipient" class="form-control" id="address_recipient" required><?=set_value('address_recipient') ?><?=$orders['address_recipient'] ?></textarea></div>

					</div>

				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Kode Pos </label>

					<div class="col-md-8"><input type="text" name="postal_code" class="form-control" id="postal_code" value="<?=$orders['postal_code'] ?>"/></div>

				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Telpon Penerima </label>

					<div class="col-md-8"><input type="text" name="phone_recipient" class="form-control" id="phone_recipient" value="<?=$orders['phone_recipient'] ?>"/></div>

				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Provinsi</label>

					<div class="col-md-8">

					<span id="notif_provinsi"><font color="red">Wajib diisi*</font></span><br/>

					<select name="prov_id" class="form-control" id="prov_id">

						<option value="">- Pilih Provinsi -</option>

					</select></div>
					<input type="hidden" name="prov_name" id="prov_name" >
				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Kota</label>

					<div class="col-md-8">

					<select name="kota_id" class="form-control" id="kota_id">

						<option value="">- Pilih Kota -</option>

					</select></div>
					<input type="hidden" name="kota_name" id="kota_name" >
				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Kecamatan</label>

					<div class="col-md-8">

					<select name="kecamatan_id" class="form-control" id="kecamatan_id">

						<option value="">- Pilih Kecamatan -</option>

					</select></div>

					<input type="hidden" name="kecamatan_name" id="kecamatan_name" >

				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Ekspedisi</label>

					<div class="col-md-8">

					<select name="ekspedisi" class="form-control" id="ekspedisi">

						<option value="">- Pilih Ekspedisi -</option>

					</select></div>


				</div>

				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Tarif Tipe</label>

					<div class="col-md-8">

					<select class="form-control" id="tarif_tipe">

						<option value="">- Pilih Tarif Tipe -</option>

					</select></div>
					<input type="hidden" name="tarif_tipe" id="tarif_name" value="<?= $orders['tarif_tipe'] ?>">

				</div>

				<div class="form-group">

					<div class="col-md-offset-1 col-md-2"><label>Total Belanja</label><span class="pull-right">Rp. </span></div>

					<div class="col-md-8">
						<input type="hidden" name="total_before" id="total_before" value="<?=$total_belanja ?>"/><?=numberformat($total_belanja) ?>
					</div>

				</div>
				<?php if ($orders['diskon'] > 0) { ?>
				<div class="form-group">

					<div class="col-md-8">
						<label class="col-md-offset-1 col-md-2">Diskon</label><span class="pull-right">Rp. </span>
						<input type="hidden" name="diskon" value="<?= $orders['diskon'] ?>" id="diskon">
					</div>

					<div class="col-md-8"><?=numberformat($orders['diskon']) ?></div>

				</div>
				<?php } ?>
				<div class="form-group">

					<label class="col-md-offset-1 col-md-2">Total Berat</label>

					<div class="col-md-8"><input type="hidden" name="shipping_weight" id="shipping_weight" value="<?=$orders['shipping_weight'] ?> "/><?=$orders['shipping_weight'] ?> Kg</div>

				</div>

				<div class="form-group">

					<div class="col-md-offset-1 col-md-2"><label>Total Ongkos Kirim</label><span class="pull-right">Rp. </span></div>
					<div class="col-md-8">
						<label><input  type="checkbox" name="shipping_manual" id="shipping_manual"/> Ongkos kirim manual </label><br>
						<input type="number" name="shipping_fee" id="shipping_fee" class="form-control" value="<?=$orders['shipping_fee'] ?>" required readonly>
					</div>

				</div>

				<div class="form-group">

					<div class="col-md-offset-1 col-md-2"><label>Total Pembayaran</label><span class="pull-right">Rp. </span></div>

					<div class="col-md-8">
						<input type="text" name="total_after" id="total_after" class="form-control input-lg" value="<?=$orders['total'] ?>" readonly required/>
					</div>

				</div>

				<input type="hidden" name="link_direct" value="kembali">
				<input type="hidden" name="origin_city_id" id="origin_city_id" value="<?=$origin_city_id['value']?>">
				<input type="hidden" name="origin_city_name" id="origin_city_name" value="<?=$origin_city_name['value']?>">

				<button name="submit" type="submit" class="btn btn-success">Simpan Pesanan</button>

				<?=form_close()?>

			</div>
		</div>
    </div>

 </div>



<?php include "includes/footer.php"; ?>

<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script>
<script type="text/javascript">
	var base_url = "<?=base_url()?>";

	var base_url_api = "http://api.tokomobile.co.id/ongkir/development/api/";
	var token_api = "<?=$this->config->item('tokomobile_token')?>";
	var domain_api = "<?=$this->config->item('tokomobile_domain')?>";
	var origin_city_id = "<?=$origin_city_id['value']?>";
	var jne_status = "<?=$jne_status['value']?>";
	var tiki_status = "<?=$tiki_status['value']?>";
	var pos_status = "<?=$pos_status['value']?>";
	var wahana_status = "<?=$wahana_status['value']?>";
	var jnt_status = "<?=$jnt_status['value']?>";
	var sicepat_status = "<?=$sicepat_status['value']?>";
	var lion_status = "<?=$lion_status['value']?>";

	var prov_awal = "<?=$orders['prov_id']?>";
	var kota_awal = "<?=$orders['kota_id']?>";
	var kecamatan_awal = "<?=$orders['kecamatan_id']?>";
	var ekspedisi_awal = "<?=$orders['ekspedisi']?>";
	var tarif_awal = "<?=$orders['tarif_tipe']?>";
	var satuan_berat = "kg";

	get_provinsi(prov_awal);
	get_kota(prov_awal, kota_awal);
	get_kecamatan(kota_awal, kecamatan_awal);
	get_ekspedisi(ekspedisi_awal);
	get_tarif_tipe(kecamatan_awal, ekspedisi_awal, $("#shipping_weight").val(), tarif_awal);

	$('#prov_id').change(function() {
		var prov_id = $(this).val();
		get_kota(prov_id);
	});

	$('#kota_id').change(function() {
		var kota_id = $(this).val();
		get_kecamatan(kota_id);
	});

	$('#kecamatan_id').change(function() {
		get_ekspedisi();
	});

	$('#ekspedisi').change(function() {
		var ekspedisi = $(this).val();
		var kecamatan_id = $('#kecamatan_id').val();
		var weight = parseFloat($('#shipping_weight').val());
		get_tarif_tipe(kecamatan_id, ekspedisi, weight);
	});

	$('#tarif_tipe').change(function() {
		var ongkos = $(this).val();
		var tarif_tipe = $('#tarif_tipe option:selected').text();
		var belanja = $('#total_before').val();
		var diskon = $('#diskon').val() || 0 ;
		var hasil = parseInt(belanja) + parseInt(ongkos) - parseInt(diskon);
		$('#shipping_fee').val(ongkos);
		$('#total_after').val(hasil);
		$('#tarif_name').val(tarif_tipe);
	});

	$('#shipping_manual').click(function() {
		if ($(this).attr('checked')) {
			$('#shipping_fee').removeAttr('readonly');
		} else {
			$('#shipping_fee').attr('readonly','readonly');
		}
	});

	$('#shipping_fee').keyup(function() {
		if ($('#shipping_manual').attr('checked')) {
			var total_before = $('#total_before').val();
			var total_shipping_fee = $(this).val();

			if (total_shipping_fee == '') {
				var total_payment = total_before;
			} else {
				var total_payment = parseFloat(total_before) + parseFloat(total_shipping_fee);
			}
			$('#total_after').val(total_payment);
		}
	});

	$('#alamat_status').change(function() {
		var alamat_id = $(this).val();
		var null_data = '';
		var cust_id = '<?=$orders["customer_id"]?>';

		if (alamat_id == 'sendiri') {
			$.post(base_url + 'administrator/main/get_alamat_pengiriman', { cust_id: cust_id, <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'},

			function(data) {
				if (data.status == 'Success') {
					var customer = data.data_customer;
					var prov = data.data_prov;
					var kota = data.data_kota;
					var kecamatan = data.data_kecamatan;
					var shipping_fee = data.data_tarif;
					var nama_toko = data.nama_toko;
					var total_weight = Math.ceil($('#shipping_weight').val());
					var total_shipping_fee = parseFloat(total_weight) * parseFloat(shipping_fee);
					var total_before = $('#total_before').val();
					var total_payment = parseFloat(total_before) + parseFloat(total_shipping_fee);
					$('#shipping_from').val(nama_toko);
					$('#shipping_to').val(customer.name);
					$('#address_recipient').val(customer.address);
					$('#postal_code').val(customer.postcode);
					$('#phone_recipient').val(customer.phone);
					$('#shipping_fee').val(total_shipping_fee);
	                get_provinsi(prov);
	                get_kota(prov, kota);
	                get_kecamatan(kota, kecamatan);
				}
			}, 'json');
		}

		if (alamat_id == 'lain') {
			$('#shipping_from').val(null_data);
			$('#shipping_to').val(null_data);
			$('#address_recipient').val(null_data);
			$('#postal_code').val(null_data);
			$('#phone_recipient').val(null_data);
			$('#shipping_fee').val(null_data);
			$('#total_after').val(null_data);
		}
	});

	function get_provinsi(prov) {
		$.get(base_url_api +  '/province', { domain: domain_api, token: token_api },

		function(data) {
			if (data.status == 'Success') {
				var result = data.result;
				var result_length = data.result.length;
				for (var i = 0; i < result_length; i++) {
					if (result[i].province_id == prov) {
						var selected = 'selected';
					} else {
						var selected = '';
					}
					var html = '<option ' + selected + ' value="' + result[i].province_id + '">' + result[i].province + '</option>';
					$('#prov_id').append(html);
				}
			}
		}, 'json');
	}

	function get_kota(prov, kota) {
		$.get(base_url_api + 'city', { domain: domain_api, token: token_api, province_id : prov },
		function(data) {
			if (data.status == 'Success') {
				$('#kota_id').html('<option>- Pilih Kota -</option>');
				var result = data.result;
				for (var i = 0; i < result.length; i++) {
					if (result[i].city_id == kota) {
						var selected = 'selected';
					} else {
						var selected = '';
					}
					var html = '<option ' + selected + ' value="' + result[i].city_id + '">' + result[i].city_name + '</option>';
					$('#kota_id').append(html);
				}
			}
		}, 'json');
	}

	function get_kecamatan(kota, kecamatan) {
		$.get(base_url_api + 'subdistrict', { domain: domain_api, token: token_api, city_id : kota },
		function(data) {
			if (data.status == 'Success') {
				$('#kecamatan_id').html('<option value="">- Pilih Kecamatan -</option>');
				var result = data.result;
				var result_length = data.result.length;
				for (var i = 0; i < result_length; i++) {
					if (result[i].subdistrict_id == kecamatan) {
						var selected = 'selected';
					} else {
						var selected = '';
					}
					var html = '<option ' + selected + ' value="' + result[i].subdistrict_id + '">' + result[i].subdistrict_name + '</option>';
					$('#kecamatan_id').append(html);
				}
			}
		}, 'json');
	}

	function get_ekspedisi(ekspedisi) {
		$('#ekspedisi').html('<option value="">Loading ...</option>');
		var selected;
		//GET EKSPEDISI
		$('#ekspedisi').html('<option value="">- Pilih Ekspedisi -</option>');

		if (jne_status == 'available') {
			selected = ekspedisi == 'jne' ? 'selected' : '';
			$('#ekspedisi').append('<option ' + selected + ' value="jne">JNE</option>');
		}

		if (tiki_status == 'available') {
			selected = ekspedisi == 'tiki' ? 'selected' : '';
			$('#ekspedisi').append('<option ' + selected + ' value="tiki">TIKI</option>');
		}

		if (pos_status == 'available') {
			selected = ekspedisi == 'pos' ? 'selected' : '';
			$('#ekspedisi').append('<option ' + selected + ' value="pos">POS</option>');
		}

		if (wahana_status == 'available') {
			selected = ekspedisi == 'wahana' ? 'selected' : '';
			$('#ekspedisi').append('<option ' + selected + ' value="wahana">WAHANA</option>');
		}

		if (jnt_status == 'available') {
			selected = ekspedisi == 'jnt' ? 'selected' : '';
			$('#ekspedisi').append('<option ' + selected + ' value="jnt">J&T</option>');
		}

		if (sicepat_status == 'available') {
			selected = ekspedisi == 'sicepat' ? 'selected' : '';
			$('#ekspedisi').append('<option ' + selected + ' value="sicepat">Sicepat</option>');
		}

		if (lion_status == 'available') {
			selected = ekspedisi == 'lion' ? 'selected' : '';
			$('#ekspedisi').append('<option ' + selected + ' value="lion">Lion</option>');
		}
	}

	function get_tarif_tipe(kecamatan_id, ekspedisi, weight, tarif_tipe_awal) {
		var destination_type = 'subdistrict';
		//GET TIPE TARIF
		$.get(base_url_api + 'cost', {
			domain: domain_api,
			token: token_api,
			origin_city_id:origin_city_id,
			weight:weight,
			destination_type:destination_type,
			destination_id : kecamatan_id,
			courier:ekspedisi,
			satuan:satuan_berat
		},
		function(data) {
			$('#tarif_tipe').html('<option value="">Loading ...</option>');
			if (data.status == 'Success') {
				$('#tarif_tipe').html('<option value="">- Pilih Tarif Tipe -</option>');

				var result = data.result;
				var result_length = data.result.length;
				for (var i = 0; i < result_length; i++) {
					if (data.status == 'Success') {
						if (result[i].code == ekspedisi || result[i].code == 'J&T') {
							var nilai = data.result[i].costs;
							var nilai_length = data.result[i].costs.length;
							for (var a = 0; a < nilai_length; a++) {
								var selected = tarif_tipe_awal == nilai[a].service ? 'selected' : '';
								var html = '<option ' + selected + ' value="' + nilai[a].cost[0].value + '">'+nilai[a].service + '</option>';
								$('#tarif_tipe').append(html);
							}
						}
					}
				}
			}
		}, 'json');
	}
</script>