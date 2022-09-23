<ul class="nav navbar-nav side-nav">
	<li>

		<a href="<?=base_url()?>administrator/main"  class="tipped" data-title="Untuk kembali ke halaman utama" ><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
	</li>
	<li>
		<a href="javascript:;" data-toggle="collapse" data-target=".pesanan_dalam_proses"><i class="fa fa-fw fa-shopping-cart"></i> Pesanan <i class="fa fa-fw fa-caret-down"></i></a>
		<ul class="collapse <?php if($this->uri->segment(3) == 'last_order_process' OR $this->uri->segment(3) == 'last_order_search' OR $this->uri->segment(3) == 'last_order_process_by_variant' OR $this->uri->segment(3) == 'product_last_order_result' OR $this->uri->segment(3) == 'last_order_process_expired' OR $this->uri->segment(3) == 'order_unpaid' OR $this->uri->segment(3) == 'list_pesanan_per_variant' OR $this->uri->segment(3) == 'master_data_pesanan' OR $this->uri->segment(3) == 'master_data_search' OR $this->uri->segment(3) == 'order_paid' OR $this->uri->segment(3) == 'create_order' OR $this->uri->segment(3) == 'create_order_tamu'){echo 'in';} ?> pesanan_dalam_proses">
			<li>
				<a href="<?=base_url()?>administrator/main/last_order_process" class="tipped <?php if( $this->uri->segment(3) == 'last_order_search' OR $this->uri->segment(3) == 'last_order_process_by_variant' OR $this->uri->segment(3) == 'product_last_order_result' OR $this->uri->segment(3) == 'last_order_process_expired' OR $this->uri->segment(3) == 'order_unpaid' OR $this->uri->segment(3) == 'list_pesanan_per_variant'  ){ echo "active"; } ?>" data-title="Untuk melihat data pesanan dalam proses berdasarkan pesanan terakhir"> Pesanan Dalam Proses</a>
			</li>
            <li>
                <a href="<?=base_url()?>administrator/main/master_data_pesanan" class="tipped <?php if($this->uri->segment(3) == 'master_data_search'){ echo "active"; } ?>" data-title="Untuk mencari seluruh data pesanan"> Master Data Pesanan</a>
            </li>
             <li>
                <a href="<?=base_url()?>administrator/main/order_rekap" class="tipped" data-title="Untuk melihat keseluruhan data pesanan (Nota) yang sudah direkap">Pesanan Rekap / COD</a>
            </li>
            <li>
                <a href="<?=base_url()?>administrator/main/order_paid" class="tipped" data-title="Untuk melihat keseluruhan data pesanan (Nota) yang sudah lunas">Nota Pesanan Lunas</a>
            </li>
            <li>
                <a href="<?=base_url()?>administrator/main/create_order" class="tipped <?php if($this->uri->segment(3) == 'create_order_tamu'){echo "active";} ?>" data-title="Untuk membuat pesanan baru">Buat Pesanan</i></a>
            </li>
		</ul>
   </li>
	 <li>
        <a href="<?=base_url()?>administrator/main/confirm_payment" class="tipped" data-title="Untuk melihat data konfirmasi pembayaran dari pelanggan"><i class="fa fa-fw fa-edit"></i> Konfirmasi Pembayaran</a>
    </li>




	<li>
        <a href="<?=base_url()?>administrator/main/logout" class="tipped" data-title="Untuk keluar dari halaman administrator anda"><i class="fa fa-fw fa-power-off"></i> Keluar</a>
    </li>
</ul>