<?php include 'includes/header.php'; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Edit Customer</h1>
			<ol class="breadcrumb">
				<li class="active">
					<i class="fa fa-fw fa-edit"></i> Edit Customer
				</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<?=$this->session->flashdata('message') ?>
		<div class="col-lg-12">
			<section class="panel">
				<div class="panel-body">
					<form method="post" action="<?=base_url()?>administrator/main/edit_customer_process" id="form-edit-customer" class="form-horizontal">
						<div class="form-group">
							<label class="col-lg-2 control-label">Nama Customer*</label>
							<div class="col-lg-8">
								<input type="text" id="name-item" name="name" class="form-control" title="masukkan nama customer anda" value="<?=$customer['name']?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Email*</label>
							<div class="col-lg-8">
								<input type="text" id="harga-modal" name="email" class="form-control" title="masukkan Email customer anda" value="<?=$customer['email']?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Password*</label>
							<!-- <?php $pass = $this->encrypt->decode($customer['password']);?>
							<div class="col-lg-8">
								<input type="text" id="harga-jual" name="password" class="form-control" value="<?=$pass?>" required>
							</div> -->
							<div class="col-lg-8">
								<input type="password" id="harga-jual" name="password" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Alamat</label>
							<div class="col-lg-8">
								<textarea id="deskripsi" name="alamat" class="form-control" title="masukkan Alamat customer anda" maxlength="180" ><?=$customer['address']?></textarea><br/>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-2 control-label">Provinsi*</label>
							<div class="col-lg-8">
								<input type="hidden" name="detail_prov" id="detail_prov" value="<?=$customer['prov_id']?>">

								<select name="provinsi" class="form-control" id="provinsi">
									<option value="">- Pilih Provinsi -</option>
									<?php foreach ($provinces as $province) { ?>
										<option value="<?= $province->province_id ?>" <?= $province->province_id == $customer['prov_id'] ? 'selected' : '' ?>><?= $province->province ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Kota*</label>
							<div class="col-lg-8">
								<select name="kota" class="form-control" id="kota">
									<option value="">- Pilih Kota -</option>
									<?php foreach ($cities as $city) { ?>
										<option value="<?= $city->city_id ?>" <?= $city->city_id == $customer['kota_id'] ? 'selected' : '' ?>><?= $city->city_name ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Kecamatan*</label>
							<div class="col-lg-8">
								<select name="kecamatan" class="form-control" id="kecamatan">
									<option value="">- Pilih Kecamatan -</option>
									<?php foreach ($subdistricts as $subdistrict) { ?>
										<option value="<?= $subdistrict->subdistrict_id ?>" <?= $subdistrict->subdistrict_id == $customer['kecamatan_id'] ? 'selected' : '' ?>><?= $subdistrict->subdistrict_name ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-2 control-label">Kode Pos</label>
							<div class="col-lg-8">
								<input type="text" id="postal_code" name="postcode" value="<?=$customer['postcode']?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Phone</label>
							<div class="col-lg-8">
								<input type="text" id="phone" name="phone" value="<?=$customer['phone']?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Pin BB</label>
							<div class="col-lg-8">
								<input type="text" id="pinbb" name="pinbb" value="<?=$customer['pin_bb']?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Point</label>
							<div class="col-lg-8">
								<span id="point-customer"><?= number_format($customer['point'], 0, '.', '.') ?></span>
								<button type="button" class="btn btn-sm btn-success btn-edit-point">Ubah</button>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Jenis Customer*</label>
							<div class="col-lg-8">
								<select name="jenis_customer" class="form-control" id="jenis_customer" required>
									<option value="">- Pilih Jenis Customer -</option>
									<?php foreach ($customertype as $row) { ?>
										<option value="<?= $row->id ?>" <?php if($customer['jenis_customer'] == $row->id){ echo 'selected="selected"';}?>><?= $row->name ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Status*</label>
							<div class="col-lg-8">
								<select name="status" class="form-control" id="status" required>
									<option value="Active" <?= $customer['status'] == 'Active' ? 'selected': '' ?>>Active</option>
									<option value="Inactive" <?= $customer['status'] == 'Inactive' ? 'selected': '' ?>>Inactive</option>
									<option value="Moderate" <?= $customer['status'] == 'Moderate' ? 'selected': '' ?>>Moderate</option>
								</select>
							</div>
						</div>
						<div class="buttons-addproduct-wrapper">
							<input type="hidden" name="redirect_url" id="redirect_url"  value="" />
							<input type="hidden" name="id_data" id="id_data"  value="<?=$customer['id']?>" />
							<a href="#" id="add-back" class="btn btn-lg btn-default m-b-mini">Save</a>
							<button type="submit" id="add-product" class="btn btn-lg btn-default m-b-mini">Save and Back to List</button>
							<input type="hidden" name="this_redirect_url" id="this_redirect_url"  value="administrator/main/edit_customer/<?=$customer['id']?>" />

							<a href="<?=base_url()?>administrator/main/customer" class="btn btn-lg btn-default m-b-mini">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-point" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Masukkan Point</h4>
			</div>
			<div class="form-point">
				<div class="modal-body">
					<div class="form-group">
						<label>Point Saat Ini</label>
						<div class="form-control"><?= $customer['point'] ?></div>
					</div>
					<div class="form-group">
						<label>Point Baru</label>
						<input type="number" class="form-control" id="new-point">
					</div>
				</div>
				<div class="modal-footer" >
					<input type="button" value="Batal" class="btn btn-default" data-dismiss="modal">
					<input type="submit" value="OK" id="submit-point" class="btn btn-primary">
				</div>
			</div>
			<div class="loading-point text-center" style="display: none">
				<div class="modal-body">
					<i class="fa fa-spin fa-spinner fa-5x"></i>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include "includes/footer.php"; ?>

<script>
	var base_url = '<?= base_url() ?>administrator/main/';

	$('#provinsi').change(function(){
		var province_id = $(this).val();
		$('#kota').html('<option value="">Loading..</option>');
		$('#kecamatan').html('<option value="">Loading..</option>');
		$.get(base_url + 'cities/' + province_id, function(data) {
			$('#kota').html('<option value="">- Pilih Kota -</option>');
			$('#kecamatan').html('<option value="">- Pilih Kecamatan -</option>');
			data.map(function(city) {
				var city = '<option value=' + city.city_id + '">' + city.city_name + '</option>';
				$('#kota').append(city);
			});
		},'json');
	});

	$("#kota").change(function(){
		var city_id = $(this).val();
		$('#kecamatan').html('<option value="">Loading..</option>');
		$.get(base_url + 'subdistricts/' + city_id, function(data) {
			$('#kecamatan').html('<option value="">- Pilih Kecamatan -</option>');
			data.map(function(kec) {
				var kecamatan = '<option value="' + kec.subdistrict_id + '">' + kec.subdistrict_name + '</option>';
				$('#kecamatan').append(kecamatan);
			});
		},'json');

	});
	$('#add-back').click(function(){
		var this_redirect_url = $('#this_redirect_url').val();
		$('#redirect_url').val(this_redirect_url);
		$('#form-edit-customer').submit();
		return false;
	});

	$(document).on('click', '.btn-edit-point', function(e) {
		e.preventDefault();
		var id = $(this).attr('no');
		$('#modal-pincode').modal('show');
		var title = $(this).attr('title');
		$('#title-pincode').val(title);
		$('#var-no').val(id);
    });
	$('#submit-pincode').click(function() {
		$('.form-pincode').hide();
		$('.loading-pincode').show();
		var code = $('#pincode').val();
		var id = $('#var-no').val();
		var title = $('#title-pincode').val();
		$.post(base_url + 'check_pincode', { code: code }, function(data) {
			$('#modal-pincode').modal('hide');
			$('.loading-pincode').hide();
			$('.form-point').show();
			if (data.status == 'Success') {
				$('#modal-point').modal('show');
	    	} else {
	    		alert('Pincode salah');
	    	}
	    }, 'json');
	});
	$('#submit-point').click(function() {
		$('.form-point').hide();
		$('.loading-point').show();
		var point = $('#new-point').val();
		var id = $('#id_data').val();
		var title = $('#title-point').val();
		$.post(base_url + 'update_point_customer', { point: point, id: id }, function(data) {
			$('#modal-point').modal('hide');
			$('.loading-point').hide();
			$('.form-point').show();
			if (data.status == 'Success') {
				$('#modal-point').modal('hide');
				$('#point-customer').html(Number(point).toLocaleString('id'));
				alert(data.message);
	    	}
	    }, 'json');
	});
</script>