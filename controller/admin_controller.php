<?php
/**
*
* @package PM Welcome
* @copyright (c) 2020 RMcGirr83
* @copyright BB3.MOBi (c) 2015 Anvar http://apwa.ru
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome\controller;

class admin_controller
{
	/**
	 * sender data
	 */
	private $user_info = array();

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\config\config									$config				Config object
	* @param \phpbb\config\db_text 									$config_text		Config text object
	* @param \phpbb\db\driver\driver_interface						$db					Database object
	* @param \phpbb\request\request									$request			Request object
	* @param \phpbb\template\template								$template			Template object
	* @param \phpbb\user											$user				User object
	* @param \phpbb\log\log											$log				Log object
	* @param string													$root_path			phpBB root path
	* @param string													$php_ext			phpEx
	* @access public
	*/
	public function __construct(
			\phpbb\config\config $config,
			\phpbb\config\db_text $config_text,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\request\request $request,
			\phpbb\template\template $template,
			\phpbb\user $user,
			\phpbb\log\log $log,
			$root_path,
			$php_ext)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->log = $log;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		if (!function_exists('display_custom_bbcodes'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}
		if (!function_exists('validate_data'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}
	}

	public function display_options()
	{
		$this->user->add_lang(array('acp/board', 'posting'));

		// Create a form key for preventing CSRF attacks
		add_form_key('pmwelcome_settings');
		$error = array();

		$pmwelcome_data		= $this->config_text->get_array(array(
			'pmwelcome_post_text',
			'pmwelcome_text_bitfield',
			'pmwelcome_text_uid',
			'pmwelcome_text_flags',
		));

		$pmwelcome_post_text		= $pmwelcome_data['pmwelcome_post_text'];
		$pmwelcome_text_bitfield	= $pmwelcome_data['pmwelcome_text_bitfield'];
		$pmwelcome_text_uid			= $pmwelcome_data['pmwelcome_text_uid'];
		$pmwelcome_text_flags		= $pmwelcome_data['pmwelcome_text_flags'];

		$sender_count = (int) $this->pm_welcome_sender_count();

		$this->user_info = $this->pm_welcome_user_name($this->config['pmwelcome_user']);
		$user_link = '<a href="' . append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=viewprofile&amp;u=' . $this->user_info['user_id']) . '" target="_blank">' . $this->user_info['username'] . '</a>';

		$pmwelcome_subject = $this->config['pmwelcome_subject'];
		$pmwelcome_edit = generate_text_for_edit($pmwelcome_post_text, $pmwelcome_text_uid, $pmwelcome_text_flags);

		if ($this->request->is_set_post('submit')  || $this->request->is_set_post('preview'))
		{
			$this->user_info = $this->pm_welcome_user_name($this->request->variable('pmwelcome_user', 0));

			if ($this->request->is_set_post('preview') && !isset($this->user_info['error']))
			{
				$user_link = '<a href="' . append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=viewprofile&amp;u=' . $this->user_info['user_id']) . '" target="_blank">' . $this->user_info['username'] . '</a>';
			}

			if (!check_form_key('pmwelcome_settings'))
			{
				$error[] = $this->user->lang('FORM_INVALID');
			}

			if (isset($this->user_info['error']))
			{
				$error[] = $this->user_info['error'];
			}

			$check_row = array(
				'pmwelcome_subject' => $this->request->variable('pmwelcome_subject', '', true),
				'pmwelcome_post_text' => $this->request->variable('pmwelcome_post_text', '', true),
				'pmwelcome_user'	=>	$this->user_info['user_id']);

			$validate_row = array(
				'pmwelcome_subject' => array('string', false, 5, 255),
				'pmwelcome_post_text' => array('string', false, 1, 2000),
				'pmwelcome_user'	=> array('num', false, 2, $sender_count));

			$error = validate_data($check_row, $validate_row);

			// Replace "error" strings with their real, localised form
			$error = array_map(array($this->user, 'lang'), $error);

			if (empty($error) && $this->request->is_set_post('submit'))
			{
				$this->config_text->set_array(array(
					'pmwelcome_post_text'		=> $check_row['pmwelcome_post_text'],
					'pmwelcome_text_bitfield'	=> $pmwelcome_text_bitfield,
					'pmwelcome_text_uid'		=> $pmwelcome_text_uid,
					'pmwelcome_text_flags'		=> $pmwelcome_text_flags,
				));

				$this->set_options();

				// and an entry into the log table
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PMWELCOME_CONFIG_UPDATE');

				meta_refresh(5, $this->u_action);
				trigger_error($this->user->lang('ACP_PMWELCOME_CONFIG_SAVED') . adm_back_link($this->u_action));
			}
		}

		$pmwelcome_text_preview = '';
		if ($this->request->is_set_post('preview'))
		{
			$pmwelcome_text_preview = (!empty($this->config['pmwelcome_sender'])) ? str_replace('{SENDER}', $this->config['pmwelcome_sender'], $check_row['pmwelcome_post_text']) : $check_row['pmwelcome_post_text'];
			generate_text_for_storage(
				$pmwelcome_text_preview,
				$pmwelcome_text_uid	,
				$pmwelcome_text_bitfield,
				$pmwelcome_text_flags,
				!$this->request->variable('disable_bbcode', false),
				!$this->request->variable('disable_magic_url', false),
				!$this->request->variable('disable_smilies', false)
			);
			$pmwelcome_text_preview = generate_text_for_display($pmwelcome_text_preview, $pmwelcome_text_uid, $pmwelcome_text_bitfield, $pmwelcome_text_flags);
			$pmwelcome_edit = generate_text_for_edit($check_row['pmwelcome_post_text'], $pmwelcome_text_uid, $pmwelcome_text_flags);
			$pmwelcome_subject = $check_row['pmwelcome_subject'];
		}



		$this->template->assign_vars(array(
			'PMWELCOME_ERROR'			=> (sizeof($error)) ? implode('<br />', $error) : false,

			'PMWELCOME_EDIT'			=> $pmwelcome_edit['text'],
			'PMWELCOME_TEXT_PREVIEW'	=> $pmwelcome_text_preview,
			'SENDER_MAX'				=> $sender_count,
			'SENDER_LINK'				=> $user_link,

			'PMWELCOME_USER'				=> $this->user_info['user_id'],
			'PMWELCOME_SUBJECT'				=> $pmwelcome_subject,

			'S_BBCODE_ALLOWED'		=> true,
			'S_SMILIES_ALLOWED'		=> true,
			'S_BBCODE_IMG'			=> true,
			'S_BBCODE_FLASH'		=> false,
			'S_LINKS_ALLOWED'		=> true,

			'U_ACTION'				=> $this->u_action,
		));
		// Assigning custom bbcodes
		display_custom_bbcodes();
	}

	public function set_options()
	{
		$this->config->set('pmwelcome_sender', (string) $this->user_info['username']);
		$this->config->set('pmwelcome_user', (int) $this->user_info['user_id']);
		$this->config->set('pmwelcome_subject', $this->request->variable('pmwelcome_subject', '', true));
	}

	/**
	* pm_welcome_user_name
	*
	* @param user_id				sender user id
	* @return array					array of user info or error if not found
	* @access private
	*/
	private function pm_welcome_user_name($user_id)
	{
		$sender = array();

		$sql = 'SELECT user_id, username
			FROM ' . USERS_TABLE . "
			WHERE user_id = " . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$sender = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$sender['username'])
		{
			$sender['error'] = $this->user->lang['NO_USER'];
		}

		return $sender;
	}

	/**
	* pm_welcome_sender_count
	*
	* @return int				a count of users of the forum used to ensure validation of sender
	* @access private
	*/
	private function pm_welcome_sender_count()
	{
		$sender_count = '';

		$sql = 'SELECT COUNT(user_id) as user_count
			FROM ' . USERS_TABLE;
		$result = $this->db->sql_query($sql);
		$sender_count = $this->db->sql_fetchfield('user_count');
		$this->db->sql_freeresult($result);

		return (int) $sender_count;
	}
	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 * @return null
	 * @access public
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
