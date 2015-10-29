
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
         $this->Js->buffer("
            $('#qa_input-leadteam-select').change(function () {
                var selected = $(this).val();
                $('#qa_spinner').show();
    
                $.ajax({
                    type: 'POST',
                    url: '/cake/tasks/parents/' + selected,
                    success: function (msg) {
                        $('#qa_linkableparents').html(msg);
                        $('#qa_spinner').hide();
                    }
                });
            });
      ");
    }

    elseif($control_team_count == 1){
        $team_input_readonly = 'readonly';
        $team_input_empty = false;
        $this->Js->buffer("
            $('#qa_linkableparents').on('blur', function(){
                var selected = $('#qa_input-leadteam-select').val();
              $('#qa_spinner').show();
        
                $.ajax({
                    type: 'POST',
                    url: '/tasks/parents/' + selected,
                    success: function (msg) {
                        $('#qa_linkableparents').html(msg);
                        $('#qa_spinner').hide();
                    }
                });
            });
            $('#qa_linkableparents').trigger('blur');
        ");
    }
    else{ $team_input_empty = false;}
    
    $this->Js->buffer("
        $('#qa_spinner').css({'display':'none'});
    
        
    
    
        
        

    
    ");
    
?>


<div class="row">
    <div class="panel panel-primary panel-taskcolored">
        <div class="panel-heading">
            <h4 class="panel-title"><b><i class="fa fa-bookmark-o"></i> &nbsp;Add Task</b></h4>
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-md-9">
                    <?php echo $this->Form->create('Task', array(
                        'action'=>'add',
                        'novalidate' => true,
                        'inputDefaults' => array(
                            'label' => false), 
                            'role' => 'form'));
                    ?>
              
                    <div class="row">
                        <div class="col-md-2">
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
                        
                        <div class="col-md-2">
                            <?php echo $this->Form->label('Color', 'Color');?>
                            <a class="text-info boot-popover" href="#" id="pop-tcolor" data-placement="auto" data-trigger="hover" data-container="body" data-toggle="popover" title="Task Colours" data-content="Team's can use colours to organize tasks.  For example, assign all purple tasks to a specific team leader, or have all yellow tasks be ones needing review within a week.  <br /><br /><b>NOTE:</b> Colours may only be visible to your team." data-original-title="Task Colors"><i class="fa fa-question"></i></a>
                            <?php echo $this->Form->input('Task.Color', array('class'=>'color_picker_menu', 'options'=>$taskColors)); ?>
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
                             <small>(Optional) Use this to store extra details about this task</small>   
                            <?php echo $this->Form->input('details', array(
                                'id'=>'qa_input-details',
                                'class' => 'input-details form-control')); ?>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-md-12">Subtask For <?php
                            echo '<span id="qa_spinner" style="display: none; ">';
                            echo $this->Html->image('ajax-loader-small.gif');
                            echo '</span>';?>
                        </label>
                        
                        <div class="col-md-12" id="qa_linkableparents">
                            Choose a Lead Team to display the tasks they can link to.     
                        </div>
                    </div>

                    <div class="row sm-top-marg">
                        <div class="col-md-12">
                            <?php echo $this->Form->submit('Add Task', array('class' => 'btn btn-large btn-success')); ?>
                        </div>
                    </div>
                </div><!--leftcol-->
                
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">Teams</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('team_id', 'Lead Team*');?>
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
                                        <?php echo $this->Form->label('ContributingTeams', 'Requesting Help From');?>
                                        <?php echo $this->Form->input('ContributingTeams', array(
                                            'empty'=>true, 
                                            'id'=>'qa_input-conteam-select', 
                                            'options'=>$teams, 
                                            'type'=>'select', 
                                            'placeholder'=>'Select Team(s)', 
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
                                        <div class="col-md-8">
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
                                        
                                        <div class="col-md-4 xsm-top-marg">
                                            <?php echo $this->Form->label('public', 'Public');?>
                                            <a class="text-info boot-popover" href="#" 
                                                id="pop-publicprivate" 
                                                data-placement="auto"
                                                data-container="body" 
                                                data-trigger="hover" 
                                                data-toggle="popover" 
                                                title="Public vs Private Tasks" 
                                                data-content="Use private tasks for tasks that ONLY involve a single team, or in general to hide tasks that no one else would need to know about. <b>All teams can view and compile your private tasks</b>, but they're hidden by default.<br /><br />
                                                You can also use private tasks to test out tasks before officially asking other teams for help. <br /><br /><b>NOTE:</b> Tasks MUST be public if they either involve other teams, or link to another team's tasks." data-original-title="Task Colors">
                                                <i class="fa fa-question"></i></a>
                                            <?php echo $this->Form->input('public', array(
                                                'label'=>'Public', 
                                                'default'=>1, 
                                                'type'=>'checkbox', 
                                                'id'=>'qa_input-task-public')); ?>
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
    <?php echo $this->Form->end(); ?>
</div>
<?php echo $this->Js->writeBuffer();?>





