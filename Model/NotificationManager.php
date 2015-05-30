<?php

namespace FryDry\NotificationBundle\Model;


use Symfony\Component\Intl\Locale\Locale;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class NotificationManager {

	/**
	 * @var RouterInterface
	 */
	protected $router;

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/**
	 * @var ThumbGenerator
	 */
	protected $thumbGenerator;

	/**
	 * @var array
	 */
	protected $configuredNotifiableEntities;

	/**
	 * @var array
	 */
	protected $list;

	/**
	 * @var array
	 */
	protected $channels = array('default' => array());

	/**
	 * @var array
	 */
	protected $entityChannelMap = array();

	public function __construct(RouterInterface $router, TranslatorInterface $translator, ThumbGenerator $thumbGenerator, array $notifiableEntities = array())
	{
		$this->router = $router;
		$this->translator = $translator;
		$this->thumbGenerator = $thumbGenerator;
		$this->configuredNotifiableEntities = $notifiableEntities;
		$this->initChannels();
		$this->initLocale();
	}

	protected function initChannels() {
		foreach ($this->configuredNotifiableEntities as $entity => $config) {
			$this->entityChannelMap[$entity] = isset($config['channel']) && $config['channel'] ? $config['channel'] : 'default';
			if (isset($config['channel']) && $config['channel']) {
				if (!array_key_exists($config['channel'], $this->channels)) {
					$this->channels[$config['channel']] = array();
				}
			}
		}
	}

	protected function initLocale()
	{
		try {
			Locale::getRegion($this->translator->getLocale());
		} catch (\Exception $e) {
			setlocale(LC_TIME, LocaleManager::$l10nCodes[$this->translator->getLocale()]);
		}
	}

	public function getEntityChannelMap()
	{
		return $this->entityChannelMap;
	}

	public function getRedirectUrlForEntity($entity)
	{
		return isset($this->configuredNotifiableEntities[$entity]) ? $this->configuredNotifiableEntities[$entity]['redirect_router_path'] : false;
	}

	public function getHowLongAgoStringForEntity(NotificationInterface $entity)
	{
		/** @var \DateInterval $interval */
		$interval = $entity->getHowLongAgo();
		$now = new \DateTime();

		switch (true) {
			case intval($interval->format('%a')) > 7:
				$dateString = $entity->getCreatedAt()->format('j') . ' ' . strftime('%B', $entity->getCreatedAt()->format('U'));
				if (intval($interval->format('%a')) > 365) {
					$dateString .= ' ' . $entity->getCreatedAt()->format('Y');
				}
				$howLongAgo = $this->translator->trans('howlongago.more_than_seven_days', array(
					'%date%' => $dateString,
					'%time%' => $entity->getCreatedAt()->format('H.i')
				), 'FDNotification');
				break;
			case intval($interval->format('%a')) > 1:
				$howLongAgo = $this->translator->trans('howlongago.in_the_last_seven_days', array(
					'%date%' => strftime('%A', $entity->getCreatedAt()->format('U')),
					'%time%' => $entity->getCreatedAt()->format('H.i')
				), 'FDNotification');
				break;
			case intval($interval->format('%a')) == 1 || (intval($interval->format('%h')) < 24 && intval($interval->format('%h') > 1) && $now->format('G') <= $entity->getCreatedAt()->format('G')):
				$howLongAgo = $this->translator->trans('howlongago.yesterday', array(
					'%time%' => $entity->getCreatedAt()->format('H.i')
				), 'FDNotification');
				break;
			case intval($interval->format('%h')) < 24 && intval($interval->format('%h') >= 1):
				$howLongAgo = $this->translator->trans('howlongago.today', array(
					'%time%' => $entity->getCreatedAt()->format('H.i')
				), 'FDNotification');
				break;
			case intval($interval->format('%h')) < 1 && intval($interval->format('%i') >= 1):
				$howLongAgo = $this->translator->trans('howlongago.minutes', array(
					'%minutes%' => $entity->getCreatedAt()->format('i')
				), 'FDNotification');
				break;
			case intval($interval->format('%i') < 1):
				$howLongAgo = $this->translator->trans('howlongago.now', array(), 'FDNotification');
				break;
			default:
				$howLongAgo = '';
		}

		return $howLongAgo;
	}

	public function getChannelsCount($entities)
	{
		foreach ($entities as $entity) {
			if (!isset($this->channels[$this->entityChannelMap[$entity->getEntityClassName()]]['count'])) {
				$this->channels[$this->entityChannelMap[$entity->getEntityClassName()]]['count'] = 0;
			}
			$this->channels[$this->entityChannelMap[$entity->getEntityClassName()]]['count'] += 1;
		}

		return $this->channels;
	}

	public function buildList($entities)
	{
		foreach ($entities as $entity) {
			/** @var NotificationInterface $entity */
			if (array_key_exists($entity->getEntityClassName(), $this->configuredNotifiableEntities)) {
				$notification = array(
					'id' => $entity->getId(),
					'redirect_url' => $this->router->generate($this->configuredNotifiableEntities[$entity->getEntityClassName()]['redirect_router_path'], array('id' => $entity->getEntityId())),
					'howlongago' => $this->getHowLongAgoStringForEntity($entity),
					'message' => $this->translator->trans($this->configuredNotifiableEntities[$entity->getEntityClassName()]['notification_message'], array('%user%' => $entity->getGenerator()->getName()), 'FDNotification')
				);
				if ($entity->getGenerator()->getProfileImage()) {
					$thumbnail = $this->thumbGenerator->thumbnail($entity->getGenerator()->getProfileImage(), 100);
					if ($thumbnail) {
						$notification['generator_profile_image'] = $thumbnail;
					}
				}
				$this->list[] = $notification;
			}
		}

		return $this->list;
	}

}