<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->web = $this->db->get('web')->row();
		$this->load->library('Pdf');
		if ($this->session->userdata('level') != 'admin') {
			$this->session->set_flashdata('message', 'swal("Ops!", "Anda harus login sebagai admin", "error");');
			redirect('/');
		}
	}

	public function index()
	{
		$tahun 			= date('Y');
		$bulan 			= date('m');
		$hari 			= date('d');
		$data['web']	= $this->web;
		$data['pegawai'] = $this->M_data->pegawai()->num_rows();
		$data['hadir']	= $this->M_data->hadirtoday($tahun, $bulan, $hari)->num_rows();
		$data['hadirlembur'] = $this->M_data->lemburtoday($tahun, $bulan, $hari)->num_rows();
		$data['cuti']	= $this->M_data->cutitoday($tahun, $bulan, $hari)->num_rows();
		$data['izin']	= $this->M_data->izintoday($tahun, $bulan, $hari)->num_rows() + $this->M_data->sakittoday($tahun, $bulan, $hari)->num_rows();
		$data['absensi'] = $this->M_data->absen();
		$absen			= $this->M_data->absendaily($this->session->userdata('kode_pegawai'), $tahun, $bulan, $hari);
		if ($absen->num_rows() == 0) {
			$data['waktu'] = 'masuk';
		} elseif ($absen->num_rows() == 1) {
			$data['waktu'] = 'pulang';
		} else {
			$data['waktu'] = 'dilarang';
		}
		$data['departemen'] = $this->db->get('departemen')->num_rows();
		$data['title']	= 'Dashboard';
		$data['body']	= 'admin/home';
		$this->load->view('template', $data);
	}
	public function jsondataabsen()
	{
		$sql = $this->M_data->absen();
	}
	public function proses_absen()
	{
		$id = $this->session->userdata('kode_pegawai');
		$p = $this->input->post();
		$data = [
			'kode_pegawai'	=> $id,
			'keterangan' => $p['ket']
		];
		$this->db->insert('absen', $data);
		redirect('Admin');
	}
	public function pengaturanabsen()
	{
		$data['web'] = $this->web;
		$data['pengaturanabsen'] = $this->db->get('configurasi_absen')->result();
		$data['title'] = 'Data Pengaturan Absen';
		$data['body'] = 'admin/pengaturanabsen';
		$this->load->view('template', $data);
	}
	public function pengaturanlembur()
	{
		$data['web'] = $this->web;
		$data['pengaturanlembur'] = $this->db->get('configurasi_lembur')->result();
		$data['title'] = 'Data Pengaturan Lembur';
		$data['body'] = 'admin/pengaturanlembur';
		$this->load->view('template', $data);
	}
	public function pengaturanlembur_add()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Tambah Data Pengaturan Lembur';
		$data['body'] = 'admin/pengaturanlembur_add';
		$this->load->view('template', $data);
	}
	public function pengaturanlembur_simpan()
	{
		$this->db->trans_start();
		$data = array(
			'ket_lembur' => $this->input->post('ketlembur'),
			'waktu_awal' => $this->input->post('awal'),
			'waktu_akhir' => $this->input->post('akhir'),
			'tipe_lembur' => $this->input->post('optiontipe'),
			'tanggal_lembur'=> $this->input->post('tanggallembur'),
		 );
		 $this->db->insert('configurasi_lembur',$data);
		 $this->db->trans_complete();
 		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah Data Pengaturan Lembur", "success");');
 		redirect('admin/pengaturanlembur');
	}
	public function pengaturanlembur_edit($id)
	{
		$data['web']	= $this->web;
		$data['data'] = $this->M_data->pengaturanlemburid($id)->row();
		$data['pengaturanlembur']	= $this->db->get_where('configurasi_lembur', ['no_lembur' => $id])->row();
		$data['title']	= 'Update Data Pengaturan Lembur';
		$data['body']	= 'admin/pengaturanlembur_edit';
		$this->load->view('template', $data);
	}
	public function pengaturanlembur_update($id)
	{
		$data = array(
			'ket_lembur' => $this->input->post('ketlembur'),
			'waktu_awal' => $this->input->post('awal'),
			'waktu_akhir' => $this->input->post('akhir'),
			'tipe_lembur' => $this->input->post('optiontipe'),
			'tanggal_lembur'=>$this->input->post('tanggallembur')
		);
		$this->db->update(
			'configurasi_lembur',$data,['no_lembur' => $id]
		);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update Pengaturan Lembur", "success");');
		redirect('admin/pengaturanlembur');
	}
	public function pengaturanlembur_delete($id)
	{
		$this->db->delete('configurasi_lembur', ['no_lembur' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Pengaturan Lembur", "success");');
		redirect('admin/pengaturanlembur');
	}
	public function pengaturanabsen_add()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Tambah Data Pengaturan Absen';
		$data['body'] = 'admin/pengaturanabsen_add';
		$this->load->view('template', $data);
	}
	public function pengaturanabsen_simpan()
	{
		$this->db->trans_start();
		$data = array(
			'waktu_awal' => $this->input->post('awal'),
			'waktu_akhir' => $this->input->post('akhir'),
			'tipe_jam' => $this->input->post('optiontipe')

		);
		$this->db->insert('configurasi_absen', $data);
		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah Data Pengaturan Absen", "success");');
		redirect('admin/pengaturanabsen');
	}

	public function pengaturanabsen_edit($id)
	{
		$data['web']	= $this->web;
		$data['data'] = $this->M_data->pengaturanabsenid($id)->row();
		$data['pengaturanabsen']	= $this->db->get_where('configurasi_absen', ['no_urut' => $id])->row();
		$data['title']	= 'Update Data Pengaturan Absen';
		$data['body']	= 'admin/pengaturanabsen_edit';
		$this->load->view('template', $data);
	}
	public function pengaturanabsen_update($id)
	{
		$data = array(
			'waktu_awal' => $this->input->post('awal'),
			'waktu_akhir' => $this->input->post('akhir'),
			'tipe_jam' => $this->input->post('optiontipe'));
		$this->db->update(
			'configurasi_absen',$data,['no_urut' => $id]
		);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update Pengaturan Absen", "success");');
		redirect('admin/pengaturanabsen');
	}
	public function pengaturanabsen_delete($id)
	{
		$this->db->delete('configurasi_absen', ['no_urut' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Pengaturan Absen", "success");');
		redirect('admin/pengaturanabsen');
	}

	public function pengangkatan()
	{
		$data['web'] = $this->web;
		$data['pengangkatan'] = $this->M_data->pengangkatanpegawai()->result();
		$data['title'] = 'Data Pengangkatan Pegawai';
		$data['body'] = 'admin/pengangkatanpegawai';
		$this->load->view('template', $data);
	}

	//CURD Departemen
	public function departemen()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get('departemen')->result();
		$data['title']	= 'Data Departemen';
		$data['body']	= 'admin/departemen';
		$this->load->view('template', $data);
	}
	public function statuspegawai()
	{
		$data['web'] = $this->web;
		$data['data'] = $this->db->get('status_pegawai')->result();
		$data['title'] = 'Data Statuspegawai';
		$data['body'] = 'admin/statuspegawai';
		$this->load->view('template', $data);
	}
	public function jabatan()
	{
		$data['web'] = $this->web;
		$data['data'] = $this->db->get('jabatan')->result();
		$data['title'] = 'Data Jabatan';
		$data['body'] = 'admin/jabatan';
		$this->load->view('template', $data);
	}
	public function statuspegawai_add()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Data Statuspegawai Add';
		$data['body'] = 'admin/statuspegawai_add';
		$this->load->view('template', $data);
	}
	public function pengangkatan_add()
	{
		$data['web']	= $this->web;
		$data['pegawai'] = $this->M_data->pegawai()->result();
		$data['title']	= 'Tambah Data Penangkatan Pegawai';
		$data['body']	= 'admin/pengangkatanpegawai_add';
		$this->load->view('template', $data);
	}
	public function pengangkatan_simpan()
	{
		$id = $this->input->post('namapegawai');
		$jabatan = $this->M_data->pegawaiid($id)->row()->id_jabatan;
		$departemen = $this->M_data->pegawaiid($id)->row()->id_departemen;
		$status = $this->M_data->pegawaiid($id)->row()->id_status_pegawai;

		$this->db->trans_start();
		$data = array(
			'id_pegawai' => $id,
			'no_urut_status_pegawai' => $status,
			'no_departemen' => $departemen,
			'no_jabatan' => $jabatan,
			'tanggal_pengangkatan' => $this->input->post('tanggalpengangkatan')
		);
		$this->db->insert('pengangkatanpegawai', $data);
		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah Data Pengangkatan Pegawai", "success");');
		redirect('admin/pengangkatan');
	}
	public function pengangkatan_edit($id)
	{
		$data['web']	= $this->web;
		$data['data'] = $this->M_data->pengangkatanpegawaiid($id)->row();
		$data['pengangkatan']	= $this->db->get_where('pengangkatanpegawai', ['id_pengangkatan_pegawai' => $id])->row();
		$data['title']	= 'Update Data Pengangkatan Pegawai';
		$data['body']	= 'admin/pengangkatanpegawai_edit';
		$this->load->view('template', $data);
	}
	public function pengangkatan_update($id)
	{
		$this->db->update(
			'pengangkatanpegawai',
			['tanggal_pengangkatan' => $this->input->post('tanggalpengangkatan')],
			['id_pengangkatan_pegawai' => $id]
		);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update Pengangkatan Pegawai", "success");');
		redirect('admin/pengangkatan');
	}
	public function pengangkatan_delete($id)
	{
		$this->db->delete('pengangkatanpegawai', ['id_pengangkatan_pegawai' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Pengangkatan Pegawai", "success");');
		redirect('admin/pengangkatan');
	}
	public function statuspegawai_edit($id)
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get_where('status_pegawai', ['id_status_pegawai' => $id])->row();
		$data['title']	= 'Update Data Status Pegawai';
		$data['body']	= 'admin/statuspegawai_edit';
		$this->load->view('template', $data);
	}
	public function statuspegawai_update($id)
	{
		$this->db->update('status_pegawai', ['ket_status_pegawai' => $this->input->post('statuspegawai')], ['id_status_pegawai' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update Status Pegawai", "success");');
		redirect('admin/statuspegawai');
	}
	public function statuspegawai_simpan()
	{
		$this->db->insert('status_pegawai', ['ket_status_pegawai' => $this->input->post('statuspegawai')]);
		$this->session->set_flashdata('message', 'swal("Behasil",Tambah Statuspegawai","success");');
		redirect('admin/statuspegawai');
	}
	public function statuspegawai_delete($id)
	{
		$this->db->delete('status_pegawai', ['id_status_pegawai' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Status Pegawai", "success");');
		redirect('admin/statuspegawai');
	}
	public function departemen_add()
	{
		$data['web']	= $this->web;
		$data['title']	= 'Tambah Data Departemen';
		$data['body']	= 'admin/departemen_add';
		$this->load->view('template', $data);
	}
	public function jabatan_add()
	{
		$data['web']	= $this->web;
		$data['title']	= 'Tambah Data Jabatan';
		$data['body']	= 'admin/jabatan_add';
		$this->load->view('template', $data);
	}
	public function jabatan_simpan()
	{
		$this->db->insert('jabatan', ['id_jabatan' => $this->input->post('kodejabatan'), 'nama_jabatan' => $this->input->post('jabatan')]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah jabatan", "success");');
		redirect('admin/jabatan');
	}
	public function departemen_simpan()
	{
		$this->db->insert('departemen', ['departemen_id' => $this->input->post('kodedepartemen'), 'departemen' => $this->input->post('departemen')]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah departemen", "success");');
		redirect('admin/departemen');
	}
	public function jabatan_edit($id)
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get_where('jabatan', ['id_jabatan' => $id])->row();
		$data['title']	= 'Update Data Departemen';
		$data['body']	= 'admin/jabatan_edit';
		$this->load->view('template', $data);
	}
	public function jabatan_update($id)
	{
		$this->db->update('jabatan', ['nama_jabatan' => $this->input->post('jabatan')], ['id_jabatan' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update jabatan", "success");');
		redirect('admin/jabatan');
	}
	public function jabatan_delete($id)
	{
		$this->db->delete('jabatan', ['id_jabatan' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete jabatan", "success");');
		redirect('admin/jabatan');
	}
	public function departemen_edit($id)
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get_where('departemen', ['departemen_id' => $id])->row();
		$data['title']	= 'Update Data Departemen';
		$data['body']	= 'admin/departemen_edit';
		$this->load->view('template', $data);
	}
	public function departemen_update($id)
	{
		$this->db->update('departemen', ['departemen' => $this->input->post('departemen')], ['departemen_id' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update departemen", "success");');
		redirect('admin/departemen');
	}
	public function departemen_delete($id)
	{
		$this->db->delete('departemen', ['departemen_id' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete departemen", "success");');
		redirect('admin/departemen');
	}
	//EDN CURD Departemen
	//CURD Pegawai
	public function pegawai()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->pegawai()->result();
		$data['title']	= 'Data Pegawai';
		$data['body']	= 'admin/pegawai';
		$this->load->view('template', $data);
	}
	public function pegawai_add()
	{
		$data['web']	= $this->web;
		$data['departemen']	= $this->db->get('departemen')->result();
		$data['status']	= $this->db->get('status_pegawai')->result();
		$data['jabatan'] = $this->db->get('jabatan')->result();
		$data['title']	= 'Tambah Data Pegawai';
		$data['body']	= 'admin/pegawai_add';
		$this->load->view('template', $data);
	}
	public function pegawai_simpan()
	{
		$p = $this->input->post();
		$email = $this->input->post('email');
		$id_pegawai = rand(10000000, 99999999);
		$status = $this->input->post('status');
		if ($status == 'menikah') {
			$jumlahanak = $this->input->post('jmlanak');
		} else {
			$jumlahanak = 0;
		}
		$user = [
			'nama'		=> $p['nama'],
			'email'		=> $email,
			'password'	=> md5($id_pegawai),
			'level'		=> $p['level'],
			'kode_pegawai'	=> $id_pegawai
		];
		$pgw = [
			'kode_pegawai'	=> $id_pegawai,
			'jenis_kelamin'	=> $p['jenis_kelamin'],
			'alamat' => $p['alamat'],
			'email'		=> $email,
			'id_departemen'	=> $p['departemen'],
			'id_status_pegawai' => $p['status_pegawai'],
			'id_jabatan' => $p['jabatan'],
			'waktu_masuk'	=> $p['masuk'],
			'nik' => $p['nik'],
			'npwp' => $p['npwp'],
			'no_telepon' => $p['notelp'],
			'nama_keluarga' => $p['namakeluarga'],
			'status' => $status,
			'jumlah_anak' => $jumlahanak,
			'no_kartu_bpjs_kesehatan' => $p['nokartubpjskesehatan'],
			'no_kartu_bpjs_tenagakerja' => $p['nokartubpjstenagakerja']
		];

		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => 'simpegpisputri@gmail.com',
			'smtp_pass' => 'simpeg123',
			'mailtype' => 'html',
			'charset' => 'iso-8859-1'
		);

		$this->load->library('email', $config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");
		$this->email->from('simpegpisputri@gmail.com', 'PT. Putri Indah Informatika Solusi');
		$this->email->to($email);
		$this->email->subject('Login User Pegawai PT. Putri Indah Informatika Solusi');
		$html = "<div>
					<h2 style=font-family:Arial Black;text-align:center>Silahkan Login User Pegawai pada dibawah ini: </h2>
						<div>
							<h3>1. Email : ".$email."</h3>
							<h3>3. Password : ".$id_pegawai."</h3>
						</div>
					</div>
					<div>
						<footer class=page-footer>
						  <span style=font-family:Arial Black;text-align:center>Sekian informasi login user dari pihak kami.</span>&nbsp;<br>
						  <span style=font-family:Arial Black;text-align:center>Admin PT.Putri Indah Informatika Solusi</span>
						</footer>
					</div>";
		$this->email->message($html);

		if($this->email->send()){
			$this->db->trans_start();
			$this->db->insert('user', $user);
			$this->db->insert('pegawai', $pgw);
			$this->db->trans_complete();
			$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah Data Pegawai", "success");');
			redirect('admin/pegawai');
		}else{
			show_error($this->email->print_debugger());
			redirect('admin/pegawai_add');
		}

	}

	public function pegawai_edit($id)
	{
		$data['web']	= $this->web;
		$data['departemen']	= $this->db->get('departemen')->result();
		$data['status']	= $this->db->get('status_pegawai')->result();
		$data['jabatan'] = $this->db->get('jabatan')->result();
		$data['detail']	= $this->M_data->pegawaiid($id)->row();
		$data['title']	= 'Update Data Pegawai';
		$data['body']	= 'admin/pegawai_edit';
		$this->load->view('template', $data);
	}

	public function pegawai_update($id)
	{
		$p = $this->input->post();
		$email = $this->input->post('email');
		$id_pegawai = $id;
		$status = $this->input->post('status');
		if ($status == 'menikah') {
			$jumlahanak = $this->input->post('jmlanak');
		} else {
			$jumlahanak = 0;
		}
		$user = [
			'nama'		=> $p['nama'],
			'email'		=> $email,
			'level' => $p['level']
		];
		$pgw = [
			'jenis_kelamin'	=> $p['jenis_kelamin'],
			'nik' => $p['nik'],
			'npwp' => $p['npwp'],
			'email' => $email,
			'nama_keluarga' => $p['namakeluarga'],
			'no_telepon' => $p['notelp'],
			'status' => $status,
			'jumlah_anak' => $jumlahanak,
			'alamat' => $p['alamat'],
			'id_departemen'	=> $p['departemen'],
			'id_status_pegawai'	=> $p['status_pegawai'],
			'id_jabatan' => $p['jabatan'],
			'no_kartu_bpjs_kesehatan' => $p['nokartubpjskesehatan'],
			'no_kartu_bpjs_tenagakerja' => $p['nokartubpjstenagakerja'],
			'waktu_masuk'	=> $p['masuk'],
		];

		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => 'simpegpisputri@gmail.com',
			'smtp_pass' => 'simpeg123',
			'mailtype' => 'html',
			'charset' => 'iso-8859-1'
		);

		$this->load->library('email', $config);
		$this->email->set_mailtype("html");
		$this->email->set_newline("\r\n");
		$this->email->from('simpegpisputri@gmail.com', 'PT. Putri Indah Informatika Solusi');
		$this->email->to($email);
		$this->email->subject('Login User Pegawai PT. Putri Indah Informatika Solusi');
		$html = "<div>
					<h2 style=font-family:Arial Black;text-align:center>Silahkan Login User Pegawai pada dibawah ini: </h2>
						<div>
							<h3>1. Email : ".$email."</h3>
							<h3>3. Password : ".$id_pegawai."</h3>
						</div>
					</div>
					<div>
						<footer class=page-footer>
						  <span style=font-family:Arial Black;text-align:center>Sekian informasi login user dari pihak kami.</span>&nbsp;<br>
						  <span style=font-family:Arial Black;text-align:center>Admin PT.Putri Indah Informatika Solusi</span>
						</footer>
					</div>";
		$this->email->message($html);

		if($this->email->send()){
			$this->db->trans_start();
			$this->db->update('user', $user, ['kode_pegawai' => $id]);
			$this->db->update('pegawai', $pgw, ['kode_pegawai' => $id]);
			$this->db->trans_complete();
			$this->session->set_flashdata('message', 'swal("Berhasil!", "Update Data Pegawai", "success");');
			redirect('admin/pegawai');
		}else{
			show_error($this->email->print_debugger());
		}
	}
	public function pegawai_delete($id)
	{
		$this->db->trans_start();
		$this->db->delete('user', ['kode_pegawai' => $id]);
		$this->db->delete('pegawai', ['kode_pegawai' => $id]);
		$this->db->trans_complete();
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Data Pegawai", "success");');
		redirect('admin/pegawai');
	}
	//end CURD pegawai
	//Data Absensi
	public function absensi()
	{
		$data['web']	= $this->web;
		// $data['data']	= $this->M_data->absen()->result();
		$data['data'] = $this->M_data->absen();
		$data['title']	= 'Data Absen Pegawai';
		$data['body']	= 'admin/absen';
		$this->load->view('template', $data);
	}
	public function lembur()
	{
		$data['web']	= $this->web;
		// $data['data']	= $this->M_data->absen()->result();
		$data['data'] = $this->M_data->lembur();
		$data['title']	= 'Data Lembur Pegawai';
		$data['body']	= 'admin/lembur';
		$this->load->view('template', $data);
	}
	public function absen_add()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Data Tambah Absen Pegawai';
		$data['body'] = 'admin/uploadabsen';
		$this->load->view('template', $data);
	}
	public function absen_simpan()
	{
		$file = ['nama_file_upload' => $_FILES['upload']['name']];
		if (isset($_FILES['upload']['name'])) {
			$config['upload_path'] 	= './absen/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg|txt|doc';
			$config['file_name'] = str_replace(" ", "_", $_FILES['upload']['name']);
			$config['override'] = true;
			$config['overwrite'] = true;
			$config['max_size'] = 100000;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('upload')) {
				$this->session->set_flashdata('message', 'swal("Ops!", "No SK gagal diupload", "error");');
				redirect('admin/absen_add');
				if ($config['max_size'] > 100000) {
					$this->session->set_flashdata('message', 'swal("Ops!", "No SK gagal diupload ukuran gambar terlalu besar", "error");');
					redirect('admin/absen_add');
				}
			}
		}
		$datafile = fopen("./absen/" . $config['file_name'], "r");
		foreach (file("./absen/" . $config['file_name'], FILE_IGNORE_NEW_LINES) as $isi) {
			$arraydata = array();
			foreach (explode(" ", $isi) as $i) {
				if ($i != null) {
					array_push($arraydata, $i);
				}
			}
			$idkaryawan = $arraydata[0];
			$waktu = $arraydata[count($arraydata) - 2];
			$status = $arraydata[count($arraydata) - 1];
			$tanggal = $arraydata[count($arraydata) - 3];

			$date = date_create_from_format("d/m/Y", $tanggal);
			$newdate =  date_format($date, 'Y-m-d');
			if ($status == "C/Masuk") {
				$ket = "masuk";
			} else if ($status == "C/Keluar") {
				$ket = "keluar";
			}
			$alldata = [
				'kode_pegawai' => $idkaryawan,
				'waktu' => $waktu,
				'status_absen' => $ket,
				'tanggal_absen' => $newdate,

			];
			$this->db->insert('new_absen', $alldata);
		}
		fclose($datafile);

		$this->db->insert('upload_absen', $file);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Upload Data Absen", "success");');
		redirect('admin/absen_add');
	}
	public function mutasi()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->mutasi()->result();
		$data['pegawai'] = $this->M_data->pegawai()->result();
		$data['jabatan'] = $this->db->get('jabatan')->result();
		$data['departemen']	= $this->db->get('departemen')->result();

		$data['title']	= 'Mutasi Pegawai';
		$data['body']	= 'admin/mutasi';
		$this->load->view('template', $data);
	}
	public function panggildata()
	{
		$data['web']	= $this->web;
		$data['title'] = 'Coba-coba';
		$data['body'] = 'admin/getdata';
		$this->load->view('template', $data);
	}
	public function pinjaman()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Pinjaman Pegawai';
		$data['body'] = 'admin/pinjaman';
		$this->load->view('template', $data);
	}
	public function pekerjaan()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Data Gaji Pegawai';
		$data['pegawai'] = $this->M_data->pegawai()->result();
		$data['body'] = 'admin/pekerjaan';
		$this->load->view('template', $data);
	}
	public function pekerjaan_simpan()
	{
	}
	public function laporanabsen()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Data Laporan Absen';
		$data['body'] = 'admin/laporanabsen';
		$this->load->view('template', $data);
	}
	public function laporangaji()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Data Laporan Gaji';
		$data['body'] = 'admin/laporangaji';
		$this->load->view('template', $data);
	}
	public function laporanpegawai()
	{
		$data['web'] = $this->web;
		$data['title'] = 'Data Laporan Pegawai';
		$data['departemen']	= $this->db->get('departemen')->result();
		$data['body'] = 'admin/laporanpegawai';
		$this->load->view('template', $data);
	}
	public function jsonbulangaji()
	{
		$gaji = $this->db->get('gaji')->result();
		foreach ($gaji as $key) {
			$date = strtotime($key->waktu);
			$var = date('M',$date);
		}
		$data = array(
			'bulan' => $var
		);
		echo json_encode($data);
	}
	public function jsontahungaji()
	{
		$gaji = $this->db->get('gaji')->result();
		foreach ($gaji as $key) {
			$date = strtotime($key->waktu);
			$var = date('Y',$date);
		}
		$data = array(
			'tahun' => $var
		);
		echo json_encode($data);
	}
	public function jsondatagaji()
	{
		$sql = 'SELECT SUBSTRING(waktu,1,4) AS tahun from gaji';
		$query = $this->db->query($sql);
	 	$sql2 = 'SELECT SUBSTRING(waktu,6,2) AS bulan from gaji';
		$query2 = $this->db->query($sql2);
		$data = array(
			'tahun' => $query->row()->tahun,
			'bulan' => $query2->row()->bulan
	 );
	 echo json_encode($data);
	}

	public function json()
	{

		// $sqltahun = "SELECT SUBSTRING(waktu, 1, 4) as tahun from gaji";
		// $querytahun = $this->db->query($sqltahun);
		// $sqlbulan = "SELECT SUBSTRING(waktu,6,2) AS bulan from gaji";
		// $querybulan = $this->db->query($sqlbulan);
		// $data = array(
		// 	'tahun' => $querytahun->row()->tahun,
		// 	'bulan' => $querybulan->row()->bulan
	 // );

	 $tahun = $this->input->post('selecttahun');
	 $bulan = $this->input->post('selectbulan');
	 $tglawal = strtotime("01 ".$bulan." ".$tahun);
	 $tglakhir = strtotime("+1 month", $tglawal);
	 if($bulan != '' && $tahun != ''){
		 $sql = "SELECT * FROM gaji WHERE waktu >='".date("Y-m-d",$tglawal)."' AND waktu < '".date("Y-m-d",$tglakhir)."'";
		 $query = $this->db->query($sql);
		 $datas = array();
		 foreach ($query->result() as $row) {
			$data['kodepegawai'] = $row->kode_pegawai;
		 	$data['gajipokok'] = $row->gaji_pokok;
			$data['uangmakan'] = $row->uang_makan;
			array_push($datas,$data);
		 }
	 }
		echo json_encode($datas);
	}
	public function jsonabsen()
	{

		// $sqltahun = "SELECT SUBSTRING(waktu, 1, 4) as tahun from gaji";
		// $querytahun = $this->db->query($sqltahun);
		// $sqlbulan = "SELECT SUBSTRING(waktu,6,2) AS bulan from gaji";
		// $querybulan = $this->db->query($sqlbulan);
		// $data = array(
		// 	'tahun' => $querytahun->row()->tahun,
		// 	'bulan' => $querybulan->row()->bulan
	 // );

	 $tahun = $this->input->post('selecttahun');
	 $bulan = $this->input->post('selectbulan');
	 $year  = date('Y');
	 $month  = date('m');
	 $tglawal = strtotime("01 ".$bulan." ".$tahun);
	 $tglakhir = strtotime("+1 month", $tglawal);
	 if($bulan != '' && $tahun != ''){
		 $sql = "SELECT * FROM absen WHERE waktu >='".date("Y-m-d",$tglawal)."' AND waktu < '".date("Y-m-d",$tglakhir)."'";
		 $query = $this->db->query($sql);
		 $datas = array();
		 foreach ($query->result() as $row) {
			$data['kodepegawai'] = $row->kode_pegawai;
		 	$data['jumlahhadir'] = $this->M_data->absenbulan($row->kode_pegawai, $year, $month)->num_rows();
			$data['jumlahcuti'] = $this->M_data->cutibulan($row->kode_pegawai, $year, $month)->num_rows();
			$data['jumlahizin'] = $this->M_data->izinbulan($row->kode_pegawai, $year, $month)->num_rows();
			$data['jumlahsakit'] = $this->M_data->sakitbulan($row->kode_pegawai, $year, $month)->num_rows();
			array_push($datas,$data);
		 }
	 }
		echo json_encode($datas);
	}
	public function json2()
	{
		$departemen = $this->input->post('selectdepartemen');
		if($departemen != ''){
			$sql = "SELECT * FROM pegawai INNER JOIN user ON pegawai.kode_pegawai = user.kode_pegawai WHERE id_departemen='".$departemen."'";
			$query = $this->db->query($sql);
			$datas = array();
			foreach ($query->result() as $row) {
				$data['kodepegawai'] = $row->kode_pegawai;
				$data['namapegawai'] = $row->nama;
				$x = $row->jenis_kelamin;
				if($x == 'L'){
					$data['jeniskelamin'] = "Laki-laki";
				}else{
					$data['jeniskelamin'] = "Perempuan";
				}
				$data['departemen'] = $row->id_departemen;
				$y = new DateTime($row->waktu_masuk);
				$data['tglmasuk'] = $y->format("d M Y");
				$data['jabatan'] = $row->id_jabatan;
				array_push($datas,$data);
			}
		}
		echo json_encode($datas);
	}


	// public function jsongaji()
	// {
	// 	if(isset($this->input->post('selectbulan') && $this->input->post('selecttahun')))
	// 	{
	// 			$query = "SELECT * FROM gaji WHERE SUBSTRING(waktu, 1, 4) as tahun = '".$this->input->post('selecttahun')."'";
	// 			echo
	// 	}
	// }


	public function riwayatmutasi()
	{
		$data['web']	= $this->web;
		$data['mutasi'] = $this->M_data->mutasi()->result();
		$data['title']	= 'Riwayat Mutasi Pegawai';
		$data['body']	= 'admin/riwayatmutasi';
		$this->load->view('template', $data);
	}
	public function riwayatgaji()
	{
		$data['web']	= $this->web;
		$data['title'] = 'Riwayat Gaji Pegawai';
		$data['body'] = 'admin/riwayatgaji';
		$this->load->view('template', $data);
	}
	public function hapus_mutasi($id)
	{
		$this->db->delete('mutasi', ['no_urut' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Mutasi", "success");');
		redirect('admin/riwayatmutasi');
	}
	public function mutasi_simpan()
	{
		$jenismutasi = $this->input->post('jenismutasi');
		$data = $this->db->get_where("pegawai", array("kode_pegawai" => $this->input->post("pegawai")))->row();
		$idjabatanlama = $data->id_jabatan;
		$iddepartemenlama = $data->id_departemen;
		$promosi =
			[
				'id_pegawai' => $this->input->post('pegawai'),
				'jenis_mutasi' => $jenismutasi,
				'id_jabatan_lama' => $idjabatanlama,
				'id_jabatan_baru' => $this->input->post('jabatanbaru'),
				'id_departemen_lama' => $iddepartemenlama,
				'id_departemen_baru' => $iddepartemenlama,
				'tgl_mutasi' => $this->input->post('tanggalmutasi'),
				'status_mutasi' => 'diajukan',
				'file_no_sk' => $_FILES['filenosk']['name'],
			];
		$mutasi =
			[
				'id_pegawai' => $this->input->post('pegawai'),
				'jenis_mutasi' => $jenismutasi,
				'id_jabatan_lama' => $idjabatanlama,
				'id_jabatan_baru' => $idjabatanlama,
				'id_departemen_lama' => $iddepartemenlama,
				'id_departemen_baru' => $this->input->post('departemenbaru'),
				'tgl_mutasi' => $this->input->post('tanggalmutasi'),
				'status_mutasi' => 'diajukan',
				'file_no_sk' => $_FILES['filenosk']['name'],
			];


		if (isset($_FILES['filenosk']['name'])) {
			$config['upload_path'] 	= './bukti/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|';
			$config['file_name'] = str_replace(" ", "_", $_FILES['filenosk']['name']);
			$config['override'] = true;
			$config['overwrite'] = true;
			$config['max_size'] = 100000;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('filenosk')) {
				$this->session->set_flashdata('message', 'swal("Ops!", "No SK gagal diupload", "error");');
				redirect('admin/mutasi');
				if ($config['max_size'] > 100000) {
					$this->session->set_flashdata('message', 'swal("Ops!", "No SK gagal diupload ukuran gambar terlalu besar", "error");');
					redirect('admin/mutasi');
				}
			}
		}

		if ($jenismutasi == "promosi") {
			$this->db->trans_start();
			$this->db->insert('mutasi', $promosi);
			$this->db->trans_complete();
		} else {
			$this->db->trans_start();
			$this->db->insert('mutasi', $mutasi);
			$this->db->trans_complete();
		}
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah Data Mutasi", "success");');
		redirect('admin/mutasi');
	}


	//Data pengajuan cuti
	public function cuti()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->cuti()->result();
		$data['title']	= 'Data Cuti Pegawai';
		$data['body']	= 'admin/cuti';
		$this->load->view('template', $data);
	}
	public function perjalanandinas()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->M_data->perjalanandinas()->result();
		$data['title'] = 'Perjalanan Pegawai';
		$data['body']	= 'admin/perjalanandinas';
		$this->load->view('template', $data);
	}
	//laporan bulanan
	function laporan()
	{
		function bulan($bln)
		{
			$bulan = $bln;
			switch ($bulan) {
				case 1:
					$bulan = "Januari";
					break;
				case 2:
					$bulan = "Februari";
					break;
				case 3:
					$bulan = "Maret";
					break;
				case 4:
					$bulan = "April";
					break;
				case 5:
					$bulan = "Mei";
					break;
				case 6:
					$bulan = "Juni";
					break;
				case 7:
					$bulan = "Juli";
					break;
				case 8:
					$bulan = "Agustus";
					break;
				case 9:
					$bulan = "September";
					break;
				case 10:
					$bulan = "Oktober";
					break;
				case 11:
					$bulan = "November";
					break;
				case 12:
					$bulan = "Desember";
					break;
			}
			return $bulan;
		}
		$bulan  = $this->input->post('bulan');
		$web 	= $this->web;
		$data   = $this->M_data->laporan($bulan)->result();

		$pdf = new FPDF('P', 'mm', 'A4');
		// membuat halaman baru
		$pdf->AddPage();
		// setting jenis font yang akan digunakan
		$pdf->SetFont('Arial', 'B', 22);
		// mencetak string
		$pdf->Image('assets/img/' . $web->logo, 10, 5, 25);
		$pdf->Cell(190, 7, $web->nama, 0, 1, 'C');
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(190, 5, $web->alamat, 0, 1, 'C');
		$pdf->Cell(190, 3, 'Phone : ' . $web->nohp . ' - Email : ' . $web->email, 0, 1, 'C');
		$pdf->Cell(10, 7, '', 0, 1);
		$pdf->Cell(190, 1, '', 'B', 1, 'L');
		$pdf->Cell(190, 1, '', 'B', 0, 'L');
		// Memberikan space kebawah agar tidak terlalu rapat
		$pdf->Cell(10, 5, '', 0, 1);
		$pdf->SetFont('Arial', 'B', 14);
		$pdf->Cell(190, 7, 'Laporan Absensi Pegawai', 0, 1, 'C');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(10, 5, 'Bulan : ' . bulan($bulan), 0, 1);
		$pdf->Cell(10, 1, '', 0, 1);
		$pdf->Cell(10, 7, 'No ', 1, 0, 'C');
		$pdf->Cell(80, 7, 'Nama ', 1, 0, 'C');
		$pdf->Cell(50, 7, 'Waktu ', 1, 0, 'C');
		$pdf->Cell(50, 7, 'Keterangan ', 1, 1, 'C');
		$no = 1;
		foreach ($data as $a) {
			$pdf->Cell(10, 7, $no++, 1, 0, 'C');
			$pdf->Cell(80, 7, $a->nama, 1, 0, 'C');
			$pdf->Cell(50, 7, $a->waktu, 1, 0, 'C');
			$pdf->Cell(50, 7, ucfirst($a->ket), 1, 1, 'C');
		}
		$pdf->Cell(10, 5, '', 0, 1, 'C');
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(170, 5, ucfirst($web->kabupaten) . ', ' . date('d-m-Y'), 0, 1, 'R');
		$pdf->Cell(190, 15, '', 0, 1, 'C');
		$pdf->Cell(160, 5, $web->author, 0, 1, 'R');


		$pdf->Output();
	}
	public function profile()
	{
		$data['web']	= $this->web;
		$data['data']	= $this->db->get_where('user', ['user_id' => $this->session->userdata('user_id')])->row();
		$data['title']	= 'Profile Pengguna';
		$data['body']	= 'admin/profile';
		$this->load->view('template', $data);
	}
	public function profile_update($id)
	{
		$usr = [
			'nama'	=> $this->input->post('nama'),
			'email'	=> $this->input->post('email'),
		];
		$this->db->update('user', $usr, ['user_id' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Update profile", "success");');
		redirect('admin/profile');
	}
	public function ganti_password()
	{
		$data['web']	= $this->web;
		$data['title']	= 'Ganti Password';
		$data['body']	= 'admin/ganti password';
		$this->load->view('template', $data);
	}
	public function password_update($id)
	{
		$p = $this->input->post();
		$cek = $this->db->get_where('user', ['user_id' => $id]);
		if ($cek->num_rows() > 0) {
			$a = $cek->row();
			if (md5($p['pw_lama']) == $a->password) {
				$this->db->update('user', ['password' => md5($p['pw_baru'])], ['user_id' => $id]);
				$this->session->set_flashdata('message', 'swal("Berhasil!", "Update password", "success");');
				redirect('admin/ganti_password');
			} else {
				$this->session->set_flashdata('message', 'swal("Ops!", "Password lama yang anda masukan salah", "error");');
				redirect('admin/ganti_password');
			}
		} else {
			$this->session->set_flashdata('message', 'swal("Ops!", "Anda harus login", "error");');
			redirect('/');
		}
	}
	//penggajian
	public function penggajian()
	{
		$data['list']	= $this->M_data->pegawai()->result();
		$data['gaji'] = $this->M_data->gaji()->result();
		$data['web']	= $this->web;
		$data['title']	= 'Penggajian Karyawan';
		$data['body']	= 'admin/penggajian';
		$this->load->view('template', $data);
	}
	public function penggajian_add($id)
	{
		$data['pegawai'] = $this->M_data->pegawai_add($id);
		$data['jumlah_hadir'] = $this->M_data->count_hadir_pegawai($id);
		$data['lembur'] = $this->M_data->count_lembur_pegawai($id);
		$data['web'] = $this->web;
		$data['title'] = 'Tambah Penggajian Karyawan';
		$data['body'] = 'admin/penggajian_add';
		$this->load->view('template', $data);
	}

	public function simpan_penggajian_add()
	{
		$p = $this->input->post();
		$kode_pegawai = $this->input->post('kode_pegawai');
		$hadir = $this->input->post('jumlahkehadiran');
		$lembur = $this->input->post('jumlahlembur');
		$id_gaji = rand(10000000, 99999999);
		$data = [
			'id_gaji'		=> $id_gaji,
			'kode_pegawai'		=> $kode_pegawai,
			'gaji_pokok'		=> $p['gajipokok'],
			'tunjangan'		=> $p['tunjangan'],
			'uang_makan'		=> $p['uangmakan'],
			'hadir'		=> $hadir,
			'lembur'		=> $lembur,
			'insentif'		=> $p['insentif'],
			'bpjs_kesehatan'		=> $p['kesehatan'],
			'bpjs_tkj'		=> $p['tenagakerja'],
			'vaksin'		=> $p['vaksin'],
			'sanksi'		=> $p['sanksi'],
			'thp'		=> $p['thp'],
		];

		$this->db->insert('gaji', $data);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Tambah Data Gaji Berhasil", "success");');

		redirect('admin/penggajian', $data);
	}



	public function penggajian_edit($id)
	{
		$data['web']	= $this->web;
		$data['detail']	= $this->M_data->pegawaiid($id)->row();
		$data['title']	= 'Update Data Penggajian';
		$data['body']	= 'admin/penggajian_edit';
		$this->load->view('template', $data);
	}
	public function penggajian_ubah($id)
	{

	}
	public function penggajian_hapus($id)
	{
		$this->db->delete('gaji', ['id_gaji' => $id]);
		$this->session->set_flashdata('message', 'swal("Berhasil!", "Delete Gaji", "success");');
		redirect('admin/penggajian');
	}
}
