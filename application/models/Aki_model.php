<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Aki_model extends CI_Model
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

    public function getAki()
    {
        $this->db->join('supplier', 'aki.supplier_id = supplier.id_supplier');
        $this->db->order_by('id_aki');
        return $this->db->get('aki')->result();
    }

    public function getAkiMasuk($limit = null, $id_aki = null, $start_date = null, $end_date = null)
    {
        $this->db->select('*'); // Menghapus alias yang digunakan sebelumnya
        $this->db->from('aki_masuk');
        $this->db->join('user', 'aki_masuk.user_id = user.id_user');
        $this->db->join('aki', 'aki_masuk.aki_id = aki.id_aki');
        $this->db->join('supplier', 'aki.supplier_id = supplier.id_supplier');

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
                $this->db->where('tanggal_keluar' . ' >=', $start_date['mulai']);
            } else {
                $this->db->where('tanggal_keluar >=', $start_date);
                $this->db->where('tanggal_keluar <=', $end_date);
            }
        }
        $this->db->order_by('id_aki_keluar', 'DESC');

        return $this->db->get('aki_keluar')->result_array();
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

    public function chartAkiMasuk($bulan)
    {
        $like = 'T-AM-' . date('y') . $bulan;
        $this->db->like('id_aki_masuk', $like, 'after');
        return count($this->db->get('aki_masuk')->result_array());
    }

    public function chartAkiKeluar($bulan)
    {
        $like = 'T-AK-' . date('y') . $bulan;
        $this->db->like('id_aki_keluar', $like, 'after');
        return count($this->db->get('aki_keluar')->result_array());
    }

    public function getAkiById($id_aki)
    {
        return $this->db->get_where('aki', ['id_aki' => $id_aki])->row();
    }

    // Method untuk mengambil peran (role) pengguna berdasarkan ID
    public function get_user_role_by_id($id_user)
    {
        $query = $this->db->select('role')->get_where('user', array('id_user' => $id_user));
        $result = $query->row();
        return $result ? $result->role : null;
    }

    public function tambahDataAki($data)
    {
        // $nomor_armada = post($data['nomor_armada']);
        $tanggal_pasang_baru = strtotime($data['tanggal_pasang_baru']);
        $tanggal_pasang_lama = strtotime($data['tanggal_pasang_lama']);
        $lama_pemakaian_hari = ($tanggal_pasang_lama - $tanggal_pasang_baru) / (60 * 60 * 24);
        // $masalah = post($data['masalah']);
        // $keterangan = post($data['keterangan']);

        $data['lama_pemakaian_hari'] = $lama_pemakaian_hari;

        // $data = [
        //     // "nomor_armada" => $this->input->post('nomor_armada', true),
        //     // "nama_supir" => $this->input->post('nama_supir', true),
        //     // // "tanggal_pasang_baru" => $this->input->post('tanggal_pasang_baru', true),
        //     // // "tanggal_pasang_lama" => $this->input->post('tanggal_pasang_lama', true),
        //     // // "lama_pemakaian_hari" => $this->input->post('lama_pemakaian_hari', true),
        //     // // "lama_pemakaian_tahun" => $this->input->post('lama_pemakaian_tahun', true),
        //     // "masalah" => $this->input->post('masalah', true),
        //     // "keterangan" => $this->input->post('keterangan', true)
        // ];

        $this->db->insert('aki', $data);
    }

    // public function editDataAki($id_aki, $data)
    // {
    //     $this->db->where('id_aki', $id_aki);
    //     $this->db->update('aki', $data);
    // }

    public function hapusDataAki($id_aki)
    {
        $this->db->where('id_aki', $id_aki);
        $this->db->delete('aki');
    }

    public function laporan($table, $mulai, $akhir)
    {
        $tgl = $table == 'aki_masuk' ? 'tanggal_masuk' : 'tanggal_keluar';
        $this->db->where($tgl . ' >=', $mulai);
        $this->db->where($tgl . ' <=', $akhir);
        return $this->db->get($table)->result_array();
    }

    public function cekStok($id)
    {
        $this->db->join('supplier', 'aki.supplier_id=supplier.id_supplier');
        return $this->db->get_where('aki', ['id_aki' => $id])->row_array();
    }
}
