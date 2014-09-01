<?php


App::uses('AppHelper', 'View/Helper');

class TagHelper extends AppHelper {

/**
 * Other helpers to load
 *
 * @var public $helpers
 */
	public $helpers = array('Html', 'Form', 'Js');
	public $options = array(
		'tag_placeholder' => 'search/add tags...',
		'tag_hidden' => false,
		'tag_width' => '90%',
		'tag_autocomplete' =>  false,
		'tag_data' => [],
		'tag_maxselect' => null
	);

	public function input($field, $identifier, $options=array()) {
		$options = am($this->options, $options);

		$this->Html->css("bootstrap-tagsinput.css", array('block' => 'css'));
		$this->Html->script("bootstrap-tagsinput.min", array('block' => 'script'));
		$this->Html->script("Tags.taginput", array('block' => 'script'));

		$formopts = [
			'type' => 'hidden',
			'class' => 'select2tags',
			'multiple' => true,
			'label' => false,
			'legend' => false,
			'div' => false,
			'tags' => json_encode($options['tag_data']),
			'placeholder' => $options['tag_placeholder'],
			'data-placeholder' => $options['tag_placeholder'],
			'identifier' => $identifier,
			'maxselect' => $options['tag_maxselect']
		];
		if (!empty($options['tag_autocomplete'])) {
			$formopts['url'] = $this->Html->url(array('action'=>'autocomplete_tags')) . ".json";
			if ($options['tag_autocomplete'] !== true) {
				$formopts['url'] = $options['tag_autocomplete'] . ".json";
			}
		}
		if ($options['tag_width']) $formopts['style'] = "width: {$options['tag_width']}";

		echo $this->Form->input($field, $formopts);

		$far = explode('.',$field);
		$tagid = "#";
		foreach ($far as $f) {
			$tagid .= ucfirst($f);
		}
		$this->Js->buffer("handleTags('$tagid');");
	}

}