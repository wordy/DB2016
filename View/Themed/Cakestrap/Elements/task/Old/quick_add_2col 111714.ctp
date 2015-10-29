
<?php

    echo $this->Html->script('form-controls');
              
    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
        //$controlled_teams = AuthComponent::user('TeamsList');
    }
    
    $control_team_count = count($controlled_teams, COUNT_RECURSIVE) - count($controlled_teams);
    
    //Form field defaults
    $readonly = false;
    $team_input_readonly = false;
    
    if($control_team_count >=2){
        $team_input_empty = true;
    }

    elseif($control_team_count == 1){
        $team_input_readonly = 'readonly';
        $team_input_empty = false;
    }
    else{ $team_input_empty = false;}
    
    $this->Js->buffer("
        //$('#qa_spinner').css({'display':'none'});
    ");
    
?>



<?php echo $this->Form->create('Task', array(
                        'action'=>'edit',
                        //'class'=>'highlight',
                        'novalidate' => true,
                        'inputDefaults' => array(
                            'label' => false), 
                            'role' => 'form'));
                    ?>
    <div class="panel panel-primary panel-taskcolored">
<!--
	<div class="panel-heading">
            <h4 class="panel-title"><b><i class="fa fa-bookmark-o"></i> &nbsp;Add Task</b></h4>
        </div>-->
        
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    
              
                    <div class="row">
                        <div class="col-md-4">
                            <?php echo $this->Form->label('task_type_id', 'Type*'); ?>
                            <?php echo $this->Form->input('task_type_id', array('options'=>$taskTypes, 'class' => 'form-control')); ?>
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
                                'error' => array(
                                    'attributes' => array(
                                        'id'=>'stime-error-msg', 
                                        'wrap' => 'span', 
                                        'class' => 'help-inline text-danger bolder')))); ?>            
                         </div>
                         <div class="col-md-4">   
                            <?php echo $this->Form->input('end_time', array(
                                'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                                'type'=>'text',
                                'label'=>'End Time',
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                    'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                    'class'=>'form-control datetimepicker-stime',
                                    'error' => array(
                                        'attributes' => array(
                                            'id'=>'etime-error-msg', 
                                            'wrap' => 'span', 
                                            'class' => 'help-inline text-danger bolder')
                                    )));
                            ?>           
                        </div>
                    </div>

                    <div class="row sm-top-marg">
                        <div class="col-md-10">
                            <?php echo $this->Form->label('short_description', 'Short Description*');?>
                            <?php echo $this->Form->input('short_description', array(
                                'class' => 'form-control',
                                'error' => array(
                                    'attributes' => array(
                                        'wrap' => 'span', 
                                        'class' => 'help-inline text-danger bolder')))); ?>
                        </div>

                        <div class="col-md-2">
                            <b>Add</b><br/>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#details">Details</button>
                            <button type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#statuses">Due Date/Status</button>
                        </div>
                    </div>

                    <div class="row collapse sm-top-marg" id="details">
                        <div class="col-md-12">
                            <?php echo $this->Form->label('description', 'Details');?>
                            <?php echo $this->Form->input('details', array(
                                'id'=>'qa_input-details',
                                'class' => 'input-details form-control')); ?>
                        </div>
                    </div>



                    <div class="row sm-top-marg">
                        <div class="col-md-12">
                            <!--submit button-->
                        </div>
                    </div>
                </div><!--leftcol-->
                
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">Teams</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('team_id', 'Lead*');?>
                                        <?php echo $this->Form->input('team_id', array(
                                            'empty'=>$team_input_empty,
                                            'readonly'=>$team_input_readonly,
                                            'div'=>false, 
                                            'multiple'=>false, 
                                            'options'=>$controlled_teams, 
                                            'id'=>'qa_input-leadteam-select', 
                                            'class' => 'form-control',
                                            'error' => array(
                                                'attributes' => array(
                                                    'id'=>'lead-team-error-message',
                                                    'wrap' => 'span', 
                                                    'class' => 'help-inline text-danger bolder')))); ?>
                                    </div><!-- .form-group -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('AssistTeams', 'Assisting');?>
                                        <?php echo $this->Form->input('AssistTeams', array(
                                            'empty'=>true, 
                                            'id'=>'qa_input-ateams-select', 
                                            'options'=>$teams, 
                                            'type'=>'select', 
                                            'placeholder'=>'Assisting Team(s)', 
                                            'multiple'=>true, 
                                            'class' => 'input-conteam-select')); ?>
                                    </div><!-- .form-group -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                
                                    <div class="form-group">
                                        <?php echo $this->Form->label('PushTeam', 'Pushed To');?>
                                        <?php echo $this->Form->input('PushTeams', array(
                                            'empty'=>true, 
                                            'id'=>'qa_input-pteam-select', 
                                            'options'=>$teams, 
                                            'type'=>'select', 
                                            'placeholder'=>'Push to Team(s)', 
                                            'multiple'=>true, 
                                            'class' => 'input-conteam-select'
                                            )); ?>
                                    </div><!-- .form-group -->    
                                </div>

                                
                            </div>
                        </div><!--panel body-->
                    </div><!--panel-->
                    
                    <div class="row collapse sm-top-marg" id="statuses">
                        <div class="col-md-12">
                            <div class="panel panel-danger">
                                <div class="panel-heading"><b>Dates &amp; Statuses</b></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php if($user_role >= 100): ?>
                                            <?php echo $this->Form->label('actionable_type_id', 'Action Item Status');?>
                                            <?php echo $this->Form->input('actionable_type_id', array(
                                                'empty'=>true,
                                                'options'=>$actionableTypes, 
                                                'div'=>array(
                                                    'class'=>'input-group'),
                                                    'after'=>'<span class="input-group-addon"><i class="fa fa-flag"></i></span>',
                                                    'class' => 'form-control')); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div><!--panel body-->
                            </div><!--panel-->
                        </div>
                    </div>   
                </div><!-- left col-->
            </div>
        </div><!--main panel body-->
    </div><!--panel-->
                                <?php echo $this->Form->submit('Add Task', array('class' => 'btn btn-large btn-success')); ?>

    <?php echo $this->Form->end(); ?>
<?php echo $this->Js->writeBuffer();?>





