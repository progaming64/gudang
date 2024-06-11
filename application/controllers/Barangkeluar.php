<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barangkeluar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Barang keluar";

        // Cek peran pengguna saat ini
        $id_user = $this->session->userdata('id_user');
        $currentRole = $this->Admin_model->get_user_role_by_id($id_user);

        $data['is_admin_or_finance'] = ($currentRole == 'admin' || $currentRole == 'finance');

        $start_date = $this->input->get('start_date') ?? null;
        $end_date = $this->input->get('end_date') ?? null;

        $data['barangkeluar'] = $this->Admin_model->getBarangkeluar(null, null, $start_date, $end_date);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dashboard/barang_keluar/index', $data);
        $this->load->view('templates/footer', $data);
    }

    // private function _validasi()
    // {
    //     $this->form_validation->set_rules('id_barang_masuk', 'Sparepart', 'required');
    //     $this->form_validation->set_rules('tanggal_keluar', 'Tanggal Keluar', 'required');
    //     $this->form_validation->set_rules('id_armada', 'Armada', 'required');
    //     $this->form_validation->set_rules('id_supir', 'Supir', 'required');
    //     $this->form_validation->set_rules('id_montir', 'Montir', 'required');

    //     $input = $this->input->post('id_barang_masuk', true);

    //     // Pastikan $input memiliki nilai sebelum digunakan
    //     if ($input) {
    //         // CEK STOK sparepart
    //         $cek_stok = $this->Admin_model->cekStokSparepart($input); // Pass the input here
    //         if ($cek_stok) {
    //             $stok_sparepart = $cek_stok->stok;
    //             $stok_valid = $stok_sparepart + 1;

    //             // Pengecekan apakah $input adalah string sebelum menggunakan trim()
    //             if (isset($input) && is_string($input)) {
    //                 $result = $this->Admin_model->get('barang', ['id_barang' => $input]);

    //                 if ($result !== null && isset($result['stok'])) {
    //                     $stok = $result['stok'];
    //                     $stok_valid = $stok + 1;

    //                     $this->form_validation->set_rules(
    //                         'jumlah_keluar',
    //                         'Jumlah Keluar',
    //                         "required|trim|numeric|greater_than[0]|less_than[{$stok_valid}]",
    //                         [
    //                             'less_than' => "Jumlah Keluar tidak boleh lebih dari {$stok}"
    //                         ]
    //                     );
    //                 } else {
    //                     // Tangani kasus di mana $result adalah null atau 'stok' tidak ada dalam $result
    //                     $this->form_validation->set_rules('jumlah_keluar', 'Jumlah Keluar', 'required|trim|numeric|greater_than[0]');
    //                 }
    //             } else {
    //                 // Tangani kasus di mana $input bukan string
    //                 $this->form_validation->set_rules(
    //                     'jumlah_keluar',
    //                     'Jumlah Keluar',
    //                     'required|numeric|greater_than[0]'
    //                 );
    //             }
    //         } else {
    //             // Tangani kasus di mana cek_stok tidak mengembalikan data
    //             $this->form_validation->set_rules('jumlah_keluar', 'Jumlah Keluar', 'required|trim|numeric|greater_than[0]');
    //         }
    //     } else {
    //         // Tangani kasus di mana $input tidak ada nilainya
    //         $this->form_validation->set_rules('jumlah_keluar', 'Jumlah Keluar', 'required|trim|numeric|greater_than[0]');
    //     }
    // }

    private function _validasi()
    {
        $this->form_validation->set_rules('id_barang_masuk', 'barang', 'required');
        $this->form_validation->set_rules('tanggal_keluar', 'Tanggal Keluar', 'required|trim');

        $id_barang_masuk = $this->input->post('id_barang_masuk', true);
        //CEK STOK barang
        $cek_stok = $this->Admin_model->cekStokSparepart($id_barang_masuk);
        $stok_barang = $cek_stok->stok;
        $valid_stok = $stok_barang + 1;

        $this->form_validation->set_rules(
            'jumlah_keluar',
            'Jumlah Keluar',
            "required|trim|numeric|greater_than[0]|less_than[$valid_stok]",
            [
                'less_than' => "Jumlah Keluar tidak boleh lebih dari {$stok_barang}"
            ]
        );
    }

    public function tambah()
    {
        error_log(print_r($this->input->post(), true));

        $data['title'] = "Sparepart Keluar";
        error_reporting(0);

        $this->_validasi();
        if ($this->form_validation->run() == false) {
            // Mengambil data oli dengan stok lebih dari 0
            $data['sparepart'] = $this->Admin_model->listSparepartMasuk();
            $data['armada'] = $this->Admin_model->get('armada');
            $data['supir'] = $this->Admin_model->get('supir');
            $data['montir'] = $this->Admin_model->get('montir');

            // Mendapatkan dan men-generate kode transaksi barang keluar
            $kode = 'T-SK-' . date('ymd');
            $kode_terakhir = $this->Admin_model->getMax('barang_keluar', 'id_barang_keluar', $kode);

            if ($kode_terakhir !== null) {
                $kode_tambah = substr($kode_terakhir, -5, 5);
                $kode_tambah++;
            } else {
                $kode_tambah = 1;
            }

            $number = str_pad($kode_tambah, 5, '0', STR_PAD_LEFT);
            $data['id_barang_keluar'] = $kode . $number;

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('dashboard/barang_keluar/tambah', $data);
            $this->load->view('templates/footer', $data);
        } else {
            // Ambil data input
            $store_data = [
                'id_barang_keluar' => $this->input->post('id_barang_keluar'),
                'barang_masuk_id' => $this->input->post('id_barang_masuk'),
                'user_id' => $this->input->post('user_id'),
                // GA PERLU barang_ID
                'barang_id' => $this->input->post('id_barang'),
                'id_armada' => $this->input->post('id_armada'),
                'id_supir' => $this->input->post('id_supir'),
                'id_montir' => $this->input->post('id_montir'),
                'jumlah_keluar' => $this->input->post('jumlah_keluar'),
                'tanggal_keluar' => $this->input->post('tanggal_keluar'),
            ];
            $insert = $this->Admin_model->insert('barang_keluar', $store_data);

            if ($insert) {
                $this->session->set_flashdata('flash', 'Sparepart berhasil di tambahkan!');
                redirect('Barangkeluar');
            } else {
                $this->session->set_flashdata('error', 'Opps ada kesalahan!');
                redirect('Barangkeluar/tambah');
            }
        }
    }

    public function delete($getId)
    {
        // Cek peran pengguna saat ini
        $id_user = $this->session->userdata('id_user');
        $currentRole = $this->Admin_model->get_user_role_by_id($id_user);

        if ($currentRole !== 'gudang') {
            $id = encode_php_tags($getId);
            if ($this->Admin_model->delete('barang_keluar', 'id_barang_keluar', $id)) {
                $this->session->set_flashdata('flash', 'Data berhasil dihapus.');
            } else {
                $this->session->set_flashdata('error', 'Data gagal dihapus.', false);
            }
        } else {
            $this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus data.', false);
        }
        redirect('Barangkeluar');
    }
}