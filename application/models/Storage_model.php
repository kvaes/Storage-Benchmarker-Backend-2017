<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
* Name: Api Model
*
* Author: 
* Karim Vaes
* storage@kvaes.be
*
* Copyright 2017 Karim Vaes
* 
* Released: yes :D
* Build on: PHP7 or above and Codeigniter 3.0+
*/

use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\EdmType;

class Storage_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('azurestorage');
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Insert Metric METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * insert_metric
	 * @Input object
	 * 		$metric
	 *			['mbsec']
	 *			['iops']
	 *			['sizeiokbytes']
	 *			['latencyms']
	 *			['outstandingios']
	 *			['type']
	 *			['target']
	 * @Return 
	 *		nothing
	 *
	 * @Example
	 *
     * <MBSec>99MB/s</MBSec>
     *   <IOPS>300.1</IOPS>
     *   <SizeIOKBytes>8</SizeIOKBytes>
     *   <LatencyMS>5</LatencyMS>
     *   <OutStandingIOs>1</OutStandingIOs>
     *   <Type>Random</Type>
     *   <Target>C:\test</Target>
     *   <Test>SmallIO</Test>
     * </data>
	 */
	public function insert_performance($metric)
	{
		// Get connection string
		$Azurestorage = new Azurestorage;
		$connectionString = $Azurestorage->getConnectionString();
		
		// Create tables for in case they do not exist 
		$tableNameScenario="storageScenario";
		$Azurestorage->createTable($connectionString,$tableNameScenario);
		$tableNamePerformance="storagePerformance";
		$Azurestorage->createTable($connectionString,$tableNamePerformance);

		// Create table REST proxy.
		$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

		// Storing the performance details
		// Create new entity.
		$entity = new Entity();

		// PartitionKey and RowKey are required.
		$partitionKey = $metric['unixdate'];
		$rowKey = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		$entity->setPartitionKey($partitionKey);
		$entity->setRowKey($rowKey);

		// If entity exists, existing properties are updated with new values and
		// new properties are added. Missing properties are unchanged.
		$entity->addProperty("metric_mbsec", null, $metric['mbsec']);
		$entity->addProperty("metric_iops", null, $metric['iops']);
		$entity->addProperty("metric_sizeiokbytes", null, $metric['sizeiokbytes']);
		$entity->addProperty("metric_latencyms", null, $metric['latencyms']);
		$entity->addProperty("metric_outstandingios", null, $metric['outstandingios']);
		$entity->addProperty("metric_type", null, $metric['type']);
		$entity->addProperty("metric_target", null, $metric['target']);
		$entity->addProperty("metric_testname", null, $metric['testname']);
		$entity->addProperty("metric_scenario", null, $metric['scenario']);
		$entity->addProperty("metric_unixdate", null, $metric['unixdate']);
		$entity->addProperty("metric_sysid_fk", null, $metric['sysid']);

		try    {
			// Calling insertOrReplaceEntity, instead of insertOrMergeEntity as shown,
			// would simply replace the entity with PartitionKey "tasksSeattle" and RowKey "1".
			$tableRestProxy->insertOrMergeEntity($tableNamePerformance, $entity);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
			log_message('error', "$code - $error_message");
		}
		
		// Storing the scenario as a kind of index
		// Create new entity.
		$entityScenario = new Entity();

		// PartitionKey and RowKey are required.
		$entityScenario->setPartitionKey($metric['sysid']);
		$entityScenario->setRowKey($metric['unixdate']);

		// If entity exists, existing properties are updated with new values and
		// new properties are added. Missing properties are unchanged.
		$entityScenario->addProperty("metric_scenario", null, $metric['scenario']);

		try    {
			// Calling insertOrReplaceEntity, instead of insertOrMergeEntity as shown,
			// would simply replace the entity with PartitionKey "tasksSeattle" and RowKey "1".
			$tableRestProxy->insertOrMergeEntity($tableNameScenario, $entityScenario);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
			log_message('error', "$code - $error_message");
		}
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Insert System METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * insert_system
	 * @Input object
	 * 		$system
	 *			['name']
	 *			['os']
	 *			['api']
	 * @Return object
	 *		$system_sysid
	 *
	 * @Example
	 *
	 * <system>
	 *   <SystemName>$systemname</SystemName>
	 *   <OperatingSystemVersion>$operatingsystem</OperatingSystemVersion>
	 *   <TestScenario>Scenario01</TestScenario>
	 *   <Date>$unixdate</Date>
	 * </system>
	 */
	
	public function insert_system($system)
	{	
		// Get connection string
		$Azurestorage = new Azurestorage;
		$connectionString = $Azurestorage->getConnectionString();
		
		// Create table for in case it did not exist
		$tableName="storageSystem";
		$Azurestorage->createTable($connectionString,$tableName);
		
		// Create table REST proxy.
		$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

		//Create new entity.
		$entity = new Entity();

		// PartitionKey and RowKey are required.
		if ($system['private'] <> 'False') {
			$private="Private";
		} else {
			$private="Public";
		}
		$entity->setPartitionKey($private);
		$entity->setRowKey($system['name']);

		// If entity exists, existing properties are updated with new values and
		// new properties are added. Missing properties are unchanged.
		$entity->addProperty("system_name", null, $system['name']);
		$entity->addProperty("system_os", null, $system['os']);
		$entity->addProperty("system_api_key", null, $system['api']);
		$entity->addProperty("system_private", null, $system['private']);
		$entity->addProperty("system_email", null, $system['email']);

		try    {
			// Calling insertOrReplaceEntity, instead of insertOrMergeEntity as shown,
			// would simply replace the entity with PartitionKey "tasksSeattle" and RowKey "1".
			$tableRestProxy->insertOrMergeEntity($tableName, $entity);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
			log_message('error', "$code - $error_message");
		}
		
		// Return Primary Key
		return $system['name'];
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Find System METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * find_system
	 * @Input object
	 * 		$system
	 *			['name']
	 *			['api']
	 * @Return object
	 *		$system_sysid
	 *
	 */
	
	public function find_system($system)
	{
		// this used to mean something, now it's just a water carrier
		return $system['name'];
	}
		
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Validate API Key METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * validate_api_key
	 * @Input object
	 * 		$system
	 *			['name']
	 *			['api']
	 * @Return object
	 *		True/False
	 *
	 */
	
	public function validate_api_key($system)
	{
		return true; //temp
		
		$filter = array('system_api_key' => $system['api']);
		$this->db->where($filter);
		$this->db->limit(1);
		$query = $this->db->get('storage_system');
		$row = $query->row();
		if ($row->system_sysid < 1) {
			return false;
		} else {
			return true;
		}
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// List Private Systems METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * list_private_systems
	 * @Input object
	 * 		Email
	 * @Return object
	 *		$query
	 *
	 */
	
	public function list_private_systems($email)
	{
		$filter = array('system_email' => $email);
		$this->db->where($filter);
		$this->db->select('system_name');
		$query = $this->db->get('storage_system');
		return $query;
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// List Systems METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * list_systems
	 * @Input object
	 * 		None
	 * @Return object
	 *		$query
	 *
	 */
	
	public function list_systems()
	{
		// Get connection string
		$Azurestorage = new Azurestorage;
		$connectionString = $Azurestorage->getConnectionString();
		
		// Create table for in case it did not exist
		$tableName="storageSystem";
		
		// Create table REST proxy.
		$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

		$filter = "PartitionKey eq 'Public'";

		try    {
			$result = $tableRestProxy->queryEntities($tableName, $filter);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}

		$entities = $result->getEntities();

		foreach($entities as $entity){
			$sys_name = trim($entity->getRowKey());
			$systems[$sys_name] = $sys_name;
		}
		return $systems;
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// List Results METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * list_results
	 * @Input object
	 * 		$system_name
	 * @Return object
	 *		$query => 'metric_unixdate','metric_scenario'
	 *
	 */
	
	public function list_results($system_name)
	{
		// Get connection string
		$Azurestorage = new Azurestorage;
		$connectionString = $Azurestorage->getConnectionString();
		
		// Create table for in case it did not exist
		$tableName="storageScenario";
		
		// Create table REST proxy.
		$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

		$filter = "PartitionKey eq '". $system_name ."'";

		try    {
			$result = $tableRestProxy->queryEntities($tableName, $filter);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}

		$entities = $result->getEntities();

		foreach($entities as $entity){
			$unixdate = $entity->getRowKey();
			$results[$unixdate]['unixdate'] = $unixdate;
			$results[$unixdate]['scenario'] = $entity->getProperty("metric_scenario")->getValue();	
		}

		return $results;
	}
	
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// List Details METHOD
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	/**
	 * list_results
	 * @Input object
	 * 		$system_name, $unix_date
	 * @Return object
	 *		$query 
	 *
	 */
	
	public function list_details($system_name,$unix_date=null)
	{
		// Get connection string
		$Azurestorage = new Azurestorage;
		$connectionString = $Azurestorage->getConnectionString();
		
		// Create table for in case it did not exist
		$tableName="storagePerformance";
		
		// Create table REST proxy.
		$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);

		$filter = "PartitionKey eq '". $unix_date ."'";

		try    {
			$result = $tableRestProxy->queryEntities($tableName, $filter);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages are here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			echo $code.": ".$error_message."<br />";
		}

		$entities = $result->getEntities();

		$count=0;
		foreach($entities as $entity){
			$count++;
			$key = $count;
			$results[$key]['metric_sysid_fk'] = $system_name;
			$results[$key]['metric_unixdate'] = $unix_date;
			$results[$key]['metric_scenario'] = $entity->getProperty("metric_scenario")->getValue();
			$results[$key]['metric_mbsec'] = $entity->getProperty("metric_mbsec")->getValue();
			$results[$key]['metric_iops'] = $entity->getProperty("metric_iops")->getValue();
			$results[$key]['metric_sizeiokbytes'] = $entity->getProperty("metric_sizeiokbytes")->getValue();
			$results[$key]['metric_latencyms'] = $entity->getProperty("metric_latencyms")->getValue();
			$results[$key]['metric_outstandingios'] = $entity->getProperty("metric_outstandingios")->getValue();
			$results[$key]['metric_type'] = $entity->getProperty("metric_type")->getValue();
			$results[$key]['metric_target'] = $entity->getProperty("metric_target")->getValue();
			$results[$key]['metric_testname'] = $entity->getProperty("metric_testname")->getValue();
		}
		return $results;
	}
	
}

/* End of file Api_model.php */
/* Location: ./application/model/Api_model.php */
