
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="printPrefs view">

			<h2><?php  echo __('User Print Preference'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($PrintPref['PrintPref']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('User'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($PrintPref['User']['handle'], array('controller' => 'users', 'action' => 'view', $PrintPref['User']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Task'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($PrintPref['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $PrintPref['Task']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>
<tr>		<td><strong><?php echo __('Hide Detail'); ?></strong></td>
		<td>
			<?php echo $PrintPref['PrintPref']['hide_detail']; ?>
			&nbsp;
		</td>
		
		
</tr>
<tr>		<td><strong><?php echo __('Hide Task'); ?></strong></td>
		<td>
			<?php echo $PrintPref['PrintPref']['hide_task']; ?>
			&nbsp;
		</td>
		
		
</tr>




					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
