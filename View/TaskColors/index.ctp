<div id="page-container" class="row">
    <div id="add-new-content" class="col-sm-10 col-sm-offset-1 well">
        <div class="taskColors form">
            <?php echo $this->Form->create('TaskColor', array(
                'controller'=>'task_color',
                'action'=>'add',
                'inputDefaults' => array(
                    'label' => false), 
                'role' => 'form')); ?>
            <fieldset>
                <h1><?php echo __('Add Task Color'); ?></h1>
                <p>Colors are used to group tasks in free-form ways.  Add colors here to allow more choices.</p>
<?php echo $this->Form->input('name', array(
                            
                            'type'=>'text',
                            'label'=>'Friendly Name of Colour*',
                            'between'=>'',
                            'before'=>'',
                            'placeholder'=>'Choose a Name',
                            'after'=>'<p class="help-block">E.g. Lime Green or Navy Blue</p>',
                            'class'=>'form-control',
                            )); ?>
                <div class="form-group">
                    <?php echo $this->Form->label('code', 'Hex Color Code');?>
                    <?php echo $this->Form->input('code', array('class' => 'form-control')); ?>
                    <p class="help-block">6-Character Hex value, starting with #.  E.g. #CCCCCC</p>
                </div><!-- .form-group -->
            </fieldset>
            <?php echo $this->Form->submit('Save Colour', array('class' => 'btn btn-large btn-yh')); ?>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.form -->
    </div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->


<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="taskColors index">
		
			<h1><?php echo __('Task Colors'); ?></h1>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
															<th><?php echo $this->Paginator->sort('id'); ?></th>
															<th><?php echo $this->Paginator->sort('name'); ?></th>
															<th><?php echo $this->Paginator->sort('code'); ?></th>
															<th class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($taskColors as $taskColor): ?>
	<tr>
		<td><?php echo h($taskColor['TaskColor']['id']); ?>&nbsp;</td>
		<td style="background: <?php echo $taskColor['TaskColor']['code'];?>"><?php echo h($taskColor['TaskColor']['name']); ?>&nbsp;</td>
		<td><?php echo h($taskColor['TaskColor']['code']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $taskColor['TaskColor']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $taskColor['TaskColor']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $taskColor['TaskColor']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $taskColor['TaskColor']['id'])); ?>
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
