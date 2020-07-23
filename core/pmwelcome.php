<?php
/**
*
* @package PM Welcome
* @copyright (c) 2020 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome\core;

use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use Symfony\Component\HttpFoundation\JsonResponse;

class pmwelcome
{
	/** @var driver_interface */
	protected $db;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(
		driver_interface $db,
		language $language,
		request $request,
		$root_path,
		$php_ext)
	{
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* sender_info					used in the ACP when choosing the "sender"
	*
	* @param user_id				user id
	* @return array					array of user info or error if not found, json response if via ajax call
	* @access public
	*/
	public function sender_info($user_id)
	{
		$sender_info = [];

		$sql = 'SELECT user_id, username
			FROM ' . USERS_TABLE . "
			WHERE user_id = " . (int) $user_id . ' AND user_type <> ' . USER_IGNORE;
		$result = $this->db->sql_query($sql);
		$sender_info = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!isset($sender_info['username']))
		{
			$sender_info['error'] = $this->language->lang('ACP_PMWELCOME_NO_USER');
		}

		if ($this->request->is_ajax())
		{
			if (!isset($sender_info['username']))
			{
				$json = new JsonResponse(array(
					'error'     => true,
				));
			}
			else
			{
				$json = new JsonResponse(array(
					'sender_link'     => '<a href="' . append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=viewprofile&amp;u=' . $sender_info['user_id']) . '" target="_blank">' . $sender_info['username'] . '</a>',
				));
			}
			return $json;
		}

		return $sender_info;
	}
}
