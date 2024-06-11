<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-bottom-primary">
            <div class="card-header bg-white py-3">
                <div class="row">
                    <div class="col">
                        <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                            Form Input Oli Masuk
                        </h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <?= form_open('', [], ['id_oli_masuk' => $id_oli_masuk, 'user_id' => $this->session->userdata('login_session')['user']]); ?>
                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="id_oli_masuk">Kode Transaksi Masuk</label>
                    <div class="col-md-4">
                        <input value="<?= $id_oli_masuk; ?>" type="text" readonly="readonly" class="form-control">
                        <?= form_error('id_oli_masuk', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="tanggal_masuk">Tanggal Masuk</label>
                    <div class="col-md-4">
                        <?php
                        // Menggunakan format "Y-m-d" untuk tanggal hari ini
                        $tanggal_masuk_value = set_value('tanggal_masuk', date('Y-m-d'));
                        ?>
                        <input value="<?= $tanggal_masuk_value; ?>" name="tanggal_masuk" id="tanggal_masuk" type="date"
                            class="form-control">
                        <?= form_error('tanggal_masuk', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="oli_id">Nama Oli</label>
                    <div class="col-md-5">
                        <div class="input-group">
                            <select name="oli_id" id="oli_id" class="custom-select select2">
                                <option value="" selected disabled>Pilih Nama</option>
                                <?php foreach ($oli as $oli) : ?>
                                <option <?= $this->uri->segment(3) == $oli['id_oli'] ? 'selected' : '';  ?>
                                    <?= set_select('oli_id', $oli['id_oli']) ?> value="<?= $oli['id_oli'] ?>">
                                    <?= $oli['id_oli'] . ' | ' . $oli['nama_oli'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <a class="btn btn-primary" href="<?= base_url('oli/tambah'); ?>"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <?= form_error('oli_id', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="harga">Harga</label>
                    <div class="col-md-5">
                        <input id="harga" type="number" name="harga" class="form-control" readonly>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="supplier">Supplier</label>
                    <div class="col-md-5">
                        <input readonly id="supplier_oli" type="text" class="form-control">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="stok">Stok Saat Ini</label>
                    <div class="col-md-5">
                        <div class="input-group">
                            <input readonly id="stok" type="number" class="form-control">
                            <div class="input-group-append">
                                <span class="input-group-text" id="satuan">Liter</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="jumlah_masuk">Jumlah Liter</label>
                    <div class="col-md-5">
                        <div class="input-group">
                            <input value="<?= set_value('jumlah_masuk'); ?>" name="jumlah_masuk" id="jumlah_masuk"
                                type="number" class="form-control" placeholder="Jumlah Masuk...">
                            <div class="input-group-append">
                                <span class="input-group-text" id="satuan">Liter</span>
                            </div>
                        </div>
                        <?= form_error('jumlah_masuk', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col offset-md-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM="
    crossorigin="anonymous"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
<?php
    if ($this->session->flashdata('flash')) { ?>
var isi = <?php echo json_encode($this->session->flashdata('flash')) ?>;
Swal.fire({
    title: "Selamat",
    text: "<?= $this->session->flashdata('flash') ?>",
    icon: "success",
    button: false,
    timer: 5000,
});
<?php
        unset($_SESSION['flash']);
    } ?>

<?php
    if ($this->session->flashdata('error')) { ?>
var isi = <?php echo json_encode($this->session->flashdata('error')) ?>;
Swal.fire({
    title: "Gagal",
    text: "<?= $this->session->flashdata('error') ?>",
    icon: "error",
    button: false,
    timer: 5000,
});
<?php
        unset($_SESSION['error']);
    } ?>
</script>