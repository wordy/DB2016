
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="changes view">

			<h2><?php  echo __('Change'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($change['Change']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Task'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($change['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $change['Task']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('User'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($change['User']['handle'], array('controller' => 'users', 'action' => 'view', $change['User']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Change Type'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($change['ChangeType']['name'], array('controller' => 'change_types', 'action' => 'view', $change['ChangeType']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Text'); ?></strong></td>
		<td>
			<?php echo h($change['Change']['text']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created'); ?></strong></td>
		<td>
			<?php echo h($change['Change']['created']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
