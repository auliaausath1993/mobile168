<?php include 'includes/header.php'; ?>
<div id="page-wrapper">
    <div class="container-fluid">
	    <div class="row">
	        <div class="col-lg-12">
	        	<h1 class="page-header">
	                Pesanan <small> Dalam Proses</small>
	            </h1>
	            <ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">
                  	<li><a href="<?=base_url()?>administrator/main/last_order_process" ><b>Pesanan Keep (Belum Lunas)</b></a></li>
                  	<li><a href="<?=base_url()?>administrator/main/last_order_process_by_variant" ><b>Pesanan Keep Per Produk</b></a></li>
                	<li><a href="<?=base_url()?>administrator/main/order_unpaid" ><b>Pesanan Dropship Belum Lunas</b></a></li>
                	<li><a href="<?=base_url()?>administrator/main/last_order_process_expired" ><b>Pesanan Jatuh Tempo</b></a></li>
                </ul>
				<h3><?=$order_payment?></h3>
	        </div>
	    </div>
	    <div class="panel">
	    	<div class="panel-body">
				<?=$this->session->flashdata('message') ?>
				<?=form_open('administrator/main/create_keep_order_to_paid'); ?>
				<input type="hidden" name="customer_id" value="<?=$customer_id?>" />
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr class="btn-info">
								<th><br/><input type="checkbox" name="check_all" id="check_all" /></th>
								<th>Produk</th>
								<th>Varian</th>
								<th>Harga</th>
								<th>Qty</th>
								<th>Subtotal</th>
								<th>Tgl Pesan</th>
								<th width="20%">Catatan</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($orders_item->result() as $items_order):
								$data_product = $this->main_model->get_detail('product',array('id' => $items_order->prod_id));
								$data_variant = $this->main_model->get_detail('product_variant',array('id' => $items_order->variant_id));
							?>
								<tr>
									<td>
										<input type="checkbox" name="order_item_id[]" class="check_list" value="<?=$items_order->id?>" />
										<input type="hidden" name="id_order_item" value="<?=$items_order->id?>">
									</td>
									<td><?=$data_product['name_item']?></td>
									<td><?=$data_variant['variant'] ?></td>
									<td>Rp <?=numberformat($items_order->price) ?></td>
									<td><?=$items_order->qty?></td>
									<td>Rp. <?=numberformat($items_order->subtotal)?></td>
									<td><?=date('D, d-M-Y H:i:s',strtotime($items_order->order_datetime)) ?></td>
									<td><?=$items_order->notes?></td>
									<input type="hidden" name="subtotal" value="<?=$items_order->subtotal?>">
									<input type="hidden" name="weight[]" value="<?=$data_product['weight']?>">
									<input type="hidden" name="qty[]" value="<?=$items_order->qty?>">
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<button type="submit" class="m-b-mini btn btn-success" name="submit" value="submit">Buat Nota Pesanan</button>
				<button type="submit" class="m-b-mini btn btn-primary" name="submit" value="dropship">Buat Nota Dropship</button>
				<a href="<?=base_url() ?>administrator/main/create_order"  class="m-b-mini btn btn-info" >+Tambah Pesanan</a>
				<a href="<?php if($this->input->post('halaman') == "view"){echo base_url()."administrator/main/last_order_process";}elseif($this->input->post('halaman') == "variant"){echo base_url()."administrator/main/list_pesanan_per_variant/".$this->input->post('number_variant');}else{echo base_url()."administrator/main/customer";}?>" class="m-b-mini btn btn-default pull-right">Kembali</a>

				<?= form_close(); ?>
			</div>
		</div>
		<hr/>
	   	<!-- Page Heading -->
	    <div class="row">
	        <div class="col-lg-12">
	            <h4>Data Pesanan</h4>
	        </div>
	    </div>
	    <!-- /.row -->

		<?= $output->output; ?>
    </div>
 </div>

<?php include "includes/footer.php"; ?>

<?php if($output->output != null) { ?>
	<?php foreach($output->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
	<?php endforeach; ?>
<?php } ?>

<script>
	$('#check_all').click(function() {
		$('.check_list').prop('checked', this.checked);
	  	if($(this).attr('checked')){
			$('.list').removeAttr('readonly');
		} else {
			$('.list').attr('readonly','readonly');
		}

	});
</script>