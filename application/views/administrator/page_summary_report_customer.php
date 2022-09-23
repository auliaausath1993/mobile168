<?php include "includes/header.php"; ?>

<!-- Content

================================================== -->

<div id="page-wrapper">



    <div class="container-fluid">



	    	<!-- Page Heading -->

	    <div class="row">

	        <div class="col-lg-12">

	            <h1 class="page-header">

	              Detail Pesanan Pelanggan

	            </h1>

	            <ol class="breadcrumb">

	                <li class="active">

	                    <i class="fa fa-fw fa-edit"></i> Detail Pesanan

	                </li>

	            </ol>

	        </div>

	    </div>

	    <!-- /.row -->
		<?=$this->session->flashdata('message') ?>

		<div class="row">
			<div class="col-sm-8">
				<?php
					$customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
				?>
				<?php echo '<h4>Detail pesanan pelanggan <strong>'.$customer['name'].' ('.$customer['id'].')</strong></h4>';?>
			</div>
			<div class="col-sm-4 text-right">
				<h4>TOTAL SEMUA PESANAN : <?= $total_pesanan ?></h4>
			</div>
			<div class="col-sm-6">
				<table class="table table-bordered">
					<thead>
						<tr class="btn-info">
							<td colspan="2" class="text-center"><strong>PESANAN BELUM LUNAS</strong></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="50%" class="text-center">Keep</td>
							<td width="50%" class="text-center">DropShip</td>
						</tr>
						<tr>
							<td class="text-center">
								<?php if ($order_keep_unpaid != 0) { ?>
									<a href="<?= base_url() ?>administrator/main/summary_report_customer/<?= $customer_id ?>/keep/unpaid/">
										<?= $order_keep_unpaid ?>
									</a>
								<?php }else{ ?>
									<?= $order_keep_unpaid ?>
								<?php } ?>
							</td>
							<td class="text-center">
								<?php if ($order_dropship_unpaid != 0) { ?>
									<a href="<?= base_url() ?>administrator/main/summary_report_customer/<?= $customer_id ?>/dropship/unpaid/">
										<?= $order_dropship_unpaid ?>
									</a>
								<?php }else{ ?>
									<?= $order_dropship_unpaid ?>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td class="text-center"><strong>TOTAL</strong></td>
							<td class="text-center"><?= $order_unpaid ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-sm-6">
				<table class="table table-bordered">
					<thead>
						<tr class="btn-info">
							<td colspan="2" class="text-center"><strong>PESANAN LUNAS</strong></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="50%" class="text-center">Keep</td>
							<td width="50%" class="text-center">DropShip</td>
						</tr>
						<tr>
							<td class="text-center">
								<?php if ($order_keep_paid != 0) { ?>
									<a href="<?= base_url() ?>administrator/main/summary_report_customer/<?= $customer_id ?>/keep/paid/">
										<?= $order_keep_paid ?>
									</a>
								<?php }else{ ?>
									<?= $order_keep_paid ?>
								<?php } ?>
							</td>
							<td class="text-center">
								<?php if ($order_dropship_paid != 0) { ?>
									<a href="<?= base_url() ?>administrator/main/summary_report_customer/<?= $customer_id ?>/dropship/paid/">
										<?= $order_dropship_paid ?>
									</a>
								<?php }else{ ?>
									<?= $order_dropship_paid ?>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td class="text-center"><strong>TOTAL</strong></td>
							<td class="text-center"><?= $order_paid ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-sm-12">
				<p><em>* Perhitungan jumlah pesanan berdasarkan total qty order pesanan</em></p>
			<p><em>* Jika anda ingin melihat detail dari jumlah pesanan tersebut silhkan klik nilai jumlah pesanan yang tersedia</em></p>
			</div>
		</div>
		<?php if($pesanan != null){?>
		<hr/>
		<div class="row">
			<div class="col-sm-8">
				<h4>Detail <?=$name_title?></h4>
			</div>
			<div class="col-sm-4">
				<div class="text-right" style="margin-bottom: 20px;">
							<a id="exsport-master-data" class="btn btn-success" href="#"><i class="fa fa-file-excel-o"></i> Export Excel</a>
							<a id="print-master-data" class="btn btn-primary" href="#"><i class="fa fa-print"></i> Cetak</a>
				</div>
			</div>
		</div>
    	<table id="flex1" class="table table-bordered">
			<thead>
				<tr class="btn-info">
					<td>No</td>
					<td>Order ID</td>
					<td>Produk</td>
					<td>Varian</td>
					<td>Tanggal Pesanan</td>
					<td>QTY</td>
					<td>Harga</td>
					<td>Subtotal</td>
					<td>Status Pembayaran</td>
				</tr>
			</thead>
			<?php
				$i = 1 * ($offset + 1);
				$total_price = 0;
				$total_subtotal = 0;
				$total_qty = 0;
				foreach($pesanan->result() as $pesan):
				$produk = $this->main_model->get_detail('product',array('id' => $pesan->prod_id));
				$variant = $this->main_model->get_detail('product_variant',array('id' => $pesan->variant_id));
			?>
			<tbody>
				<tr>
					<td><?=$i?></td>
					<td><?=$pesan->order_id ?></td>
					<td><?=$produk['name_item'] ?></td>
					<td><?=$variant['variant'] ?></td>
					<td><?=$pesan->order_datetime ?></td>
					<td><?=$pesan->qty ?></td>
					<td>Rp. <?=numberformat($pesan->price) ?></td>
					<td>Rp. <?=numberformat($pesan->subtotal) ?></td>
					<td><?=$pesan->order_payment ?></td>
				</tr>


			<?php
				$i++;
				$total_price = $total_price + $pesan->price;
				$total_subtotal = $total_subtotal + $pesan->subtotal;
				$total_qty = $total_qty + $pesan->qty;
				endforeach;
				?>
				<tr>
					<td colspan="5">TOTAL</td>
					<td><?= $total_qty ?></td>
					<td><strong>Rp. <?=numberformat($total_price) ?></strong></td>

					<td><strong>Rp. <?=numberformat($total_subtotal) ?></strong></td>


				</tr>
				</tbody>
    	</table>

		<?=$this->pagination->create_links(); ?>
		<?php }?>
    </div>



 </div>



<?php include "includes/footer.php"; ?>
<script>
$( "#exsport-master-data" ).click(function() {
	      $('#flex1').tableExport({type:'excel',tableName:'download_table', escape:'false'});
	    });

	    function printData()
	    {
	       var divToPrint = document.getElementById('flex1');
		    var htmlToPrint = '' +
		        '<style type="text/css">' +
		        'table th, table td {' +
		        'border:1px solid #666;' +
		        'padding:5px;' +
		        '}' +
		        'table th{' +
		        'background-color: #999; color: #fff;' +
		        '}' +
		        '</style>';
		    htmlToPrint += divToPrint.outerHTML;
		    newWin = window.open("");
		    newWin.document.write(htmlToPrint);
		    newWin.print();
		    newWin.close();
	    }

	   $('#print-master-data').on('click',function(){
			printData();
		})
</script>