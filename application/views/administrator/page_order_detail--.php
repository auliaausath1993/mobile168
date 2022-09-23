<?php include "includes/header.php";
error_reporting(E_ALL ^ (E_NOTICE )) ?>



<!-- Content

================================================== -->

<div id="page-wrapper">



	    <div class="container-fluid">



	        <!-- Page Heading -->

	        <div class="row">

	           <div class="col-lg-12">

	                <h1 class="page-header">

	                   Pesanan <small> Detail</small>

	                </h1>

	               <div class="message"></div>

	            </div>

	        </div>

	        <!-- /.row -->

			

	        <div class="row">

	           <div class="col-lg-12">

					<?=$this->session->flashdata('message'); ?>

	               	<div class="insert-box border-bottom">

	               		<div class="col-md-3">

							<strong>ID PESANAN</strong><br/>

							<strong>CUSTOMER / PELANGGAN</strong><br/>
							<?php if ($order['customer_id'] != 0) { ?>
							<strong>EMAIL</strong><br/>
							<?php } ?>

						</div>

						

						<div class="col-md-3">

							: # <?=$order['id'] ?><br/>

							<?php if ($order['customer_id'] == 0) { ?>
							: <span style="color:#e23427;"><?=$order['name_customer']?> <strong>(Guest)</strong></span><br/>
							<?php }else{ ?>
							: <?=$customer['name']?> (<?=$customer['id']?>)<br/>
							: <?=$customer['email'] ?>
							<?php } ?>

						</div>

						<div class="col-md-3">

							<strong>TANGGAL PESANAN </strong><br/>

							<strong>JENIS PESANAN </strong><br/>

							<strong>STATUS PEMBAYARAN </strong><br/>

							<strong>METODE PEMBAYARAN</strong><br/>

						</div>

						<div class="col-md-3">

							: <?=date("D, d-M-Y",strtotime($order['order_datetime'])); ?><br/>

							: <?=get_order_status($order['order_status']) ?><br/>

							: <?=get_order_payment_label($order['order_payment']) ?><br/>

							: <form role="form" id="payment_methode_update" style="display: inline;" action="<?= base_url() ?>administrator/main/payment_methode_process" method="post" enctype="multipart/form-data">
									
									<select  class="payment-methode-select" name="methode_pembayaran" title="insert methode pembayaran" style="padding: 7px;">
					                   <?php if ($order['payment_method_id'] != 0) { ?>
					                    	<option value="">Pilih Metode pembayaran</option>
					                    	<?php foreach($payment_method->result() as $payment_method) : ?>
												<option value="<?= $payment_method->id ?>"<?php if ($payment_method->id  == $order['payment_method_id']) { echo 'selected="selected" '; } ?>><?= $payment_method->name ?></option>
											<?php endforeach; ?>
					                   <?php }else{ ?>
					                   		<option value="">Pilih Metode pembayaran</option>
					                   		<?php foreach($payment_method->result() as $payment_method) : ?>
												<option value="<?= $payment_method->id ?>"><?= $payment_method->name ?></option>
											<?php endforeach; ?>
					                   <?php } ?>		                   
					                </select>
					                <input type="hidden" class="order_id" name="$order_id" value="<?=$order['id']?>">
					            </form>

						</div>

						

						<div class="clearfix"></div>

	               	</div>

	               	<div class="insert-box">

	               		

						<div class="well">

							<div class="row">

							

								<?=form_open('administrator/main/order_detail_change_status') ?>

								

								<input type="hidden" name="order_id" value="<?=$order['id'] ?>" />

								<div class="col-md-3">

									<select name="order_payment" class="form-control">

										<option value="<?=$order['order_payment'] ?>" ><?=get_order_payment($order['order_payment']) ?></option>

										<option> --- </option>

										<option value="Unpaid" >Belum Lunas</option>

										<option value="Paid">Lunas</option>

									</select>

									

								</div>

								<div class="col-md-2">

									<button class="btn btn-success btn-sm"><i class="fa fa-edit"></i> UBAH STATUS</button>
									

								</div>

								<?=form_close() ?>

								
								<div class="col-md-7">

									<p>
								
								
							
									<a href="<?=base_url()?>administrator/main/nota_detail_print/<?=$order['id'] ?>" class="btn btn-success btn-sm" ><i class="fa fa-print"></i> CETAK NOTA</a>

									<a href="<?=base_url()?>administrator/main/ekspedisi_print_a4/<?=$order['id'] ?>" class="btn btn-info btn-sm" ><i class="fa fa-print"></i> CETAK DATA EKSPEDISI</a>

									<a href="<?=base_url()?>administrator/main/nota_detail_save/<?=$order['id'] ?>" class="btn btn-success btn-sm" id="export-pdf"><i class="fa fa-file-pdf-o"></i> Export PDF</a>

									<a href="<?=base_url()?>administrator/main/cancel_order/<?=$order['id'] ?>" onClick="return confirm('Anda yakin ingin membatalkan ?');" class="btn btn-danger btn-sm" ><i class="fa fa-times"></i> BATALKAN PESANAN</a>
									<p/>

								</div>

							</div>

						</div>



						<hr/>

						<?php if ($order['customer_id'] != 0) { ?>
						<input type="hidden" name="customer_id" value="<?=$customer['id']?>" />
						<?php } ?>
						<?php if ($direct == 'kembali'){?>
						<a href="<?=base_url() ?>administrator/main/last_order_process" class="btn btn-default" >Kembali</a>
						<?php }else {?>
						<button class="btn btn-default" onclick="history.back(-1)">Kembali</button>
						<?php }?>
						<?php if($order['order_status'] == 'Dropship') { ?>
						<a href="<?=base_url()?>administrator/main/kirim_pesan_belum_lunas/<?=$order['id']?>" title="Kirim Pesan Kepada Customer" class="btn btn-danger pull-right">Kirim Pesan</a>

						<h4>PENGIRIMAN (Shipping) <span class="pull-right"><a href="<?=base_url()?>administrator/main/create_order_dropship/<?=$order['id']?>/<?=$order['order_payment']?>" class="btn"><strong>Edit Pengiriman</strong></a></span></h4>

						
						<table class="table table-striped table-bordered">

							<thead>

								<tr class="btn-primary">

									<th width="10%">

										DARI

									</th>	

									<th width="10%">

										DIKIRIM KE	

									</th>

									<th width="10%">

										Telpon Penerima	
									</th>

									<th width="20%">

										Alamat Penerima	
									</th>

									<th width="10%">

										Kode Pos	

									</th>

								</tr>

							</thead>

							<tbody>

								<tr>

									<td><?=$order['shipping_from'] ?></td>

									<td><?=$order['shipping_to'] ?></td>
									<td><?=$order['phone_recipient'] ?></td>
									<td><?=$order['address_recipient'] ?></td>
									<td><?=$order['postal_code'] ?></td>

								</tr>

							</tbody>	

						</table>

						<?php } ?>

						<div class="view-nota">

						</div>

						

						<h4>ITEM PESANAN</h4>

						<form method="POST" action="<?=base_url() ?>administrator/main/order_detail_update_qty" >
						<input type="hidden" name="order_id" value="<?=$order['id'] ?>" />
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
							foreach($order_item->result() as $orders) : 

							$data_product = $this->main_model->get_detail('product',array('id' => $orders->prod_id));

							$data_color = $this->main_model->get_detail('product_variant',array('id' => $orders->variant_id));

							$total = $total + $orders->subtotal;

							?>
								<tr>

									<td><?=$i?><input type="hidden" name="order_item_id[]" value="<?=$orders->id?>" /></td>

									<td><?=$data_product['name_item'] ?></td>

									<td><?=$data_color['variant'] ?></td>

									<td>Rp.<?=numberformat($orders->price)?></td>

									<td><?=$data_product['weight'] ?> Kg</td>

									<td><input type="number" min="1" name="order_item_qty[]" class="form-control" value="<?=$orders->qty ?>" /></td>

									<td>Rp.<?=numberformat($orders->subtotal) ?></td>
									
									
									<td>
									
									<?php if($total_items > 1) { ?>
									<a href="<?=base_url()?>administrator/main/cancel_order_item/<?=$orders->id?>" onClick="return confirm('Anda yakin ingin membatalkan ?');" class="btn-sm btn btn-danger">Cancel</a>
									<?php } ?>
									
									</td>

								</tr>	

							<?php 

							$i++;
							endforeach; ?>	

							<!-- Button trigger modal -->							
							<tr><td colspan="5"><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#myModal" id="form_add_item"><strong>[+] Tambah Item Pesanan</strong></a></td><td><button type="submit" class="btn btn-info btn-sm" name="update_order">SIMPAN</button></td><td colspan="2"></td></tr>	

								<?php if($order['order_status'] == 'Dropship') { ?>

								<tr>	

									<td></td>

									<td>Ongkos Pengiriman</td>

									<td>Ke <strong><?=$kota['kota_nama'] ?></strong></td>

									
									
									<td>-</td>

									<td><?=$order['shipping_weight'] ?> Kgs</td>
									<td>-</td>

									<td>Rp.<?=numberformat($order['shipping_fee']) ?></td>
									
									<td></td>
									
								</tr>	

								<?php } ?>

							</tbody>

						</table>

						

						<table class="table table-striped table-bordered">

							<thead>

								<tr class="btn-inverse">

									<th><big>TOTAL</big></th>

									<th width="30%"><big>Rp.<?=numberformat($order['total'])?></big></th>

								</tr>	

							</thead>

						</table>	

						</form>

	               	</div>

	            </div>

	        </div>

	        <!-- /.row -->



	    </div>

	    <!-- /.container-fluid -->


	    
	</div>

	<!-- /#page-wrapper -->


	<!-- ADD PRODUCT -->


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
	      
	      	<input type="hidden" name="add_form_order_id" value="<?=$order['id'] ?>" />
	      
	      	<div class="row">
	      		<div class="col-md-5">
	      		 Produk <br/>
			        <select name="add_form_product" id="add_form_product" class="form-control">
			        	<option> - </option>
			        	
			        </select>			
	      		</div>
	      		<div class="col-md-5">
	      			 Variant <br/>
			        <select name="add_form_variant" id="add_form_variant" class="form-control">
			        	<option> - </option>
			        	
			        </select>
			        <span id="notif_stock"></span>
	      		</div>
	      		<div class="col-md-2">
	      			QTY <br/>
	      			 <input type="number" min="1" name="add_form_qty" id="add_form_qty" class="form-control" />
	      		</div>
	      	</div>
	      
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <button type="submit" class="btn btn-primary">Tambahkan</button>
	      </div>
	       </form>
	    </div>
	  </div>
	</div>




