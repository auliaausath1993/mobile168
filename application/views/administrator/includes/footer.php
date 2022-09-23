  <?php

    $data_version = $this->main_model->get_list('data_version_update',array('perpage' => 1,'offset' => 0),array('by' => 'id','sorting' => 'DESC'));

    $data_array_version_last = $data_version->row_array();

    $name_last_vertion = $data_array_version_last['name_version'];

    $name_toko = $this->config->item('tokomobile_online_shop');

    $name_domain = $this->config->item('tokomobile_domain');

  ?>







	 </section>
	 </section>
		<footer id="footer">

			<div class="container-fluid">

               <div class="col-md-6">

               		<div class="links">

			          <small>

					  <b><?=$this->config->item('tokomobile_online_shop'); ?> Ver. <?php echo $name_last_vertion; ?></b><br/>

              <?php if ($this->tokomobile_white_label == "No") { ?>



					    <?php } ?>

					  </small>

					  <br/><br/>

			        </div>

               </div>

               <div class="col-md-6">

               	 <div class="back-top text-right">

               	 	<a href="#top">

                   	 	<i class="fa fa-chevron-circle-up"></i>

                   	 	Back to Top

                   	 </a>

               	 </div>

               </div>

			</div>

		</footer>
		<div class="modal fade" id="modal-pincode" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Masukkan PIN Code</h4>
					</div>
					<div class="form-pincode">
						<div class="modal-body">
							<div class="form-group">
								<label>PIN Code</label>
								<input type="password" class="form-control" id="pincode">
								<input type="hidden" id="var-no">
								<input type="hidden" id="title-pincode">
							</div>
						</div>
						<div class="modal-footer" >
							<input type="button" value="Batal" class="btn btn-default" data-dismiss="modal">
							<input type="submit" value="OK" id="submit-pincode" class="btn btn-primary">
						</div>
					</div>
					<div class="loading-pincode text-center" style="display: none">
						<div class="modal-body">
							<i class="fa fa-spin fa-spinner fa-5x"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade" id="modal-cancel-order" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<form class="modal-content" method="POST">
					<div class="modal-header">
						<h4 class="modal-title">Alasan Pembatalan</h4>
					</div>
					<div class="modal-body">
						<div class="m-b">
							<label for="reason_cancel">Alasan Pembatalan</label>
							<input type="text" name="reason_cancel" id="reason_cancel" class="form-control"required />
							<div id="order_item_id"></div>
						</div>
					</div>
					<div class="modal-footer" >
						<input type="button" value="Batal" class="btn btn-default" data-dismiss="modal">
						<input type="submit" value="OK" class="btn btn-primary">
					</div>
				</form>
			</div>
		</div>
	<script>
		$('.tool_tips').tipper({ 'direction': 'bottom' });
	</script>
   <audio id="audio" src="https://www.soundjay.com/button/sounds/beep-02.mp3" autostart="false" ></audio>
    <!-- /#wrapper -->

	<script src="<?= base_url('application/views/administrator/assets/new-theme/js/jquery.min.js') ?>"></script>
	<!-- Bootstrap -->
	<script src="<?= base_url('application/views/administrator/assets/new-theme/js/bootstrap.js') ?>"></script>
	<!-- app -->
	<script src="<?= base_url('application/views/administrator/assets/new-theme/js/app.js') ?>"></script>
	<script src="<?= base_url('application/views/administrator/assets/new-theme/js/app.plugin.js') ?>"></script>
	<script src="<?= base_url('application/views/administrator/assets/new-theme/js/app.data.js') ?>"></script>


    <script src="<?=base_url()?>application/views/administrator/assets/js/datepicker/js/bootstrap-datepicker.js"></script>

    <!-- Pushy JS -->

    <script src="<?=base_url()?>application/views/administrator/assets/js/pushy.js"></script>



    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jquery-multicomplete.js"></script>

	<script type='text/javascript' src='<?php echo base_url();?>assets/js/jquery.autocomplete.js'></script>

    <!-- Scrollbar JavaScript -->

    <!-- <script src="<?=base_url()?>application/views/administrator/assets/js/jquery.mCustomScrollbar.concat.min.js"></script> -->

    <script src="<?=base_url()?>application/views/administrator/assets/js/jquery.cookie.js"></script>

    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/tableExport.js"></script>

    <script type="text/javascript" src="<?=base_url()?>application/views/administrator/assets/js/jquery.base64.js"></script>

    <!--

    <script src="<?=base_url()?>application/views/administrator/assets/js/jquery.uploadfile.js"></script>

     -->

    <script src="<?=base_url()?>application/views/administrator/assets/js/custome.js"></script>

    <script type="text/javascript">


      jQuery(document).ready(function ($) {


       $('.search-input').multicomplete({

           minimum_length: 1,

           result_template: function(result, group, matched_field) {

                tmpl = '<div>';

                if(!!result.name_product) {

                    tmpl += '<a href="#">' + result.name_product + '</a>';

                }

                if(!!result.name) {

                    tmpl += '<a href="#">' + result.name + '</a>';

                }

                tmpl += '</div>';

                return tmpl;

           },

           source: '<?=base_url()?>application/views/administrator/includes/demo-source.json'

       });

      });

    </script>



    <!-- Morris Charts JavaScript -->

    <!-- <script src="<?=base_url()?>application/views/administrator/assets/js/plugins/morris/raphael.min.js"></script>

    <script src="<?=base_url()?>application/views/administrator/assets/js/plugins/morris/morris.min.js"></script>

    <script src="<?=base_url()?>application/views/administrator/assets/js/plugins/morris/morris-data.js"></script> -->

    <script src="<?=base_url()?>application/views/administrator/assets/js/autosize.min.js?v=1"></script>

    <script type="text/javascript">

    $(".fitur_disable").click(function(){

      alert("Fitur tak dapat diakses saat Demo");

    });

    </script>

