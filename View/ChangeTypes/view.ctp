<div class="changeTypes view">
<h2><?php echo __('Change Type'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($changeType['ChangeType']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($changeType['ChangeType']['name']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Change Type'), array('action' => 'edit', $changeType['ChangeType']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Change Type'), array('action' => 'delete', $changeType['ChangeType']['id']), null, __('Are you sure you want to delete # %s?', $changeType['ChangeType']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Change Types'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Change Type'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Changes'), array('controller' => 'changes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Change'), array('controller' => 'changes', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Changes'); ?></h3>
	<?php if (!empty($changeType['Change'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Task Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Change Type Id'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($changeType['Change'] as $change): ?>
		<tr>
			<td><?php echo $change['id']; ?></td>
			<td><?php echo $change['task_id']; ?></td>
			<td><?php echo $change['user_id']; ?></td>
			<td><?php echo $change['change_type_id']; ?></td>
			<td><?php echo $change['text']; ?></td>
			<td><?php echo $change['created']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'changes', 'action' => 'view', $change['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'changes', 'action' => 'edit', $change['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'changes', 'action' => 'delete', $change['id']), null, __('Are you sure you want to delete # %s?', $change['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Change'), array('controller' => 'changes', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
