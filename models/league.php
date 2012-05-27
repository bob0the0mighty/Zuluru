<?php
class League extends AppModel {
	var $name = 'League';
	var $displayField = 'name';

	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'A valid league name must be entered.',
			),
		),
		'sport' => array(
			'inlist' => array(
				'rule' => array('inconfig', 'options.sport'),
				'message' => 'You must select a valid sport.',
			),
		),
		'season' => array(
			'inlist' => array(
				'rule' => array('inconfig', 'options.season'),
				'message' => 'You must select a valid season.',
			),
		),
		'display_sotg' => array(
			'inlist' => array(
				'rule' => array('inconfig', 'options.sotg_display'),
				'message' => 'You must select a valid spirit display method.',
			),
		),
		'sotg_questions' => array(
			'inlist' => array(
				'rule' => array('inconfig', 'options.spirit_questions'),
				'message' => 'You must select a valid spirit questionnaire.',
			),
		),
		'numeric_sotg' => array(
			'inlist' => array(
				'rule' => array('inconfig', 'options.enable'),
				'message' => 'You must select whether or not numeric spirit entry is enabled.',
			),
		),
	);

	var $hasMany = array(
		'Division' => array(
			'className' => 'Division',
			'foreignKey' => 'league_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	function _afterFind ($record) {
		$long_name = '';

		if (array_key_exists ('name', $record[$this->alias])) {
			$long_name = $record[$this->alias]['name'];
		}

		// Add the season, if it's not already part of the name
		if (array_key_exists ('season', $record[$this->alias])) {
			if (strpos ($long_name, $record[$this->alias]['season']) === false) {
				$long_name = $record[$this->alias]['season'] . ' ' . $long_name;
			}
		}

		// Add the sport, if there are multiple options
		if (array_key_exists ('sport', $record[$this->alias]) && count(Configure::read('options.sport')) > 1) {
			$long_name .= ' ' . Inflector::humanize($record[$this->alias]['sport']);
		}

		// Add the year, if it's not already part of the name
		$full_name = $long_name;
		if (array_key_exists ('open', $record[$this->alias]) && $record[$this->alias]['open'] != '0000-00-00') {
			$year = date ('Y', strtotime ($record[$this->alias]['open']));
			if (strpos ($full_name, $year) === false) {
				// TODO: Add closing year, if different than opening
				$full_name = $year . ' ' . $full_name;
			}
			if (array_key_exists('season', $record[$this->alias])) {
				$record[$this->alias]['long_season'] = "$year {$record[$this->alias]['season']}";
			}
		}

		$record[$this->alias]['long_name'] = trim($long_name);
		$record[$this->alias]['full_name'] = trim($full_name);

		return $record;
	}

	static function compareLeagueAndDivision ($a, $b) {
		if (array_key_exists('League', $a)) {
			$a_league = $a['League'];
			$b_league = $b['League'];
		} else {
			$a_league = $a['Division']['League'];
			$b_league = $b['Division']['League'];
		}

		// If they are different sports, we use that
		if ($a_league['sport'] > $b_league['sport']) {
			return 1;
		} else if ($a_league['sport'] < $b_league['sport']) {
			return -1;
		}

		// If they are in different years, we use that
		if (date('Y', strtotime($a_league['open'])) > date('Y', strtotime($b_league['open']))) {
			return 1;
		} else if (date('Y', strtotime($a_league['open'])) < date('Y', strtotime($b_league['open']))) {
			return -1;
		}

		// If they are in different seasons, we use that
		$seasons = array_flip(array_values(Configure::read('options.season')));
		$a_season = $seasons[$a_league['season']];
		$b_season = $seasons[$b_league['season']];
		if ($a_season > $b_season) {
			return 1;
		} else if ($a_season < $b_season) {
			return -1;
		}

		// If the league open dates are far apart, we use that
		$a_open = strtotime ($a_league['open']);
		$b_open = strtotime ($b_league['open']);
		if (abs ($a_open - $b_open) > 5 * WEEK) {
			if ($a_open > $b_open) {
				return 1;
			} else if ($a_open < $b_open) {
				return -1;
			}
		}

		if (array_key_exists('Division', $a)) {
			if (array_key_exists('season_days', $a['Division'])) {
				$a_days = $a['Division']['season_days'];
			} else if (array_key_exists('Day', $a)) {
				// Set::extract fails when there's a numeric key at the top level,
				// like when we have a count in the statistics page, so we use this
				// method instead of Set::extract('/Day/id', $a)
				$a_days = Set::extract('/id', $a['Day']);
			} else {
				$a_days = array();
			}

			if (array_key_exists('season_days', $b['Division'])) {
				$b_days = $b['Division']['season_days'];
			} else if (array_key_exists('Day', $b)) {
				$b_days = Set::extract('/id', $b['Day']);
			} else {
				$b_days = array();
			}

			if (empty ($a_days)) {
				$a_min = 0;
			} else {
				$a_min = min($a_days);
			}
			if (empty ($b_days)) {
				$b_min = 0;
			} else {
				$b_min = min($b_days);
			}

			if ($a_min > $b_min) {
				return 1;
			} else if ($a_min < $b_min) {
				return -1;
			}
			// Divisions on the same day use the id to sort. Assumption is that
			// higher-level divisions are created first.
			return $a['Division']['id'] > $b['Division']['id'];
		}

		return $a_league['id'] > $b_league['id'];
	}

	static function hasSpirit($league) {
		if (array_key_exists('League', $league)) {
			$league = $league['League'];
		} else if (array_key_exists('Division', $league) && array_key_exists('League', $league['Division'])) {
			$league = $league['Division']['League'];
		}
		return ($league['numeric_sotg'] || $league['sotg_questions'] != 'none');
	}
}
?>