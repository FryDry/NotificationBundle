<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 24/02/15
 * Time: 19:11
 */

namespace FryDry\NotificationBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class NotificationRelationshipSubscriber implements  EventSubscriber {

	/**
	 * @var string
	 */
	protected $notificationClassName;

	/**
	 * @var string
	 */
	protected $userClassName;

	public function __construct($notificationClassName, $userClassName)
	{
		$this->notificationClassName = $notificationClassName;
		$this->userClassName = $userClassName;

	}

	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			Events::loadClassMetadata
		);
	}

	public function loadClassMetadata(LoadClassMetadataEventArgs $args)
	{
		$metadata = $args->getClassMetadata();
		$className = $metadata->getName();

		if ($className == $this->notificationClassName) {

			$metadata->mapManyToOne(array(
				'targetEntity' => $this->userClassName,
				'fieldName' => 'generator',
				'cascade' => array('all'),
				'joinColumns' => array(
					array(
						'name' => 'generator',
						'referencedColumnName' => 'id'
					)
				)
			));

			$metadata->mapManyToOne(array(
				'targetEntity' => $this->userClassName,
				'fieldName' => 'recipient',
				'cascade' => array('all'),
				'joinColumns' => array(
					array(
						'name' => 'recipient',
						'referencedColumnName' => 'id'
					)
				)
			));

		}
	}
}
