<ul class="nav navbar-nav side-nav">
	<li>

		<a href="<?=base_url()?>administrator/main"  class="tipped" data-title="Untuk kembali ke halaman utama" ><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
	</li>
	
	              
    <li>
        <a href="<?=base_url()?>administrator/main/customer" class="tipped" data-title="Untuk melihat keseluruhan data pelanggan"><i class="fa fa-fw fa-list"></i> Data Pelanggan 
			<?php if($data_header_status->num_rows() > 0) { ?>
			<span class="label label-danger"><?=$data_header_status->num_rows()?></span> 
		<?php } ?>
		</a>
    </li>
    <li>
        <a href="javascript:;" data-toggle="collapse" data-target=".katalog"><i class="fa fa-fw fa-list"></i> Katalog <i class="fa fa-fw fa-caret-down"></i></a>
        <ul class="collapse katalog <?php if($this->uri->segment(3) == 'product_category' OR $this->uri->segment(3) == 'add_product' OR $this->uri->segment(3) == 'edit_product' OR $this->uri->segment(3) == 'stock' OR $this->uri->segment(3) == 'search_product_stock' OR $this->uri->segment(3) == 'product' ){echo 'in';} ?>">
            <li>
                <a href="<?=base_url()?>administrator/main/product_category" class="tipped <?php if( $this->uri->segment(3) == 'product_category' ){echo "active";} ?>" data-title="Untuk melihat & menambahkan data kategori pada peroduk">Kategori Produk</a>
            </li>                          
            <li>
                  <a href="<?=base_url()?>administrator/main/add_product/ready_stock" class="tipped <?php if( $this->uri->segment(4) == 'Ready_Stock' OR $this->uri->segment(4) == 'ready_stock' ){echo "active";} ?>" data-title="Untuk menambahkan produk baru pada katalog Ready Stock & <br/> melihat keseluruhan data produk pada katalog Ready Stock yang telah ditampilkan">Produk Ready Stock</a>
            </li>
            <li>
                <a href="<?=base_url()?>administrator/main/add_product/pre_order" class="tipped <?php if( $this->uri->segment(4) == 'PO' OR $this->uri->segment(4) == 'pre_order' ){echo "active";} ?>" data-title="Untuk menambahkan produk baru pada katalog Pre Order & <br/> melihat keseluruhan data produk pada katalog Pre Order yang telah ditampilkan"> Produk Pre Order</a>
            </li> 
            <li>
               <a href="<?=base_url()?>administrator/main/stock" class="tipped <?php if( $this->uri->segment(3) == 'stock' OR $this->uri->segment(3) == 'search_product_stock' ){echo "active";} ?>" data-title="Untuk melihat dan menambahkan jumlah stock yang ada pada produk anda">Stok</a>
            </li>                         
        </ul>
    </li>

  	 <li>
        <a href="<?=base_url()?>administrator/main/resi_pengiriman" class="tipped" data-title="Untuk menginputkan data resi pengiriman"><i class="fa fa-fw fa-list"></i> Data Resi Pengiriman</a>                     
    </li>  		
	<li>
        <a href="javascript:;" data-toggle="collapse" data-target=".kontenwebsite"><i class="fa fa-fw fa-gear"></i> Konten Website <i class="fa fa-fw fa-caret-down"></i></a>
        <ul class="collapse kontenwebsite <?php if($this->uri->segment(3) == 'home_slideshow' OR $this->uri->segment(3) == 'testimonial' OR $this->uri->segment(3) == 'resi' OR $this->uri->segment(3) == 'cara_pesan' ){echo 'in';} ?>">
            <li>
                <a href="<?=base_url()?>administrator/main/home_slideshow">Home Slideshow</a>
            </li>
            <li>
                <a href="<?=base_url()?>administrator/main/testimonial">Testimonial</a>
            </li>
            <li>
                <a href="<?=base_url()?>administrator/main/resi">Info Resi</a>
            </li>
            <li>
                <a href="<?=base_url()?>administrator/main/cara_pesan/edit/20">Cara Pesan</a>
            </li>       
        </ul>
    </li>
   <?php
        $data_tiket_status =  $this->main_model->get_list_where('chatting',array('status' => 'Unread','sender' => 'Customer'),null,array('by' => 'id','sorting' => 'DESC'));
    ?>
    <li>
        <a href="<?=base_url()?>administrator/main/chatting" class="tipped" data-title="Untuk chatting ke pelanggan secara individu"><i class="fa fa-fw fa-envelope"></i> 
        Chatting <span class="label label-danger" id="qty-chat"><?=$data_tiket_status->num_rows()?></span></a>
    </li>
	<li>
        <a href="<?=base_url()?>administrator/main/logout" class="tipped" data-title="Untuk keluar dari halaman administrator anda"><i class="fa fa-fw fa-power-off"></i> Keluar</a>
    </li>
</ul>