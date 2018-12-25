
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="actors view">

			<h2><?php  echo __('Actor'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($actor['Actor']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Handle'); ?></strong></td>
		<td>
			<?php echo h($actor['Actor']['handle']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Team'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($actor['Team']['code'], array('controller' => 'teams', 'action' => 'view', $actor['Team']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('User'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($actor['User']['handle'], array('controller' => 'users', 'action' => 'view', $actor['User']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

					
			<div class="related">

				<h3><?php echo __('Related Assignments'); ?></h3>
				
				<?php if (!empty($actor['Assignment'])): ?>
					
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Actor Id'); ?></th>
		<th><?php echo __('Task Id'); ?></th>
		<th><?php echo __('Assign Role'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($actor['Assignment'] as $assignment): ?>
		<tr>
			<td><?php echo $assignment['id']; ?></td>
			<td><?php echo $assignment['actor_id']; ?></td>
			<td><?php echo $assignment['task_id']; ?></td>
			<td><?php echo $assignment['assign_role']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'assignments', 'action' => 'view', $assignment['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'assignments', 'action' => 'edit', $assignment['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'assignments', 'action' => 'delete', $assignment['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $assignment['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				<div class="actions">
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Assignment'), array('controller' => 'assignments', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>				</div><!-- /.actions -->
				
			</div><!-- /.related -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
