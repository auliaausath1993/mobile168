<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Add User
				</h1>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-fw fa-edit"></i> Add User
					</li>
				</ol>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel">
					<div class="panel-body">
						<?= $this->session->flashdata('message'); ?>
						<form method="post" action="<?=base_url()?>administrator/main/add_user_process" id="form-user" class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-lg-2">Username*</label>
								<div class="col-lg-8"><input type="text" name="name" class="form-control" value="<?=$this->session->flashdata('customer') ?>" required ></div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Email*</label>
								<div class="col-lg-8"><input type="text" name="email" class="form-control" value="<?=$this->session->flashdata('email') ?>" required ></div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Password*</label>
								<div class="col-lg-8"><input type="text" name="password" class="form-control" required></div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">User Full Name*</label>
								<div class="col-lg-8"><input type="text" name="fullname" class="form-control" value="<?=$this->session->flashdata('fullname') ?>" required ></div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">User Level*</label>
								<div class="col-lg-8">
									<select name="user_level" class="form-control" required>
										<option value="Staff">Staff</option>
										<option value="Supervisor">Supervisor</option>
										<option value="Manager">Manager</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-lg-2">Menu*</label>
								<div class="col-lg-8">
									<div class="checkbox">
										<label><input type="checkbox" onclick="checkAll(this)">Pilih Semua</label>
									</div>
									<div class="row">
										<div class="col-md-3">
											<label>Master</label>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="product_category">Product Category</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="methode_pembayaran">Payment Methode</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="keep_limit_customer">Keep Limit Customer</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="customer" >Data Customer</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="customertype">Customer Category</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="bank_accounts">Bank Account</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="suppliers">Data Supplier</label>
											</div>
											<div class="checkbox"></div>
											<label>Pengaturan</label>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="edit_info/toko" >Pengaturan Toko Online</label>
											</div>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="cara_order/toko" >Cara Order / Tutorial</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="home_slideshow">Pengaturan Slideshow</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="name_tag">Pengaturan Name Tag</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="edit_info/stok">Pengaturan Stok</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="edit_info/image">Pengaturan Image</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="edit_info/nota">Format Nota</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="edit_info/link">Pengaturan Link Aplikasi</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="edit_info/aplikasi">Pengaturan Aplikasi</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="edit_info/ekspedisi">Pengaturan Ekspedisi</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="content_faq">FAQ</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="discount">Pengaturan Diskon</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="point_reward">Pengaturan Point & Reward</label>
											</div>
											<div class="checkbox"></div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="users_management">Manajemen User</label>
											</div>
											<div class="checkbox"></div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="input_pengeluaran">Input Pengeluaran</label>
											</div>
										</div>
										<div class="col-md-3">
											<label>Stock</label>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="stock">Stock</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="re_stock">Re-Stock</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="opname_stock">Opname Stock</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="add_product">Add Product</label>
											</div>
											<div class="checkbox"></div>
											<label>Informasi Pembayaran & Resi</label>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="confirm_payment" >Konfirmasi Pembayaran</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="resi_pengiriman">Data Resi dan Pengiriman</label>
											</div>
											<div class="checkbox"></div>
											<label>Customer Service</label>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="message_add">Kirim Pesan</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="chatting">Chatting</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="chat_product">Chat Product</label>
											</div>
										</div>
										<div class="col-md-3">
											<label>Penjualan</label>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="last_order_process" >Pesanan On Proses</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="order_rekap">Pesanan Rekap / COD</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="order_dropship">Pesanan Dropship</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="order_paid">Pesanan Lunas</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="create_order">Buat Pesanan</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="scan_order">Scan Pesanan</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="order_piutang">Pesanan Piutang</label>
											</div>
										</div>
										<div class="col-md-3">
											<label>Laporan</label>
											<div class="checkbox" style="margin-top: 0 !important">
												<label><input type="checkbox" name="menu[]" value="master_data_pesanan" >Master Data Pesanan</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_per_day">Laporan Pesanan</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="master_data_penjualan">Master Data Penjualan</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_pembayaran_perday">Laporan Pelunasan</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_omset">Laporan Omset</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_autocancel">Master Autocancel</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="laporan_autocancel">Laporan Autocancel</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_pengeluaran">Laporan Pengeluaran</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_stock">Laporan Perubahan Stock</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="pembelian">Data Pembelian</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_pembelian">Laporan Pembelian</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_piutang">Laporan Piutang</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_neraca">Laporan Neraca</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" name="menu[]" value="report_assets">Laporan Assets</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="tutup_buku">Laporan Tutup Buku</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="point_diskon">Laporan Point dan Diskon</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="produk_terjual">Laporan Produk Terjual
												</label>
											</div>
											<!-- <div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="price_modal">Harga Modal</label>
											</div> -->
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="update_harga">Update Harga (kategori & umum)</label>
											</div>
											<!-- <div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="detail_aplikasi">Detail Aplikasi Dashboard</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="info_variant">Detail Info Variant & Stock</label>
											</div> -->
										</div>
									</div>
								</div>
							</div>
							<hr>
							<!-- akses bukan menu -->
							<div class="form-group">
								<label class="control-label col-lg-2">Feature*</label>
								<div class="col-lg-8">
									<!-- <div class="checkbox">
										<label><input type="checkbox" onclick="checkAll(this)">Pilih Semua</label>
									</div> -->
									<div class="row">
										<div class="col-md-3">
											
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="price_modal">Harga Modal</label>
											</div>
											
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="detail_aplikasi">Detail Aplikasi Dashboard</label>
											</div>
											<div class="checkbox">
												<label style="color: red; font-weight:bold"><input type="checkbox" name="menu[]" value="info_variant">Detail Info Variant & Stock</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="form-group">
								<label class="control-label col-lg-2">Pin Code User*</label>
								<div class="col-lg-8"><input type="text" name="pincode" class="form-control" value="<?=$this->session->flashdata('pincode') ?>"></div>
							</div>
							<div class="buttons-addproduct-wrapper">
								<button type="submit" class="btn btn-lg btn-default">Save</button>
								<a href="<?=base_url()?>administrator/main/users_management" class="btn btn-lg btn-default">Cancel</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<script>
	function checkAll(ele) {
		var checkboxes = document.getElementsByTagName('input');
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].type == 'checkbox') {
				checkboxes[i].checked = ele.checked;
			}
		}
	}

	$('#form-user').submit(function(e) {
		e.preventDefault();
		$('#modal-pincode').modal('show');
		$('#submit-pincode').click(function() {
			$('.form-pincode').hide();
			$('.loading-pincode').show();
			var code = $('#pincode').val();
			$.post(base_url + 'administrator/main/check_pincode', { code: code }, function(data) {
				$('.form-pincode').show();
				$('.loading-pincode').hide();
				if (data.status == 'Success') {
					$('#form-user').submit();
				} else {
					alert('Pincode salah');
				}
			}, 'json');
		});
	});
</script>
<?php include 'includes/footer.php'; ?>
