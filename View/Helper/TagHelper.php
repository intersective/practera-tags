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
	);

	public function input($field, $namespace, $options=array()) {
		$options = am($this->options, $options);

		$this->Html->script("bootstrap-tag.min", array('block' => 'script'));
		$this->Html->script("Tags.taginput", array('block' => 'script'));

		echo $this->Form->input($field, array('type' => 'text', 'label' => false, 'legend' => false, 'div' => false, 'data-provide'=>'tag', 'placeholder' => $options['tag_placeholder'], 'data-placeholder' => $options['tag_placeholder'], 'url' => $this->Html->url(array('action'=>'autocomplete_tags')) . ".json", 'namespace' => $namespace));

		$far = explode('.',$field);
		$tagid = "#";
		foreach ($far as $f) {
			$tagid .= ucfirst($f);
		}
		$this->Js->buffer("
			handleTags('$tagid');
			$('.tags').attr('style', 'width: {$options['tag_width']}');

		");
	}

}