<?php
/**
 * List Sublime Projects
 *
 * @author           Suat Secmen (https://su.at)
 * @copyright        2017 Suat Secmen
 * @license          GNU General Public License
 */
if (!ini_get('date.timezone')) date_default_timezone_set('Europe/Berlin');

require 'workflow/Workflow.php';
require 'workflow/Result.php';
$workflow = new Alfred\Workflows\Workflow;

$extension = 'sublime-workspace'; // sublime-project (with empty file) or sublime-workspace (with last session)

$dir = getenv('HOME').'/Library/Application Support/Sublime Text 3/Packages/User/Projects/';
$projects = glob($dir.'*.'.$extension);

if ($query) {
	$chars = strlen($query);
	$items = [];
}

foreach ($projects as $project) {
	$name = basename($project, '.'.$extension);
	if ($query) {
		$c = 0;
		$firstFound = 0;
		for ($i = 0, $l = strlen($name); $i < $l; $i++) {
			if ($name[$i] == $query[$c]) {
				$c++;
				if (!$firstFound) $firstFound = $i + 1;
				if ($c == $chars) break;
			}
		}
		if ($c == $chars) $items[] = [$firstFound, $name, $project];
	} else {
		$workflow->result()
			->uid($name)
			->title($name)
			->subtitle(date('d.m.Y H:i', filemtime($project)))
			->arg($project)
			->text('copy', $name);
	}
}

if ($query) {
	sort($items);
	foreach ($items as $item) {
		$name = $item[1];
		$project = $item[2];
		$workflow->result()
			->uid($query.'-'.$name)
			->title($name)
			->subtitle(date('d.m.Y H:i', filemtime($project)))
			->arg($project)
			->text('copy', $name);
	}
}

echo $workflow->output();
