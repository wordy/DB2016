
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="users view">

			<h2><?php  echo __('User'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Handle'); ?></strong></td>
		<td>
			<?php echo h($user['User']['handle']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Username'); ?></strong></td>
		<td>
			<?php echo h($user['User']['username']); ?>
			&nbsp;
		</td>
</tr>
<!--
<tr>		<td><strong><?php echo __('Password'); ?></strong></td>
		<td>
			<?php echo h($user['User']['password']); ?>
			&nbsp;
		</td>
</tr>
-->
<tr>		<td><strong><?php echo __('User Role'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($user['UserRole']['role'], array('controller' => 'user_roles', 'action' => 'view', $user['UserRole']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>
<tr>        <td><strong><?php echo __('Email'); ?></strong></td>
        <td>
            <?php echo $user['User']['email']; ?>
            &nbsp;
        </td>
</tr>
<tr>        <td><strong><?php echo __('Status'); ?></strong></td>
        <td>
            <?php echo $user['User']['status'];?>
            &nbsp;
        </td>
</tr>

					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

					
			<div class="related">

				<h3><?php echo __('Related Teams Users'); ?></h3>
				
				<?php if (!empty($user['TeamsUser'])): ?>
					
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Team Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($user['TeamsUser'] as $teamsUser): ?>
		<tr>
			<td><?php echo $teamsUser['id']; ?></td>
			<td><?php echo $teamsUser['team_code']; ?></td>
			<td><?php echo $teamsUser['user_handle']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'teams_users', 'action' => 'view', $teamsUser['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'teams_users', 'action' => 'edit', $teamsUser['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'teams_users', 'action' => 'delete', $teamsUser['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $teamsUser['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				<div class="actions">
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Teams User'), array('controller' => 'teams_users', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>				</div><!-- /.actions -->
				
			</div><!-- /.related -->

			<div class="related">

				<h3><?php echo __('Related Changes'); ?></h3>
				
				<?php if (!empty($user['Change'])): ?>
					
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Task Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Change Type Id'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Created'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($user['Change'] as $change): ?>
		<tr>
			<td><?php echo $change['id']; ?></td>
			<td><?php echo $change['task_id']; ?></td>
			<td><?php echo $change['user_id']; ?></td>
			<td><?php echo $change['change_type_id']; ?></td>
			<td><?php echo $change['text']; ?></td>
			<td><?php echo $change['created']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'changes', 'action' => 'view', $change['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'changes', 'action' => 'edit', $change['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'changes', 'action' => 'delete', $change['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $change['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				<div class="actions">
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Change'), array('controller' => 'changes', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>				</div><!-- /.actions -->
				
			</div><!-- /.related -->

					
			<div class="related">

				<h3><?php echo __('Related Comments'); ?></h3>
				
				<?php if (!empty($user['Comment'])): ?>
					
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Task Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Created'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($user['Comment'] as $comment): ?>
		<tr>
			<td><?php echo $comment['id']; ?></td>
			<td><?php echo $comment['task_id']; ?></td>
			<td><?php echo $comment['user_id']; ?></td>
			<td><?php echo $comment['text']; ?></td>
			<td><?php echo $comment['created']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'comments', 'action' => 'view', $comment['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'comments', 'action' => 'edit', $comment['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'comments', 'action' => 'delete', $comment['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $comment['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				<div class="actions">
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Comment'), array('controller' => 'comments', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>				</div><!-- /.actions -->
				
			</div><!-- /.related -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
