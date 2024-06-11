<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Oli extends CI_Controller
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
		$data['title'] = "Oli";

		// Cek peran pengguna saat ini
		$id_user = $this->session->userdata('id_user');
		$currentRole = $this->Oli_model->get_user_role_by_id($id_user);

		$data['is_admin_or_finance'] = ($currentRole == 'admin' || $currentRole == 'finance');

		$data['currentRole'] = $currentRole; // Tambahkan ini

		$data['oli'] = $this->Oli_model->getOli();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('dashboard/oli/index', $data);
		$this->load->view('templates/footer', $data);
	}

	public function tambah()
	{
		error_reporting(0);
		$data['title'] = 'Form Tambah Data Oli';

		$this->form_validation->set_rules('nama_oli', 'Jenis Oli', 'required');
		$this->form_validation->set_rules('harga', 'Harga Oli', 'required');

		if ($this->form_validation->run() == FALSE) {
			$data['supplier'] = $this->Oli_model->get('supplier');

			// Mengenerate ID Barang
			$kode_terakhir = $this->Oli_model->getMax('oli', 'id_oli');
			$kode_tambah = substr($kode_terakhir, -6, 6);
			$kode_tambah++;
			$number = str_pad($kode_tambah, 6, '0', STR_PAD_LEFT);
			$data['id_oli'] = 'O' . $number;

			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('dashboard/oli/tambah', $data);
			$this->load->view('templates/footer', $data);
		} else {
			$input = $this->input->post(null, true);

			$insert = $this->Oli_model->insert('oli', $input);

			if ($insert) {
				$this->session->set_flashdata('flash', 'Master oli berhasil di tambahkan!');
				echo '<script>window.history.go(-2);</script>';
			} else {
				$this->session->set_flashdata('flash', 'Master oli gagal di tambahkan!');
				redirect('oli/tambah');
			}
		}
	}

	public function edit($getId)
	{
		$data['title'] = 'Form Edit Data oli';

		$id = encode_php_tags($getId);

		$this->form_validation->set_rules('nama_oli', 'Jenis oli', 'required');
		$this->form_validation->set_rules('harga', 'Harga oli', 'required');

		if ($this->form_validation->run() == FALSE) {
			$data['oli'] = $this->Oli_model->get('oli', ['id_oli' => $id]);
			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('dashboard/oli/edit', $data);
			$this->load->view('templates/footer', $data);
		} else {
			$input = $this->input->post(null, true);
			$update = $this->Oli_model->update('oli', 'id_oli', $id, $input);

			if ($update) {
				$this->session->set_flashdata('flash', 'Data oli berhasil di edit!');
				redirect('oli');
			} else {
				$this->session->set_flashdata('flash', 'Data oli gagal di edit!');
				redirect('oli/edit/' . $id);
			}
		}
	}

	public function delete($id_oli)
	{
		$this->Oli_model->hapusDataOli($id_oli);
		$this->session->set_flashdata('flash', 'Data oli berhasil di hapus!');
		redirect('oli');
	}

	public function getstok($getId)
	{
		$id_oli_masuk = encode_php_tags($getId);
		$query = $this->Oli_model->cekStok($id_oli_masuk);
		output_json($query);
	}

	public function getstokoli($getId)
	{
		$id_oli_masuk = encode_php_tags($getId);
		$query = $this->Oli_model->cekStokOli($id_oli_masuk);
		output_json($query);
	}
}