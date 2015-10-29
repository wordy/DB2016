
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="taskTypes view">

			<h2><?php  echo __('Task Type'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($taskType['TaskType']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($taskType['TaskType']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Description'); ?></strong></td>
		<td>
			<?php echo h($taskType['TaskType']['description']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

					
			<div class="related">

				<h3><?php echo __('Related Tasks'); ?></h3>
				
				<?php if (!empty($taskType['Task'])): ?>
					
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Parent Id'); ?></th>
		<th><?php echo __('Task Type Id'); ?></th>
		<th><?php echo __('Actionable Type Id'); ?></th>
		<th><?php echo __('Task Color Id'); ?></th>
		<th><?php echo __('Team Id'); ?></th>
		<th><?php echo __('Start Time'); ?></th>
		<th><?php echo __('End Time'); ?></th>
		<th><?php echo __('Short Description'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Due Date'); ?></th>
		<th><?php echo __('Actionable Date'); ?></th>
		<th><?php echo __('Active'); ?></th>
		<th><?php echo __('Public'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($taskType['Task'] as $task): ?>
		<tr>
			<td><?php echo $task['id']; ?></td>
			<td><?php echo $task['parent_id']; ?></td>
			<td><?php echo $task['task_type_id']; ?></td>
			<td><?php echo $task['actionable_type_id']; ?></td>
			<td><?php echo $task['task_color_id']; ?></td>
			<td><?php echo $task['team_id']; ?></td>
			<td><?php echo $task['start_time']; ?></td>
			<td><?php echo $task['end_time']; ?></td>
			<td><?php echo $task['short_description']; ?></td>
			<td><?php echo $task['description']; ?></td>
			<td><?php echo $task['due_date']; ?></td>
			<td><?php echo $task['actionable_date']; ?></td>
			<td><?php echo $task['active']; ?></td>
			<td><?php echo $task['public']; ?></td>
			<td><?php echo $task['deleted']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'tasks', 'action' => 'view', $task['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'tasks', 'action' => 'edit', $task['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'tasks', 'action' => 'delete', $task['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $task['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				<div class="actions">
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Task'), array('controller' => 'tasks', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>				</div><!-- /.actions -->
				
			</div><!-- /.related -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
