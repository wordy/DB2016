
<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="teams index">
		
			<h2><?php echo __('Teams'); ?></h2>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
    						<th><?php echo $this->Paginator->sort('id'); ?></th>
    						<th><?php echo $this->Paginator->sort('name'); ?></th>
    						<th><?php echo $this->Paginator->sort('code'); ?></th>
    						<th><?php echo $this->Paginator->sort('zone'); ?></th>
    						<th><?php echo $this->Paginator->sort('zone_id', "Zone ID"); ?></th>
    						<th class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($teams as $team): ?>
	<tr>
		<td
		<?php if(!empty($team['TaskColor']['code'])){ echo 'style="background-color:'.$team['TaskColor']['code'].'"';
        } 
        ?>
		
		
		><?php echo h($team['Team']['id']); ?>&nbsp;</td>
		<td><?php echo h($team['Team']['name']); ?>&nbsp;</td>
		<td><?php echo h($team['Team']['code']); ?>&nbsp;</td>
		<td><?php echo h($team['Team']['zone']); ?>&nbsp;</td>
        <td><?php echo h($team['Team']['zone_id']); ?>&nbsp;</td>

		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $team['Team']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $team['Team']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $team['Team']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $team['Team']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
					</tbody>
				</table>
			</div><!-- /.table-responsive -->
			
			<p><small>
				<?php
				echo $this->Paginator->counter(array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
				));
				?>			</small></p>

			<ul class="pagination">
				<?php
		echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
		echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
		echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	?>
			</ul><!-- /.pagination -->
			
		</div><!-- /.index -->
	
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
