<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

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
	public function index()
	{
		$this->load->view('api_index');
	}
	
	public function send()
	{
		$xmldata = file_get_contents('php://input');
		$xml=simplexml_load_string($xmldata) or die("*!* No data provided! *!*");
		
		// Load API Model
		$this->load->model('Storage_model');
		
		// Set System Data
		$SystemName 		= (string)$xml->system[0]->SystemName;               
		$SystemOS 			= (string)$xml->system[0]->OperatingSystemVersion;
		$SystemApiKey 		= (string)$xml->system[0]->ApiKey;
		$TestScenario 		= (string)$xml->system[0]->TestScenario;
		$Private	 		= (string)$xml->system[0]->Private;
		$Email		 		= (string)$xml->system[0]->Email;
		$TestDate 			= (string)$xml->system[0]->Date;
		$system['name'] 	= $SystemName;
		$system['os']		= $SystemOS;
		$system['api']		= $SystemApiKey;
		$system['private']	= $Private;
		$system['email']	= $Email;
		
		// Import System
		$sys_id = $this->Storage_model->insert_system($system);
		
		// Import Result Metrics
		foreach ($xml->data as $measurement) {		
			$metric['mbsec'] = (string)$measurement->MBSec;
			$metric['iops'] = (string)$measurement->IOPS;
			$metric['sizeiokbytes'] = (string)$measurement->SizeIOKBytes;
			$metric['latencyms'] = (string)$measurement->LatencyMS;
			$metric['outstandingios'] = (string)$measurement->OutStandingIOs;
			$metric['type'] = (string)$measurement->Type;
			$metric['target'] = (string)$measurement->Target;
			$metric['scenario'] = (string)$TestScenario;
			$metric['testname'] = (string)$measurement->Test;		
			$metric['unixdate'] = $TestDate;
			$metric['sysid'] = $sys_id;
			$this->Storage_model->insert_performance($metric);
		}
		
		echo "*** Data accepted ***";
	}
}
