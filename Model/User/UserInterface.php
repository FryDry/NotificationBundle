<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 22/02/15
 * Time: 14:04
 */

namespace FryDry\NotificationBundle\Model\User;


interface UserInterface {

	/** @return mixed int|string */
	public function getId();

	/** @return string */
	public function getName();

	/** @return string */
	public function getProfileImage();

}