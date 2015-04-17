<?php

namespace FryDry\NotificationBundle\Controller;

use FryDry\NotificationBundle\Model\NotificationInterface;
use FryDry\NotificationBundle\Model\NotificationManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller {

	public function getAction(Request $request)
	{
		if (!$this->getUser()) {
			return new JsonResponse();
		}

		$entities = $this->getDoctrine()->getRepository($this->container->getParameter('frydry_notification.notification_class'))->findBy(
			array('recipient' => $this->getUser()->getId()),
			array('createdAt' => 'DESC')
		);

		$body = $this->get('frydry_notification.notification_manager')->getChannelsCount($entities);

		return new JsonResponse($body);

	}

	public function getListByChannelAction(Request $request)
	{
		$channel = $request->get('channel');

		if (!$channel) {
			return new JsonResponse();;
		}
		/** @var NotificationManager $notificationManager */
		$notificationManager = $this->get('frydry_notification.notification_manager');

		$entityChannelMap = $notificationManager->getEntityChannelMap();

		$entityClassNames = array_search($channel, $entityChannelMap);

		$entities = $this->getDoctrine()->getRepository($this->container->getParameter('frydry_notification.notification_class'))->findBy(array(
			'recipient' => $this->getUser(),
			'entityClassName' => $entityClassNames
		), array(
			'createdAt' => 'DESC'
		));

		$body = array();

		foreach ($entities as $entity) {
			/** @var NotificationInterface $entity */
			$item = array();
			$item['id'] = $entity->getId();
			$item['redirect_url'] = '';
			$redirectRoute = $notificationManager->getRedirectUrlForEntity($entity->getEntityClassName());
			if ($redirectRoute) {
				$item['redirect_url'] = $this->generateUrl($redirectRoute, array('id' => $entity->getEntityId()));
			}
			$item['howlongago'] = $notificationManager->getHowLongAgoStringForEntity($entity);

			$body[] = $item;
		}

		return new JsonResponse($body);

	}

}