<!--- AUTO REFRESH -->

		<script type="text/javascript">

		autosize($('#deskripsi'));

		var notifikasi = localStorage.getItem('notifikasi');
		var sound = document.getElementById('audio');
		setInterval(function () {
			$.get('<?=base_url('administrator/main/getConfirmChat')?>' , function(data) {
				var konfirmasi = localStorage.getItem('total-konfirmasi');
				if (Number(konfirmasi) < Number(data.confirm)) {
					localStorage.setItem('total-konfirmasi', data.confirm);
					if (notifikasi.search('Konfirmasi') >= 0) {
						sound.play();
					}
					var label = '<i class="fa fa-inbox">&nbsp;</i><span>Konfirmasi</span>&nbsp;';
					var count = data.confirm > 0 ? '<span class="label label-danger" id="qty-konfirmasi">' + data.confirm + '</span>' : '';
					$('#confirm').html(label + count);
				}

				var chat = localStorage.getItem('total-chat');
				var chat_product = localStorage.getItem('total-chat-product');

				if (Number(chat) < Number(data.chat)) {
					localStorage.setItem('total-chat', data.chat);
					if (notifikasi.search('Chatting') >= 0) {
						sound.play();
					}
					$('.qty-chat').html(data.chat);
				}

				if (Number(chat_product) < Number(data.chatProduct)) {
					localStorage.setItem('total-chat-product', data.chatProduct);
					if (notifikasi.search('Chatting') >= 0) {
						sound.play();
					}
					$('.qty-chat-product').html(data.chatProduct);
				}
				var total_qty_chat_product = parseInt(data.chat) + parseInt(data.chatProduct);
				$('.qty-chat-product-chat').html(total_qty_chat_product);
			}, 'json');
		}, 120000);

    var konfirmasi = $('#qty-konfirmasi').text();
    localStorage.setItem('total-konfirmasi', konfirmasi);

    var chat = $('.qty-chat').text();
    localStorage.setItem('total-chat', chat);

    var chat_product = $('.qty-chat-product').text();
    localStorage.setItem('total-chat-product', chat_product);

    $('.dropdown-submenu').click(function() {
        $('.dropdown-menu').each(function() {
            if (window.innerWidth > 767) {
                $(this).removeClass('collapse in');
            }
        });
    });

	</script>

	<!--- END AUTO REFRESH -->


</body>



</html>
