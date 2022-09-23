<?php

$data_user = $this->main_model->get_detail('users',array('id' => $this->session->userdata('webadmin_user_id')));

$data_confirmation = $this->db->select('COUNT(*) AS total')
	->get_where('confirmation',array('status' => 'Pending'))->row_array();

$data_customer = $this->db->select('COUNT(*) AS total')
	->get_where('customer',array('status' => 'Moderate'))->row_array();
$data_value_stock = $this->main_model->get_detail('content',array('id' => 10));
$data_toolstip = $this->main_model->get_detail('content',array('id' => 17));
$bts = $this->total_available_space_product;
$publish =$this->total_publish_product ;
$max = $this->total_max_product;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">



    <title><?=$this->config->item('tokomobile_online_shop'); ?> - Administrator</title>

    <script src="<?= base_url() ?>application/views/administrator/assets/js/jquery.js"></script>
    <script type="text/javascript">
    var base_url = '<?= base_url() ?>';
    </script>

	<?php if($output) { ?>
		<?php foreach($output->css_files as $file) { ?>
			<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
		<?php }
	} ?>


	<link rel="stylesheet" href="<?= base_url('application/views/administrator/assets/new-theme/css/bootstrap.css') ?>">
	<link rel="stylesheet" href="<?= base_url('application/views/administrator/assets/new-theme/css/font-awesome.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('application/views/administrator/assets/new-theme/css/font.css') ?>">
	<link rel="stylesheet" href="<?= base_url('application/views/administrator/assets/new-theme/css/style.css') ?>">
	<link rel="stylesheet" href="<?= base_url('application/views/administrator/assets/new-theme/css/plugin.css') ?>">


	<link href='<?php echo base_url();?>assets/css/jquery.autocomplete.css' rel='stylesheet' />

    <link href="<?= base_url() ?>application/views/administrator/assets/css/site.css" rel="stylesheet">

    <link href="<?= base_url() ?>application/views/administrator/assets/js/datepicker/css/datepicker.css" rel="stylesheet">



    <link href="<?= base_url() ?>application/views/administrator/assets/css/jquery-multicomplete.css" rel="stylesheet" media="all" />

    <link href="<?= base_url() ?>application/views/administrator/assets/css/jquery.fs.tipper.css" rel="stylesheet" media="all" />
    <!-- Scrollbar -->

    <!-- <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/css/jquery.mCustomScrollbar.css"> -->

    <!-- Css Upload
        <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/css/uploadfile.css">
    -->
    <!-- Custom Fonts -->

    <link href="<?=base_url()?>application/views/administrator/assets/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>

    <![endif]-->
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jquery.fs.tipper.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
	<script src="https://unpkg.com/jspdf-autotable@3.1.1/dist/jspdf.plugin.autotable.js"></script>


    <style type="text/css">
        table.table-grosir tr > th, table.table-grosir tr > td  {
            text-align: center;
            vertical-align: middle;
        }

        table.table-grosir input[type="text"] {
            width: 100%;
        }

        table.table-grosir {
            margin-bottom: 0px;
        }

        .navbar-inverse .nav .top-menu, .navbar-inverse .navbar-brand {
      		<?php
			$header_color = $this->main_model->get_detail('content',array('name' => 'header_color'));
      		$font_color = $this->main_model->get_detail('content',array('name' => 'font_color')); ?>

      		color: <?= $font_color['value'] ?> !important;
      	}

      	.subsubmenu ul {
      		padding: 0;
      	}
      	.subsubmenu ul > li > a {
			color: #ccc !important;
			display: block;
			padding: 0px 15px 5px 60px;
			text-decoration: none;
      	}

      	ul.navbar-nav > li {
      		display: flex;
		    align-items: center;
		    height: 50px;
      	}
      	li.message-preview {
      		min-width: 220px;
      		max-width: 275px;
		    border-bottom: 1px solid rgba(0,0,0,.15);
		}
		.open>.dropdown-menu>li>a {
		    white-space: normal;
		}
		li.message-preview>a {
		    padding-top: 15px;
		    padding-bottom: 15px;
		}
		ul.message-dropdown {
		    padding: 0;
		    max-height: 250px;
		    overflow-x: hidden;
		    overflow-y: auto;
		}
		.datepicker[readonly] {
			background-color: #FFF !important;
		}
		.datepicker[disabled] {
			background-color: #EEE !important;
		}
		.nav.navbar-nav.navbar-avatar.pull-right {
			display: flex;
		}
		@media (max-width: 767px) {
			.navbar-fixed {
			    /*padding-top: 86px;*/
			}
			.dropdown-submenu .dropdown-submenu li a {
				padding-left: 30px;
			}
		}
		@media (max-width: 575px) {
			.flexigrid div.form-div input[type=text], .flexigrid div.form-div input[type=password], .chzn-container-single .chzn-single {
				width: 100%;
			}
		}
		@media (min-width: 992px) {
			.form-pembelian .form-inline .datepicker {
				width: 130px;
			}
		}
		.btn-wa {
			position: fixed;
			right: 20px;
			bottom: 20px;
			width: 50px;
			height: 50px;
			z-index: 999;
			background: #00e676;
			border-radius: 50%;
			display: flex;
			justify-content: center;
			align-items: center;
			box-shadow: 0 0 5px rgba(0,0,0,.1);
		}

		.btn-wa img {
			width: 35px;
			height: 35px;
		}
    </style>
    <script src="<?= mix('js/app.js') ?>" defer></script>
    <script type="text/javascript">
    	<?php if ($data_toolstip['value'] != 'OFF') { ?>
            jQuery(document).ready(function($) {
                $('.tipped').tipper({ direction: 'right' });
            });
        <?php } ?>
         jQuery(document).ready(function($) {
            $('.tool_tips').tipper({ direction: 'bottom' });
        });
        </script>
</head>

<?php
$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'http://mobileapp168.com/mobile_demo/_api_/whatsapp' );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

$no_wa = json_decode(curl_exec($ch ));
curl_close( $ch );
?>

<body class="navbar-fixed">
	<a href="<?= 'https://wa.me/62' . $no_wa->no_wa ?>" class="btn-wa" target="_blank">
		<img src="http://mobileapp168.com/mobile_demo/assets/logo-wa.png">
	</a>
	<input type="hidden" id="base_url" value="<?= base_url() ?>">
	<header id="header" class="navbar">
		<a class="navbar-brand hidden-xs" href="<?= base_url() ?>" style="height: 35px; color:<?= $header_color['value'] ?>;">
			<?= $this->config->item('tokomobile_online_shop'); ?>
		</a>
		<?php include 'top-menu.php'; ?>
		<button type="button" class="btn btn-link pull-left nav-toggle visible-xs" data-toggle="class:slide-nav slide-nav-left" data-target="body">
			<i class="fa fa-bars fa-lg text-default"></i>
		</button>
	</header>

	<nav id="nav" class="nav-primary hidden-xs">
		<a class="navbar-brand hidden-sm hidden-md hidden-lg" href="<?= base_url() ?>" style="height: 35px; color: #fff">
			<?= $this->config->item('tokomobile_online_shop'); ?>
		</a>
		<?php include 'sidebar-menu-owner.php'; ?>
	</nav>

    <section id="content">
    	<section class="main padder">