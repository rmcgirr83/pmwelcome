<?php
/**
*
* @package PM Welcome
* @copyright BB3.MOBi (c) 2015 Anvar http://apwa.ru
* @copyright (c) 2020 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome\acp;

class pmwelcome_module
{
	public	$u_action;

	function main($id, $mode)
	{
		global $user, $phpbb_container;

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('apwa.pmwelcome.admin.controller');

		$admin_controller->set_page_url($this->u_action);

		// Load the "settings" or "manage" module modes
		switch ($mode)
		{
			case 'settings':

				$this->page_title = $user->lang('ACP_PMWELCOME');

				$this->tpl_name = 'acp_pmwelcome';

				// Load the display options handle in the admin controller
				$admin_controller->display_options();
			break;

		}

	}
}
