<div id="page-container" class="row">
    <div id="page-content" class="col-sm-8 col-sm-offset-2 well">
        <div class="taskTypes form">
            <?php echo $this->Form->create('TaskType', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
            <fieldset>
                <h2><?php echo __('Add Task Type'); ?></h2>
                <p>
                    Task Types are used to classify tasks.  For example, when adding a task you can specify that 
                    a task is a phone call, email, or cue to another team.
                </p>
                <div class="form-group">
                    <?php echo $this->Form->label('name', 'Name of Task Type');?>
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                    <p class="help-block">E.g. REQ-Cue or SUB-Info</p>
                </div><!-- .form-group -->
                <div class="form-group">
                    <?php echo $this->Form->label('description', 'Description');?>
                    <?php echo $this->Form->input('description', array('class' => 'form-control')); ?>
                </div><!-- .form-group -->
                <div class="form-group">
                    <?php echo $this->Form->label('grouping', 'Grouping');?>
                    <?php echo $this->Form->input('grouping', array(
                        'options'=>array(
                            'Basic'=>'Basic',
                            'Request'=>'Requests',
                            'Send'=>'Submissions',
                            'Broadcast' => 'Broadcast'
                        ),
                        
                        'class' => 'form-control')); ?>
                    <p class="help-block">Type of TaskType.  I.e. Submissions or Requests or Team Tasks</p>
                </div><!-- .form-group -->
            </fieldset>
            <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.form -->
    </div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
