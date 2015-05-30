<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 22/02/15
 * Time: 16:58
 */

namespace FryDry\NotificationBundle\EventListener;


use FryDry\NotificationBundle\Entity\Notification;
use FryDry\NotificationBundle\Model\NotifiableObjectInterface;
use FryDry\NotificationBundle\Model\User\UserInterface;
use FryDry\NotificationBundle\NotificationEvent;
use FryDry\NotificationBundle\NotificationEvents;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;

class NotifiableObjectListener {

	/**
	 * @var string
	 */
	protected $notificationClass;

	/**
	 * @var NotifiableObjectInterface
	 */
	protected $entity;

	/**
	 * @var EntityManagerInterface
	 */
	protected $em;

	/**
	 * @var Notification
	 */
	protected $notification;

	public function __construct($notificationClass)
	{
		$this->notificationClass = $notificationClass;
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		$this->entity = $args->getEntity();
		$this->em = $args->getEntityManager();

		if ($this->entity instanceof NotifiableObjectInterface && $this->entity->getNotifier() && $this->entity->getNotificationRecipients()) {

			switch (true) {
				case $this->entity->getNotificationRecipients() instanceof Collection:
				case is_array($this->entity->getNotificationRecipients()):
					foreach ($this->entity->getNotificationRecipients() as $recipient) {
						$this->buildNotificationForRecipient($recipient);
					}
					break;
				case $this->entity->getNotificationRecipients() instanceof UserInterface:
					$this->buildNotificationForRecipient($this->entity->getNotificationRecipients());
					break;
			}

			$this->em->flush();

		}
	}

	protected function buildNotificationForRecipient(UserInterface $recipient) {

		$this->notification = new $this->notificationClass;

		$this->notification->setGenerator($this->entity->getNotifier());
		$this->notification->setRecipient($recipient);
		$this->notification->setEntityClassName(get_class($this->entity));
		$this->notification->setEntityId($this->entity->getId());

		$this->em->persist($this->notification);
	}

	protected function dispatchNotificationCreatedEvent()
	{
		$dispatcher = new EventDispatcher();
		$event = new NotificationEvent($this->notification);
		$dispatcher->dispatch(NotificationEvents::NOTIFICATION_CREATED, $event);
	}

}
