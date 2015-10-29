<div class="changeTypes form">
<?php echo $this->Form->create('ChangeType'); ?>
	<fieldset>
		<legend><?php echo __('Add Change Type'); ?></legend>
	<?php
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Change Types'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Changes'), array('controller' => 'changes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Change'), array('controller' => 'changes', 'action' => 'add')); ?> </li>
	</ul>
</div>
