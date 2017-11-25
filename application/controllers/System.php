<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System extends CI_Controller {

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
		$this->load->helper('url');
		
		// Load Storage Model
		$this->load->model('Storage_model');
		
    } 
	public function index()
	{
		$data['systems'] = $this->Storage_model->list_systems();
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('system/system_index',$data);
		$this->load->view('tpl/tpl_footer');
	}
	public function private()
	{
		$data['systems'] = $this->Storage_model->list_systems();
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('system/system_private',$data);
		$this->load->view('tpl/tpl_footer');
	}
	public function details()
	{
		$data['system_name'] = $this->uri->segment(3);
		$data['results'] = $this->Storage_model->list_results($data['system_name']);
		$header['script'] = "";
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('system/system_details',$data);
		$this->load->view('tpl/tpl_footer');
	}
	
	private function fix_string($string) {
		$length = strlen($string);
		return substr($string, 0, $length -1)."]";
	}
	
	public function compare()
	{
		$data['system_name'] = $this->uri->segment(3);
		$data['results'] = $this->Storage_model->list_details($data['system_name']);
		
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		foreach ($data['results']->result() as $row)
		{
			$results[$row->metric_testname]['metrics'][$row->metric_unixdate]++;
			if ($results[$row->metric_testname]['metrics'][$row->metric_unixdate] <> 1) { 
				$divider[$row->metric_testname][$row->metric_unixdate] = ",";
			}
			$results[$row->metric_testname]['mbsec'][$row->metric_unixdate] 			.= $divider[$row->metric_testname][$row->metric_unixdate].$row->metric_mbsec;
			$results[$row->metric_testname]['iops'][$row->metric_unixdate] 				.= $divider[$row->metric_testname][$row->metric_unixdate].$row->metric_iops;
			$results[$row->metric_testname]['latencyms'][$row->metric_unixdate]	 		.= $divider[$row->metric_testname][$row->metric_unixdate].$row->metric_latencyms;
			$results[$row->metric_testname]['outstandingios'][$row->metric_unixdate] 	.= $divider[$row->metric_testname][$row->metric_unixdate].$row->metric_outstandingios;
			$results[$row->metric_testname]['sizeiokbytes'] 							= $row->metric_sizeiokbytes;
			$results[$row->metric_testname]['type'] 									= $row->metric_type;
			$results[$row->metric_testname]['target'] 									= $row->metric_target;
			$data['target'] 															= $row->metric_target;
			$results[$row->metric_testname]['scenario'][$row->metric_unixdate]			= $row->metric_scenario;
			$data['scenario'] 															= $row->metric_scenario;
			$results[$row->metric_testname]['name'] 									= strtolower(str_replace('-', '',str_replace(' ', '', $row->metric_testname)));
			$results[$row->metric_testname]['title'] 									= $row->metric_testname;
			
		}
		
		$data['results'] = $results;
		
		$script1 = "
		<script type=\"text/javascript\" src=\"https://cdn.zingchart.com/zingchart.min.js\"></script>";

		$script2 = "<script class=\"code\" type=\"text/javascript\">
			$(document).ready(function(){ ";
			
		foreach($results as $test) {
			$script2mbsec = '"series":[';
			$script2iops = '"series":[';
			$script2latencyms = '"series":[';
			$script2outstandingio = '"series":[';
			foreach ($test['metrics'] as $unix_date=>$count) {
				$script2mbsec .= '
				{ 
					"values":['.$test['mbsec'][$unix_date].'], "text": "'.$test['scenario'][$unix_date].'"
				},';
				$script2iops .= '
				{ 
					"values":['.$test['iops'][$unix_date].'], "text": "'.$test['scenario'][$unix_date].'"
				},';
				$script2latencyms .= '
				{ 
					"values":['.$test['latencyms'][$unix_date].'], "text": "'.$test['scenario'][$unix_date].'"
				},';
				$script2outstandingio .= '
				{ 
					"values":['.$test['outstandingios'][$unix_date].'], "text": "'.$test['scenario'][$unix_date].'"
				},';
				$testname = $test['name'];
				$xaxis[$testname] = $test['outstandingios'][$unix_date];
			}
			
			$script2mbsec = $this->fix_string($script2mbsec);
			$script2iops = $this->fix_string($script2iops);
			$script2latencyms = $this->fix_string($script2latencyms);
			$script2outstandingio = $this->fix_string($script2outstandingio);
		}

		$script2post = ",
				\"legend\": {
					\"layout\": \"float\",
					\"margin\": \"85% auto auto auto\",
					\"shadow\": 0,
					\"marker\": {
						\"type\": \"match\",
						\"show-line\": true,
						\"line-width\": 4,
						\"shadow\": \"none\"
					}
				}
		";
		
		foreach ($results as $test) {
			$testname = $test['name'];
			$testname = str_replace("%", "", $testname);
			$script2 .= "
			  
			  var ".$testname."mbsec={
				\"type\": \"line\",
				\"crosshair-x\":{},
				".$script2mbsec.$script2post.",
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$xaxis[$testname]."],
				},
				\"scale-y\":{ 
					\"label\":{ \"text\":\"Bandwidth in MB/s\" }, 
				}
			  };
			  zingchart.render({
				id:'".$testname."mbsec',
				height:400,
				width:'100%',
				data:".$testname."mbsec
			  });
			  var ".$testname."iops={
				\"type\": \"line\",
				\"crosshair-x\":{},
				".$script2iops.$script2post.",
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$xaxis[$testname]."],
				},
				\"scale-y\":{ 
					\"label\":{ \"text\":\"Input Output Operations per Second (IOPS)\" }, 
					\"markers\":[
						{
							\"type\":\"area\",
							\"range\":[75,100],
							\"background-color\":\"LightSteelBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"7.2k SATA Disk\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[125,150],
							\"background-color\":\"LightSlateGray\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"10k SAS/SATA Disk\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[175,210],
							\"background-color\":\"LightBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"15k SAS/FC Disk\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[285,315],
							\"background-color\":\"DeepSkyBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure Single Disk (Basic)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[485,515],
							\"background-color\":\"LightSkyBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure Single Disk (Standard) or P10 Disk (Premium)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[2290,2310],
							\"background-color\":\"CornflowerBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure P20 (Premium) or Azure DS1 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[4990,5010],
							\"background-color\":\"DodgerBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure P30 (Premium)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[6390,6410],
							\"background-color\":\"MediumOrchid\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS11/DS2 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[12790,12810],
							\"background-color\":\"BlueViolet\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS12/DS3 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[19990,20010],
							\"background-color\":\"DeepPink\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure Storage Account (Standard)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[19990,20010],
							\"background-color\":\"DarkViolet\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS13/DS4 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[19990,20010],
							\"background-color\":\"DarkOrchid\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS14 (Limit)\",
							}
						},
					]
				}
			  };
			  zingchart.render({
				id:'".$testname."iops',
				height:400,
				width:'100%',
				data:".$testname."iops
			  });
			  var ".$testname."latency={
				\"type\": \"line\",
				\"crosshair-x\":{},
				".$script2latencyms.$script2post.",
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$xaxis[$testname]."],
				},
				\"scale-y\":{ 
					\"label\":{ \"text\":\"Latency in ms\" }, 
					\"markers\":[
						{
							\"type\":\"area\",
							\"range\":[0,5],
							\"background-color\":\"green\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Perfect\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[5,10],
							\"background-color\":\"blue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Attention\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[10,20],
							\"background-color\":\"yellow\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Warning\",
							}
						},
						
						{
							\"type\":\"area\",
							\"range\":[20,30],
							\"background-color\":\"yellow\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Warning\",
							}
						},
					]
				}
			  };
			  zingchart.render({
				id:'".$testname."latency',
				height:400,
				width:'100%',
				data:".$testname."latency
			  });
			  var ".$testname."outstanding={
				\"type\": \"line\",
				\"crosshair-x\":{},
				".$script2outstandingio.$script2post.",
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$xaxis[$testname]."],
				},
				\"scale-y\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" }, 
				}
			  };
			  zingchart.render({
				id:'".$testname."outstanding',
				height:400,
				width:'100%',
				data:".$testname."outstanding
			  });
			  
			";
		}

		$script3=	"});
		</script>";
			
		$script = $script1.$script2.$script3;
		
		$header['script'] = $script;
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('system/system_results',$data);
		$this->load->view('tpl/tpl_footer');
	}
	
	public function results()
	{
		$data['system_name'] = $this->uri->segment(3);
		$data['unix_date'] = $this->uri->segment(4);
		$data['results'] = $this->Storage_model->list_details($data['system_name'],$data['unix_date']);
		
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		foreach ($data['results'] as $row)
		{		
			$key = $row['metric_testname'];
			$results[$key]['metrics']++;
			if ($results[$key]['metrics'] <> 1) { 
				$divider[$key] = ",";
			}
			$results[$key]['mbsec'] 			.= $divider[$key].$row['metric_mbsec'];
			$results[$key]['iops'] 				.= $divider[$key].$row['metric_iops'];
			$results[$key]['latencyms']	 		.= $divider[$key].$row['metric_latencyms'];
			$results[$key]['outstandingios'] 	.= $divider[$key].$row['metric_outstandingios'];
			$results[$key]['sizeiokbytes'] 		= $row['metric_sizeiokbytes'];
			$results[$key]['type'] 				= $row['metric_type'];
			$results[$key]['target'] 			= $row['metric_target'];
			$data['target'] 					= $row['metric_target'];
			$results[$key]['scenario'] 			= $row['metric_scenario'];
			$data['scenario'] 					= $row['metric_scenario'];
			$results[$key]['name'] 				= strtolower(str_replace('-', '',str_replace(' ', '', $row['metric_testname'])));
			$results[$key]['title'] 			= $row['metric_testname'];
		}
		
		$data['results'] = $results;
		
		$script1 = "
		<script type=\"text/javascript\" src=\"https://cdn.zingchart.com/zingchart.min.js\"></script>";

		$script2 = "<script class=\"code\" type=\"text/javascript\">
			$(document).ready(function(){ ";
			
		foreach ($results as $test) {
			$testname = $test['name'];
			$testname = str_replace("%", "", $testname);
			$script2 .= "
			  
			  var ".$testname."mbsec={
				\"type\": \"area\",
				\"series\": [
				{ \"values\": [".$test['mbsec']."] }
				],
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$test['outstandingios']."],
				},
				\"scale-y\":{ \"label\":{ \"text\":\"Bandwidth in MB/s\" } }
			  };
			  zingchart.render({
				id:'".$testname."mbsec',
				height:400,
				width:'100%',
				data:".$testname."mbsec
			  });
			  var ".$testname."iops={
				\"type\": \"area\",
				\"series\": [
				{ \"values\": [".$test['iops']."] }
				],
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$test['outstandingios']."],
				},
				\"scale-y\":{ 
					\"label\":{ \"text\":\"Input Output Operations per Second (IOPS)\" }, 
					\"markers\":[
						{
							\"type\":\"area\",
							\"range\":[75,100],
							\"background-color\":\"LightSteelBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"7.2k SATA Disk\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[125,150],
							\"background-color\":\"LightSlateGray\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"10k SAS/SATA Disk\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[175,210],
							\"background-color\":\"LightBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"15k SAS/FC Disk\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[285,315],
							\"background-color\":\"DeepSkyBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure Single Disk (Basic)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[485,515],
							\"background-color\":\"LightSkyBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure Single Disk (Standard) or P10 Disk (Premium)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[2290,2310],
							\"background-color\":\"CornflowerBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure P20 (Premium) or Azure DS1 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[4990,5010],
							\"background-color\":\"DodgerBlue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure P30 (Premium)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[6390,6410],
							\"background-color\":\"MediumOrchid\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS11/DS2 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[12790,12810],
							\"background-color\":\"BlueViolet\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS12/DS3 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[19990,20010],
							\"background-color\":\"DeepPink\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure Storage Account (Standard)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[19990,20010],
							\"background-color\":\"DarkViolet\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS13/DS4 (Limit)\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[19990,20010],
							\"background-color\":\"DarkOrchid\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Azure DS14 (Limit)\",
							}
						},
					]
				}
			  };
			  zingchart.render({
				id:'".$testname."iops',
				height:400,
				width:'100%',
				data:".$testname."iops
			  });
			  var ".$testname."latency={
				\"type\": \"area\",
				\"series\": [
				{ \"values\": [".$test['latencyms']."] }
				],
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$test['outstandingios']."],
				},
				\"scale-y\":{ 
					\"label\":{ \"text\":\"Latency in ms\" }, 
					\"markers\":[
						{
							\"type\":\"area\",
							\"range\":[0,5],
							\"background-color\":\"green\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Perfect\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[5,10],
							\"background-color\":\"blue\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Attention\",
							}
						},
						{
							\"type\":\"area\",
							\"range\":[10,20],
							\"background-color\":\"yellow\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Warning\",
							}
						},
						
						{
							\"type\":\"area\",
							\"range\":[20,30],
							\"background-color\":\"yellow\",
							\"alpha\":0.5,
							\"label\":{
									\"text\":\"Warning\",
							}
						},
					]
				}
			  };
			  zingchart.render({
				id:'".$testname."latency',
				height:400,
				width:'100%',
				data:".$testname."latency
			  });
			  var ".$testname."outstanding={
				\"type\": \"line\",
				\"series\": [
				{ \"values\": [".$test['outstandingios']."] }
				],
				\"scale-x\":{ 
					\"label\":{ \"text\":\"Outstanding IO\" },
					\"values\": [".$test['outstandingios']."],
				},
				\"scale-y\":{ \"label\":{ \"text\":\"Outstanding IO\" } }
			  };
			  zingchart.render({
				id:'".$testname."outstanding',
				height:400,
				width:'100%',
				data:".$testname."outstanding
			  });
			  
			";
		}
		$script3=	"});
		</script>";
			
		$script = $script1.$script2.$script3;
		
		$header['script'] = $script;
		$this->load->view('tpl/tpl_header',$header);
		$this->load->view('system/system_results',$data);
		$this->load->view('tpl/tpl_footer');
	}
}
