<?php
/**
*
* @package PM Welcome
* @copyright BB3.MOBi (c) 2015 Anvar http://apwa.ru
* @copyright (c) 2020 O Belde (obelde.com) - Halil ESEN
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters for use
// ’ » “ ” …


$lang = array_merge($lang, [
	// General config options
	'TRANSLATION_INFO'	=> '<br />Tercüme: <a href="https://obelde.com/">O Belde</a> <a href="https://forum.obelde.com/">Forum</a>',
	'ACP_PMWELCOME'					=> 'Hoş Geldin Mesajı',
	'ACP_PMWELCOME_EXPLAIN'			=> 'Kayıt sonrasında üyelere otomatik gönderilecek karşılama özel mesajı oluşturabilirsiniz.',
	'ACP_PMWELCOME_SETTINGS'		=> 'Hoş Geldin özel mesaj ayarları',
	'ACP_PMWELCOME_USER'			=> 'Gönderen',
	'ACP_PMWELCOME_USER_EXPLAIN'	=> 'Yeni üyeye gönderilecek özel mesajı gönderen yönetici',
	'ACP_PMWELCOME_SUBJECT'			=> 'Hoş Geldin ÖM Konusu',
	'ACP_PMWELCOME_TEXT'			=> 'Mesaj',
	'ACP_PMWELCOME_TEXT_EXPLAIN'	=> 'Üyeye hitaben {USERNAME} kodunuz kullanırsanız, bu onun  kullanıcı adını gösterecektir. Mesajınızın sonunda imza olarak {SENDER} kodunu kullanırsanız bu gönderen yöneticinin kullanıcı adını gösterecektir.',
	'ACP_PMWELCOME_PREVIEW'			=> 'Ön izleme',
	'ACP_PMWELCOME_NO_USER'			=> '<b>Metin mevcut değil.</b>',
	'ACP_PMWELCOME_CONFIG_SAVED'	=> 'ÖM karşılama ayarlarınız kaydedildi.',
	'TOO_SHORT_PMWELCOME_SUBJECT'	=> 'Konu başlığı çok kısa',
	'TOO_SHORT_PMWELCOME_POST_TEXT'	=> 'Metin çok kısa',
	// Log entries
	'LOG_CONFIG_PMWELCOME_ADMIN'		=> '<strong>Ayarlarınız başarıyla oluşturuldu.</strong>',
	'LOG_PMWELCOME_CONFIG_UPDATE'		=> '<strong>Ayarlarınız başarıyla güncellendi.</strong>',
	//Donation
	'PAYPAL_IMAGE_URL'          => 'https://www.paypalobjects.com/webstatic/en_US/i/btn/png/silver-pill-paypal-26px.png',
	'PAYPAL_ALT'                => 'Paypal ile bağış yap',
	'BUY_ME_A_BEER_URL'         => 'https://paypal.me/RMcGirr83',
	'BUY_ME_A_BEER'				=> 'Bu eklenti için bana bir döner-ayran alabilirsin.',
	'BUY_ME_A_BEER_SHORT'		=> 'Bu eklenti için geliştiricisine bağış yapın.',
	'BUY_ME_A_BEER_EXPLAIN'		=> 'Bu eklenti tamamen ücretsizdir. phpBB forumlarını daha güzel ve vasıflı bir yer hale getirmek için zaman harcadığım bir projedir. Bu eklentiyi sevdiyseniz veya forumunuza fayda sağladıysa, lütfen <a href="https://paypal.me/RMcGirr83" target="_blank" rel=”noreferrer noopener”> bana döner-ayran alın</ a>, buna çok sevinirim <i class="fa fa-smile-o" style="color:green;font-size:1.5em;" aria-hidden="true"></i>',
]);
