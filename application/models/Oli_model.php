<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Oli_model extends CI_Model
{
	public function get($table, $data = null, $where = null)
	{
		if ($data != null) {
			return $this->db->get_where($table, $data)->row_array();
		} else {
			return $this->db->get_where($table, $where)->result_array();
		}
	}

	public function update($table, $pk, $id, $data)
	{
		$this->db->where($pk, $id);
		return $this->db->update($table, $data);
	}

	public function insert($table, $data, $batch = false)
	{
		return $batch ? $this->db->insert_batch($table, $data) : $this->db->insert($table, $data);
	}

	public function delete($table, $pk, $id)
	{
		return $this->db->delete($table, [$pk => $id]);
	}

	public function getOli()
	{
		$this->db->join('supplier', 'oli.supplier_id = supplier.id_supplier');
		$this->db->order_by('id_oli');
		return $this->db->get('oli')->result();
	}

	function listOliMasuk()
	{
		return $this->db->from('oli_masuk')
			->join('oli', 'oli_masuk.oli_id = oli.id_oli')
			->join('supplier', 'oli.supplier_id = supplier.id_supplier')
			->get()->result();
	}

	// public function listOliMasuk()	
	// {
	//     $this->db->select('oli_masuk.id_oli_masuk, oli.nama_oli, oli.stok');
	//     $this->db->from('oli_masuk');
	//     $this->db->join('oli', 'oli.id_oli = oli_masuk.oli_id', 'left');
	//     $this->db->where('oli.stok >', 0);
	//     $query = $this->db->get();
	//     return $query->result();
	// }

	public function getOliMasuk($limit = null, $id_oli = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*'); // Menghapus alias yang digunakan sebelumnya
		$this->db->from('oli_masuk');
		$this->db->join('user', 'oli_masuk.user_id = user.id_user');
		$this->db->join('oli', 'oli_masuk.oli_id = oli.id_oli');
		$this->db->join('supplier', 'oli.supplier_id = supplier.id_supplier');
		// $this->db->join('satuan', 'oli.satuan_id = satuan.id_satuan');
		// $this->db->order_by('id_barang_masuk');

		if ($limit != null) {
			$this->db->limit($limit);
		}

		if ($id_oli != null) {
			$this->db->where('oli_masuk.oli_id', $id_oli);
		}

		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('oli_masuk.tanggal_masuk', $start_date);
			} else {
				$this->db->where('oli_masuk.tanggal_masuk >=', $start_date);
				$this->db->where('oli_masuk.tanggal_masuk <=', $end_date);
			}
		}


		$this->db->order_by('oli_masuk.id_oli_masuk', 'DESC');

		// Hitung total harga dengan mengalikan harga_barang dengan jumlah_masuk
		$this->db->select('(oli.harga * oli_masuk.jumlah_masuk) as total_harga', false);

		return $this->db->get()->result_array();
	}

	public function getOliKeluar($limit = null, $id_oli = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*');
		$this->db->join('user', 'oli_keluar.user_id = user.id_user');
		$this->db->join('oli', 'oli_keluar.oli_id = oli.id_oli');
		$this->db->join('armada', 'oli_keluar.id_armada = armada.id_armada');

		if ($limit != null) {
			$this->db->limit($limit);
		}
		if ($id_oli != null) {
			$this->db->where('id_oli', $id_oli);
		}
		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('oli_keluar.tanggal_keluar', $start_date);
			} else {
				$this->db->where('oli_keluar.tanggal_keluar >=', $start_date);
				$this->db->where('oli_keluar.tanggal_keluar <=', $end_date);
			}
		}
		$this->db->order_by('id_oli_keluar', 'DESC');

		return $this->db->get('oli_keluar')->result_array();
	}

	public function getMax($table, $field, $kode = null)
	{
		$this->db->select_max($field);
		if ($kode != null) {
			$this->db->like($field, $kode, 'after');
		}
		return $this->db->get($table)->row_array()[$field];
	}

	public function count($table)
	{
		return $this->db->count_all($table);
	}

	public function sum($table, $field)
	{
		$this->db->select_sum($field);
		return $this->db->get($table)->row_array()[$field];
	}

	public function min($table, $field, $min)
	{
		$field = $field . ' <=';
		$this->db->where($field, $min);
		return $this->db->get($table)->result_array();
	}

	public function charOliMasuk($bulan)
	{
		$like = 'T-OM-' . date('y') . $bulan;
		$this->db->like('id_oli_masuk', $like, 'after');
		return count($this->db->get('oli_masuk')->result_array());
	}

	public function chartOliKeluar($bulan)
	{
		$like = 'T-OK-' . date('y') . $bulan;
		$this->db->like('id_oli_keluar', $like, 'after');
		return count($this->db->get('oli_keluar')->result_array());
	}

	public function getOliById($id_aki)
	{
		return $this->db->get_where('oli', ['id_oli' => $id_aki])->row();
	}

	// Method untuk mengambil peran (role) pengguna berdasarkan ID
	public function get_user_role_by_id($id_user)
	{
		$query = $this->db->select('role')->get_where('user', array('id_user' => $id_user));
		$result = $query->row();
		return $result ? $result->role : null;
	}

	public function hapusDataOli($id_aki)
	{
		$this->db->where('id_oli', $id_aki);
		$this->db->delete('oli');
	}

	public function laporan($table, $mulai, $akhir)
	{
		$tgl = $table == 'oli_masuk' ? 'tanggal_masuk' : 'tanggal_keluar';
		$this->db->where($tgl . ' >=', $mulai);
		$this->db->where($tgl . ' <=', $akhir);
		return $this->db->get($table)->result_array();
	}

	public function cekStok($id)
	{
		$this->db->join('supplier', 'oli.supplier_id=supplier.id_supplier');
		return $this->db->get_where('oli', ['id_oli' => $id])->row_array();
	}

	public function cekStokOli($id_oli_masuk)
	{
		return $this->db->join('oli', 'oli.id_oli = oli_masuk.oli_id', 'left')
			->join('supplier', 'oli.supplier_id=supplier.id_supplier', 'left')
			->get_where('oli_masuk', ['id_oli_masuk' => $id_oli_masuk])->row();


		// $this->db->join('supplier', 'oli.supplier_id=supplier.id_supplier');
		// return $this->db->get_where('oli', ['id_oli' => $id])->row_array();
	}
}