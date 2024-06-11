<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-bottom-primary">
            <div class="card-header bg-white py-3">
                <div class="row">
                    <div class="col">
                        <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                            Form Tambah Oli
                        </h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= form_open('', [], ['stok' => 0]); ?>
                <div class="row form-group">
                    <label class="col-md-3 text-md-right" for="id_oli">ID Oli</label>
                    <div class="col-md-9">
                        <input readonly value="<?= set_value('id_oli', $id_oli); ?>" name="id_oli" id="id_oli"
                            type="text" class="form-control">
                        <?= form_error('id_oli', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-3 text-md-right" for="nama_oli">Nama Oli</label>
                    <div class="col-md-9">
                        <input value="<?= set_value('nama_oli'); ?>" name="nama_oli" id="nama_oli" type="text"
                            class="form-control" placeholder="Nama Oli">
                        <?= form_error('nama_oli', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-3 text-md-right" for="supplier_id">Supplier</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <select name="supplier_id" id="supplier_id" class="custom-select">
                                <option value="" selected disabled>Pilih Supplier</option>
                                <?php foreach ($supplier as $s) : ?>
                                <option <?= set_select('supplier_id', $s['id_supplier']) ?>
                                    value="<?= $s['id_supplier'] ?>"><?= $s['nama_supplier'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <a class="btn btn-primary" href="<?= base_url('supplier/tambah'); ?>"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <?= form_error('supplier_id', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-3 text-md-right" for="nama_aki">Harga</label>
                    <div class="col-md-9">
                        <input value="<?= set_value('harga'); ?>" name="harga" id="harga" type="number"
                            class="form-control" placeholder="Harga Oli">
                        <?= form_error('harga', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-9 offset-md-3">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="reset" class="btn btn-secondary">Reset</bu>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>