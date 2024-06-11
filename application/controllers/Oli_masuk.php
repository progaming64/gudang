<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Oli_masuk extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Oli_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Oli Masuk";

        $id_user = $this->session->userdata('id_user');
        $currentRole = $this->Oli_model->get_user_role_by_id($id_user);

        $data['is_admin_or_finance'] = ($currentRole == 'admin' || $currentRole == 'finance');

        $data['currentRole'] = $currentRole;

        $start_date = $this->input->get('start_date') ?? null;
        $end_date = $this->input->get('end_date') ?? null;

        $data['oli_masuk'] = $this->Oli_model->getOliMasuk(null, null, $start_date, $end_date);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dashboard/transaksi/oli/oli_masuk/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function filter()
    {
        // Metode ini akan menangani permintaan AJAX untuk filter tanggal
        // Anda dapat menggunakan kode yang sama dengan di atas untuk memfilter data
        // dan mengembalikan data yang difilter dalam format JSON
        $dateRange = $this->input->get('date_range');
        if (!empty($dateRange)) {
            list($start_date, $end_date) = explode(' - ', $dateRange);
            $range = [
                'mulai' => date('Y-m-d', strtotime($start_date)),
                'akhir' => date('Y-m-d', strtotime($end_date)),
            ];
            $filteredData = $this->Oli_model->getOliMasuk(null, null, $range);
        } else {
            // Jika tidak ada filter, kembalikan semua data
            $filteredData = $this->Oli_model->getOliMasuk();
        }

        // Kembalikan data dalam format JSON
        $jsonData = json_encode($filteredData);

        // Atur header Content-Type
        header('Content-Type: application/json');

        // Mengirimkan respons JSON
        echo $jsonData;
    }

    private function _validasi()
    {
        $this->form_validation->set_rules('tanggal_masuk', 'Tanggal Masuk', 'required|trim');
        // $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('oli_id', 'Oli', 'required');
        $this->form_validation->set_rules('jumlah_masuk', 'Jumlah Masuk', 'required|trim|numeric|greater_than[0]');
    }

    public function tambah()
    {
        error_reporting(0);
        $data['title'] = "Oli Masuk";

        $this->_validasi();
        if ($this->form_validation->run() == false) {
            // $data['supplier'] = $this->Aki_model->get('supplier');
            $data['oli'] = $this->Oli_model->get('oli');

            // Mendapatkan dan men-generate kode transaksi oli masuk
            $kode = 'T-OM-' . date('ymd');
            $kode_terakhir = $this->Oli_model->getMax('oli_masuk', 'id_oli_masuk', $kode);

            if ($kode_terakhir === null) {
                $kode_tambah = 1;
            } else {
                $kode_tambah = (int) substr($kode_terakhir, -5, 5);
                $kode_tambah++;
            }

            $number = str_pad($kode_tambah, 5, '0', STR_PAD_LEFT);
            $data['id_oli_masuk'] = $kode . $number;

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('dashboard/transaksi/oli/oli_masuk/tambah', $data);
            $this->load->view('templates/footer', $data);
        } else {
            $input = $this->input->post(null, true);

            // Fetch the selected oli's price from the database
            $oli = $this->Oli_model->get('oli', ['id_oli' => $input['oli_id']]);
            if ($oli) {
                $total_harga = $oli['harga'] * $input['jumlah_masuk'];
                $input['total_harga'] = $total_harga;

                $insert = $this->Oli_model->insert('oli_masuk', $input);

                if ($insert) {
                    $this->session->set_flashdata('flash', 'Data berhasil di tambahkan!');
                    redirect('oli_masuk');
                } else {
                    $this->session->set_flashdata('error', 'Opps ada kesalahan!');
                    redirect('oli_masuk/tambah');
                }
            } else {
                $this->session->set_flashdata('error', 'oli tidak ditemukan!');
                redirect('oli_masuk/tambah');
            }
        }
    }

    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->Oli_model->delete('oli_masuk', 'id_oli_masuk', $id)) {
            $this->session->set_flashdata('flash', 'Data berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Data gagal dihapus.', false);
        }
        redirect('oli_masuk');
    }
}
