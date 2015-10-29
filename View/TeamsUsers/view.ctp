
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="teamsUsers view">

			<h2><?php  echo __('Teams User'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($teamsUser['TeamsUser']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Team'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($teamsUser['Team']['code'], array('controller' => 'teams', 'action' => 'view', $teamsUser['Team']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('User'); ?></strong></td>
		<td>
			<?php echo $this->Html->link($teamsUser['User']['handle'], array('controller' => 'users', 'action' => 'view', $teamsUser['User']['id']), array('class' => '')); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
