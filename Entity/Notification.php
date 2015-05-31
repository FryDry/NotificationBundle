<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 22/02/15
 * Time: 14:37
 */

namespace FryDry\NotificationBundle\Entity;


use FryDry\NotificationBundle\Model\NotificationInterface;
use FryDry\NotificationBundle\Model\User\UserInterface;

class Notification implements NotificationInterface {

	/**
	 * @var mixed
	 */
	protected $id;

	/**
	 * @var UserInterface
	 */
	protected $generator;

	/**
	 * @var UserInterface
	 */
	protected $recipient;

	/**
	 * @var string
	 */
	protected $entityClassName;

	/**
	 * @var mixed
	 */
	protected $entityId;

	/**
	 * @var \DateTime
	 */
	protected $createdAt;

	/**
	 * @var boolean
	 */
	protected $read;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $messageIconClass;

	/**
	 * @var string
	 */
	protected $redirectUrl;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return UserInterface
	 */
	public function getGenerator()
	{
		return $this->generator;
	}

	/**
	 * @param UserInterface $generator
	 * @return Notification
	 */
	public function setGenerator(UserInterface $generator)
	{
		$this->generator = $generator;
		return $this;
	}

	/**
	 * @return UserInterface
	 */
	public function getRecipient()
	{
		return $this->recipient;
	}

	/**
	 * @param UserInterface $recipient
	 * @return Notification
	 */
	public function setRecipient(UserInterface $recipient)
	{
		$this->recipient = $recipient;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEntityClassName()
	{
		return $this->entityClassName;
	}

	/**
	 * @param string $entityClassName
	 * @return Notification
	 */
	public function setEntityClassName($entityClassName)
	{
		$this->entityClassName = $entityClassName;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEntityId()
	{
		return $this->entityId;
	}

	/**
	 * @param mixed $entityId
	 * @return Notification
	 */
	public function setEntityId($entityId)
	{
		$this->entityId = $entityId;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 * @return Notification
	 */
	public function setCreatedAt(\DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isRead()
	{
		return $this->read;
	}

	/**
	 * @param boolean $read
	 * @return Notification
	 */
	public function setRead($read)
	{
		$this->read = $read;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 * @return Notification
	 */
	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMessageIconClass()
	{
		return $this->messageIconClass;
	}

	/**
	 * @param mixed $messageIconClass
	 * @return Notification
	 */
	public function setMessageIconClass($messageIconClass)
	{
		$this->messageIconClass = $messageIconClass;
		return $this;
	}

	/**
	 * @return \DateInterval indicating how long ago the notification has been created
	 */
	public function getHowLongAgo()
	{
		if ($this->getCreatedAt() !== null) {
			$now = new \DateTime();
			$interval = $now->diff($this->getCreatedAt());
		} else {
			$interval = \DateInterval::createFromDateString(date('Y-m-d H:i:s'));
		}

		return $interval;
	}

	public function setDefaults()
	{
		$this->setRead(false);
		$this->setCreatedAt(new \DateTime());

	}
}
