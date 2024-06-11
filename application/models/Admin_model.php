<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	public function get($table, $data = null, $where = null)
	{
		if ($data != null) {
			return $this->db->get_where($table, $data)->row_array();
		} else {
			return $this->db->get_where($table, $where)->result_array();
		}
	}

	public function cekNamaBarang($nama_barang)
    {
        return $this->db->get_where('barang', ['nama_barang' => $nama_barang])->num_rows();
    }

	public function getPerbaikanByid($id_perbaikan)
	{
		return $this->db->get_where('perbaikan', ['id_perbaikan' => $id_perbaikan])->row_array();
	}

	public function totalSparepart()
	{
		$this->db->select('SUM(harga * stok) as total_harga');
		$query = $this->db->get('barang');
		return $query->row()->total_harga;
	}

	public function totalAki()
	{
		$this->db->select('SUM(harga * stok) as total_harga');
		$query = $this->db->get('aki');
		return $query->row()->total_harga;
	}

	public function totalBan()
	{
		$this->db->select('SUM(harga * stok) as total_harga');
		$query = $this->db->get('ban');
		return $query->row()->total_harga;
	}

	public function totalOli()
	{
		$this->db->select('SUM(harga * stok) as total_harga');
		$query = $this->db->get('oli');
		return $query->row()->total_harga;
	}

	// public function getBanId($id)
	// {
	//     return $this->db->get_where('ban', ['id_ban' => $id])->row_array();
	// }


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

	public function getUsers($id)
	{
		$this->db->where('id_user !=', $id);
		return $this->db->get('user')->result_array();
	}

	// Method untuk mengambil peran (role) pengguna berdasarkan ID
	public function get_user_role_by_id($id_user)
	{
		$query = $this->db->select('role')->get_where('user', array('id_user' => $id_user));
		$result = $query->row();
		return $result ? $result->role : null;
	}

	public function getBarang()
	{
		$this->db->join('supplier', 'barang.supplier_id = supplier.id_supplier');
		$this->db->order_by('id_barang');
		return $this->db->get('barang')->result_array();
	}

	function listSparepartMasuk()
	{
		return $this->db->from('barang_masuk')
			->join('barang', 'barang_masuk.barang_id = barang.id_barang')
			->join('supplier', 'barang.supplier_id = supplier.id_supplier')
			->get()->result();
	}

	public function getBan()
	{
		$this->db->join('supplier', 'ban.supplier_id = supplier.id_supplier');
		$this->db->order_by('id_ban');
		return $this->db->get('ban')->result();
	}

	public function getBarangMasuk($limit = null, $id_barang = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*'); // Menghapus alias yang digunakan sebelumnya
		$this->db->from('barang_masuk');
		$this->db->join('user', 'barang_masuk.user_id = user.id_user');
		$this->db->join('barang', 'barang_masuk.barang_id = barang.id_barang');
		$this->db->join('supplier', 'barang.supplier_id = supplier.id_supplier');
		// $this->db->order_by('id_barang_masuk');

		if ($limit != null) {
			$this->db->limit($limit);
		}

		if ($id_barang != null) {
			$this->db->where('barang_masuk.barang_id', $id_barang);
		}

		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('barang_masuk.tanggal_masuk', $start_date);
			} else {
				$this->db->where('barang_masuk.tanggal_masuk >=', $start_date);
				$this->db->where('barang_masuk.tanggal_masuk <=', $end_date);
			}
		}

		$this->db->order_by('barang_masuk.id_barang_masuk', 'DESC');

		// Hitung total harga dengan mengalikan harga_barang dengan jumlah_masuk
		$this->db->select('(barang.harga * barang_masuk.jumlah_masuk) as total_harga_barang_masuk', false);

		return $this->db->get()->result_array();
	}

	public function getBarangKeluar($limit = null, $id_barang = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*');
		$this->db->join('user u', 'bk.user_id = u.id_user');
		$this->db->join('barang b', 'bk.barang_id = b.id_barang');
		// $this->db->join('satuan s', 'b.satuan_id = s.id_satuan');
		$this->db->join('supplier sp', 'b.supplier_id = sp.id_supplier');
		$this->db->join('armada', 'bk.id_armada = armada.id_armada');
		$this->db->join('supir', 'bk.id_supir = supir.id_supir');
		$this->db->join('montir', 'bk.id_montir = montir.id_montir');

		if ($limit != null) {
			$this->db->limit($limit);
		}
		if ($id_barang != null) {
			$this->db->where('id_barang', $id_barang);
		}

		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('bk.tanggal_keluar', $start_date);
			} else {
				$this->db->where('bk.tanggal_keluar >=', $start_date);
				$this->db->where('bk.tanggal_keluar <=', $end_date);
			}
		}
		$this->db->order_by('id_barang_keluar', 'DESC');

		return $this->db->get('barang_keluar bk')->result_array();
	}

	public function getCetakLaporan($limit = null, $id_barang = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*');
		$this->db->join('user u', 'bk.user_id = u.id_user');
		$this->db->join('barang b', 'bk.barang_id = b.id_barang');
		// $this->db->join('satuan s', 'b.satuan_id = s.id_satuan');
		$this->db->join('supplier sp', 'b.supplier_id = sp.id_supplier');
		$this->db->join('armada', 'bk.id_armada = armada.id_armada');
		$this->db->join('supir', 'bk.id_supir = supir.id_supir');
		$this->db->join('montir', 'bk.id_montir = montir.id_montir');

		if ($limit != null) {
			$this->db->limit($limit);
		}
		if ($id_barang != null) {
			$this->db->where('id_barang', $id_barang);
		}

		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('bk.tanggal_keluar', $start_date);
			} else {
				$this->db->where('bk.tanggal_keluar >=', $start_date);
				$this->db->where('bk.tanggal_keluar <=', $end_date);
			}
		}
		$this->db->order_by('id_barang_keluar', 'DESC');

		return $this->db->get('barang_keluar bk')->result_array();
	}

	public function getAkiMasuk($limit = null, $id_aki = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*'); // Menghapus alias yang digunakan sebelumnya
		$this->db->from('aki_masuk');
		$this->db->join('user', 'aki_masuk.user_id = user.id_user');
		$this->db->join('aki', 'aki_masuk.aki_id = aki.id_aki');
		$this->db->join('supplier', 'aki.supplier_id = supplier.id_supplier');
		// $this->db->join('satuan', 'aki.satuan_id = satuan.id_satuan');
		// $this->db->order_by('id_barang_masuk');

		if ($limit != null) {
			$this->db->limit($limit);
		}

		if ($id_aki != null) {
			$this->db->where('aki_masuk.aki_id', $id_aki);
		}

		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('aki_masuk.tanggal_masuk', $start_date);
			} else {
				$this->db->where('aki_masuk.tanggal_masuk >=', $start_date);
				$this->db->where('aki_masuk.tanggal_masuk <=', $end_date);
			}
		}

		$this->db->order_by('aki_masuk.id_aki_masuk', 'DESC');

		// Hitung total harga dengan mengalikan harga_barang dengan jumlah_masuk
		$this->db->select('(aki.harga * aki_masuk.jumlah_masuk) as total_harga', false);

		return $this->db->get()->result_array();
	}

	public function getAkiKeluar($limit = null, $id_aki = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*');
		$this->db->join('user', 'aki_keluar.user_id = user.id_user');
		$this->db->join('aki', 'aki_keluar.aki_id = aki.id_aki');
		$this->db->join('armada', 'aki_keluar.armada_id = armada.id_armada');
		$this->db->join('supir', 'aki_keluar.supir_id = supir.id_supir');
		$this->db->join('montir', 'aki_keluar.montir_id = montir.id_montir');
		if ($limit != null) {
			$this->db->limit($limit);
		}

		if ($id_aki != null) {
			$this->db->where('id_aki', $id_aki);
		}
		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('aki_keluar.tanggal_keluar', $start_date);
			} else {
				$this->db->where('aki_keluar.tanggal_keluar >=', $start_date);
				$this->db->where('aki_keluar.tanggal_keluar <=', $end_date);
			}
		}
		$this->db->order_by('id_aki_keluar', 'DESC');

		return $this->db->get('aki_keluar')->result_array();
	}

	public function getBanMasuk($limit = null, $id_ban = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*'); // Menghapus alias yang digunakan sebelumnya
		$this->db->from('ban_masuk');
		$this->db->join('user', 'ban_masuk.user_id = user.id_user');
		$this->db->join('ban', 'ban_masuk.ban_id = ban.id_ban');
		$this->db->join('supplier', 'ban.supplier_id = supplier.id_supplier');
		// $this->db->join('satuan', 'ban.satuan_id = satuan.id_satuan');
		// $this->db->order_by('id_ban_masuk');

		if ($limit != null) {
			$this->db->limit($limit);
		}

		if ($id_ban != null) {
			$this->db->where('ban_masuk.ban_id', $id_ban);
		}

		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('ban_masuk.tanggal_masuk', $start_date);
			} else {
				$this->db->where('ban_masuk.tanggal_masuk >=', $start_date);
				$this->db->where('ban_masuk.tanggal_masuk <=', $end_date);
			}
		}

		$this->db->select('(ban.harga * ban_masuk.jumlah_masuk) as total_harga', false);
		$this->db->order_by('ban_masuk.id_ban_masuk', 'DESC');

		// Hitung total harga dengan mengalikan harga_ban dengan jumlah_masuk

		return $this->db->get()->result_array();
	}

	public function getBanKeluar($limit = null, $id_ban = null, $start_date = null, $end_date = null)
	{
		$this->db->select('*');
		$this->db->join('user', 'ban_keluar.user_id = user.id_user');
		$this->db->join('ban', 'ban_keluar.ban_id = ban.id_ban');
		$this->db->join('armada', 'ban_keluar.armada_id = armada.id_armada');
		$this->db->join('supir', 'ban_keluar.supir_id = supir.id_supir');
		$this->db->join('montir', 'ban_keluar.montir_id = montir.id_montir');
		if ($limit != null) {
			$this->db->limit($limit);
		}
		if ($id_ban != null) {
			$this->db->where('id_ban', $id_ban);
		}
		if ($start_date != null) {
			if ($start_date == $end_date) {
				$this->db->where('ban_keluar.tanggal_keluar', $start_date);
			} else {
				$this->db->where('ban_keluar.tanggal_keluar >=', $start_date);
				$this->db->where('ban_keluar.tanggal_keluar <=', $end_date);
			}
		}
		$this->db->order_by('id_ban_keluar', 'DESC');

		return $this->db->get('ban_keluar')->result_array();
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

	public function chartBarangMasuk($bulan)
	{
		$like = 'T-AM-' . date('y') . $bulan;
		$this->db->like('id_aki_masuk', $like, 'after');
		return count($this->db->get('aki_masuk')->result_array());
	}

	public function chartBarangKeluar($bulan)
	{
		$like = 'T-SK-' . date('y') . $bulan;
		$this->db->like('id_barang_keluar', $like, 'after');
		return count($this->db->get('barang_keluar')->result_array());
	}

	public function laporan($table, $mulai, $akhir)
	{
		$tgl = $table == 'barang_masuk' ? 'tanggal_masuk' : 'tanggal_keluar';
		$this->db->where($tgl . ' >=', $mulai);
		$this->db->where($tgl . ' <=', $akhir);
		return $this->db->get($table)->result_array();
	}

	public function cekHarga($id)
	{
		return $this->db->get_where('barang', ['id_barang' => $id])->row_array();
	}

	public function cekHargaBan($id)
	{
		return $this->db->get_where('ban', ['id_ban' => $id])->row_array();
	}

	public function cekHargaAki($id)
	{
		return $this->db->get_where('aki', ['id_aki' => $id])->row_array();
	}

	public function cekStok($id)
	{
		$this->db->join('supplier', 'barang.supplier_id=supplier.id_supplier');
		return $this->db->get_where('barang', ['id_barang' => $id])->row_array();
	}

	public function cekStokSparepart($id_barang_masuk)
	{
		return $this->db->join('barang', 'barang.id_barang = barang_masuk.barang_id', 'left')
			->join('supplier', 'barang.supplier_id=supplier.id_supplier', 'left')
			->get_where('barang_masuk', ['id_barang_masuk' => $id_barang_masuk])->row();
	}

	public function cekStokBan($id)
	{
		$this->db->join('supplier', 'ban.supplier_id=supplier.id_supplier');
		return $this->db->get_where('ban', ['id_ban' => $id])->row_array();
	}
	}

