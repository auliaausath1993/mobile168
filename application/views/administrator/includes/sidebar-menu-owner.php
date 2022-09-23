<?php $uri = $this->uri->segment(3) ?>
 <ul class="nav" data-spy="affix" data-offset-top="50" style="background-color: #233445;">
	<li>
		<a href="<?=base_url()?>administrator/main"  class="tipped" data-title="Untuk kembali ke halaman utama" ><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
	</li>
	<?php
		$user_id = $this->session->userdata('webadmin_user_id');
    	$user_login = $this->db->get_where('users', array('id' => $user_id))->row_array();
        $user_akses_menu = explode(',', $user_login['akses_menu']);
	if (in_array('product_category', $user_akses_menu) || in_array('methode_pembayaran', $user_akses_menu) || in_array('customer', $user_akses_menu) || in_array('customertype', $user_akses_menu) || in_array('bank_accounts', $user_akses_menu) || in_array('suppliers', $user_akses_menu)) { ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".master"><i class="fa fa-fw fa-table"></i> Master
				<?php if ($data_customer['total'] > 0) { ?>
				<span class="label label-danger"><?=$data_customer['total']?></span>
				<?php } ?>
			</a>
			<ul class="dropdown-menu collapse master">
				<?php if (in_array('product_category', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/product_category" class="tipped" data-title="Untuk melihat & menambahkan data kategori pada produk">Product Category</a>
					</li>
				<?php } if (in_array('methode_pembayaran', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/methode_pembayaran" class="tipped" data-title="Untuk menambahkan data metode pembayaran">Payment Methode</a>
					</li>
				<?php } if (in_array('customer', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/customer" class="tipped" data-title="Untuk melihat keseluruhan data pelanggan">Data Customer
							<?php if ($data_customer['total'] > 0) { ?>
								<span class="label label-danger"><?=$data_customer['total']?></span>
							<?php } ?>
						</a>
					</li>
				<?php } if (in_array('keep_limit_customer', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/limit_customer" class="tipped" data-title="Untuk setting keep limit customer" style="color: yellow; background: blue;">Keep limit customer
						</a>
					</li>
				<?php } if (in_array('suplier', $user_akses_menu)) { ?>
					<li>
						<a href="#" class="tipped" data-title="Untuk melihat keseluruhan data suplier">Data Suplier / Vendor
						</a>
					</li>
				<?php } if (in_array('customertype', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/customertype" class="tipped" data-title="Untuk melihat data kategori pelanggan">Customer Category</a>
					</li>
				<?php } if (in_array('bank_accounts', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/bank_account" class="tipped" data-title="Untuk melihat data bank">Bank Account</a>
					</li>
				<?php } if (in_array('suppliers', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/supplier" class="tipped" data-title="Untuk melihat data supplier">Data Supplier</a>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } if (in_array('purchase', $user_akses_menu)) { ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".purchase"><i class="fa fa-fw fa-list"></i> Purchase</a>
			<ul class="dropdown-menu collapse purchase">
			</ul>
		</li>
	<?php } if (in_array('stock', $user_akses_menu) || in_array('add_product', $user_akses_menu)) { ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".stok"><i class="fa fa-fw fa-list"></i> Stock</a>
			<ul class="dropdown-menu collapse stok">
				<?php if (in_array('re_stock', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/restok_stok" class="tipped" data-title="Untuk melihat dan menambahkan jumlah stock yang ada pada produk anda" style="color: yellow; background: blue;">Re-Stok</a>
					</li>
				<?php } if (in_array('opname_stock', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/opname_stok" class="tipped" data-title="Untuk melihat dan menambahkan jumlah stock yang ada pada produk anda" style="color: yellow; background: blue;">Opname Stok</a>
					</li>
				<?php } if (in_array('stock', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/stock" class="tipped" data-title="Untuk melihat dan menambahkan jumlah stock yang ada pada produk anda">Stock</a>
					</li>
				<?php } if (in_array('add_product', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/add_product/ready_stock" class="tipped" data-title="Untuk menambahkan produk baru pada katalog Ready Stock & <br/> melihat keseluruhan data produk pada katalog Ready Stock yang telah ditampilkan">Add Product Ready</a>
					</li>
					<li>
						<a href="<?=base_url()?>administrator/main/add_product/pre_order" class="tipped" data-title="Untuk menambahkan produk baru pada katalog Pre Order & <br/> melihat keseluruhan data produk pada katalog Pre Order yang telah ditampilkan"> Add Product Pre-Order</a>
					</li>
				<?php } ?>
			</ul>
		</li>
	 <?php 
		$user_id = $this->session->userdata('webadmin_user_id');
		$user_login = $this->db->get_where('users', array('id' => $user_id))->row_array();
		$user_akses_menu = explode(',', $user_login['akses_menu']);

		if (in_array('update_harga', $user_akses_menu)) { ?>
			<li class="dropdown-submenu">
				<a href="javascript:;" data-toggle="collapse" data-target=".cs"><i class="fa fa-fw fa-table"></i>  Update Harga</a>
				<ul class="dropdown-menu collapse cs">
					<li>
						<a href="<?=base_url()?>administrator/main/get_category" class="tipped" data-title="Untuk update harga by kategori" style="color: yellow; background: blue;">Update Harga Kategori</a>
					</li>
					<li>
						<a href="<?=base_url()?>administrator/main/update_harga_umum" class="tipped" data-title="Untuk update harga secara umum" style="color: yellow; background: blue;">
							Update Harga Umum
						</a>
					</li>
				</ul>
			</li>
		<?php } ?>
	<?php } if (in_array('input_pengeluaran', $user_akses_menu)) { ?>
		<li>
			<a href="<?= base_url()?>administrator/main/input_pengeluaran"><i class="fa fa-fw fa-sign-out"></i> Input Pengeluaran</a>
		</li>
	<?php } if (in_array('last_order_process', $user_akses_menu) || in_array('order_rekap', $user_akses_menu) || in_array('order_dropship', $user_akses_menu) || in_array('order_paid', $user_akses_menu) || in_array('create_order', $user_akses_menu) || in_array('scan_order', $user_akses_menu) || in_array('order_piutang', $user_akses_menu)) { ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".penjualan"><i class="fa fa-fw fa-shopping-cart"></i> Penjualan</a>
			<ul class="dropdown-menu collapse penjualan">
				<?php if (in_array('last_order_process', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/last_order_process" class="tipped" data-title="Untuk melihat data pesanan dalam proses berdasarkan pesanan terakhir"> Pesanan On Proses</a>
					</li>
				<?php } if (in_array('order_rekap', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/order_rekap" class="tipped" data-title="Untuk melihat keseluruhan data pesanan (Nota) yang sudah direkap">Pesanan Rekap / COD</a>
					</li>
				<?php } if (in_array('order_dropship', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/order_dropship" class="tipped" data-title="Untuk melihat keseluruhan data pesanan (Nota) yang sudah dropship">Pesanan Dropship</a>
					</li>
				<?php } if (in_array('order_paid', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/order_paid" class="tipped" data-title="Untuk melihat keseluruhan data pesanan (Nota) yang sudah lunas">Pesanan Lunas</a>
					</li>
				<?php } if (in_array('create_order', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/create_order" class="tipped <?php if ($uri == 'create_order_tamu'){echo "active";} ?>" data-title="Untuk membuat pesanan baru">Buat Pesanan</a>
					</li>
				<?php } if (in_array('scan_order', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/scan_order" class="tipped" data-title="Untuk scan pesanan menjadi lunas">Scan Pesanan</a>
					</li>
				<?php } if (in_array('order_piutang', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/order_piutang" class="tipped" data-title="Untuk melihat pesanan piutang">Pesanan Piutang</a>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } if (in_array('report_per_day', $user_akses_menu) || in_array('report_per_day_process', $user_akses_menu) || in_array('report_per_month', $user_akses_menu) || in_array('report_per_month_process', $user_akses_menu) || in_array('report_pembayaran_perday', $user_akses_menu) || in_array('report_pembayaran_perday_process', $user_akses_menu) || in_array('report_pembayaran_permonth', $user_akses_menu) || in_array('report_pembayaran_permonth_process', $user_akses_menu) || in_array('report_assets', $user_akses_menu) || in_array('master_data_pesanan', $user_akses_menu) || in_array('master_data_penjualan', $user_akses_menu) || in_array('report_autocancel', $user_akses_menu) || in_array('laporan_autocancel', $user_akses_menu) || in_array('report_pengeluaran', $user_akses_menu) || in_array('pembelian', $user_akses_menu) || in_array('result_pembelian', $user_akses_menu) || in_array('report_pembelian', $user_akses_menu) || in_array('result_report_pembelian', $user_akses_menu) || in_array('report_piutang', $user_akses_menu) || in_array('report_neraca', $user_akses_menu) || in_array('report_omset', $user_akses_menu) || in_array('report_stock', $user_akses_menu)) { ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".laporan"><i class="fa fa-fw fa-list"></i> Laporan</a>
			<ul class="dropdown-menu collapse laporan">
				<?php if (in_array('master_data_pesanan', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/master_data_pesanan" class="tipped <?php if ($uri == 'master_data_search'){ echo "active"; } ?>" data-title="Untuk mencari seluruh data pesanan"> Master Data Pesanan</a>
					</li>
				<?php } if (in_array('report_per_day', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/report_per_day" class="tipped" data-title="Untuk melihat keseluruhan laporan pesanan yang telah terjadi  per hari maupun per bulan">Laporan Pesanan</a>
					</li>
				<?php } if (in_array('report_pembayaran_perday', $user_akses_menu) || in_array('master_data_penjualan', $user_akses_menu) || in_array('report_omset', $user_akses_menu)) { ?>
					<li class="dropdown-submenu">
						<a href="javascript:;" data-toggle="collapse" data-target=".laporan-penjualan">Laporan Penjualan</a>
						<ul class="dropdown-menu collapse laporan-penjualan">
						<?php if (in_array('master_data_penjualan', $user_akses_menu)) { ?>
							<li>
								<a href="<?=base_url()?>administrator/main/master_data_penjualan" class="tipped" data-title="Untuk melihat keseluruhan laporan penjualan ">Master Data Penjualan</a>
							</li>
						<?php } if (in_array('report_pembayaran_perday', $user_akses_menu)) { ?>
							<li>
								<a href="<?=base_url()?>administrator/main/report_pembayaran_perday" class="tipped" data-title="Untuk melihat keseluruhan laporan pembayaran yang telah terjadi per hari maupun per bulan ">Laporan Pelunasan</a>
							</li>
						<?php } if (in_array('report_omset', $user_akses_menu)) { ?>
							<li>
								<a href="<?=base_url()?>administrator/main/report_omset" class="tipped" data-title="Untuk melihat keseluruhan laporan omset">Laporan Omset</a>
							</li>
						<?php } ?>
						</ul>
					</li>
				<?php } if (in_array('report_autocancel', $user_akses_menu) || in_array('laporan_autocancel', $user_akses_menu)) { ?>
					<li class="dropdown-submenu">
						<a href="javascript:;" data-toggle="collapse" data-target=".laporan-autocancel">Laporan Autocancel</a>
						<ul class="dropdown-menu collapse laporan-autocancel">
						<?php if (in_array('report_autocancel', $user_akses_menu)) { ?>
							<li>
								<a href="<?=base_url()?>administrator/main/report_autocancel" class="tipped" data-title="Untuk melihat master autocancel">Master Autocancel</a>
							</li>
						<?php } if (in_array('laporan_autocancel', $user_akses_menu)) { ?>
							<li>
								<a href="<?=base_url()?>administrator/main/laporan_autocancel" class="tipped" data-title="Untuk melihat keseluruhan laporan Autocancel">Laporan Autocancel</a>
							</li>
						<?php } ?>
						</ul>
					</li>
				<?php } if (in_array('report_pengeluaran', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/report_pengeluaran" class="tipped" data-title="Untuk melihat keseluruhan laporan pengeluaran">Laporan Pengeluaran</a>
					</li>
				<?php } if (in_array('report_stock', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/report_stock" class="tipped" data-title="Untuk melihat keseluruhan laporan perubahan stock">Laporan Perubahan Stock</a>
					</li>
				<?php } if (in_array('pembelian', $user_akses_menu) || in_array('report_pembelian', $user_akses_menu)) { ?>
					<li class="dropdown-submenu">
						<a href="javascript:;" data-toggle="collapse" data-target=".laporan_pembelian">Laporan Pembelian</a>
						<ul class="dropdown-menu collapse laporan_pembelian">
							<?php if (in_array('pembelian', $user_akses_menu)) { ?>
							<li>
								<a href="<?=base_url()?>administrator/main/pembelian" class="tipped <?= $uri == 'result_pembelian' ? 'active' : '' ?>" data-title="Untuk melihat keseluruhan data pembelian">Data Pembelian</a>
							</li>
							<?php } if (in_array('report_pembelian', $user_akses_menu)) { ?>
							<li>
								<a href="<?=base_url()?>administrator/main/report_pembelian" class="tipped <?= $uri == 'result_report_pembelian' ? 'active' : '' ?>" data-title="Untuk melihat keseluruhan laporan pembelian">Laporan Pembelian</a>
							</li>
							<?php } ?>
						</ul>
					</li>
				<?php } if (in_array('report_piutang', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/report_piutang" class="tipped" data-title="Untuk melihat keseluruhan laporan piutang">Laporan Piutang</a>
					</li>
				<?php } if (in_array('report_neraca', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/report_neraca" class="tipped" data-title="Untuk melihat keseluruhan neraca laporan">Laporan Neraca</a>
					</li>
				<?php } if (in_array('report_assets', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/report_assets" class="tipped <?php if ($uri == 'report_assets'){echo "active"; } ?>" data-title="Untuk melihat keseluruhan laporan assets">Laporan Assets</a>
					<?php } if (in_array('tutup_buku', $user_akses_menu)) { ?>
					<li>
						<a style="color: yellow; background: blue;" href="<?=base_url()?>administrator/main/report_tutup_buku_v2" class="tipped <?php if ($uri == 'laporan_tutu_buku'){echo "active"; } ?>" data-title="Laporan Tutup Buku">Laporan Tutup Buku V2</a>
					</li>
				<?php } if (in_array('point_diskon', $user_akses_menu)) { ?>
					<li>
						<a style="color: yellow; background: blue;" href="<?=base_url()?>administrator/main/report_per_month_diskon_point" class="tipped <?php if ($uri == 'laporan_tutu_buku'){echo "active"; } ?>" data-title="Laporan Tutup Buku">Laporan Point & Diskon V2</a>
					</li>
				<?php } if (in_array('produk_terjual', $user_akses_menu)) { ?>
					<li>
						<a style="color: yellow; background: blue;" href="<?=base_url()?>administrator/main/get_jumlah_laku_v2" class="tipped <?php if ($uri == 'laporan_tutu_buku'){echo "active"; } ?>" data-title="List Produk Terjual">List Produk Terjual V2</a>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } if (in_array('confirm_payment', $user_akses_menu) || in_array('resi_pengiriman', $user_akses_menu)) { ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".info"><i class="fa fa-fw fa-info-circle"></i> Informasi Pembayaran & Resi</a>
			<ul class="dropdown-menu collapse info">
				<?php if (in_array('confirm_payment', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/confirm_payment/all" class="tipped" data-title="Untuk melihat data konfirmasi pembayaran dari pelanggan">Konfirmasi Pembayaran</a>
					</li>
				<?php } if ( in_array('resi_pengiriman', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/resi_pengiriman" class="tipped" data-title="Untuk menginputkan data resi pengiriman">Data Resi & Pengiriman</a>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } if (in_array('message_add', $user_akses_menu) || in_array('chatting', $user_akses_menu)) {
		$data_tiket_status =  $this->db->select('COUNT(*) AS total')
    		->get_where('chatting',array('status' => 'Unread','sender' => 'Customer'))->row_array();
    	$data_chat_product =  $this->db->select('COUNT(*) AS total')
    		->get_where('chat_product', array('read_chat' => 0, 'sender' => 'Customer'))->row_array(); ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".cs"><i class="fa fa-fw fa-envelope"></i>  Customer Service <span class="label label-danger qty-chat-product-chat"><?=$data_tiket_status['total'] + $data_chat_product['total'] ?></span></a>
			<ul class="dropdown-menu collapse cs">
				<?php if (in_array('message_add', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/message_add" class="tipped" data-title="Untuk membuat pesan ke pelanggan secara individu">Kirim Pesan</a>
					</li>
				<?php } if (in_array('chatting', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/chatting" class="tipped" data-title="Untuk chatting ke pelanggan secara individu">
							Chatting <span class="label label-danger qty-chat"><?=$data_tiket_status['total']?></span>
						</a>
					</li>
				<?php } if (in_array('chat_product', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/chat_product" class="tipped" data-title="Data chat produk dari pelanggan">
							Chat Product <span class="label label-danger qty-chat-product"><?=$data_chat_product['total']?></span>
						</a>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } if (in_array('edit_info/toko', $user_akses_menu) || in_array('home_slideshow', $user_akses_menu) || in_array('edit_info/stok', $user_akses_menu) || in_array('edit_info/image', $user_akses_menu) || in_array('edit_info/nota', $user_akses_menu) || in_array('edit_info/link', $user_akses_menu) || in_array('edit_info/aplikasi', $user_akses_menu) || in_array('ekspedisi', $user_akses_menu) || in_array('content_faq', $user_akses_menu) || in_array('cara_order', $user_akses_menu) || in_array('name_tag', $user_akses_menu) || in_array('discount', $user_akses_menu) || in_array('point_reward', $user_akses_menu)) { ?>
		<li class="dropdown-submenu">
			<a href="javascript:;" data-toggle="collapse" data-target=".pengaturan"><i class="fa fa-fw fa-gear"></i> Pengaturan</a>
			<ul class="dropdown-menu collapse pengaturan">
				<?php if (in_array('edit_info/toko', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/edit_info/toko" class="tipped" data-title="Untuk pengaturan toko online anda">Data Toko Online</a>
					</li>
				<?php } if (in_array('home_slideshow', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/home_slideshow">Pengaturan Slideshow</a>
					</li>
				<?php } if (in_array('cara_order', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/cara_order">Cara Order / Tutorial</a>
					</li>
				<?php } if (in_array('name_tag', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/name_tag">Pengaturan Name Tag</a>
					</li>
				<?php } if (in_array('content_faq', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/content_faq/edit/21">FAQ</a>
					</li>
				<?php } if (in_array('discount', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/discount">Diskon</a>
					</li>
				<?php } if (in_array('point_reward', $user_akses_menu)) { ?>
					<li>
						<a href="<?=base_url()?>administrator/main/point_reward">Point & Reward</a>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } if (in_array('users_management', $user_akses_menu)) { ?>
		<li>
			<a href="<?= base_url()?>administrator/main/users_management"><i class="fa fa-fw fa-users"></i> Manajemen User</a>
		</li>
	<?php } ?>
	<?php if ($this->tokomobile_white_label == "Yes") { ?>
		<li>
			<a href="<?= base_url()?>administrator/main/info_paket"><i class="fa fa-fw fa-medkit"></i> Informasi Paket</a>
		</li>
	<?php } ?>
	<li>
		<a href="<?=base_url()?>administrator/main/logout" class="tipped" data-title="Untuk keluar dari halaman administrator anda"><i class="fa fa-fw fa-power-off"></i> Keluar</a>
	</li>
</ul>