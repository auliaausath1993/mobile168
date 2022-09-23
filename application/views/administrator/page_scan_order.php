<?php include "includes/header.php"; ?>
<div id="page-wrapper" class="app">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Scan Pesanan</h1>
				<ol class="breadcrumb">
					<li class="active">
						<i class="fa fa-fw fa-edit"></i> Scan Pesanan
					</li>
				</ol>
			</div>
		</div>
		<!-- /.row -->
		<div class="row">
			<div class="col-lg-12 panel">
				<div class="panel-body">
					<div v-html="message"></div>
					<form @submit.prevent="scanOrder" class="form-horizontal">
						<div class="form-group">
							<label class="col-lg-3 control-label">Masukan / Scan Order ID</label>
							<div class="col-lg-6">
								<div v-show="!loading">
									<input type="number" v-model="order_id" ref="order_id" id="order_id" class="form-control" placeholder="Masukan / Scan Order ID" required>
								</div>
								<div class="text-center" v-show="loading" style="display: none;">
									<i class="fa fa-spin fa-spinner fa-5x"></i>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modal-confirm-payment" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<form class="modal-content" @submit.prevent="submitPelunasan">
				<div class="modal-header">
					<h4 class="modal-title">Konfirmasi Pelunasan</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>ID Pesanan : {{ order_id }}</label>
					</div>
					<div class="form-group">
						<label>Customer : {{ customer_name + ' (' + customer_id + ')' }}</label>
					</div>
					<div class="form-group">
						<label>Total Belanja : {{ 'Rp. ' + order_total }}</label>
					</div>
					<div class="form-group">
						<label>Methode Pembayaran</label>
						<select v-model="paymentMethod" class="form-control" required>
							<?php foreach ($payment_method as $method) { ?>
							<option value="<?= $method->id ?>"><?= $method->name ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label>Diskon</label>
						<input type="number" min="0" v-model="diskon" class="form-control">
					</div>
				</div>
				<div class="modal-footer" v-if="!loadingModal">
					<input type="button" value="Batal" class="btn btn-default" data-dismiss="modal">
					<input type="submit" value="Konfirmasi Pelunasan" class="btn btn-primary">
				</div>
				<div class="modal-footer text-center" v-if="loadingModal">
					<i class="fa fa-spin fa-spinner fa-5x"></i>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
	var vm = new Vue({
		el: '.app',
		data: {
			message: '',
			loading: false,
			order_id: '',
			customer_id: '',
			customer_name: '',
			order_total: 0,
			loadingModal: false,
			paymentMethod: '',
			diskon: 0
		},
		mounted: function() {
			this.focusOrderId();
		},
		methods: {
			focusOrderId: function() {
				this.$refs.order_id.focus();
			},
			scanOrder: function() {
				vm.loading = true;
				var params = new URLSearchParams();
				params.append('order_id', vm.order_id);
				axios.post(base_url + 'administrator/main/scan_check_order', params).then(function(response) {
					vm.loading = false;
					var data = response.data;
					if (data.status == 'Success') {
						$('#modal-confirm-payment').modal('show');
						vm.order_id = data.order_id;
						vm.customer_id = data.customer_id;
						vm.customer_name = data.customer_name;
						vm.order_total = data.order_total;
						vm.paymentMethod = '';
						vm.diskon = 0;
					} else {
						vm.message = data.message;
						vm.order_id = ''
						setTimeout(function() {
							vm.message = '';
						}, 5000);
						vm.focusOrderId();
					}
				}).catch(function(error) {
					vm.loading = false;
					vm.focusOrderId();
					console.log(error.response);
				});
			},
			submitPelunasan: function() {
				vm.loadingModal = true;
				var params = new URLSearchParams();
				params.append('order_id', vm.order_id);
				params.append('paymentMethod', vm.paymentMethod);
				params.append('diskon', vm.diskon);
				axios.post(base_url + 'administrator/main/scan_order_proccess', params).then(function(response) {
					vm.loadingModal = false;
					$('#modal-confirm-payment').modal('hide');
					vm.message = response.data.message;
					setTimeout(function() {
						vm.message = '';
					}, 5000);
					vm.order_id = '';
					vm.focusOrderId();
				}).catch(function(error) {
					vm.loadingModal = false;
					vm.focusOrderId();
					console.log(error.response);
				});
			}
		}
	});
</script>
<?php include 'includes/footer.php'; ?>
