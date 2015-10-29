<?php
$this->Js->buffer("

    $('.tc-add-button').on('click', function(event){
        //var tid = $(this).data('tid');
        
        var add_button = $(this);
        
        
        var spinner = $('".$this->Html->image('ajax-loader-small.gif', array('id' => 'spinner'))."');
        //var ebtd = eb.closest('td');
        
        //var ebtdid = ebtd.attr('id');
        
        //var ebtr = eb.closest('tr');
        
        

        
        
        
        $.ajax( {
            url:'/task_colors/ajaxAdd/,
           
           
            beforeSend:function () {
                add_button.text('Saving...');
                //$(this).dropdown('toggle');
                var ahid = add.hide();
                ebtd.append(ahid);                
                spinner.fadeIn();
                //$('#spinner').fadeIn();
            },

            success:function(data, textStatus) {
                //$('#ajax-edit-load').html(data).hide();
                new_edit.slideDown('slow');
                
                //var_new_edit = ebtr.after(new_row).hide();
                
                //new_tr.addClass('ajax-edit-tr');
                //new_tr.
                
                //)
                //ebtr.after('<tr class=\"ajax-edit-tr\"><td colspan=\"6\">'+data+'</td></tr>');
                
                //$('#ajax-edit-load').slideDown('slow');
                
              
                
                
            },
            
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut();
                //$('#input-conteam-select').select2();
                
            }, 
            type: 'get',
            dataType:'html',
          });
          return false;
    });
    
    
    
    
    
    
    
    
    
");


?>

<div id="page-container" class="row">
    <div id="add-new-content" class="col-sm-8 col-sm-offset-2 well">
        <div class="taskColors form">
            <?php echo $this->Form->create('TaskColor', array(
                'controller'=>'task_color',
                'action'=>'add',
                'inputDefaults' => array(
                    'label' => false), 
                'role' => 'form')); ?>
            <fieldset>
                <h2><?php echo __('Add Task Color'); ?></h2>
                <p>
                    Colors are used to group tasks in free-form ways.  Add colors here to allow more choices.
                </p>
<?php echo $this->Form->input('name', array(
                            'format' => array(
                                'label', 'between', 'before', 'input', 'after', 'error'),
                            'type'=>'text',
                            'label'=>'Friendly Name of Colour*',
                            'between'=>'',
                            'before'=>'',
                            'placeholder'=>'Choose a Name',
                            'after'=>'<p class="help-block">E.g. Lime Green or Navy Blue</p>',
                            'class'=>'form-control',
                            'error' => array(
                                'attributes' => array(
                                    'wrap' => 'span', 
                                    'class' => 'help-inline text-danger bolder')))); ?>
                <div class="form-group">
                    <?php echo $this->Form->label('code', 'Hex Color Code');?>
                    <?php echo $this->Form->input('code', array('class' => 'form-control')); ?>
                    <p class="help-block">6-Character Hex value, starting with #.  E.g. #CCCCCC</p>
                </div><!-- .form-group -->
            </fieldset>
            <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.form -->
    </div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->


<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="taskColors index">
		
			<h2><?php echo __('Task Colors'); ?></h2>
			
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
