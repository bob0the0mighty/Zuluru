<?php
/**
 * Rule helper for returning a user's membership type.
 */

class RuleMemberTypeComponent extends RuleComponent
{
	function parse($config) {
		$this->config = trim ($config, '"\'');
		return true;
	}

	// Check if the user was a member on the configured date
	function evaluate($params) {
		$date = strtotime ($this->config);
		// TODO: A better way to rank membership types that handles more than just intro and full
		$intro = false;
		if (is_array($params) && array_key_exists ('Registration', $params)) {
			foreach ($params['Registration'] as $reg) {
				if (array_key_exists('membership_begins', $reg['Event']) &&
					strtotime ($reg['Event']['membership_begins']) <= $date &&
					$date <= strtotime ($reg['Event']['membership_ends']))
				{
					if ($reg['Event']['membership_type'] == 'full') {
						return 'full';
					} else if ($reg['Event']['membership_type'] == 'intro') {
						$intro = true;
					}
				}
			}
		}

		return ($intro ? 'intro' : 'none');
	}

	function desc() {
		App::import('helper', 'Time');
		App::import('helper', 'ZuluruTime');
		$ZuluruTime = new ZuluruTimeHelper();
		$date = $ZuluruTime->date ($this->config);
		return __('have a membership type', true);
	}
}

?>
