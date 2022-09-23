<html lang="en"><head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <title>Login admin</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="utf-8">



    <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/bootstrap/css/bootstrap.css" media="screen">

	<link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/css/login.css" media="all">





    <link href="<?= base_url() ?>application/views/administrator/assets/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">



    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->

    <!--[if lt IE 9]>

      <script src="../assets/js/html5shiv.js"></script>

    <![endif]-->



    <!-- Fav and touch icons -->

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">

    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">

    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">

    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">

    <link rel="shortcut icon" href="../assets/ico/favicon.png">

  </head>




  <body>



    <div class="container">

	<?= $this->session->flashdata('message'); ?>

	<br/><br/><br/><br/>

     <?=form_open('administrator/login/login_process',array('class' => 'form-signin')); ?>



	<p><h3>404 Not Found</h3></p>
	<p><?php echo'<h3>'.$invalid.'</h3>';?></p>
	<p>This url not valid, please enter right url or go back<a href="<?=base_url()?>administrator/forgot"> click hear</a></p>


     <?=form_close()?>



	<center>

	<p class="powered">Powered by <b>TokoMobile </b><br/>

	Aplikasi Smartphone Online Shop

	</p>



	</center>

    </div>


    <div class="modal fade" id="Sucsess" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header" style="background-color: rgb(40, 169, 131); color: white;">
                  <h4 class="modal-title" id="myModalLabel">Activation Sucsess</h4>
              </div>

              <div class="modal-body text-center">
                  <p class="text-center">Terimakasih anda telah berhasil melakukan activasi.</p>
              </div>
          </div>
      </div>
    </div>



	<div class="bottom-navbar"><strong><?=$this->config->item('tokomobile_online_shop'); ?></strong>

	</div>

    <script src="<?= base_url() ?>application/views/administrator/assets/js/jquery.js"></script>

    <script src="<?= base_url() ?>application/views/administrator/assets/bootstrap/js/bootstrap.js"></script>

	<script src="<?= base_url() ?>application/views/administrator/assets/js/jquery.js"></script>

	<script>

		setTimeout("$('.form-signin').fadeIn('slow');",400);

		setTimeout("$('.powered').fadeIn('slow');",1000);

    $.post( base_url+"administrator/activation/activation_process", { code: code, expired_date: data.expired_date, paket: data.paket, token: data.token},
       function( data ) {

          if(data.status == 'Success')
          {
              $('#alret_sucsess').modal('show');
          }

    }, "json");

	</script>

</body>

</html>

