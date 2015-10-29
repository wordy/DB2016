<div id="page-container" class="row">

    
    <div id="page-content" class="col-md-8 col-md-offset-2 well">

        <div class="attachments form">
        
            <?php echo $this->Form->create('Attachment', array('action'=>'attachToTask', 'inputDefaults' => array(
                    'div'=>'form-group',
                    'label' => false,
                    'class'=>'form-control',
                    'error'=>array(
                        'attributes'=>array(
                            'wrap'=>'span',
                            'class'=>'help-inline text-danger bolder'))), 
                'role' => 'form')); ?><fieldset>
                    <h2><?php echo __('Add File/Link'); ?></h2>





<div class="form-group">
    <?php echo $this->Form->label('attachment_id', 'Attachment');?>
        <?php echo $this->Form->input('attachment_id', array('options'=>$attachments, 'class' => 'form-control')); ?>
</div><!-- .form-group -->


<div class="form-group">
    <?php echo $this->Form->label('task_id', 'Task');?>
        <?php echo $this->Form->input('task_id', array('options'=>$tasks, 'class' => 'form-control')); ?>
</div><!-- .form-group -->

                </fieldset>
            <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
            
        </div><!-- /.form -->
            
    </div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->

















<?php

     /* Load templates 
    //echo $this->UploadTemplate->renderForm(array('action' => 'upload')); //Set action for form
    //echo $this->UploadTemplate->renderListFiles(array('action_delete' => 'deleteFile')); //Set action for remove files

    /* Load libs js e css jQuery-File-Upload and dependences */
    
    /*
   echo $this->UploadScript->loadLibs();
   echo $this->Html->scriptBlock("
      $(function () {
        $('#fileupload').fileupload({
                    xhrFields   : {withCredentials: true},
                    url         : '/cake/attachments/upload',
                    dataType: 'html', //Set your action
            });
        });    
    ");
    */
?>		
			
