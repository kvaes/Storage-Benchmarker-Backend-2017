<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once __DIR__ . '/azurestorage/vendor/autoload.php';
use MicrosoftAzure\Storage\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Queue\Models\CreateQueueOptions;
use MicrosoftAzure\Storage\Queue\Models\PeekMessagesOptions;
use MicrosoftAzure\Storage\Table\Models\Entity;
use MicrosoftAzure\Storage\Table\Models\EdmType;

class Azurestorage
{
    public function __construct()
    {
        // nothing to see here
	}
	public function getConnectionString()
	{
		$storageaccountkey = getenv('STORAGE_ACCOUNT_KEY');
		$storageaccountname = getenv('STORAGE_ACCOUNT_NAME');
		return "DefaultEndpointsProtocol=https;AccountName=$storageaccountname;AccountKey=$storageaccountkey";
	}
	
	public function createTable($connectionString, $tableName)
	{
		// Create table REST proxy.
		$tableRestProxy = ServicesBuilder::getInstance()->createTableService($connectionString);
		try    {
			// Create table.
			$tableRestProxy->createTable($tableName);
		}
		catch(ServiceException $e){
			// Handle exception based on error codes and messages.
			// Error codes and messages can be found here:
			// http://msdn.microsoft.com/library/azure/dd179438.aspx
			$code = $e->getCode();
			$error_message = $e->getMessage();
			log_message('error', "$code - $error_message");
		}
	}
	
}