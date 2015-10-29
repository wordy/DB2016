
<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="changes index">
		
			<h2><?php echo __('Changes'); ?></h2>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
															<th><?php echo $this->Paginator->sort('id'); ?></th>
															<th><?php echo $this->Paginator->sort('task_id'); ?></th>
															<th><?php echo $this->Paginator->sort('user_id'); ?></th>
															<th><?php echo $this->Paginator->sort('change_type_id'); ?></th>
															<th><?php echo $this->Paginator->sort('text'); ?></th>
															<th><?php echo $this->Paginator->sort('created'); ?></th>
															<th class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($changes as $change): ?>
	<tr>
		<td><?php echo h($change['Change']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($change['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $change['Task']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($change['User']['handle'], array('controller' => 'users', 'action' => 'view', $change['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($change['ChangeType']['name'], array('controller' => 'change_types', 'action' => 'view', $change['ChangeType']['id'])); ?>
		</td>
		<td><?php echo h($change['Change']['text']); ?>&nbsp;</td>
		<td><?php echo h($change['Change']['created']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $change['Change']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $change['Change']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $change['Change']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $change['Change']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
					</tbody>
				</table>
			</div><!-- /.table-responsive -->
			
			<p><small>
				<?php
				echo $this->Paginator->counter(array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
				));
				?>			</small></p>

			<ul class="pagination">
				<?php
		echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
		echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
		echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	?>
			</ul><!-- /.pagination -->
			
		</div><!-- /.index -->
	
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
