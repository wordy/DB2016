
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="notifications view">

			<h2><?php  echo __('Notification'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($notification['Notification']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Type Id'); ?></strong></td>
		<td>
			<?php echo h($notification['Notification']['type_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Parent'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($notification['Parent']['short_description'], array('controller' => 'tasks', 'action' => 'view', $notification['Parent']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Child'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($notification['Child']['short_description'], array('controller' => 'tasks', 'action' => 'view', $notification['Child']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Receive Team'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($notification['ReceiveTeam']['code'], array('controller' => 'teams', 'action' => 'view', $notification['ReceiveTeam']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Send Team'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($notification['SendTeam']['code'], array('controller' => 'teams', 'action' => 'view', $notification['SendTeam']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Body'); ?></strong></td>
		<td>
			<?php echo h($notification['Notification']['body']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Is Read'); ?></strong></td>
		<td>
			<?php echo h($notification['Notification']['is_read']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
