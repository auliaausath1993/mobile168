<?php include "includes/header.php"; ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Add Customer
			</h1>
			<ol class="breadcrumb">
				<li class="active">
					<i class="fa fa-fw fa-edit"></i> Add Customer
				</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<?=$this->session->flashdata('message') ?>
			<section class="panel">
				<div class="panel-body">
					<form method="post" action="<?=base_url()?>administrator/main/add_customer_process" id="form-add-customer" class="form-horizontal">
						<div class="form-group">
							<label class="col-lg-2 control-label">Nama Customer*</label>
							<div class="col-lg-8">
								<input type="text" id="name-item" name ="name" class="form-control" title="masukkan nama customer anda" value="<?=$this->session->flashdata('customer') ?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Email*</label>
							<div class="col-lg-8">
								<input type="text" id="harga-modal" name ="email" class="form-control" title="masukkan Email customer anda" value="<?=$this->session->flashdata('email') ?>" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Password*</label>
							<div class="col-lg-8">
								<input type="text" id="harga-jual" name ="password" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Alamat</label>
							<div class="col-lg-8">

								<textarea id="deskripsi" name ="alamat" class="form-control" title="masukkan Alamat customer anda" maxlength="180" ><?=$this->session->flashdata('alamat') ?></textarea><br/>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-2 control-label">Provinsi*</label>
							<div class="col-lg-8">

								<select name="provinsi" class="form-control" id="provinsi">
									<option value="">- Pilih Provinsi -</option>

								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Kota*</label>
							<div class="col-lg-8">

								<select name="kota" class="form-control" id="kota">
									<option value="">- Pilih Kota -</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Kecamatan*</label>
							<div class="col-lg-8">

								<select name="kecamatan" class="form-control" id="kecamatan">
									<option value="">- Pilih Kecamatan -</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-2 control-label">Kode Pos</label>
							<div class="col-lg-8">
								<input type="text" id="harga-jual" name ="postcode" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Phone</label>
							<div class="col-lg-8">
								<input type="text" id="harga-jual" name ="phone" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Pin BB</label>
							<div class="col-lg-8">
								<input type="text" id="harga-jual" name ="pinbb" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Jenis Customer*</label>
							<div class="col-lg-8">

								<select name="jenis_customer" class="form-control" id="jenis_customer" required>
									<option value="Lokal">Lokal</option>
									<option value="Luar">Luar</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">Status*</label>
							<div class="col-lg-8">

								<select name="status" class="form-control" id="status" required>
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
									<option value="Moderate">Moderate</option>
								</select>
							</div>
						</div>
						<div class="buttons-addproduct-wrapper">
							<input type="hidden" name="redirect_url" id="redirect_url"  value="" />
							<a href="#" id="add-back" class="btn btn-lg btn-default m-b-mini">Save</a>
							<button type="submit" id="add-product" class="btn btn-lg btn-default m-b-mini">Save and Back to List</button>
							<input type="hidden" name="this_redirect_url" id="this_redirect_url"  value="administrator/main/add_new_customer" />
							<a href="<?=base_url()?>administrator/main/customer" class="btn btn-lg btn-default m-b-mini">Cancel</a>
						</div>
					</form>
				</div>
			</section>
		</div>
	</div>
</div>

<?php include "includes/footer.php"; ?>

<script>



	var base_url_api = "https://api.tokomobile.co.id/ongkir/development/api";
	var token_api = "<?=$this->config->item('tokomobile_token')?>";
	var domain_api = "<?=$this->config->item('tokomobile_domain')?>";

	$.get(base_url_api+"/province",{token : token_api,domain : domain_api},
		function(data)
		{
			if(data.status == 'Success')
			{
				var list_data = data.result;
				var list_length = data.result.length;

				for(var i = 0; i < list_length; i++)
				{
					var prov = "<option value='"+list_data[i].province_id+"'>"+list_data[i].province+"</option>";

					$("#provinsi").append(prov);
				}
			}

		},"json");

	$("#provinsi").change(function(){

		var province_id = $(this).val();
		$("#kota").html("<option value=''>Loading..</option>");
		$("#kecamatan").html("<option value=''>- Pilih Kecamatan -</option>");
		//Get List Kota
		$.get(base_url_api+"/city",{token : token_api,domain : domain_api, province_id : province_id},function(data)
		{
			if(data.status == 'Success')
			{
				$("#kota").html("<option value=''>- Pilih Kota -</option>");
				var city_data = data.result;
				var city_length = data.result.length;

				for(var i = 0; i < city_length; i++)
				{
					var city = "<option value='"+city_data[i].city_id+"'>"+city_data[i].city_name+"</option>";

					$("#kota").append(city);
				}
			}

		},"json");


	});

	$("#kota").change(function(){

		var city_id = $(this).val();
		$("#kecamatan").html("<option value=''>Loading..</option>");
		//Get List kecamatan
		$.get(base_url_api+"/subdistrict",{token : token_api,domain : domain_api, city_id : city_id},function(data)
		{
			if(data.status == 'Success')
			{
				$("#kecamatan").html("<option value=''>- Pilih Kecamatan -</option>");
				var kec_data = data.result;
				var kect_length = data.result.length;

				for(var i = 0; i < kect_length; i++)
				{
					var kecamatan = "<option value='"+kec_data[i].subdistrict_id+"'>"+kec_data[i].subdistrict_name+"</option>";

					$("#kecamatan").append(kecamatan);
				}
			}

		},"json");

	});
	$("#add-back").click(function(){

		var this_redirect_url = $("#this_redirect_url").val();
		$("#redirect_url").val(this_redirect_url);

		$("#form-add-customer").submit();

		return false;

	});

</script>