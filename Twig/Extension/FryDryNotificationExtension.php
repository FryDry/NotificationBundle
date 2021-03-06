<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 25/02/15
 * Time: 19:28
 */

namespace FryDry\NotificationBundle\Twig\Extension;


use Symfony\Component\Routing\RouterInterface;

class FryDryNotificationExtension extends \Twig_Extension {

	/**
	 * @var RouterInterface
	 */
	protected $router;

	/**
	 * @var array
	 */
	protected $settings;

	public function __construct(RouterInterface $router, $settings)
	{
		$this->router = $router;
		$this->settings = $settings;
	}

	public function getName()
	{
		return 'frydry_notification_twig';
	}

	public function getGlobals()
	{
		$globals = array();
		if (isset($this->settings['notification_get_url']) && $this->settings['notification_get_url']) {
			$globals['frydry_notification_notification_get_url'] = $this->settings['notification_get_url'];
		}
		if (isset($this->settings['notification_get_list_by_channel_url']) && $this->settings['notification_get_list_by_channel_url']) {
			$globals['frydry_notification_notification_get_list_by_channel_url'] = $this->settings['notification_get_list_by_channel_url'];
		}
		return $globals;
	}


	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('notification_manager_script', array($this, 'printNotificationManagerScript'), array(
				'is_safe' => array('html'),
				'needs_environment' => true
			)),
			new \Twig_SimpleFunction('notification_manager_settings', array($this, 'printNotificationManagerSettings'), array(
				'is_safe' => array('html'),
				'needs_environment' => true
			))
		);
	}

	public function printNotificationManagerScript(\Twig_Environment $twig, $options)
	{
		return $twig->render('FryDryNotificationBundle:Script:notification_manager.html.twig', array(
			'jquery' => isset($options['jquery']) ? $options['jquery'] : false
		));
	}

	public function printNotificationManagerSettings(\Twig_Environment $twig)
	{
		return $twig->render(('FryDryNotificationBundle:Settings:settings.html.twig'));
	}


}