// 	public function getLaporanBarangMasuk($limit = null, $id_barang = null, $range = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user', 'barang_masuk.user_id = user.id_user');
// 		$this->db->join('barang', 'barang_masuk.barang_id = barang.id_barang');
// 		$this->db->join('supplier', 'barang.supplier_id = supplier.id_supplier');
// 		$this->db->join('satuan', 'barang.satuan_id = satuan.id_satuan');
// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}

// 		if ($id_barang != null) {
// 			$this->db->where('id_barang', $id_barang);
// 		}

// 		if ($range != null) {
// 			$this->db->where('tanggal_masuk' . ' >=', $range['mulai']);
// 			$this->db->where('tanggal_masuk' . ' <=', $range['akhir']);
// 		}

// 		$this->db->order_by('id_barang_masuk', 'DESC');
// 		return $this->db->get('barang_masuk')->result_array();
// 	}

// 	public function getLaporanBarangKeluar($limit = null, $id_barang = null, $range = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user u', 'bk.user_id = u.id_user');
// 		$this->db->join('barang b', 'bk.barang_id = b.id_barang');
// 		// $this->db->join('satuan s', 'b.satuan_id = s.id_satuan');
// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}
// 		if ($id_barang != null) {
// 			$this->db->where('id_barang', $id_barang);
// 		}
// 		if ($range != null) {
// 			$this->db->where('tanggal_keluar' . ' >=', $range['mulai']);
// 			$this->db->where('tanggal_keluar' . ' <=', $range['akhir']);
// 		}
// 		$this->db->order_by('id_barang_keluar', 'DESC');
// 		return $this->db->get('barang_keluar bk')->result_array();
// 	}

