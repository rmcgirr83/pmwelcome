<?php
/**
*
* @package PM WELCOME
* @copyright BB3.MOBi (c) 2015 Anvar http://apwa.ru
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome\migrations;

class v_1_2_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['pmwelcome_version']) && version_compare($this->config['pmwelcome_version'], '1.2.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\config_db_text');
	}

	public function update_data()
	{
		return array(
			// Update version
			array('config.update', array('pmwelcome_version', '1.2.0')),
			array('config.add', array('pmwelcome_sender', '')),
			array('custom', array(array($this, 'pmwelcome_text'))),
		);
	}

	public function pmwelcome_text()
	{
		$text_config = new \phpbb\config\db_text($this->db, $this->table_prefix . 'config_text');
		$text_config->set_array(array(
			'pmwelcome_text_uid'		=> '',
			'pmwelcome_text_bitfield'	=> '',
			'pmwelcome_text_flags'		=> OPTION_FLAG_BBCODE + OPTION_FLAG_SMILIES + OPTION_FLAG_LINKS,
		));
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('pmwelcome_sender')),
			// Remove config text
			array('config_text.remove', array('pmwelcome_text_uid')),
			array('config_text.remove', array('pmwelcome_text_bitfield')),
			array('config_text.remove', array('pmwelcome_text_flags')),
		);
	}
}
