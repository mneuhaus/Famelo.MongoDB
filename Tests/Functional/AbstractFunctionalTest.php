<?php
namespace Famelo\MongoDB\Tests\Functional;

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

/**
 * Abstract functional test class, setting up a DocumentManager and httpClient
 * object for usage in functional tests.
 */
abstract class AbstractFunctionalTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	 * @var \Famelo\MongoDB\Persistence\DocumentManagerFactory
	 */
	protected $documentManagerFactory;

	/**
	 * @var \Doctrine\ODM\MongoDB\DocumentManager
	 */
	protected $documentManager;

	/**
	 * @var string
	 */
	protected $databaseName = 'doctrine_sandbox';

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Set up test
	 */
	public function setUp() {
		parent::setUp();

		$this->settings = $this->objectManager->getSettingsByPath(array('Radmiraal', 'CouchDB', 'persistence', 'backendOptions'));

		$this->documentManagerFactory = $this->objectManager->get('\Famelo\MongoDB\Persistence\DocumentManagerFactory');
		$this->documentManager = $this->documentManagerFactory->create();

		$mongoDbHelper = new \Famelo\MongoDB\MongoDBHelper();
		$mongoDbHelper->injectSettings($this->objectManager->getSettingsByPath(array('Radmiraal', 'CouchDB')));
		$mongoDbHelper->injectDocumentManagerFactory($this->documentManagerFactory);
		$mongoDbHelper->createDatabaseIfNotExists();
		$mongoDbHelper->createOrUpdateDesignDocuments();
	}

	/**
	 * Clean up database after running tests
	 */
	public function tearDown() {
		parent::tearDown();
		if (isset($this->documentManager)) {
			$this->documentManager->getHttpClient()->request('DELETE', '/' . $this->settings['databaseName']);
		}
	}

}

?>