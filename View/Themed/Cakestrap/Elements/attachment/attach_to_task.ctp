<?php 

//debug($task);

    if(empty($poss_attachments)){
        $data = $this->requestAction(array(
            'controller' => 'attachments', 
            'action' => 'attachToTaskFromEdit', $task));

        $poss_attachments = $data['attachments'];
        $tasks = $data['tasks'];
        $selected_task = $data['selected_task'];
    }

?>


    <div class="panel panel-default" id="newattachmentcontent">
    <div class="panel-heading"><i class="fa fa-paperclip fa-lg"></i>&nbsp;&nbsp;<b>Attach New File</b></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
<div class="attachments">
            <?php  debug($tasks);
                if(!empty($poss_attachments)){
                    echo $this->Form->create('Attachment', array('action'=>'attachToTask2', 'inputDefaults' => array(
                        'div'=>'form-group',
                        'label' => false,
                        'class'=>'form-control',
                        'error'=>array(
                            'attributes'=>array(
                                'wrap'=>'span',
                                'class'=>'help-inline text-danger bolder'))), 
                        'role' => 'form')); ?>
                        
                    <fieldset>
                        <div class="form-group">
                        <?php echo $this->Form->label('attachment_id', 'Attachment');?>
                        <?php 
                            echo $this->Form->input('attachment_id', array('options'=>$poss_attachments, 'class' => 'form-control'));
                        ?>
                        </div><!-- .form-group -->
        
                        <div class="form-group">
                        <?php echo $this->Form->label('task_id', 'Task');?>
                        <?php echo $this->Form->input('task_id', array(
                            //'type'=>'hidden',
                            'options'=>$tasks, 
                            'selected'=> (!empty($selected_task)) ? $selected_task: '',
                            'class' => 'form-control',
                            //'readonly' => 'readonly'
                            //'disabled'=> (!empty($selected_task)) ? 'disabled': false,
                            )); ?>
                        </div><!-- .form-group -->
                    </fieldset>
                    <?php echo $this->Form->submit('Attach', array('class' => 'btn btn-large btn-primary')); ?>
                    <?php echo $this->Form->end(); 
            }                 
            else{ ?>
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="alert alert-info">
                            You currently have no files available to attach.  Check:<br>
                            <ul>
                                <li>Has your team uploaded any files?</li>
                                <li>Are all of your files already attached to tasks?</li>
                            </ul>
                            
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div><!-- /.form -->
                
            </div>
        </div>
    </div><!-- end panelbody-->
    </div><!-- end panelsuccess-->

    
    
    
    
    
    
    
    
    

















			
