<?php
/**
*
* @package PM Welcome
* @copyright BB3.MOBi (c) 2015 Anvar http://apwa.ru
* @copyright (c) 2020 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
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


$lang = array_merge($lang, array(
	// General config options
	'ACP_PMWELCOME'					=> 'Welcome message',
	'ACP_PMWELCOME_EXPLAIN'			=> 'You can specify the text of the personal message that will be sent to the user upon registration.',
	'ACP_PMWELCOME_SETTINGS'		=> 'Settings private message welcome',
	'ACP_PMWELCOME_USER'			=> 'Sender',
	'ACP_PMWELCOME_USER_EXPLAIN'	=> 'User From, on behalf of which will be sent the private message to the new board member.',
	'ACP_PMWELCOME_SUBJECT'			=> 'Welcome subject',
	'ACP_PMWELCOME_TEXT'			=> 'Text of the welcome message',
	'ACP_PMWELCOME_TEXT_EXPLAIN'	=> 'You can use bbcode and smilies, and the token {USERNAME} to replace the name of the user who receives a private message as well as {SENDER} to insert the senders name.',
	'ACP_PMWELCOME_PREVIEW'			=> 'Private message welcome Text - Preview',
	'ACP_PMWELCOME_CONFIG_SAVED'	=> 'Private message welcome config was saved',
	'TOO_SHORT_PMWELCOME_SUBJECT'	=> 'The value for the welcome subject is too short',
	'TOO_LONG_PMWELCOME_SUBJECT'	=> 'The value for the welcome subject is too long',
	'TOO_SHORT_PMWELCOME_POST_TEXT'	=> 'The value for the private message welcome text is too short',
	'TOO_LONG_PMWELCOME_POST_TEXT'	=> 'The value for the private message welcome text is too long',
	// Log entries
	'LOG_CONFIG_PMWELCOME_ADMIN'		=> '<strong>Altered private message welcome extension page settings</strong>',
	'LOG_PMWELCOME_CONFIG_UPDATE'		=> '<strong>Updated Private message welcome config settings</strong>',

));
