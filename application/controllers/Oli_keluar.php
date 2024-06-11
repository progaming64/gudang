<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Oli_keluar extends CI_Controller
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
		$data['title'] = "Oli keluar";

		// Cek peran pengguna saat ini
		$id_user = $this->session->userdata('id_user');
		$currentRole = $this->Oli_model->get_user_role_by_id($id_user);

		$data['is_admin_or_finance'] = ($currentRole == 'admin' || $currentRole == 'finance');

		$data['currentRole'] = $currentRole; // Tambahkan ini

		$start_date = $this->input->get('start_date') ?? null;
		$end_date = $this->input->get('end_date') ?? null;

		$data['oli_keluar'] = $this->Oli_model->getOlikeluar(null, null, $start_date, $end_date);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('dashboard/transaksi/oli/oli_keluar/index', $data);
		$this->load->view('templates/footer', $data);
	}

	private function _validasi()
	{
		$this->form_validation->set_rules('id_oli_masuk', 'Oli', 'required');
		$this->form_validation->set_rules('tanggal_keluar', 'Tanggal Keluar', 'required|trim');

		$id_oli_masuk = $this->input->post('id_oli_masuk', true);
		//CEK STOK OLI
		$cek_stok = $this->Oli_model->cekStokOli($id_oli_masuk);
		$stok_oli = $cek_stok->stok;
		$valid_stok = $stok_oli + 1;

		$this->form_validation->set_rules(
			'jumlah_keluar',
			'Jumlah Keluar',
			"required|trim|numeric|greater_than[0]|less_than[$valid_stok]",
			[
				'less_than' => "Jumlah Keluar tidak boleh lebih dari {$stok_oli}"
			]
		);
	}

	public function tambah()
	{
		// Debug input data
		error_log(print_r($this->input->post(), true));

		$data['title'] = "Oli Keluar";
		error_reporting(0);

		$this->_validasi();
		if ($this->form_validation->run() == false) {
			// Mengambil data oli dengan stok lebih dari 0
			$data['oli'] = $this->Oli_model->listOliMasuk();
			$data['armada'] = $this->Oli_model->get('armada');

			// Mendapatkan dan men-generate kode transaksi barang keluar
			$kode = 'T-OK-' . date('ymd');
			$kode_terakhir = $this->Oli_model->getMax('oli_keluar', 'id_oli_keluar', $kode);

			if ($kode_terakhir !== null) {
				$kode_tambah = substr($kode_terakhir, -5, 5);
				$kode_tambah++;
			} else {
				$kode_tambah = 1;
			}

			$number = str_pad($kode_tambah, 5, '0', STR_PAD_LEFT);
			$data['id_oli_keluar'] = $kode . $number;

			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('dashboard/transaksi/oli/oli_keluar/tambah', $data);
			$this->load->view('templates/footer', $data);
		} else {
			// Ambil data input
			$store_data = [
				'id_oli_keluar' => $this->input->post('id_oli_keluar'),
				'oli_masuk_id' => $this->input->post('id_oli_masuk'),
				'user_id' => $this->input->post('user_id'),
				// GA PERLU OLI_ID
				'oli_id' => $this->input->post('id_oli'),
				'id_armada' => $this->input->post('id_armada'),
				'jumlah_keluar' => $this->input->post('jumlah_keluar'),
				'tanggal_keluar' => $this->input->post('tanggal_keluar'),
			];

			// Insert data ke dalam tabel oli_keluar
			$insert = $this->Oli_model->insert('oli_keluar', $store_data);

			if ($insert) {
				// UPDATE STOK OLI 
				// $id_oli = $this->input->post('id_oli');

				// $cek_stok = $this->Oli_model->cekStok($this->input->post('id_oli_masuk'));
				// $update_stok = $cek_stok->stok - $this->input->post('jumlah_keluar');

				// $this->Oli_model->update('oli', 'id_oli', $id_oli, ['stok' => $update_stok]);

				$this->session->set_flashdata('flash', 'Oli berhasil ditambahkan!');
				redirect('oli_keluar');
			} else {
				$this->session->set_flashdata('error', 'Oops, ada kesalahan!');
				redirect('oli_keluar/tambah');
			}
		}
	}


	public function delete($getId)
	{
		// Cek peran pengguna saat ini
		$id_user = $this->session->userdata('id_user');
		$currentRole = $this->Oli_model->get_user_role_by_id($id_user);

		if ($currentRole !== 'gudang') {
			$id = encode_php_tags($getId);
			if ($this->Oli_model->delete('oli_keluar', 'id_oli_keluar', $id)) {
				$this->session->set_flashdata('flash', 'Data berhasil dihapus.');
			} else {
				$this->session->set_flashdata('error', 'Data gagal dihapus.', false);
			}
		} else {
			$this->session->set_flashdata('error', 'Anda tidak memiliki izin untuk menghapus data.', false);
		}
		redirect('oli_keluar');
	}
}