// 	public function getLaporanBanMasuk($limit = null, $id_ban = null, $range = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user', 'ban_masuk.user_id = user.id_user');
// 		$this->db->join('ban', 'ban_masuk.ban_id = ban.id_ban');
// 		$this->db->join('supplier', 'ban.supplier_id = supplier.id_supplier');

// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}

// 		if ($id_ban != null) {
// 			$this->db->where('id_ban', $id_ban);
// 		}

// 		if ($range != null) {
// 			$this->db->where('tanggal_masuk' . ' >=', $range['mulai']);
// 			$this->db->where('tanggal_masuk' . ' <=', $range['akhir']);
// 		}
// 		// if ($start_date != null) {
// 		// 	if ($start_date == $end_date) {
// 		// 	$this->db->where('ban_masuk.tanggal_masuk', $start_date);
// 		// 	} else {
// 		// 		$this->db->where('ban_masuk.tanggal_masuk >=', $start_date);
// 		// 		$this->db->where('ban_masuk.tanggal_masuk <=', $end_date);
// 		// 	}
// 		// }

// 		$this->db->order_by('id_ban_masuk', 'DESC');
// 		$this->db->select('(ban.harga * ban_masuk.jumlah_masuk) as total_harga', false);

// 		// Hitung total harga dengan mengalikan harga_ban dengan jumlah_masuk

