<?php include "includes/header.php"; ?>
<div id="page-wrapper">

    <div class="container-fluid">

	   	<!-- Page Heading -->

	    <div class="row">

	        <div class="col-lg-12">

	            <h1 class="page-header">

	                Pesanan <small> Dalam Proses</small>

	            </h1>

	            <ul id="submenu-container" class="nav nav-tabs nav-justified" style="margin-bottom: 20px;">

                  	<li><a href="<?=base_url()?>administrator/main/last_order_process" ><b>Keep (Belum Lunas)</b></a></li>
                  	<li><a href="<?=base_url()?>administrator/main/last_order_process_by_variant" ><b>Keep Per Produk</b></a></li>

                	<li><a href="<?=base_url()?>administrator/main/order_unpaid" ><b>Dropship Belum Lunas</b></a></li>

                	 <li class="active">
		    		    	<a href="<?=base_url()?>administrator/main/order_rekap_unpaid_expired" >
		    		    		<b>Jatuh Tempo (Rekap / COD)</b>
		    		    	</a>
		    		    </li>
						<li><a href="<?=base_url()?>administrator/main/order_unpaid_expired" ><b>Jatuh Tempo (Dropship)</b></a></li>

                </ul>

				 <ol class="breadcrumb">

	                <li class="active">

	                    <i class="fa fa-fw fa-list"></i> Pesanan Jatuh Tempo

	                </li>

	            </ol>

	        </div>

	    </div>

	    <!-- /.row -->


	    <?=$this->session->flashdata('message') ?>
	    <div class="panel">
	    	<div class="panel-body">
				<div id="list_ubah_status" class="table-responsive">

					<table class="table table-bordered">

						<thead>

							<tr class="btn-info">

								<th><br/><input type="checkbox" name="check_all" id="check_all" /></th>

								<th width="2%">No</th>

								<th width="10%">Pelanggan</th>

								<th width="12%">Produk</th>

								<th width="8%">Tgl Pesan</th>

								<th width="10%">Varian</th>

								<th width="2%">QTY</th>

								<th width="13%">Harga Jual</th>

								<th width="13%">Subtotal</th>

								<th width="10%">Tipe Produk</th>

								<th width="8%">Status</th>

								<th width="5%">Action</th>

							</tr>

						</thead>



							<?php

							if($list_orders->result() !=null){

								$i = 1 * ($offset + 1);

								foreach($list_orders->result() as $items):

								$data_customer = $this->main_model->get_detail('customer',array('id' => $items->customer_id));

								$data_product = $this->main_model->get_detail('product',array('id' => $items->prod_id));

								$data_variant= $this->main_model->get_detail('product_variant',array('id' => $items->variant_id));

							?>

						<tbody>

							<tr>

								<td>
										<input type="checkbox" name="order_item_id[]" class="check_list" value="<?=$items->id?>" />

									<input type="hidden" name="id_order_item" value="<?=$items->id?>">
								</td>
								<td><?=$i?></td>

								<td><?=$data_customer['name']." (".$data_customer['id'].")"?></td>

								<td><?=$data_product['name_item']?></td>

								<td><?=$items->order_datetime?></td>

								<td><?=$data_variant['variant']?></td>

								<td><?=$items->qty ?></td>

								<td>Rp <?=numberformat($items->price) ?></td>

								<td>Rp <?=numberformat($items->subtotal) ?></td>

								<td><?=$items->tipe?></td>

								<td><?=$items->order_status ?></td>

								<td>

								<form method="post" action="<?=base_url()?>administrator/main/order_per_customer/<?=$data_customer['id'] ?>">

								<a class="btn btn-danger"  onClick="return confirm('Anda yakin ingin membatalkan ?');" href="<?=base_url()?>administrator/main/cancel_order_process/<?=$items->id ?>">Batal</a>

								</form>
								</td>


							</tr>



						</tbody>



					<?php $i++; endforeach; ?>

					</table>

					<?php }else{ ?>

					<table align="center">

					<tr>

						<td>

							<p>Data yang anda inginkan tidak ada</p>

						</td>

					</tr>

					</table>

					<?php }?>

					<input type='button' class='btn btn-md btn-danger' id='btn_check_batal' value="BATALKAN" >

					<?=$this->pagination->create_links(); ?>

				</div>
			</div>
		</div>
	</div>

 </div>

 <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.1.min.js"></script>

<script type='text/javascript'>



// Using jQuery.





$('#check_all').click(function() {



    		 $('.check_list').prop('checked', this.checked);



		  if($(this).attr('checked')){



				$('.list').removeAttr('readonly');



			}



			else



			{



				$('.list').attr('readonly','readonly');



			}



 		});



$("#btn_check_batal").click(function(e){

	e.preventDefault();
	var checked = [];
	$('#list_ubah_status .check_list:checked').each(function() {
		checked.push($(this).val());
	});
	if (!checked.length) {
		return alert('Pilih Terlebih dahulu data yang akan dibatalkan !!');
	}
	$('#modal-pincode').modal('show');
	var action_link = base_url + 'administrator/main/update_status_batal_pesanan';
	var form = $('#modal-cancel-order form');
	form.attr('action', action_link);
	$('#order_item_id').html('');
	checked.map(function(id) {
		$('#order_item_id').append('<input type="hidden" name="order_item_id[]" value="' + id + '">');
	});



});



</script>





<?php include "includes/footer.php"; ?>