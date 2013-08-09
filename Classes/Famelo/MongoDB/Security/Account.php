<?php
namespace Famelo\MongoDB\Security;

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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Account implements \TYPO3\Flow\Security\AccountInterface {
	/**
	 * @var string
	 * @ODM\Id
	 */
	protected $id;

	/**
	 * @var string
	 * @ODM\String
	 */
	protected $accountIdentifier;

	/**
	 * @var string
	 * @ODM\String
	 */
	protected $authenticationProviderName;

	/**
	 * @var string
	 * @ODM\String
	 */
	protected $credentialsSource;

	/**
	 * @var \TYPO3\Party\Domain\Model\AbstractParty
	 */
	protected $party;

	/**
	 * @var \DateTime
	 * @ODM\Date
	 */
	protected $creationDate;

	/**
	 * @var \DateTime
	 * @ODM\Date
	 */
	protected $expirationDate;

	/**
	 */
	protected $roles = array('Administrator');

	/**
	 * Upon creation the creationDate property is initialized.
	 */
	public function __construct() {
		$this->creationDate = new \DateTime();
		$this->roles = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Returns the account identifier
	 *
	 * @return string The account identifier
	 */
	public function getAccountIdentifier() {
		return $this->accountIdentifier;
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set the account identifier
	 *
	 * @param string $accountIdentifier The account identifier
	 * @return void
	 */
	public function setAccountIdentifier($accountIdentifier) {
		$this->accountIdentifier = $accountIdentifier;
	}

	/**
	 * Returns the authentication provider name this account corresponds to
	 *
	 * @return string The authentication provider name
	 */
	public function getAuthenticationProviderName() {
		return $this->authenticationProviderName;
	}

	/**
	 * Set the authentication provider name this account corresponds to
	 *
	 * @param string $authenticationProviderName The authentication provider name
	 * @return void
	 */
	public function setAuthenticationProviderName($authenticationProviderName) {
		$this->authenticationProviderName = $authenticationProviderName;
	}

	/**
	 * Returns the credentials source
	 *
	 * @return mixed The credentials source
	 */
	public function getCredentialsSource() {
		return $this->credentialsSource;
	}

	/**
	 * Sets the credentials source
	 *
	 * @param mixed $credentialsSource The credentials source
	 * @return void
	 */
	public function setCredentialsSource($credentialsSource) {
		$this->credentialsSource = $credentialsSource;
	}

	/**
	 * Returns the party object this account corresponds to
	 *
	 * @return \TYPO3\Party\Domain\Model\AbstractParty The party object
	 */
	public function getParty() {
		return $this->party;
	}

	/**
	 * Sets the corresponding party for this account
	 *
	 * @param \TYPO3\Party\Domain\Model\AbstractParty $party The party object
	 * @return void
	 */
	public function setParty(\TYPO3\Party\Domain\Model\AbstractParty $party) {
		$this->party = $party;
	}

	/**
	 * Returns the roles this account has assigned
	 *
	 * @return array<\TYPO3\Flow\Security\Policy\Role> The assigned roles, indexed by role identifier
	 */
	public function getRoles() {
		$roles = array();
		foreach ($this->roles->toArray() as $role) {
			$roles[$role->getIdentifier()] = $role;
		}
		return $roles;
	}

	/**
	 * Sets the roles for this account
	 *
	 * @param array|\Doctrine\Common\Collections\Collection $roles A Collection of TYPO3\Flow\Security\Policy\Role objects
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setRoles($roles) {
		if ($roles instanceof Collection) {
			$this->roles = clone $roles;
		} elseif (is_array($roles)) {
			$this->roles->clear();
			foreach ($roles as $role) {
				$this->roles->add($role);
			}
		} else {
			throw new \InvalidArgumentException(sprintf('setRoles() expects an array or Doctrine Collection, %s given.', is_object($roles) ? get_class($roles) : gettype($roles)), 1366103284);
		}
	}

	/**
	 * Return if the account has a certain role
	 *
	 * @param \TYPO3\Flow\Security\Policy\Role $role
	 * @return boolean
	 */
	public function hasRole(\TYPO3\Flow\Security\Policy\Role $role) {
		return $this->roles->contains($role);
	}

	/**
	 * Adds a role to this account
	 *
	 * @param \TYPO3\Flow\Security\Policy\Role $role
	 * @return void
	 */
	public function addRole(\TYPO3\Flow\Security\Policy\Role $role) {
		if (!$this->hasRole($role)) {
			$this->roles->add($role);
		}
	}

	/**
	 * Removes a role from this account
	 *
	 * @param \TYPO3\Flow\Security\Policy\Role $role
	 * @return void
	 */
	public function removeRole(\TYPO3\Flow\Security\Policy\Role $role) {
		if ($this->hasRole($role)) {
			$this->roles->removeElement($role);
		}
	}

	/**
	 * @return \DateTime
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getExpirationDate() {
		return $this->expirationDate;
	}

	/**
	 * @param \DateTime $expirationDate
	 * @return void
	 */
	public function setExpirationDate(\DateTime $expirationDate = NULL) {
		$this->expirationDate = $expirationDate;
	}

}

?>