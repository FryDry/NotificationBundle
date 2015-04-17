<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 22/02/15
 * Time: 14:47
 */

namespace FryDry\NotificationBundle\Model;


use FryDry\NotificationBundle\Model\User\UserInterface;

interface NotificationInterface {

	/**
	 * @return mixed
	 */
	public function getId();

	/**
	 *
	 * @return \DateTime
	 */
	public function getCreatedAt();

	/**
	 *
	 * @param \DateTime $date
	 * @return NotificationInterface
	 */
	public function setCreatedAt(\DateTime $date);

	/**
	 *
	 * @return string the message of the notification
	 */
	public function getMessage();

	/**
	 *
	 * @param string $message the message of the notification
	 * @return NotificationInterface
	 */
	public function setMessage($message);

	/**
	 *
	 * @return string
	 */
	public function getEntityClassName();

	/**
	 *
	 * @param string $entity
	 * @return NotificationInterface
	 */
	public function setEntityClassName($entity);

	/**
	 *
	 * @return mixed
	 */
	public function getEntityId();

	/**
	 *
	 * @param mixed $entityId
	 * @return NotificationInterface
	 */
	public function setEntityId($entityId);

	/**
	 *
	 * @return UserInterface
	 */
	public function getGenerator();

	/**
	 *
	 * @param UserInterface $user
	 * @return NotificationInterface
	 */
	public function setGenerator(UserInterface $user);

	/**
	 *
	 * @return UserInterface
	 */
	public function getRecipient();

	/**
	 *
	 * @param UserInterface $recipient
	 * @return NotificationInterface
	 */
	public function setRecipient(UserInterface $recipient);

	/**
	 *
	 * @return boolean
	 */
	public function isRead();

	/**
	 *
	 * @param boolean $read
	 * @return NotificationInterface
	 */
	public function setRead($read);

	/**
	 *
	 * @return string
	 */
	public function getMessageIconClass();

	/**
	 *
	 * @param string
	 * @return NotificationInterface
	 */
	public function setMessageIconClass($class);

	/**
	 * @return \DateInterval indicating how long ago the notification has been created
	 */
	public function getHowLongAgo();
}