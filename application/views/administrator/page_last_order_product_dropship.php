<?php include "includes/header.php"; ?>

<!-- Content
================================================== -->
<section id="content">

	<!-- Headings & Paragraph Copy -->
	<div class="row">

    <div class="span12">
	
	<h4>Pesanan Terakhir Per Produk (Dropship)</h4>
	<br/>
	<div>
	
		<div class="row-fluid">
			<div class="span12">
			<table class="table table-bordered">
					<thead>
					
						<tr class="btn-inverse">
							
							<th>Item</th>
							<th>Color/Variant</th>
							<th>Total Pesanan (QTY)</th>
							<th>Subtotal</th>
							<th>#</th>
							
						</tr>
						<?php 
						$i = 1;
						foreach($list_product->result() as $items): 
	
						$data_product = $this->main_model->get_detail('product',array('id' => $items->prod_id));
						$count_subtotal_query = "SELECT SUM(subtotal) AS sub_total FROM orders_item WHERE color_id = '$items->id'";
						$count_subtotal = $this->db->query($count_subtotal_query)->row_array();
						
						$count_qty_query = "SELECT SUM(qty) AS total_qty FROM orders_item WHERE color_id = '$items->id'";
						$count_qty = $this->db->query($count_qty_query)->row_array(); 
						
						?>	
						<tr>
							<td><?=$data_product['name_item'] ?></td>	
							<td><?=$items->color ?></td>	
							<td><?=$count_subtotal['sub_total'] ?></td>	
							<td><?=$count_qty['total_qty']?></td>
							<td><a href="<?=base_url()?>backend/crud/last_order_product_detail/<?=$items->id?>" class="btn btn-info">LIHAT ORDER</a></td>	
						</tr>	
						<?php 
						$i++;
						endforeach; ?>
					</thead>
				</table>
				
				<?=$this->pagination->create_links(); ?>
			</div>
			
		</div>
		<hr/>
		
	</div>
	</div>
	</div>
  
	

</section>

<?php include "includes/footer.php"; ?>