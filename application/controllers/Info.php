<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct()
    {
		parent::__construct();
		$this->auth = new stdClass;
    } 
	public function index()
	{
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('info/info_landing');
		$this->load->view('tpl/tpl_footer');
	}
	public function about()
	{
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('info/info_about');
		$this->load->view('tpl/tpl_footer');
	}
	public function privacy()
	{
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('info/info_privacy');
		$this->load->view('tpl/tpl_footer');
	}
	public function legal()
	{
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('info/info_legal');
		$this->load->view('tpl/tpl_footer');
	}
	public function contact()
	{
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('info/info_contact');
		$this->load->view('tpl/tpl_footer');
	}
	public function pricing()
	{
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('info/info_pricing');
		$this->load->view('tpl/tpl_footer');
	}
}
