<?php 

if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
    }

?>

<div class="row">
                    <div class="col-md-9">
                        <?php echo $this->Form->create('Task', array(
                            //'url' => array(
                            //  'controller' => 'tasks', 
                            //'action' => 'edit', $task['Task']['id']),
                            'action'=>'edit',
                            'type'=>'post', 
                            'id'=>'form-edit-task', 
                            //'novalidate' => true,
                            'inputDefaults' => array(
                                'label' => false), 
                            'role' => 'form')); ?>
                        <?php echo $this->Form->input('id', array('type'=>'hidden')); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->label('task_type_id', 'Task Type*'); ?>
                                <?php echo $this->Form->input('task_type_id', array('class' => 'form-control', 'id'=>'input-tasktype-select', 'options'=>$taskTypes)); ?>
                            </div><!-- .form-group -->
                        </div>

                        <div class="col-md-4">
                            <?php echo $this->Form->input('start_time', array(
                                'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                                'type'=>'text',
                                'label'=>'Start Time*',
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                'class'=>'form-control datetimepicker-stime',
                                'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')))); 
                            ?>
                        </div>

                        <div class="col-md-4">
                            <?php echo $this->Form->input('end_time', array(
                                'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                                'type'=>'text',
                                'label'=>'End Time*',
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                'class'=>'form-control datetimepicker-etime',
                                'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')))); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php echo $this->Form->label('short_description', 'Short Description*');?>
                                <?php echo $this->Form->input('short_description', array(
                                    'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')),
                                    'class' => 'form-control')); ?>
                            </div><!-- .form-group -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?php echo $this->Form->input('details', array(
                                'label'=>'Details', 
                                'id'=>'edit_input-details', 
                                'class'=>'input-details form-control', 
                                'type' => 'textarea')); ?>
                            <p class="help-block">(Optional) Use this to store extra details about this task</p>   
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?php 
                            echo $this->Form->submit('  Save Changes  ', array('id'=>'form-submit-button', 'div'=>false, 'class' => 'btn btn-large btn-success'));
                            echo '&nbsp;&nbsp;';
                            echo $this->Html->link('Cancel', array('action'=>'compile'), array('class'=>'btn btn-large btn-danger'));
                            echo $this->Form->end(); ?> 
                        </div>
                    </div>
                </div><!--col-md-9-->

                <div class="col-md-3">
                    <div class="panel panel-yh">
                        <div class="panel-heading"><b>Teams</b></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('team_id', 'Lead Team*');?>
                                        <?php echo $this->Form->input('team_id', array(
                                            'empty'=>1,
                                            'readonly'=>1, 
                                            'div'=>false, 
                                            'multiple'=>false, 
                                            'options'=>array(1,2,3,4), 
                                            'id'=>'edit_input-leadteam-select', 
                                            'class' => 'form-control',
                                            'error' => array('attributes' => array(
                                                'id'=>'lead-team-error-message',
                                                'wrap' => 'span', 
                                                'class' => 'help-inline text-danger bolder')
                                            ))); ?>
                                    </div><!-- .form-group -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('AssistTeams', 'Request Response From');?>
                                        <?php echo $this->Form->input('AssistTeams', array(
                                            'empty'=>true, 
                                            'id'=>'e10', 
                                            'options'=>array(1,2,3), 
                                            'selected'=>array(1,2,3),
                                            'type'=>'select', 
                                            'placeholder'=>'Select Assisting Team(s)', 
                                            'multiple'=>true, 
                                            'class' => 'input-conteam-select')); ?>
                                    </div><!-- .form-group -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('PushTeams', 'Notify (Nothing Needed)');?>
                                        <?php echo $this->Form->input('PushTeams', array(
                                            'empty'=>true, 
                                            'id'=>'e11', 
                                            'options'=>array(1,2,3),
                                            'selected'=>array(1,2,3), 
                                            'type'=>'select', 
                                            'placeholder'=>'Select Pushed Team(s)', 
                                            'multiple'=>true, 
                                            'class' => 'input-conteam-select')); ?>
                                    </div> <!--.form-group--> 
                                </div>
                            </div>
                        </div><!--panel body-->
                    </div><!--panel-->
                
                    <div class="panel panel-danger">
                        <div class="panel-heading"><b>Dates &amp; Statuses</b></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('due_date', 'Due Date'); ?>
                                        <?php echo $this->Form->input('due_date', array(
                                            'empty'=>true,
                                            'type'=>'text',
                                            'placeholder'=>'Set due date',
                                            'div'=>array(
                                                'class'=>'input-group'),
                                            'after'=>'<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>',
                                            'class'=>'form-control input-date-notime')); ?>
                                    </div>  
                                </div>
                            </div>
                            
                        <?php if($user_role >= 200): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('actionable_type_id', 'Action Item Status');?>
                                        <?php echo $this->Form->input('actionable_type_id', array(
                                            //'id'=>'input-actionabletype-select',
                                            'empty'=>true,
                                            'value'=>1,
                                            'options'=>array(1,2,3), 
                                            'div'=>array(
                                                'class'=>'input-group'),
                                                //'label'=>'Actionable Type',        
                                            'after'=>'<span class="input-group-addon"><i class="fa fa-flag"></i></span>',
                                            'class' => 'form-control')); ?>
                                    </div><!-- .form-group -->
                                </div>
                            </div>                        
                        <?php endif; ?>
                        </div><!--panel-->
                    </div><!--end status panel-->
                </div><!--col-md-3-->
            </div><!--outer panel body-->
