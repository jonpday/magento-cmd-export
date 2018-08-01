<?php
/**
 * Import/Export from Shell
 *
 */
/**
 * Import/Export Script to run Import/Export profile
 * from command line or cron. 
 */
require_once 'abstract.php';

class Mage_Shell_Export extends Mage_Shell_Abstract {

	/** @var  Import/Export log file */
	protected $logFile;

	public function _construct() {
		$this->logFile = Mage::getBaseDir() . '/var/log/export_data.log';

		return parent::_construct();
	}

	public function run() {
		/** Magento Import/Export Profiles */
		$profileId = $this->getArg('profile');
		if ($profileId) {
			$this->log('Starting...');

			/** @var Mage_Dataflow_Model_Profile $profile */
			$profile = Mage::getModel('dataflow/profile');
			$profile->load($profileId);
			if (!$profile->getId()) {
				Mage::throwException('ERROR: Incorrect Profile for id ' . $profileId);
			}

			Mage::register('current_convert_profile', $profile);

			$profile->run();

			$batchModel = Mage::getSingleton('dataflow/batch');
			$this->log('Export Complete. ProfileID: ' . $profileId . '. BatchID: ' . $batchModel->getId());
		
		} else {
			echo $this->usageHelp();
		}
	}
	
	protected function log($msg) {
		echo $msg . "\n";
		Mage::log($msg, null, $this->logFile);
	}

	/**
	 * Retrieve Usage Help Message
	 */
	public function usageHelp()	{
		return <<<USAGE
Usage:  php -f export.php -- [options]

  --profile <identifier>            Profile ID from System > Import/Export > Profiles

USAGE;
	}

}

$shell = new Mage_Shell_Export();
$shell->run();
