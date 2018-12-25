
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="assignments view">

			<h2><?php  echo __('Assignment'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($assignment['Assignment']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Actor'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($assignment['Actor']['handle'], array('controller' => 'actors', 'action' => 'view', $assignment['Actor']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Task'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($assignment['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $assignment['Task']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Assign Role'); ?></strong></td>
		<td>
			<?php echo h($assignment['Assignment']['assign_role']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
