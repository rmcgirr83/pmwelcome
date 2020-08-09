<?php
/**
*
* @package PM WELCOME
* @copyright (c) 2020 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome\migrations;

class v_1_2_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\apwa\pmwelcome\migrations\v_1_2_0_schema'];
	}

	public function update_data()
	{
		return [
			// Remove version
			['config.remove', ['pmwelcome_version']],
			['config.add', ['pmwelcome_sender', '']],
			['custom', [[$this, 'pmwelcome_text']]],
			['custom', [[$this, 'user_pm_welcome']]],
		];
	}

	public function pmwelcome_text()
	{
		$text_config = new \phpbb\config\db_text($this->db, $this->table_prefix . 'config_text');
		$text_config->set_array([
			'pmwelcome_text_uid'		=> '',
			'pmwelcome_text_bitfield'	=> '',
			'pmwelcome_text_flags'		=> OPTION_FLAG_BBCODE + OPTION_FLAG_SMILIES + OPTION_FLAG_LINKS,
		]);
	}

	public function user_pm_welcome()
	{
		// set already registered users to have received the pm_welcome...meh
		$sql = 'UPDATE ' . $this->table_prefix . 'users
			SET user_pm_welcome = 1';
		$this->db->sql_query($sql);
	}

	public function revert_data()
	{
		return [
			['config.remove',['pmwelcome_sender']],
			// Remove config text
			['config_text.remove', ['pmwelcome_text_uid']],
			['config_text.remove', ['pmwelcome_text_bitfield']],
			['config_text.remove', ['pmwelcome_text_flags']],
		];
	}
}
