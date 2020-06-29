<?php
/**
*
* @package PM Welcome
* @copyright (c) 2020 RMcGirr83
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome;

/**
* Extension class for custom enable/disable/purge actions
*/
class ext extends \phpbb\extension\base
{
	/**
	 * Enable extension if phpBB version requirement is met
	 *
	 * @return bool
	 * @access public
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');
		// phpBB only supports 3.2 and so will I
		return phpbb_version_compare($config['version'], '3.2', '>=');
	}
}
