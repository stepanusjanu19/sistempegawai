<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {


	public function index()
	{
		$data['web'] = $this->db->get('web')->row();
		$this->load->view('login',$data);
	}
	public function aksi_login()
	{
		$email = $this->input->post('email');
		$data = [
			'email'		=> $this->input->post('email'),
			'password'	=> md5($this->input->post('password'))
		];
		$cek = $this->db->get_where('user',$data);
		$cekemail = $this->M_data->getemail($email);
		if(empty($cekemail)){
			$this->session->set_flashdata('message', 'swal("Ops!", "Email yang anda masukkan tidak ada atau belum terdaftar", "error");');
			redirect('/');
		}else{
			if ($cek->num_rows() > 0 || $cek->password == md5($this->input->post('password'))) {
				$usr = $cek->row_array();
				$this->session->set_userdata( $usr );
				if ($usr['level'] == 'admin') {
					redirect('admin');
				}else if($usr['level'] == 'manajer'){
					redirect('manajer');
				}else{
					redirect('pegawai');
				}
			}
			else
			{
				$this->session->set_flashdata('message', 'swal("Ops!", "Email / Password yang anda masukan salah", "error");');
				redirect('/');
			}
		}


	}
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/');
	}
}
