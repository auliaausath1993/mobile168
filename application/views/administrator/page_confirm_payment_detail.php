<?php include "includes/header.php"; ?>



<!-- Content

================================================== -->

<div id="page-wrapper">



    <div class="container-fluid">



	    	<!-- Page Heading -->

	    <div class="row">

	        <div class="col-lg-12">

	            <h1 class="page-header">

	                Konfirmasi Pembayaran

	            </h1>

	            <ol class="breadcrumb">

	                <li class="active">

	                    <i class="fa fa-fw fa-edit"></i> Konfirmasi Pembayaran

	                </li>

	            </ol>

	        </div>

	    </div>

	    <!-- /.row -->

    

    	<?=$this->session->flashdata('message') ?>

	

		<table class="table table-bordered">

			<tbody>

				<tr>

					<td width="30%"><strong>Nomor Konfirmasi</strong></td>

					<td><?=$confirmation['id']?> </td>

				</tr>	

				<tr>

					<td><strong>ID Pesanan</strong></td>

					<td><?php if($confirmation['order_id'] != 0) { ?><a href="<?=base_url()?>administrator/main/order_detail/<?=$confirmation['order_id'] ?>" target="_blank"> #<?=$confirmation['order_id'] ?></a> <? } else { ?> Tidak ada referensi pesanan<?php } ?></td>
				</tr>
				<tr>

					<td><strong>Tanggal</strong></td>

					<td><?=$confirmation['date'] ?></td>

				</tr>

				<tr>

					<td><strong>Nama</strong></td>

					<td><?=$confirmation['name'] ?></td>

				</tr>

				<tr>

					<td><strong>Jumlah</strong></td>

					<td><?=$confirmation['amount'] ?></td>

				</tr>

				<tr>

					<td><strong>Bank</strong></td>

					<td><?=$confirmation['bank'] ?></td>

				</tr>	

				<tr>

					<td><strong>Rekening</strong></td>

					<td><?=$confirmation['bank_account_number'] ?></td>

				</tr>	

				<tr>

					<td><strong>Status</strong></td>

					<td><?=$confirmation['status'] ?></td>

				</tr>

				<tr>

					<td><strong>Metode Pembayaran</strong></td>

					<td>
						
						<form role="form" action="<?= base_url() ?>administrator/main/payment_methode_process/" method="post" enctype="multipart/form-data">
							<select  class="paket-select" name="methode_pembayaran" title="insert metode pembayaran" style="padding: 7px;">
			                   <?php if ($confirmation['payment_method_id'] != 0) { ?>
			                    	<option value="">Pilih Metode pembayaran</option>
			                    	<?php foreach($payment_method->result() as $payment_method) : ?>
										<option value="<?= $payment_method->id ?>"<?php if ($payment_method->id  == $confirmation['payment_method_id']) { echo 'selected="selected" '; } ?>><?= $payment_method->name ?></option>
									<?php endforeach; ?>
			                   <?php }else{ ?>
			                   		<option value="">Pilih Methode pembayaran</option>
			                   		<?php foreach($payment_method->result() as $payment_method) : ?>
										<option value="<?= $payment_method->id ?>"><?= $payment_method->name ?></option>
									<?php endforeach; ?>
			                   <?php } ?>		                   
			                </select>
			                <input type="hidden" name="confrim_id" value="<?=$confirmation['id']?>">
			                <input type="hidden" name="order_id" value="<?=$confirmation['order_id']?>">
			                <button class="btn btn-success">Ubah Metode Pembayaran</button>
			            </form>
					</td>

				</tr>	

			</tbody>

		</table>

		

	

			<a href="<?=base_url()?>administrator/main/confirm_payment_change_status/<?=$confirmation['id']?>/Approve" class="btn btn-success" <?php if($confirmation['order_id']== 0){echo 'readonly';}?> >Approve</a> 
		
			<a href="<?=base_url()?>administrator/main/confirm_payment_change_status/<?=$confirmation['id']?>/Approve/Paid" class="btn btn-success" <?php if($confirmation['order_id']== 0){echo 'disabled';}?> >Approve & Lunas</a>

			<a href="<?=base_url()?>administrator/main/confirm_payment_change_status/<?=$confirmation['id']?>/Reject" class="btn btn-danger">Reject</a> 
			
			<a href="<?=base_url()?>administrator/main/confirm_payment" class="btn btn-default pull-right">Back to list</a> 

		

    </div>



 </div>



<?php include "includes/footer.php"; ?>

