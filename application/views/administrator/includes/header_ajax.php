<?php

$data_user = $this->main_model->get_detail('users',array('id' => $this->session->userdata('webadmin_user_id')));

//data header

$data_header_confirmation =  $this->main_model->get_list_where('confirmation',array('status' => 'Pending'),null,array('by' => 'id','sorting' => 'DESC'));

$data_header_status =  $this->main_model->get_list_where('customer',array('status' => 'Moderate'),null,array('by' => 'id','sorting' => 'DESC'));

?>

<div id="load_content">
	<ul  class="nav navbar-right top-nav">
		<li react-component="OrderKeep" class="dropdown"></li>
		<li react-component="OrderRekap" class="dropdown"></li>
		<li react-component="OrderDropship" class="dropdown"></li>
		<li class="dropdown">
			<a href="<?=base_url()?>administrator/main/confirm_payment" class="top-menu" ><i class="fa fa-inbox"></i> <span>Konfirmasi</span> 
				<?php if ($data_header_confirmation->num_rows() > 0) { ?>
					<span class="label label-danger" id="qty-konfirmasi"><?=$data_header_confirmation->num_rows()?></span> 
				<?php } ?>
			</a>
		</li>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle  top-menu" data-toggle="dropdown"><i class="fa fa-user"></i> <span>Akun</span> <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li>
					<a href="<?=base_url()?>administrator/main/edit_profile"><i class="fa fa-fw fa-user"></i> Profil</a>
				</li>
				<li>
					<a href="<?=base_url()?>administrator/main/message"><i class="fa fa-fw fa-envelope"></i> Pesan</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="<?=base_url()?>administrator/main/logout"><i class="fa fa-fw fa-power-off"></i> Keluar</a>
				</li>
			</ul>
		</li>
	</ul>
	<?php
	$data_tiket_status =  $this->main_model->get_list_where('chatting',array('status' => 'Unread','sender' => 'Customer'),null,array('by' => 'id','sorting' => 'DESC'));
	$data_chat_product =  $this->main_model->get_list_where('chat_product', array('read_chat' => 0, 'sender' => 'Customer'),null,array('by' => 'id','sorting' => 'DESC'));
	?>

	<script>
		var notifikasi = localStorage.getItem('notifikasi');

		var keep = $('#qty-keep').text();
		localStorage.setItem('total-keep-new', keep);

		var rekap = $('#qty-rekap').text();
		localStorage.setItem('total-rekap-new', rekap);

		var dropship = $('#qty-dropship').text();
		localStorage.setItem('total-dropship-new', dropship);

		var konfirmasi = $('#qty-konfirmasi').text();
		localStorage.setItem('total-konfirmasi-new', konfirmasi);

		var chat = '<?= $data_tiket_status->num_rows() ?>';
		localStorage.setItem('total-chat-new', chat);
		$('.qty-chat').text(chat);

		var sound = document.getElementById("audio");

		var keep = localStorage.getItem('total-keep');
		var keep_new = localStorage.getItem('total-keep-new');

		if (Number(keep) < Number(keep_new)) {
			localStorage.setItem('total-keep', keep_new);
			if (notifikasi.search('Keep') >= 0) {
				sound.play();
			}
		}

		var rekap = localStorage.getItem('total-rekap');
		var rekap_new = localStorage.getItem('total-rekap-new');
		if (Number(rekap) < Number(rekap_new)) {
			localStorage.setItem('total-rekap', rekap_new);
			if (notifikasi.search('Rekap') >= 0) {
				sound.play();
			}
		}

		var dropship = localStorage.getItem('total-dropship');
		var dropship_new = localStorage.getItem('total-dropship-new');
		if (Number(dropship) < Number(dropship_new)) {
			localStorage.setItem('total-dropship', dropship_new);
			if (notifikasi.search('Dropship') >= 0) {
				sound.play();
			}
		}

		var konfirmasi = localStorage.getItem('total-konfirmasi');
		var konfirmasi_new = localStorage.getItem('total-konfirmasi-new');
		if (Number(konfirmasi) < Number(konfirmasi_new)) {
			localStorage.setItem('total-konfirmasi', konfirmasi_new);
			if (notifikasi.search('Konfirmasi') >= 0) {
				sound.play();
			}
		}

		var chat = localStorage.getItem('total-chat');
		var chat_new = localStorage.getItem('total-chat-new');

		var chat_product = localStorage.getItem('total-chat-product');
		var chat_product_new = localStorage.getItem('total-chat-product-new');

		if (Number(chat) < Number(chat_new)) {
			localStorage.setItem('total-chat', chat_new);
			if (notifikasi.search('Chatting') >= 0) {
				sound.play();
			}
			$('.qty-chat').html(chat_new);
			var total_qty_chat_product = parseInt(chat_new) + parseInt(chat_product_new);
			$('.qty-chat-product-chat').html(total_qty_chat_product);
		}


		if (Number(chat) < Number(chat_new)) {
			localStorage.setItem('total-chat-product', chat_new);
			if (notifikasi.search('Chatting') >= 0) {
				sound.play();
			}
			$('.qty-chat-product').html(chat_new);
			var total_qty_chat_product = parseInt(chat_new) + parseInt(chat_product_new);
			$('.qty-chat-product-chat').html(total_qty_chat_product);
		}

	</script>