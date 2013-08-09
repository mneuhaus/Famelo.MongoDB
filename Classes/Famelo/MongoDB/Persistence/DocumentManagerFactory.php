<?php
namespace Famelo\MongoDB\Persistence;

/*                                                                        *
 * This script belongs to the Flow package "Famelo.MongoDB".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

/**
 * Factory for creating Doctrine ODM DocumentManager instances
 * @Flow\Scope("singleton")
 */
class DocumentManagerFactory {

	/**
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 * @Flow\Inject
	 */
	protected $packageManager;

	/**
	 * @var \TYPO3\Flow\Utility\Environment
	 * @Flow\Inject
	 */
	protected $environment;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @var \Doctrine\ODM\MongoDB\DocumentManager
	 */
	protected $documentManager;

	/**
	 * @return void
	 */
	public function initializeObject() {
		$settings = $this->configurationManager->getConfiguration(
			\TYPO3\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
			'Famelo.MongoDB'
		);
		$this->settings = array_merge(array('host' => 'localhost', 'port' => 27017), $settings['persistence']['backendOptions']);
	}

	/**
	 * Creates a Doctrine ODM DocumentManager
	 *
	 * @return \Doctrine\ODM\MongoDB\DocumentManager
	 */
	public function create() {
		if (isset($this->documentManager)) {
			return $this->documentManager;
		}

		$config = new Configuration();

		$proxyDirectory = \TYPO3\Flow\Utility\Files::concatenatePaths(array($this->environment->getPathToTemporaryDirectory(), 'DoctrineODM/Proxies'));
		\TYPO3\Flow\Utility\Files::createDirectoryRecursively($proxyDirectory);
		$config->setProxyDir($proxyDirectory);
		$config->setProxyNamespace('TYPO3\Flow\Persistence\DoctrineODM\Proxies');
		$config->setAutoGenerateProxyClasses(TRUE);

		$hydratorDir = \TYPO3\Flow\Utility\Files::concatenatePaths(array($this->environment->getPathToTemporaryDirectory(), 'DoctrineODM/Hydrators'));
		\TYPO3\Flow\Utility\Files::createDirectoryRecursively($hydratorDir);
		$config->setHydratorDir($hydratorDir);
		$config->setHydratorNamespace('TYPO3\Flow\Persistence\DoctrineODM\Hydrators');

		$reader = new \Doctrine\Common\Annotations\AnnotationReader();
		$metaDriver = new \Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver($reader);

		$config->setMetadataDriverImpl($metaDriver);

		$config->setDefaultDB($this->settings['databaseName']);

		$server = 'mongodb://' . $this->settings['host'] . ':' . $this->settings['port'];
		$connection = new Connection(
			$server,
			$this->settings
		);

		$this->documentManager = \Doctrine\ODM\MongoDB\DocumentManager::create($connection, $config);

		return $this->documentManager;
	}

}

?>