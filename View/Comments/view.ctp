
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="comments view">

			<h2><?php  echo __('Comment'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($comment['Comment']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Task'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($comment['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $comment['Task']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('User'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($comment['User']['handle'], array('controller' => 'users', 'action' => 'view', $comment['User']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Text'); ?></strong></td>
		<td>
			<?php echo h($comment['Comment']['text']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created'); ?></strong></td>
		<td>
			<?php echo h($comment['Comment']['created']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
