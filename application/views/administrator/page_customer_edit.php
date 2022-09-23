<?php include "includes/header.php"; ?>
<!-- Content
================================================== -->
<section id="content">

  <!-- Headings & Paragraph Copy -->

  <div class="row">

    <div class="span12">

	 <div class="well">
	 <?=$this->session->flashdata('message') ?>
		<?=$content?>

		<h4>Password : <?=$this->encrypt->decode($pelanggan['password']) ?></h4>

		<form action="<?=base_url()?>backend/crud/change_password_customer" method="post">
		<input type="hidden" name="customer_id" value="<?=$pelanggan['id'] ?>"/>
		Ubah Pasword (kosongkan jika tidak ingin mengubah password)<br/><input type="text" name="password"><br/>
		<button type="submit" name="change" class="btn">Change Password</button>
		</form>
	</div>

  </div>
  </div>
</section>

<?php include "includes/footer.php"; ?>