<script type="text/javascript">

	$("#export-pdf").click(function(){
		window.location = base_url+"administrator/main/nota_detail_save/<?=$order['id'] ?>";
		$('#view-nota').tableExport({type:'png',escape:'false'});
    });


	$('.payment-methode-select').change(function(){
		var base_url = "<?=base_url()?>";
    	var methode_pembayaran =  $('.payment-methode-select :selected').val();
    	var order_id = $('.order_id').val();

    	$.post(base_url+"administrator/main/order_payment_methode_process",{order_id: order_id, methode_pembayaran: methode_pembayaran },
		
			function(data){

				if(data.status == 'Success')
               {
               	 
				$('.message').html('<div class=\"alert alert-success\">Methode Pembayaran telah berhasil dirubah</div>');
               }
	
		}, "json");

    });

	$("#form_add_item").click(function(){
		
		
		var base_url = "<?=base_url()?>";
		
		$("#add_form_product").html("");
		$("#add_form_product").append("<option>- Pilih Produk -</option>");
		$("#add_form_qty").val("0");
		
		$.post(base_url+"administrator/main/order_detail_get_publish_product",
		
			function(data){
				
				for(var i=0;i<data.length;i++)
				{
					$("#add_form_product").append("<option value='"+data[i].prod_id+"'>"+data[i].name_item+"</option>");
				}			
	
		}, "json");
		
		$("#add_form_variant").html("<option>- Pilih Variant -</option>");
		$("#add_form_variant").attr("disabled","disabled");
		
	});
	
	
	$("#add_form_product").change(function(){
		
		var prod_id = $(this).val();
		
		$("#add_form_variant").removeAttr("disabled");
		$("#add_form_variant").html("<option> - Pilih Variant -</option>");
		
		$.post(base_url+"administrator/main/order_detail_get_variant",{prod_id: prod_id},
		function(data){
			
			
				for(var i=0;i<data.length;i++)
				{
					$("#add_form_variant").append("<option value='"+data[i].variant_id+"'>"+data[i].variant+"</option>");
				}		
			
		}, "json");
			
	});
	
	$("#add_form_variant").change(function(){
		
		var variant_id  = $(this).val();
		
		$("#add_form_qty").val("");
		
		$.post(base_url+"administrator/main/order_detail_get_variant_detail",{variant_id: variant_id},
		function(data){
			
			
			$("#add_form_qty").attr("max",data.stock);
			$("#notif_stock").html("<font color='red'>Sisa Stock : <strong>"+data.stock+"</strong></font>")
			
		}, "json");
			
	});
</script>

<?php include "includes/footer.php"; ?>
