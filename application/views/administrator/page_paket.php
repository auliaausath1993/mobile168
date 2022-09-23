<?php 

include "includes/header.php"; 

?>



<!-- Content

================================================== -->

<div id="page-wrapper">



    <div class="container-fluid">



        <!-- Page Heading -->

        <div class="row">

            <div class="col-lg-12">

                <h1 class="page-header">

                    Informasi <small>Paket Anda</small>

                </h1>

                <ol class="breadcrumb">

                    <li class="active">

                        <i class="fa fa-user"></i> Paket Anda

                    </li>

                </ol>

                  <div id="alret_sucsess" class="alert alert-success" role="alert" hidden>
                    <strong>Activation Sucsess : </strong>Terimakasih anda telah berhasil melakukan aktivasi.
                  </div>

            </div>

   
        	<div class="col-sm-4">
        		<div class="paket-wrapper">
                    <div class="paket">
                        <span>Nama Online Shop</span>
                        <span>: <?= $name_toko ?> </span>
                    </div>
        			<div class="paket">
        				<span>Domain Anda</span>
        				<span>: <?= $domain ?> </span>
        			</div>
        			<div class="paket">
        				<span>Tanggal Aktivasi</span>
        				<span >: <?= date("d M Y", strtotime($paket['activation_date'])) ?>  </span>
        			</div>
        			<div class="paket">
        				<span>Tanggal Berakhir</span>
        				<span>: <?= date("d M Y", strtotime($expired_date)) ?> </span>
        			</div>
        		</div>
        	</div>
            <div class="col-sm-4">
                <div class="paket-wrapper">
                    <div class="paket">
                        <span>Paket Anda</span>
                        <span >: <?= $package ?> </span>
                    </div>
                    <div class="paket">
                        <span>Maksimal Produk</span>
                        <span>: <?= $total_max_product ?> </span>
                    </div>
                    <div class="paket">
                        <span>Produk ter-publish</span>
                        <span >: <?=$total_publish_product?>  </span>
                    </div>
                    <div class="paket">
                        <span>Sisa Kuota Produk</span>
                        <span>: <?= $total_available_space_product ?> </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="paket-wrapper">
                    <div class="paket">
                        <span>Maksimal Pelanggan </span>
                        <span>: <?= $total_max_customer ?> </span>
                    </div>
                    <div class="paket">
                        <span>Jumlah Pelanggan </span>
                        <span >: <?= $total_customer ?>  </span>
                    </div>
                    <div class="paket">
                        <span>Sisa Kuota Pelanggan</span>
                        <span>: <?= $total_available_space_customer ?> </span>
                    </div>
                    <div class="paket">
                        <span></span>
                        <span ></span>
                    </div>
                </div>
            </div>
        	<div class="clearfix"></div>
            
        </div>

        <?php if ($this->tokomobile_white_label == "No") { ?>

            <div class="row">
                <div class='upgrade-wrapper col-md-12' style="margin-top:35px;">
                    <form method="post" action="" id="form_upgrade_paket" class="form-paket">
                        <h4>Upgrade Paket</h4>
                        <h5>Masukan kode aktivasi disini</h5>
                        <div class="col-sm-10">
                            <div class="code-wrapper">
                                <input type="text" class="input-code" name="code" id="code" placeholder="XXXXXXXXXXXXXXXXXXXX" maxlength="20">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="button-wrapper">
                                
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="bg-warning alert">
                            <span> Untuk mendapatkan blok code upgrade lisence. Silahkan hubungi contact sales kami. Terima kasih  </span>
                        </div>
                    </form>

                </div>
            </div>

        <?php } ?>

        <!-- /.row -->
	</div>
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
  <?php $token = $this->config->item('tokomobile_token'); ?>

<script type="text/javascript">

    $(document).ready(function () {

        $('#code').on('keyup',function(){

            var maxlength = $(this).attr('maxlength');
            var val = $(this).val();
             var styles = {
                backgroundColor : "#28a983",
                border: "1px solid #28a983"
                };

            var styles2 = {
                backgroundColor : "#d9534f",
                border: "1px solid #d9534f"
                };

              if (val.length == maxlength) {
                $(this).css(styles);
                $('.button-wrapper').html('<button class=\"btn btn-lg btn-warning\" type=\"submit\" id=\"submit_upgrade_code\">Upgrade Paket</button>');
              }else{
                $(this).css(styles2);
                $('.button-wrapper').html('<button class=\"btn btn-lg btn-warning\" type=\"submit\" id=\"submit_upgrade_code\" disabled style=\"display:none;\">Upgrade Paket</button>');
                return false;
              };
        }); 

        $("#form_upgrade_paket").submit(function(){

            $('#myloading').modal('show');

            var base_url = "<?=base_url()?>";
            var domain = "<?= $domain ?>";
            var token = "<?= $token ?>";    

            var code_1 = $("#code").val();

            var code = code_1;

            $.post( "http://tokomobile.co.id/gen/activation", { code: code,domain: domain,token: token},
            function( data ) {

                   if(data.status == 'Success')
                   {
                        $.post( base_url+"administrator/activation/activation_process", { code: code, expired_date: data.expired_date, paket: data.paket, token: data.token},
                         function( data ) {

                            if(data.status == 'Success')
                            {
                                $('#myloading').modal('hide');
                                window.location = base_url+"administrator/main/info_paket";
                                $('#alret_sucsess').show();
                            }  

                        }, "json");   
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

<?php include "includes/footer.php"; ?>