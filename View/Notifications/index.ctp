
<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="notifications index">
		
			<h2><?php echo __('Notifications'); ?></h2>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
															<th><?php echo $this->Paginator->sort('id'); ?></th>
															<th><?php echo $this->Paginator->sort('type_id'); ?></th>
															<th><?php echo $this->Paginator->sort('parent_task_id'); ?></th>
															<th><?php echo $this->Paginator->sort('child_task_id'); ?></th>
															<th><?php echo $this->Paginator->sort('rec_team_id'); ?></th>
															<th><?php echo $this->Paginator->sort('send_team_id'); ?></th>
															<th><?php echo $this->Paginator->sort('body'); ?></th>
															<th><?php echo $this->Paginator->sort('is_read'); ?></th>
															<th class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($notifications as $notification): ?>
	<tr>
		<td><?php echo h($notification['Notification']['id']); ?>&nbsp;</td>
		<td><?php echo h($notification['Notification']['type_id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($notification['Parent']['short_description'], array('controller' => 'tasks', 'action' => 'view', $notification['Parent']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($notification['Child']['short_description'], array('controller' => 'tasks', 'action' => 'view', $notification['Child']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($notification['ReceiveTeam']['code'], array('controller' => 'teams', 'action' => 'view', $notification['ReceiveTeam']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($notification['SendTeam']['code'], array('controller' => 'teams', 'action' => 'view', $notification['SendTeam']['id'])); ?>
		</td>
		<td><?php echo h($notification['Notification']['body']); ?>&nbsp;</td>
		<td><?php echo h($notification['Notification']['is_read']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $notification['Notification']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $notification['Notification']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $notification['Notification']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $notification['Notification']['id'])); ?>
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
