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
?>
<div class='page-header'>
	<h1><?php echo __d('tags', 'Tags');?>
		<a class='btn btn-sm btn-primary pull-right' style='top: -5px;' href='<?php echo $this->Html->url(array('admin'=>true, 'plugin'=>'tags', 'action' => 'add')); ?>'>
			<i class="icon-plus"></i> Add Tag</i>
		</a>
	</h1>

</div>
<div class='row'>
	<!--
	<div class='col-md-8'>
		<label for='TagKeyname'>Select Namespace</label>
		<div class='input-group'>
		<?php //echo $this->Form->select('Tag.keyname', $identifiers, array('empty' => false, 'class' => 'chosen-select')); ?>
		</div>
		<div class='vspace-6'></div>
		<noscript>
			<button class="blue small" type="submit">
				<?php echo $this->Html->image("icons/small/white/bended_arrow_right.png", array('height' => '24', 'width' => '24')); ?>
			</button>
		</noscript>
	</div>
	-->
</div>
</form>
<div class='vspace-sm-6'></div>
<div class="row">
	<div class='col-xs-12'>
		<?php if (!isset($tags) or !count($tags)): echo "No tags yet... add some!"; else: ?>

		<div class="table-header">
			<span id='filterLabel'>Showing all results</span>
		</div>
		<div class='table-responsive'>
			<table id="indextbl" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th><?php echo $this->Paginator->sort('id');?></th>
						<th><?php echo $this->Paginator->sort('identifier');?></th>
						<th><?php echo $this->Paginator->sort('name');?></th>
						<th><?php echo $this->Paginator->sort('keyname');?></th>
						<th><?php echo $this->Paginator->sort('weight');?></th>
						<th><?php echo $this->Paginator->sort('created');?></th>
						<th><?php echo $this->Paginator->sort('modified');?></th>
						<th class="actions"><?php echo __d('tags', 'Actions');?></th>
					</tr>
				</thead>
				<tbody>


<?php
$i = 0;
foreach ($tags as $tag):
?>
	<tr>
		<td>
			<?php echo $tag['Tag']['id']; ?>
		</td>
		<td>
			<?php echo $tag['Tag']['identifier']; ?>
		</td>
		<td>
			<?php echo $tag['Tag']['name']; ?>
		</td>
		<td>
			<?php echo $tag['Tag']['keyname']; ?>
		</td>
		<td>
			<?php echo $tag['Tag']['weight']; ?>
		</td>
		<td>
			<?php echo $tag['Tag']['created']; ?>
		</td>
		<td>
			<?php echo $tag['Tag']['modified']; ?>
		</td>
		<td>
			<?php echo $this->Html->link(__d('tags', 'View'), array('action' => 'view', $tag['Tag']['keyname'])); ?>
			<?php echo $this->Html->link(__d('tags', 'Edit'), array('action' => 'edit', $tag['Tag']['id'])); ?>
			<?php echo $this->Html->link(__d('tags', 'Delete'), array('action' => 'delete', $tag['Tag']['id']), null, sprintf(__d('tags', 'Are you sure you want to delete # %s?'), $tag['Tag']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
		</tbody>
	</table>
<?php endif;
	echo "</div></div>";

$this->Html->script('jquery.dataTables.min', array('block' => 'script'));
$this->Html->script('jquery.dataTables.bootstrap', array('block' => 'script'));
$this->Js->buffer("

		var oTable = $('#indextbl').dataTable( {
		    'iDisplayLength': 25,
		    'bAutoWidth': false,
		    'aoColumns': [
		    	{ 'bVisible': true, 'bSearchable': false }, // id
				{ 'bVisible': true, 'bSearchable': false }, // identifier
				null, // name
				null, // keyname
				{ 'bSearchable': false }, // weight
				{ 'bSearchable': false }, //created
				{ 'bSearchable': false }, // modified
				{ 'bSortable': false, 'sClass': 'td-actions center', 'bSearchable': false }
		    ]
		 });
");