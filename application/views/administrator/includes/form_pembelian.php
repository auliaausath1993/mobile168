<form method="post" action="<?= $action ?>" class="form-horizontal form-pembelian">
	<div class="row">
		<div class="col-md-12">
			<div class="form-group">
				<label class="col-md-2" style="margin-top: 10px">Pilih Pembelian</label>
				<div class="col-md-1">
					<div class="radio">
						<label>
							<input type="radio" name="purchase_date_type" class="purchase_date_type" value="date" checked>
							Tanggal
						</label>
					</div>
				</div>
				<div class="col-md-9 form-inline">
					<input type="text" <?= $form_pembelian['purchase_date_type'] == 'month' ? 'disabled' : '' ?> name="purchaseDateFrom" autocomplete="off" class="datepicker form-control purchaseDateFrom col-md-2" data-date-format="yyyy-mm-dd" placeholder="Pilih Tanggal" value="<?= $form_pembelian['purchaseDateFrom'] ?>" required>
					<div class="col-md-1 text-center" style="margin-top: 5px">
						<b>&ndash;</b>
					</div>
					<input type="text" <?= $form_pembelian['purchase_date_type'] == 'month' ? 'disabled' : '' ?> name="purchaseDateTo" autocomplete="off" class="datepicker form-control purchaseDateTo col-md-2" data-date-format="yyyy-mm-dd" placeholder="Pilih Tanggal" value="<?= $form_pembelian['purchaseDateTo'] ?>" required>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2"></label>
				<div class="col-md-1">
					<div class="radio">
						<label>
							<input type="radio" name="purchase_date_type" class="purchase_date_type" value="month" <?= $form_pembelian['purchase_date_type'] == 'month' ? 'checked' : '' ?>>
							Bulan
						</label>
					</div>
				</div>
				<div class="col-md-9 form-inline">
					<input type="text" <?= $form_pembelian['purchase_date_type'] == 'date' ? 'disabled' : '' ?> name="purchase_month" autocomplete="off" class="datepicker form-control purchase_month col-md-2" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" placeholder="Pilih Bulan" value="<?= $form_pembelian['purchase_month'] ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<div class="col-md-5">
				<label for="no_invoice">No. Invoice</label>
			</div>
			<div class="col-md-7 row" style="padding-right: 0">
				<input type="text" name="no_invoice" id="no_invoice" class="form-control" placeholder="No. Invoice" value="<?= $form_pembelian['no_invoice'] ?>">
			</div>
		</div>
		<div class="col-md-7">
			<div class="col-md-3" style="padding: 0">
				<label for="payment_status">Status Pembayaran</label>
			</div>
			<div class="col-md-6">
				<select name="payment_status" id="payment_status" class="form-control">
					<option value="Lunas" <?= $form_pembelian['payment_status'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
					<option value="Belum Lunas" <?= $form_pembelian['payment_status'] == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 10px">
		<div class="col-md-5">
			<div class="col-md-5">
				<label for="supplier">Nama Supplier</label>
			</div>
			<div class="col-md-7 row" style="padding-right: 0">
				<input type="text" name="supplier" id="supplier" class="form-control" placeholder="Nama Supplier" value="<?= $form_pembelian['supplier'] ?>">
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 10px">
		<div class="col-md-5">
			<div class="col-md-5">
				<label for="product">Nama Produk</label>
			</div>
			<div class="col-md-7 row" style="padding-right: 0">
				<input type="text" name="product" id="product" class="form-control" placeholder="Nama Produk" value="<?= $form_pembelian['product'] ?>">
			</div>
		</div>
		<div class="col-md-7">
			<div class="form-group">
				<label class="col-md-3" style="margin-top: 10px; padding: 0">Pilih Pembayaran</label>
				<div class="col-md-2">
					<div class="radio">
						<label>
							<input type="radio" <?= $form_pembelian['payment_date_type'] && $form_pembelian['payment_date_type'] == 'date' ? 'checked' : '' ?> name="payment_date_type" class="payment_date_type" value="date">
							Tanggal
						</label>
					</div>
				</div>
				<div class="col-md-7 form-inline">
					<input type="text" <?= !$form_pembelian['payment_date_type'] || $form_pembelian['payment_date_type'] == 'month' ? 'disabled' : '' ?> name="paymentDateFrom" autocomplete="off" class="datepicker form-control paymentDateFrom col-md-5" data-date-format="yyyy-mm-dd" placeholder="Pilih Tanggal" value="<?= $form_pembelian['paymentDateFrom'] ?>">
					<div class="col-md-2 text-center" style="margin-top: 5px"><b>&ndash;</b></div>
					<input type="text" <?= !$form_pembelian['payment_date_type'] || $form_pembelian['payment_date_type'] == 'month' ? 'disabled' : '' ?> name="paymentDateTo" autocomplete="off" class="datepicker form-control paymentDateTo col-md-5" data-date-format="yyyy-mm-dd" placeholder="Pilih Tanggal" value="<?= $form_pembelian['paymentDateTo'] ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="margin-top: 10px">
		<div class="col-md-5">
			<div class="col-md-5">
				<label for="purchase_status">Status Pembelian</label>
			</div>
			<div class="col-md-7 row" style="padding-right: 0">
				<select name="purchase_status" id="purchase_status" class="form-control">
					<option value="Cash" <?= $form_pembelian['purchase_status'] == 'Cash' ? 'selected' : '' ?>>Cash</option>
					<option value="Kredit" <?= $form_pembelian['purchase_status'] == 'Kredit' ? 'selected' : '' ?>>Kredit</option>
				</select>
			</div>
		</div>
		<div class="col-md-7">
			<div class="form-group">
				<label class="col-md-3" style="margin-top: 10px; padding: 0"></label>
				<div class="col-md-2">
					<div class="radio">
						<label>
							<input type="radio" <?= $form_pembelian['payment_date_type'] && $form_pembelian['payment_date_type'] == 'month' ? 'checked' : '' ?> name="payment_date_type" class="payment_date_type" value="month">
							Bulan
						</label>
					</div>
				</div>
				<div class="col-md-7 form-inline">
					<input type="text" <?= $form_pembelian['payment_date_type'] == 'date' || !$form_pembelian['payment_date_type'] ? 'disabled' : '' ?> name="payment_month" autocomplete="off" class="datepicker form-control payment_month col-md-5" data-date-format="yyyy-mm" data-date-viewmode="years" data-date-minviewmode="months" placeholder="Pilih Bulan" value="<?= $form_pembelian['payment_month'] ?>">
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12" style="margin-top: 20px; margin-bottom: 20px">
		<button type="submit" class="btn btn-primary"><i class="fa fa-fw fa-search"></i> CARI DATA</button>
		<a href="<?= $reset_link ?>" class="btn btn-danger">RESET</a>
	</div>
</form>
