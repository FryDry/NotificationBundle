<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 22/03/15
 * Time: 14:46
 */

namespace FryDry\NotificationBundle\Model;


use Symfony\Component\Intl\Locale;

class LocaleManager {

	public static $l10nCodes = array(
		'it' => 'it_IT',
		'en' => 'en_UK',
		'fr' => 'fr_FR',
		'es' => 'es_ES',
		'de' => 'de_DE'
	);

	public static function addL10NCodeForLocale($code, $locale)
	{
		self::$l10nCodes[$locale] = $code;
	}

}