// 		return $this->db->get('ban_masuk')->result_array();
// 	}

// 	public function getLaporanBanKeluar($limit = null, $id_ban = null, $range = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user', 'ban_keluar.user_id = user.id_user');
// 		$this->db->join('ban', 'ban_keluar.ban_id = ban.id_ban');
// 		$this->db->join('armada', 'ban_keluar.id_armada = armada.id_armada');
// 		$this->db->join('supir', 'ban_keluar.id_supir = supir.id_supir');
// 		$this->db->join('montir', 'ban_keluar.id_montir = montir.id_montir');
// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}
// 		if ($id_ban != null) {
// 			$this->db->where('id_ban', $id_ban);
// 		}

// 		if ($range != null) {
// 			$this->db->where('tanggal_keluar' . ' >=', $range['mulai']);
// 			$this->db->where('tanggal_keluar' . ' <=', $range['akhir']);
// 		}

// 		// if ($start_date != null) {
// 		// 	if ($start_date == $end_date) {
// 		// 		$this->db->where('ban_keluar.tanggal_keluar', $start_date);
// 		// 	} else {
// 		// 		$this->db->where('ban_keluar.tanggal_keluar >=', $start_date);
// 		// 		$this->db->where('ban_keluar.tanggal_keluar <=', $end_date);
// 		// 	}
// 		// }
// 		$this->db->order_by('id_ban_keluar', 'DESC');

// 		return $this->db->get('ban_keluar')->result_array();
// 	}

// 	public function getLaporanAkiMasuk($limit = null, $id_ban = null, $range = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user', 'ban_masuk.user_id = user.id_user');
// 		$this->db->join('ban', 'ban_masuk.ban_id = ban.id_ban');
// 		$this->db->join('supplier', 'ban.supplier_id = supplier.id_supplier');

// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}

// 		if ($id_ban != null) {
// 			$this->db->where('id_ban', $id_ban);
// 		}

// 		if ($range != null) {
// 			$this->db->where('tanggal_masuk' . ' >=', $range['mulai']);
// 			$this->db->where('tanggal_masuk' . ' <=', $range['akhir']);
// 		}
// 		// if ($start_date != null) {
// 		// 	if ($start_date == $end_date) {
// 		// 	$this->db->where('ban_masuk.tanggal_masuk', $start_date);
// 		// 	} else {
// 		// 		$this->db->where('ban_masuk.tanggal_masuk >=', $start_date);
// 		// 		$this->db->where('ban_masuk.tanggal_masuk <=', $end_date);
// 		// 	}
// 		// }

