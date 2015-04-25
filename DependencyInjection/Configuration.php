<?php

namespace FryDry\NotificationBundle\DependencyInjection;

use FryDry\NotificationBundle\Entity\Notification;
use FryDry\NotificationBundle\Model\User\UserInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fry_dry_notification');

        $rootNode
			->children()
				->booleanNode('enabled')->defaultFalse()->end()
				->scalarNode('notification_class')
					->defaultValue('FryDry\\NotificationBundle\\Entity\\Notification')
					->validate()
					->ifTrue(function($v){

						$testObj = new $v;

						return !($testObj instanceof Notification);
					})
					->thenInvalid('Notification class must extend FryDry\\NotificationBundle\\Entity\\Notification')
					->end()
				->end()
				->scalarNode('user_class')
					->isRequired()
					->cannotBeEmpty()
					->validate()
					->ifTrue(function($v){

						$testObj = new $v;

						return !($testObj instanceof UserInterface);
					})
					->thenInvalid('User class must extend FryDry\\NotificationBundle\\Model\\User\\UserInterface')
					->end()
				->end()
				->scalarNode('notification_get_url')->defaultValue('frydry_notification_get')->end()
				->scalarNode('notification_get_list_by_channel_url')->defaultValue('frydry_notification_get_list_by_channel')->end()
				->arrayNode('entities')
				->useAttributeAsKey('class')
					->prototype('array')
						->children()
//							->scalarNode('name')->end()
							->scalarNode('channel')->end()
							->scalarNode('redirect_router_path')->end()
							->scalarNode('notification_message')->isRequired()->cannotBeEmpty()->end()
							->scalarNode('class')->end()
						->end()
					->end()
				->end()
				->arrayNode('services')
				->addDefaultsIfNotSet()
					->children()
						->scalarNode('notification_manager')->defaultValue('frydry_notification.notification_manager_default')->end()
					->end()
				->end()
				->arrayNode('notification_list')
				->addDefaultsIfNotSet()
					->children()
						->arrayNode('thumb')
						->addDefaultsIfNotSet()
							->children()
								->scalarNode('cache_dir')->defaultValue('/images/frydry/thumb/cache')->end()
								->integerNode('width')->defaultValue(100)->end()
							->end()
						->end()
					->end()
				->end()
			->end()
		;

        return $treeBuilder;
    }
}
