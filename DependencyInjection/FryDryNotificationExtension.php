<?php

namespace FryDry\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class FryDryNotificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

		if (isset($config['enabled']) && $config['enabled']) {

			$container->setParameter('frydry_notification.notification_class', $config['notification_class']);
			$container->setParameter('frydry_notification.user_class', $config['user_class']);
			$container->setParameter('frydry_notification.notification_get_url', $config['notification_get_url']);
			$container->setParameter('frydry_notification.notification_get_list_by_channel_url', $config['notification_get_list_by_channel_url']);
			// NOTIFICATION LIST
			$container->setParameter('frydry_notification.notification_list.thumb.cache_dir', $config['notification_list']['thumb']['cache_dir']);
			$container->setParameter('frydry_notification.notification_list.thumb.width', $config['notification_list']['thumb']['width']);
			// ENTITIES
			$container->setParameter('frydry_notification.entities', isset($config['entities']) ? $config['entities'] : array());
			// SERVICES
			$container->setAlias('frydry_notification.notification_manager', $config['services']['notification_manager']);


			$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
			$loader->load('services.yml');
		}
    }
}
