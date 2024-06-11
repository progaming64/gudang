<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-bottom-primary">
            <div class="card-header bg-white py-3">
                <div class="row">
                    <div class="col">
                        <h4 class="h5 align-middle m-0 font-weight-bold text-primary">
                            Form Input Sparepart Keluar
                        </h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?= form_open('', [], ['id_barang_keluar' => $id_barang_keluar, 'user_id' => $this->session->userdata('login_session')['user']]); ?>
                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="id_barang_keluar">Kode Transaksi Keluar</label>
                    <div class="col-md-4">
                        <input value="<?= $id_barang_keluar; ?>" type="text" readonly="readonly" class="form-control">
                        <?= form_error('id_barang_keluar', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="tanggal_keluar">Tanggal Keluar</label>
                    <div class="col-md-4">
                        <?php
                        // Menggunakan format "Y-m-d" untuk tanggal hari ini
                        $tanggal_keluar_value = set_value('tanggal_keluar', date('Y-m-d'));
                        ?>
                        <input value="<?= $tanggal_keluar_value; ?>" name="tanggal_keluar" id="tanggal_keluar"
                            type="date" class="form-control" placeholder="Tanggal Masuk...">
                        <?= form_error('tanggal_keluar', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="id_barang_masuk">Nama Sparepart</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <select name="id_barang_masuk" id="id_barang_masuk" class="custom-select select2">
                                <option value="" selected disabled>Pilih Nama</option>
                                <?php
                                $nama_barang_terlihat = [];
                                foreach ($sparepart as $o) :
                                    if (!in_array($o->nama_barang, $nama_barang_terlihat)) :
                                        $nama_barang_terlihat[] = $o->nama_barang;
                                ?>
                                <option value="<?= $o->id_barang_masuk; ?>" data-id="<?= $o->barang_id; ?>">
                                    <?= $o->nama_barang; ?> | <?php
                                     $date = new DateTime($o->tanggal_masuk);
                                    echo $date->format('d-m-Y'); ?>
                                </option>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <?= form_error('id_barang_masuk', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <!-- ID BARANG  -->
                <input type="hidden" name="id_barang" id="id_barang" value="">

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="supplier">Supplier</label>
                    <div class="col-md-4">
                        <input readonly id="supplier" type="text" class="form-control">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="harga">Harga</label>
                    <div class="col-md-4">
                        <input readonly id="harga" type="number" class="form-control">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="stok">Stok</label>
                    <div class="col-md-4">
                        <input readonly="readonly" id="stok" type="number" class="form-control">
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="jumlah_keluar">Jumlah Keluar</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input value="<?= set_value('jumlah_keluar'); ?>" name="jumlah_keluar" id="jumlah_keluar"
                                type="number" class="form-control" placeholder="Jumlah Keluar...">
                        </div>
                        <?= form_error('jumlah_keluar', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="id_armada">Armada</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <select name="id_armada" id="id_armada" class="custom-select select2">
                                <option value="" selected disabled>Pilih Armada</option>
                                <?php foreach ($armada as $ar) : ?>
                                <option value="<?= $ar['id_armada'] ?>">
                                    <?= $ar['nama_armada'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <a class="btn btn-primary" href="<?= base_url('armada/tambah'); ?>"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <?= form_error('id_armada', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="id_supir">Supir</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <select name="id_supir" id="id_supir" class="custom-select select2">
                                <option value="" selected disabled>Pilih Supir</option>
                                <?php foreach ($supir as $sp) : ?>
                                <option value="<?= $sp['id_supir'] ?>">
                                    <?= $sp['nama_supir'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <a class="btn btn-primary" href="<?= base_url('supir/tambah'); ?>"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <?= form_error('id_supir', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-md-4 text-md-right" for="id_montir">Montir</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <select name="id_montir" id="id_montir" class="custom-select select2">
                                <option value="" selected disabled>Pilih Montir</option>
                                <?php foreach ($montir as $mt) : ?>
                                <option value="<?= $mt['id_montir'] ?>">
                                    <?= $mt['nama_montir'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <a class="btn btn-primary" href="<?= base_url('montir/tambah'); ?>"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <?= form_error('id_montir', '<small class="text-danger">', '</small>'); ?>
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

<script>
$(document).on('change', '#id_barang_masuk', function() {
    // Ambil nilai dari atribut data-id
    let selectedOption = $(this).find('option:selected');
    let dataId = selectedOption.data('id');

    // Ambil nilai dari input dengan id 'id_barang'
    $('#id_barang').val(dataId);

    let url = '<?= base_url('barang/getstoksparepart/'); ?>' + this.value;
    $.getJSON(url, function(data) {
        supplier_barang.val(data.nama_supplier);
        supplier_barang_keluar.val(data.nama_supplier);
        stok.val(data.stok);
        harga.val(data.harga);
        total.val(data.stok);
    });
});
</script>