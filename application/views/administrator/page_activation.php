<!DOCTYPE html>

<html lang="en"><head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <title>Activation Code</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="utf-8">

	

    <link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/css/bootstrap.css" media="screen">

	<link rel="stylesheet" href="<?= base_url() ?>application/views/administrator/assets/css/activation.css" media="all">





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


  <?php 
    $data_version = $this->main_model->get_list('data_version_update',array('perpage' => 1,'offset' => 0),array('by' => 'id','sorting' => 'DESC'));
    $data_array_version_last = $data_version->row_array();
    $name_last_vertion = $data_array_version_last['name_version'];
  ?>

  
  <body>



    <div class="container">

	<br/><br/><br/><br/>

    <form method="post" action="" id="form_submit_code" class="form-activation">

        

		<h5>Input Your Activation Code</h5>
        <div class="code-wrapper">
            <input type="text" class="input-code" name="code" id="code" placeholder="XXXXXXXXXXXXXXXXXXXX" maxlength="20">
        </div>

        <div class="button-wrapper">
            
        </div>

     <?=form_close()?>



	<center>

	<p class="powered">Powered by <b><?= $this->config->item('tokomobile_online_shop') ?> </b>Ver. <?php echo $name_last_vertion; ?><br/>

	Aplikasi Smartphone Online Shop

	</p>

	

	</center>

    </div>



	<div class="bottom-navbar"><strong><?= $this->config->item('tokomobile_online_shop') ?></strong>

	</div>

  <div class="modal fade" id="myloading" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(40, 169, 131); color: white;">
                <h4 class="modal-title" id="myModalLabel">PROSES UPDATES JANGAN ANDA REFRESH</h4>
            </div>

            <div class="modal-body">
                <p class="text-center">Please Wait.... </p>
                <div class="bubbles">
                  <span></span>
                  <span id="bubble2"></span>
                  <span id="bubble3"></span>
                </div>
            </div>                    
        </div>
    </div>
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

    <?php $domain = $this->config->item('tokomobile_domain'); ?>
    <?php $token = $this->config->item('tokomobile_token'); ?>

    <script src="<?= base_url() ?>application/views/administrator/assets/js/jquery.js"></script>

    <script src="<?= base_url() ?>application/views/administrator/assets/bootstrap/js/bootstrap.js"></script>

	<script>

		setTimeout("$('.form-activation').fadeIn('slow');",400);

		setTimeout("$('.powered').fadeIn('slow');",1000);

    $(document).ready(function () {

        $('#code').on('keyup',function(){

            var maxlength = $(this).attr('maxlength');
            var val = $(this).val();
             var styles = {
                backgroundColor : "#28a983",
                border: "1px solid #28a983",
                color: "#fff"
                };

            var styles2 = {
                backgroundColor : "#d9534f",
                border: "1px solid #d9534f",
                color: "#fff"
                };

              if (val.length == maxlength) {
                $(this).css(styles);
                $('.button-wrapper').html('<button class=\"btn btn-lg btn-warning\" type=\"submit\" id=\"submit_activation_code\">Activation</button>');
                
              }else{

                $(this).css(styles2);
                $('.button-wrapper').html('<button class=\"btn btn-lg btn-warning\" type=\"submit\" id=\"submit_activation_code\" disabled style=\"display:none;\">Activation</button>');
                return false;
              };
        });

        $("#form_submit_code").submit(function(){


          var base_url = "<?=base_url()?>";
          var domain = "<?= $domain ?>"; 
          var token = "<?= $token ?>";   

          var code_1 = $("#code").val();

          var code = code_1;

          $('#myloading').modal('show');

          $.post( "http://tokomobile.co.id/gen/activation", { code: code,domain: domain,token: token},
          function( data ) {
      

                 if(data.status == 'Success')
                 {
                      $.post( base_url+"administrator/activation/activation_process", { code: code, expired_date: data.expired_date, paket: data.paket, token: data.token},
                       function( data ) {

                          if(data.status == 'Success')
                          {

                              window.location = base_url+"administrator/main";
                              $('#Sucsess').modal('show');
                          }  

                      }, "json"); 

                      $('#myloading').modal('hide');  
                 }
                 else
                 {
                      $('#myloading').modal('hide');

                      alert("Code is Invalid");
                 } 
          
          }, "json");

          return false;

      });    
    });

 

       
	</script>


</body>

</html>

