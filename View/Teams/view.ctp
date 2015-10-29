
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="teams view">

			<h2><?php  echo __('Team'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($team['Team']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Name'); ?></strong></td>
		<td>
			<?php echo h($team['Team']['name']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Code'); ?></strong></td>
		<td>
			<?php echo h($team['Team']['code']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Zone'); ?></strong></td>
		<td>
			<?php echo h($team['Team']['zone']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

					
			<div class="related">

				
				<?php if (!empty($team['Attachment'])): ?>
					               <h3><?php echo __('Team\'s Attachments'); ?></h3>

					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Task Id'); ?></th>
		<th><?php echo __('Team Id'); ?></th>
		<th><?php echo __('Filename'); ?></th>
		<th><?php echo __('Url'); ?></th>
		<th><?php echo __('Created'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($team['Attachment'] as $attachment): ?>
		<tr>
			<td><?php echo $attachment['id']; ?></td>
			<td><?php echo $attachment['task_id']; ?></td>
			<td><?php echo $teams[$attachment['team_id']]; ?></td>
			<td><?php echo $attachment['filename']; ?></td>
			<td><?php echo $attachment['url']; ?></td>
			<td><?php echo $attachment['created']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'attachments', 'action' => 'view', $attachment['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'attachments', 'action' => 'edit', $attachment['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'attachments', 'action' => 'delete', $attachment['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $attachment['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
				                
                <div class="actions">
                    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Attachment'), array('controller' => 'attachments', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>             </div><!-- /.actions -->
                
            </div><!-- /.related -->	
				<?php endif; ?>

			<div class="related">
				<?php if (!empty($team['TeamsUser'])): ?>
					           <h3><?php echo __('Related Users'); ?></h3>
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											
		<th><?php echo __('Handle'); ?></th>
		<th><?php echo __('User Role'); ?></th>
	<!--								<th class="actions"><?php echo __('Actions'); ?></th>-->
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($team['TeamsUser'] as $user): ?>
		<tr>
			<td><?php echo $this->Html->link($user['user_handle'], array('controller' => 'users', 'action' => 'view', $user['user_id'])); ?>
			<td><?php echo $user['User']['user_role']; ?></td>
			
			</td>
<!--			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'users', 'action' => 'view', $user['user_id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'users', 'action' => 'edit', $user['user_id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php //echo $this->Form->postLink(__('Delete'), array('controller' => 'users', 'action' => 'delete', $user['user_id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $user['id'])); ?>
			</td>
-->
		</tr>

	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
				<?php endif; ?>

				


					
			<div class="related">

				
				
				<?php if (!empty($team['MessageSent'])): ?>
					<h3><?php echo __('Sent Messages'); ?></h3>
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('From'); ?></th>
		<th><?php echo __('To'); ?></th>
		<th><?php echo __('Subject'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Is Read'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($team['MessageSent'] as $messageSent): ?>
		<tr>
			<td><?php echo $messageSent['id']; ?></td>
			<td><?php echo $teams[$messageSent['send_team_id']]; ?></td>
			<td><?php echo $teams[$messageSent['rec_team_id']]; ?></td>
			<td><?php echo $messageSent['subject']; ?></td>
			<td><?php echo $messageSent['text']; ?></td>
			<td><?php echo $messageSent['created']; ?></td>
			<td><?php echo $messageSent['is_read']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'messages', 'action' => 'view', $messageSent['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'messages', 'action' => 'edit', $messageSent['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'messages', 'action' => 'delete', $messageSent['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $messageSent['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					               <div class="actions">
                    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Message Sent'), array('controller' => 'messages', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>              </div><!-- /.actions -->
                
            </div><!-- /.related -->
				<?php endif; ?>

				


					
			<div class="related">


				
				<?php if (!empty($team['MessageReceived'])): ?>
				                <h3><?php echo __('Received Messages'); ?></h3>	
					<div class="table-responsive">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
											<th><?php echo __('Id'); ?></th>
		<th><?php echo __('From'); ?></th>
		<th><?php echo __('To'); ?></th>
		<th><?php echo __('Subject'); ?></th>
		<th><?php echo __('Text'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Is Read'); ?></th>
									<th class="actions"><?php echo __('Actions'); ?></th>
								</tr>
							</thead>
							<tbody>
									<?php
										$i = 0;
										foreach ($team['MessageReceived'] as $messageReceived): ?>
		<tr>
			<td><?php echo $messageReceived['id']; ?></td>
			<td><?php echo $teams[$messageReceived['send_team_id']]; ?></td>
			<td><?php echo $teams[$messageReceived['rec_team_id']]; ?></td>
			<td><?php echo $messageReceived['subject']; ?></td>
			<td><?php echo $messageReceived['text']; ?></td>
			<td><?php echo $messageReceived['created']; ?></td>
			<td><?php echo $messageReceived['is_read']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'messages', 'action' => 'view', $messageReceived['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'messages', 'action' => 'edit', $messageReceived['id']), array('class' => 'btn btn-default btn-xs')); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'messages', 'action' => 'delete', $messageReceived['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $messageReceived['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
							</tbody>
						</table><!-- /.table table-striped table-bordered -->
					</div><!-- /.table-responsive -->
					               
                <div class="actions">
                    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> '.__('New Message Received'), array('controller' => 'messages', 'action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>              </div><!-- /.actions -->
                
            </div><!-- /.related -->
				<?php endif; ?>



			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->

<?php //debug($team)?>
