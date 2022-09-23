<ul class="nav navbar-nav navbar-avatar pull-right">
	<li react-component="OrderKeep" class="dropdown"></li>
	<li react-component="OrderRekap" class="dropdown"></li>
	<li react-component="OrderPiutang" class="dropdown"></li>
	<li react-component="OrderDropship" class="dropdown"></li>
	<li class="dropdown">
		<a href="<?=base_url()?>administrator/main/confirm_payment" class="top-menu tool_tips" id="confirm" data-title="Konfirmasi">
			<i class="fa fa-inbox fa-fw fa-lg text-default"></i>
			<?php if ($data_confirmation['total'] > 0) { ?>
				<b class="badge badge-notes bg-danger" id="qty-konfirmasi"><?=$data_confirmation['total']?></b>
			<?php } ?>
		</a>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<span class="hidden-xs-only">
				<?= $this->session->userdata('webadmin_user_name'); ?>
			</span>
			<span class="thumb-small avatar inline hidden-lg hidden-md hidden-sm">
				<i class="fa fa-user"></i>
			</span>
			<b class="caret hidden-xs-only"></b>
		</a>
		<ul class="dropdown-menu pull-right">
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