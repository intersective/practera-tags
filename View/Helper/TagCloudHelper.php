<?php
/**
 * Copyright 2009-2012, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2009-2012, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Tag cloud helper
 *
 * @package tags
 * @subpackage tags.views.helpers
 */
class TagCloudHelper extends AppHelper {

/**
 * Other helpers to load
 *
 * @var public $helpers
 */
	public $helpers = array('Html');

/**
 * Method to output a tag-cloud formatted based on the weight of the tags
 *
 * @param array $tags
 * @param array $options Display options. Valid keys are:
 * 	- shuffle: true to shuffle the tag list, false to display them in the same order than passed [default: true]
 *  - extract: Set::extract() compatible format string. Path to extract weight values from the $tags array [default: {n}.Tag.weight]
 *  - before: string to be displayed before each generated link. "%size%" will be replaced with tag size calculated from the weight [default: empty]
 *  - after: string to be displayed after each generated link. "%size%" will be replaced with tag size calculated from the weight [default: empty]
 *  - maxSize: size of the heaviest tag [default: 160]
 *  - minSize: size of the lightest tag [default: 80]
 *  - url: an array containing the default url
 *  - named: the named parameter used to send the tag [default: by]
 * @return string
 */
	public function display($tags = null, $options = array()) {
		if (empty($tags) || !is_array($tags)) {
			return '';
		}
		$defaults = array(
			'tagModel' => 'Tag',
			'shuffle' => true,
			'extract' => '{n}.occurance',
			'before' => '',
			'after' => '',
			'maxSize' => 5,
			'minSize' => 0,
			'url' => array(
				'controller' => 'search'
			),
			'named' => 'by'
		);
		$options = array_merge($defaults, $options);

		$weights = Set::extract($tags, $options['extract']);
		$maxWeight = max($weights);
		$minWeight = min($weights);

		// find the range of values
		$spread = $maxWeight - $minWeight;
		if (0 == $spread) {
			$spread = 1;
		}

		if ($options['shuffle'] == true) {
			shuffle($tags);
		}

		$cloud = "<style>
		.label-minier {
			font-size: 8px;
			line-height: 1.75;
		}
		.label-xs {
			font-size: 10px;
			line-height: 1.75;
		}
		.label {
			margin-bottom: 5px;
		}
		.label-lg {
			line-height: 1;
		}
		</style>";
		foreach ($tags as $tag) {
			$size = $options['minSize'] + (($tag['occurance'] - $minWeight) * (($options['maxSize'] - $options['minSize']) / ($spread)));
			$size = $tag['size'] = ceil($size);

			$cloud .= $this->_replace($options['before'], $size);
			$cloud .= $this->Html->link($tag['name'], $this->_tagUrl($tag, $options), array('class' => 'label label' . $this->_size($size) . ' size-' . $size . ' arrowed-right', 'id' => 'tag-' . $tag['id'])) . ' ';
			$cloud .= $this->_replace($options['after'], $size);
		}

		return $cloud;
	}

/**
 * Generates the URL for a tag
 *
 * @param array
 * @param array
 * @return array|string
 */
	protected function _tagUrl($tag, $options) {
		$options['url'][$options['named']] = $tag['keyname'];
		return $options['url'];
	}

/**
 * Replaces %size% in strings with the calculated "size" of the tag
 *
 * @param string
 * @param float
 * @return string
 */
	protected function _replace($string, $size) {
		return str_replace("%size%", $size, $string);
	}

 	protected function _size($size) {
 		if ($size < 1) {
 		  	return "-minier";
		} else if ($size < 2) {
		  	return "-xs";
		} else if ($size < 3) {
		  	return "-sm";
		} else if ($size < 4) {
		  	return "-md";
		} else {
		  	return "-lg";
		}
	}
}
