
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="tasksTeams view">

			<h2><?php  echo __('Tasks Team'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($tasksTeam['TasksTeam']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Task'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($tasksTeam['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $tasksTeam['Task']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Team'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($tasksTeam['Team']['code'], array('controller' => 'teams', 'action' => 'view', $tasksTeam['Team']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Task Role'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($tasksTeam['TaskRole']['description'], array('controller' => 'task_roles', 'action' => 'view', $tasksTeam['TaskRole']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Active'); ?></strong></td>
		<td>
			<?php echo h($tasksTeam['TasksTeam']['active']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Public'); ?></strong></td>
		<td>
			<?php echo h($tasksTeam['TasksTeam']['public']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Deleted'); ?></strong></td>
		<td>
			<?php echo h($tasksTeam['TasksTeam']['deleted']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
