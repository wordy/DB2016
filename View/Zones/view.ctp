
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="zones view">

			<h2><?php  echo __('Zone'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($zone['Zone']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Code'); ?></strong></td>
		<td>
			<?php echo h($zone['Zone']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Org Level'); ?></strong></td>
		<td>
			<?php echo h($zone['Zone']['org_level']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Description'); ?></strong></td>
		<td>
			<?php echo h($zone['Zone']['description']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Gm User Id'); ?></strong></td>
		<td>
			<?php echo h($zone['Zone']['gm_user_id']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

					
			<div class="related">

				<h3><?php echo __('Related Teams'); ?></h3>
				
				<?php if (!empty($zone['Team'])): ?>
					
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Code'); ?></th>
		<th><?php echo __('Zone'); ?></th>
		<th><?php echo __('Zone Id'); ?></th>
		<th><?php echo __('Task Color Id'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($zone['Team'] as $team): ?>
		<tr>
			<td><?php echo $team['id']; ?></td>
			<td><?php echo $team['name']; ?></td>
			<td><?php echo $team['code']; ?></td>
			<td><?php echo $team['zone']; ?></td>
			<td><?php echo $team['zone_id']; ?></td>
			<td><?php echo $team['task_color_id']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'teams', 'action' => 'view', $team['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'teams', 'action' => 'edit', $team['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'teams', 'action' => 'delete', $team['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $team['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					
				<?php endif; ?>

				
				<div class="actions">
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Team'), array('controller' => 'teams', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>				</div><!-- /.actions -->
				
			</div><!-- /.related -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
