<?php
/**
*
* @package PM Welcome
* @copyright (c) bb3.mobi 2014 Anvar
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome\event;

/*
* ignore
*/
use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\db\driver\driver_interface;
use phpbb\user;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\config\config									$config				Config object
	* @param \phpbb\config\db_text 									$config_text		Config text object
	* @param \phpbb\db\driver\driver_interface						$db					Database object
	* @param \phpbb\user											$user				User object
	* @param string													$phpbb_root_path	phpBB root path
	* @param string													$php_ext			phpEx
	* @access public
	*/
	public function __construct(
		config $config,
		db_text $config_text,
		driver_interface $db,
		user $user,
		$phpbb_root_path,
		$php_ext)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->db = $db;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_add_after'		=> 'pm_welcome',
			'core.user_active_flip_before'	=> 'user_active_flip_before',
		);
	}

	/**
	* pm_welcome
	* @param $event		the event object
	* Used when user registration is set to no verification
	*/
	public function pm_welcome($event)
	{
		$user_row = $event['user_row'];

		if ($user_row['user_type'] == USER_NORMAL && $this->check_for_items())
		{
			$user_to = $event['user_id'];

			$this->user_welcome($user_to);
		}
	}

	/**
	* user_active_flip_before
	* @param $event		the event object
	* Used when activation is set By Admin or user email
	*/
	public function user_active_flip_before($event)
	{
		$reason = $event['reason'];

		if (!$this->check_for_items() || $reason != INACTIVE_MANUAL)
		{
			return false;
		}

		$sql_statements = $event['sql_statements'];

		$user_ids = array_keys($sql_statements);

		$already_pmed = $this->check_user($user_ids);

		foreach ($sql_statements as $user_id => $sql_ary)
		{
			if (!array_key_exists($user_id, $already_pmed))
			{
				$sql_ary += [
					'user_pm_welcome'	=> true,
				];
				$sql_statements[$user_id] = $sql_ary;

				$this->user_welcome($user_id);
			}
		}

		$event['sql_statements'] = $sql_statements;
	}

	/**
	* add entry to users table so the user doesn't get pm welcome upon deactivation and reactivation
	* @param $user_id_arry		the user_id passed from user_active_flip_before
	* Used when activation is set By Admin
	* return Bool
	*/
	private function check_user($user_id_arry)
	{
		if (!is_array($user_id_arry))
		{
			$user_id_arry = [$user_id_arry];
		}

		$sql = 'SELECT user_id, user_pm_welcome
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_id', $user_id_arry) . ' AND user_pm_welcome = 1';
		$result = $this->db->sql_query($sql);

		$already_pmed = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$already_pmed[$row['user_id']] = (int) $row['user_pm_welcome'];
		}
		$this->db->sql_freeresult($result);

		return $already_pmed;
	}

	/**
	* user_welcome
	* @param $user_to	the user id of the user
	* Sends an email to the registering user
	*/
	private function user_welcome($user_to)
	{
		if (!is_array($user_to))
		{
			$user_to = [$user_to];
		}

		$user_id = $this->config['pmwelcome_user'];
		$subject = $this->config['pmwelcome_subject'];

		$pmwelcome_text_data		= $this->config_text->get_array([
			'pmwelcome_post_text',
			'pmwelcome_text_uid',
			'pmwelcome_text_bitfield',
			'pmwelcome_text_flags',
		]);

		$pmwelcome_text	= $pmwelcome_text_data['pmwelcome_post_text'];
		$uid			= $pmwelcome_text_data['pmwelcome_text_uid'];
		$bitfield		= $pmwelcome_text_data['pmwelcome_text_bitfield'];
		$flags			= $pmwelcome_text_data['pmwelcome_text_flags'];
		$allow_bbcode = $allow_urls = $allow_smilies = true;

		// change the wording of the message if so desired
		$pmwelcome_text = str_replace('{SENDER}', $this->config['pmwelcome_sender'], $pmwelcome_text);
		generate_text_for_storage($pmwelcome_text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);

		if (!function_exists('submit_pm'))
		{
			include($this->phpbb_root_path . 'includes/functions_privmsgs.' . $this->php_ext);
		}

		$pm_data = [
			'from_user_id'		=> $user_id,
			'from_user_ip'		=> $this->user->ip,
			'enable_sig'		=> false,
			'enable_bbcode'		=> $allow_bbcode,
			'enable_smilies'	=> $allow_smilies,
			'enable_urls'		=> $allow_urls,
			'icon_id'			=> 0,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
		];

		// need the username in all cases
		$sql = 'SELECT username, user_id
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_id', $user_to);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			// Loop through our list of users
			$pmwelcome_text = str_replace('{USERNAME}', $row['username'], $pmwelcome_text);

			$pm_data['address_list'] = ['u' => [$row['user_id'] => 'to']];
			$pm_data['message'] = $pmwelcome_text;

			submit_pm('post', $subject, $pm_data, false);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	* check_for_items
	* @return bool
	* ensures all settings are set in ACP
	*/
	private function check_for_items()
	{
		$pwm_user = $this->config['pmwelcome_user'];
		$pwm_subject = $this->config['pmwelcome_subject'];
		$pwm_text = $this->config_text->get('pmwelcome_post_text');

		if (!empty($pwm_user) && !empty($pwm_subject) && !empty($pwm_text))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