// 		$this->db->order_by('id_ban_masuk', 'DESC');
// 		$this->db->select('(ban.harga * ban_masuk.jumlah_masuk) as total_harga', false);

// 		// Hitung total harga dengan mengalikan harga_ban dengan jumlah_masuk

// 		return $this->db->get('ban_masuk')->result_array();
// 	}

// 	public function getLaporanAkiKeluar($limit = null, $id_aki = null, $range = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user', 'aki_keluar.user_id = user.id_user');
// 		$this->db->join('aki', 'aki_keluar.aki_id = aki.id_aki');
// 		$this->db->join('armada', 'aki_keluar.id_armada = armada.id_armada');
// 		$this->db->join('supir', 'aki_keluar.id_supir = supir.id_supir');
// 		$this->db->join('montir', 'aki_keluar.id_montir = montir.id_montir');
// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}

// 		if ($id_aki != null) {
// 			$this->db->where('id_aki', $id_aki);
// 		}

// 		if ($range != null) {
// 			$this->db->where('tanggal_keluar' . ' >=', $range['mulai']);
// 			$this->db->where('tanggal_keluar' . ' <=', $range['akhir']);
// 		}

// 		// if ($start_date != null) {
// 		//     if ($start_date == $end_date) {
// 		//     	$this->db->where('aki_keluar.tanggal_keluar', $start_date);
// 		// 	} else {
// 		// 		$this->db->where('aki_keluar.tanggal_keluar >=', $start_date);
// 		// 		$this->db->where('aki_keluar.tanggal_keluar <=', $end_date);
// 		// 	}
// 		// }

// 		$this->db->order_by('id_aki_keluar', 'DESC');

// 		return $this->db->get('aki_keluar')->result_array();
// 	}

// 	public function getCetakLaporanOli($limit = null, $id_oli = null, $start_date = null, $end_date = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user', 'oli_keluar.user_id = user.id_user', 'left');
// 		$this->db->join('oli', 'oli_keluar.oli_id = oli.id_oli', 'left');
// 		$this->db->join('armada', 'oli_keluar.id_armada = armada.id_armada', 'left');
// 		$this->db->join('oli_masuk', 'oli_keluar.oli_masuk_id = oli_masuk.id_oli_masuk', 'left');

// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}
// 		if ($id_oli != null) {
// 			$this->db->where('id_oli', $id_oli);
// 		}
// 		if ($start_date != null) {
// 			if ($start_date == $end_date) {
// 				$this->db->where('oli_keluar.tanggal_keluar', $start_date);
// 			} else {
// 				$this->db->where('oli_keluar.tanggal_keluar >=', $start_date);
// 				$this->db->where('oli_keluar.tanggal_keluar <=', $end_date);
// 			}
// 		}
// 		$this->db->order_by('id_oli_keluar', 'DESC');

// 		return $this->db->get('oli_keluar')->result_array();
// 	}
// 	public function getCetakLaporanSparepart($limit = null, $id_barang = null, $start_date = null, $end_date = null)
// 	{
// 		$this->db->select('*');
// 		$this->db->join('user', 'barang_keluar.user_id = user.id_user', 'left');
// 		$this->db->join('barang', 'barang_keluar.barang_id = barang.id_barang', 'left');
// 		$this->db->join('armada', 'barang_keluar.id_armada = armada.id_armada', 'left');
// 		$this->db->join('barang_masuk', 'barang_keluar.barang_masuk_id = barang_masuk.id_barang_masuk', 'left');

// 		if ($limit != null) {
// 			$this->db->limit($limit);
// 		}
// 		if ($id_barang != null) {
// 			$this->db->where('id_barang', $id_barang);
// 		}
// 		if ($start_date != null) {
// 			if ($start_date == $end_date) {
// 				$this->db->where('barang_keluar.tanggal_keluar', $start_date);
// 			} else {
// 				$this->db->where('barang_keluar.tanggal_keluar >=', $start_date);
// 				$this->db->where('barang_keluar.tanggal_keluar <=', $end_date);
// 			}
// 		}
// 		$this->db->order_by('id_barang_keluar', 'DESC');

// 		return $this->db->get('barang_keluar')->result_array();
// 	}
// }