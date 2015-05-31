<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 22/02/15
 * Time: 14:19
 */

namespace FryDry\NotificationBundle\Model;


use FryDry\NotificationBundle\Model\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface NotifiableObjectInterface {

	/**
	 * return integer
	 */
	public function getId();

	/**
	 * @return UserInterface
	 */
	public function getNotifier();

	/**
	 * @return UserInterface|ArrayCollection
	 */
	public function getNotificationRecipients();

}
