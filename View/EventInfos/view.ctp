
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
		<div class="eventInfos view">

			<h2><?php  echo __('Event Info'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>		<td><strong><?php echo __('Id'); ?></strong></td>
		<td>
			<?php echo h($eventInfo['EventInfo']['id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Entertainment'); ?></strong></td>
		<td>
			<?php echo nl2br($eventInfo['EventInfo']['entertainment']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Prizes'); ?></strong></td>
		<td>
			<?php echo nl2br($eventInfo['EventInfo']['prizes']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Food'); ?></strong></td>
		<td>
			<?php echo nl2br($eventInfo['EventInfo']['food']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Auction'); ?></strong></td>
		<td>
			<?php echo nl2br($eventInfo['EventInfo']['auction']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('User Id'); ?></strong></td>
		<td>
			<?php echo h($eventInfo['EventInfo']['user_id']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Created'); ?></strong></td>
		<td>
			<?php echo h($eventInfo['EventInfo']['created']); ?>
			&nbsp;
		</td>
</tr><tr>		<td><strong><?php echo __('Modified'); ?></strong></td>
		<td>
			<?php echo h($eventInfo['EventInfo']['modified']); ?>
			&nbsp;
		</td>
</tr>					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
			
		</div><!-- /.view -->

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
