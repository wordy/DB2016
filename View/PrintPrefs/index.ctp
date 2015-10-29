
<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="PrintPrefs index">
		
			<h2><?php echo __('User Print Preferences'); ?></h2>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
															<th><?php echo $this->Paginator->sort('id'); ?></th>
															<th><?php echo $this->Paginator->sort('user_id'); ?></th>
															<th><?php echo $this->Paginator->sort('task_id'); ?></th>
															<th><?php echo $this->Paginator->sort('hide_detail'); ?></th>
															<th><?php echo $this->Paginator->sort('hide_task'); ?></th>
															<th class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($PrintPrefs as $PrintPref): ?>
	<tr>
		<td><?php echo h($PrintPref['PrintPref']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($PrintPref['User']['handle'], array('controller' => 'users', 'action' => 'view', $PrintPref['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($PrintPref['Task']['short_description'], array('controller' => 'tasks', 'action' => 'view', $PrintPref['Task']['id'])); ?>
		</td>
        <td>
            <?php echo $PrintPref['PrintPref']['hide_detail']; ?>
        </td>
		<td>
            <?php echo $PrintPref['PrintPref']['hide_task']; ?>
        </td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $PrintPref['PrintPref']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $PrintPref['PrintPref']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $PrintPref['PrintPref']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $PrintPref['PrintPref']['id'])); ?>
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
