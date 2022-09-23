<?php include "includes/header.php"; ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					Pesanan <small> Dalam Proses</small>
				</h1>
				<ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
                  	<li><a href="<?=base_url()?>administrator/main/last_order_process" ><b>Keep (Belum Lunas)</b></a></li>
                  	<li class="active"><a href="<?=base_url()?>administrator/main/last_order_process_by_variant" ><b>Keep Per Produk</b></a></li>
                	<li><a href="<?=base_url()?>administrator/main/order_unpaid" ><b>Dropship Belum Lunas</b></a></li>
                	 <li>
						<a href="<?=base_url()?>administrator/main/order_rekap_unpaid_expired" >
	    		    		<b>Jatuh Tempo (Rekap / COD)</b>
	    		    	</a>
					</li>
					<li><a href="<?=base_url()?>administrator/main/order_unpaid_expired" ><b>Jatuh Tempo (Dropship)</b></a></li>
                </ul>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-fw fa-list"></i> Pesanan Per Produk
					</li>
				</ol>
			</div>
		</div>
		<div class="panel">
			<div class="panel-body">
				<?=$this->session->flashdata('message') ?>
				<div class="search-wrapper row" style="margin-bottom: 15px;">
					<div class="keyword-wrapper col-sm-8" >
						<h4>Pencarian Nama Produk Dengan Kata Kunci "<?= $keyword ?>"</h4>
					</div>
					<div class="search-name-wrapper col-sm-4">
						<form name="form1" id="form1" method="post" action="<?=base_url()?>administrator/main/search_product_last_order_session" >
							<div class="col-sm-10 col-xs-10">
								<input type='text' id='name_product' name='name_product' class="autocomplete_pro form-control" placeholder="Nama Produk" style="padding: 6px; margin-right: 5px;" />
							</div>
							<div class="col-sm-2 col-xs-2">
								<button  type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
							</div>
						</form>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr class="btn-info">
								<td>NO</td>
								<td>Produk</td>
								<td>Varian</td>
								<td>Pesanan</td>
								<td>Action</td>
							</tr>
						</thead>
						<?php
						$i = 1 * ($offset + 1);
						foreach($list_product->result() as $list):

							$data_pesanan=$this->main_model->get_list_where('orders_item',array('variant_id' => $list->variant_id,'order_payment'=>'Unpaid','order_status'=>'Keep'));
							$total_pesanan = $data_pesanan->num_rows();
							$data_variant = $this->main_model->get_detail('product_variant',array('id' => $list->variant_id));
							$data_product = $this->main_model->get_detail('product',array('id' => $data_variant['prod_id']));
							?>
							<tbody>
								<tr>
									<td><?=$i ?></td>
									<td><?=$list->name_item ?></td>
									<td><?=$data_variant['variant'] ?></td>
									<td><?=$total_pesanan?></td>
									<td><a href="<?=base_url() ?>administrator/main/list_pesanan_per_variant/<?=$list->variant_id ?>" class="btn btn-primary" >Lihat Pesanan</a></td>

								</tr>
							</tbody>

							<?php
							$i++;
						endforeach;
						?>

					</table>
				</div>
				<?=$this->pagination->create_links(); ?>
			</div>
		</div>
		<?= $output ? $output->output : null; ?>
		<br/>
	</div>


</div>

<?php include "includes/footer.php"; ?>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script>
<script type='text/javascript' src='<?php echo base_url();?>assets/js/jquery.autocomplete.js'></script>
<?php if($output && $output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
<?php } ?>