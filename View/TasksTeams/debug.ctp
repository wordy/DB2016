
<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="tasksTeams index">
		
			<h2><?php echo __('Tasks Teams'); ?></h2>
            <p class="help-block">This assigns teams roles within tasks. Try and avoid doing this manually (edit the task to change roles instead). </p>

			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
															<th><?php echo $this->Paginator->sort('id'); ?></th>
															<th><?php echo $this->Paginator->sort('task_id'); ?></th>
															<th><?php echo $this->Paginator->sort('team_id'); ?></th>
															<th><?php echo $this->Paginator->sort('task_role_id'); ?></th>
															<th class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($tasksTeams as $tasksTeam): ?>
	<tr>
		<td><?php echo h($tasksTeam['TasksTeam']['id']); ?>&nbsp;</td>
		<td>
			<?php //echo $this->Html->link($tasksTeam['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $tasksTeam['Task']['id'])); ?>
		</td>
		<td>
			<?php //echo $this->Html->link($tasksTeam['Team']['code'], array('controller' => 'teams', 'action' => 'view', $tasksTeam['Team']['id'])); ?>
		</td>
		<td>
			<?php //echo $this->Html->link($tasksTeam['TaskRole']['description'], array('controller' => 'task_roles', 'action' => 'view', $tasksTeam['TaskRole']['id'])); ?>
		</td>
		<td class="actions">
			<?php //echo $this->Html->link(__('View'), array('action' => 'view', $tasksTeam['TasksTeam']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $tasksTeam['TasksTeam']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $tasksTeam['TasksTeam']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $tasksTeam['TasksTeam']['id'])); ?>
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

<?php if($tasksTeams){ debug($tasksTeams);//die;
} ?>
