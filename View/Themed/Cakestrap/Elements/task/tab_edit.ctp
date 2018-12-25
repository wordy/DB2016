<?php
    if (AuthComponent::user('id')){
        $userRole = AuthComponent::user('user_role_id');
        $userTeamList = AuthComponent::user('TeamsList');
    }

    if(!empty($task)){
        $this->request->data = $task;
        $tid = $task['Task']['id'];
    }

    $assigned = array();
    if(!empty($task['Assignment'])){
        $assigned = Hash::extract($task['Assignment'],'{n}.role_id');
        //$assigned = array_values($task['Assignment']);
        //debug($assigned);
    }
    
    //$this->log($task);
    //debug($this->request->data);
    
    $singleTeamControl = false;
    $showAdvPid = false;
    if(!empty($task['Task']['parent_id'])){
        $showAdvPid = true;
    }
    
    
    
    //If user controls >2 teams, force empty input so they can't forget and accidently set an incorrect team
    $team_input_readonly = false;
    
    if(count($controlled_teams) == 1){
        $team_input_empty = false;
        $team_input_readonly = 'readonly';
        $ar_k = array_keys($userTeamList);
    
        $singleTeamControlled = $ar_k[0];
        $singleTeamControl = true;
    }
    elseif(count($controlled_teams)>=2){
        $team_input_empty = true;
    }
    
    // Task Duration
    $iTstart = strtotime($task['Task']['start_time']);
    $iTend = strtotime($task['Task']['end_time']);
    $iDurr = $iTend - $iTstart;
    
    if(($iDurr > 0) && ($iDurr < 86400)){
        $dateDurr = date('H:i:s', mktime(0, 0, $iDurr));
        $strDurr = '(<b>Duration: '.$dateDurr.'</b>)';    
    }
    elseif ($iDurr > 86400){
        $strDurr = '(<b>Duration: >1 Day</b>)';
    }
    else{
        $strDurr = '(<b>Duration: None</b>)';
    }

    $this->Js->buffer("
        $('#eaStartTime".$tid." ,#eaEndTime".$tid."').datetimepicker({
            sideBySide: true, showTodayButton: true, allowInputToggle: true, format: 'YYYY-MM-DD HH:mm:ss', 
        });
        
        $('#eaDueDate".$tid."').datetimepicker({
            sideBySide: true, showTodayButton: true, allowInputToggle: true, format: 'YYYY-MM-DD', 
        });
        
        bindSummernoteStd('#eaInputDetails".$tid."');

        var orig_start = moment($('#eaStartTime".$tid."').val());
        var orig_end = moment($('#eaEndTime".$tid."').val());
        var orig_duration = orig_end.diff(orig_start);
        //console.log('orig_duration '+orig_duration/1000);
        
        
        $('.inputStartTime').on('dp.change', function(){
            //console.log('chk start got dp-dp.change change from tab_edit');

            var inStart = $(this).parents('form').find('.inputStartTime');
            var inEnd = $(this).parents('form').find('.inputEndTime');
            var startTime = inStart.data('DateTimePicker').date();
            var endTime = inEnd.data('DateTimePicker').date();
            var diff = endTime-startTime;
            
            //var emom = endTime.data('DateTimePicker').date();
            if(orig_duration > 0){
                inEnd.data('DateTimePicker').minDate(moment(startTime)).date(moment(startTime).add(orig_duration,'ms'));
            }
            else{
                inEnd.data('DateTimePicker').minDate(moment(startTime));
            }
        });

        $('.inputEndTime').on('dp.change', function(){
            //console.log('chk end got dp-dp.change change from tab_edit');
            var endTime = $(this).data('DateTimePicker').date();
            var startTime = $(this).parents('form').find('.inputStartTime');
            var smom = startTime.data('DateTimePicker').date();
            if(smom>endTime){
                startTime.data('DateTimePicker').date(endTime);
            }
        });

        // Show duration
        $('.inputStartTime, .inputEndTime').on('dp.change', function(){
            //console.log('got dp-dp.change change from tab_edit');
            var inStart = $(this).parents('form').find('.inputStartTime');
            var inEnd = $(this).parents('form').find('.inputEndTime');
            var startTime = inStart.data('DateTimePicker').date();
            var endTime = inEnd.data('DateTimePicker').date();
            var diff = endTime-startTime;
            if((diff > 0) && (diff <86400000)){
                var dstr = moment.utc(diff).format('HH:mm:ss');
                var diff_msg = '(<b>Duration: '+dstr+'</b>)';
                $(this).parents('form').find('.endTimeLabel').html(diff_msg);
            }
            else if(diff >= 86400000){
                $(this).parents('form').find('.endTimeLabel').html('(<b>Duration: \>1 Day</b>)');
            }
            else{
                $(this).parents('form').find('.endTimeLabel').html('(<b>Duration: None</b>)');
            }
        });
                
        // Triggers
        $('#eaEdit".$tid."').find('.inputTC').trigger('change');

        $('.eaCancelBut').on('click', function(){
            updateCo();
            return false;
        });
        
        $('.inputAssignments').select2({
            theme:'bootstrap',
            placeholder: 'Assign To',
            multiple: true,
            minimumResultsForSearch: Infinity,
        });
    
    ");

    //Figure out current team contributions
    if(!empty($task['TasksTeam'])){
        $tt = $task['TasksTeam'];
        $lead_id = Hash::extract($tt, '{n}[task_role_id=1].team_id');
        $push_id = Hash::extract($tt, '{n}[task_role_id=2].team_id');
        $ot_id = Hash::extract($tt, '{n}[task_role_id=3].team_id');
        $ct_id = Hash::extract($tt, '{n}[task_role_id=4].team_id');
        $non_lead = Hash::extract($tt, '{n}[task_role_id!=1].team_id');
    }

    $taskIsTC = (isset($task['Task']['time_control']))? true:false;

    echo $this->Form->create('Task', array(
        'url'=>array('controller'=>'tasks', 'action'=>'edit'),
        'class'=>'formEditTask',
        'type'=>'post',
        'data-tid'=> $tid, 
        'id'=>'eaEdit'.$tid, 
        'novalidate' => true,
        'inputDefaults' => array(
            'label' => false), 
        'role' => 'form')); 
?>

<?php 
//debug($task);
echo $this->Form->input('id', array('id'=>'input-task-id_'.$tid, 'type'=>'hidden')); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="eaValidationContent" id="validation_content_<?php echo $tid?>"></div>
    </div>
</div>

<div class="row" id="eaTask<?php echo $tid;?>">
    <div class="col-md-9">
        <div>
            <div class="row">
                <span class="hiddenTaskId" style = "display:none;"><?php echo $task['Task']['id'];?></span>
                <div class="col-xs-4 col-md-4">
                    <div class="form-group">
                        <?php echo $this->Form->label('task_type_id', 'Task Type*'); ?>
                        <?php echo $this->Form->input('task_type_id', array(
                            'class' => 'form-control', 
                            'id'=>'input-tasktype-select_'.$tid,
                            'div'=>array(
                                'class'=>'input-group'),
                            'after'=>'<span class="input-group-addon"><i class="fa fa-tag"></i></span>', 
                            'options'=>$taskTypes)); 
                        ?>
                    </div><!-- .form-group -->
                </div>
                    
                <div class="col-xs-4 col-md-4">
                    <div class="form-group">
                        <?php echo $this->Form->label('start_time', 'Start Time*'); ?>
    
                        <?php echo $this->Form->input('start_time', array(
                            'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                            'type'=>'text',
                            'id'=>'eaStartTime'.$tid, 
                            'between'=>'',
                            'before'=>'<div class="input-group">',
                            'placeholder'=>'Choose a date',
                            'div'=>array(
                                'data-date-format' => 'Y-m-d H:i:s'),
                            'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                            'class'=>'form-control eaStartTime inputStartTime',
                            )); 
                        ?>
                        <div class="alert alert-info slim-alert stHelpWhenTC collapse">
                            <i class="fa fa-clock-o"></i> <b>Time Synced: </b> Start time is controlled by the linked task &amp; Offset.
                        </div>
                    </div>
                </div>
                <div class="col-xs-4 col-md-4">
                    <div class="form-group">
                    <?php echo $this->Form->label('end_time', 'End Time*&nbsp; '); 
                        echo '<span class="endTimeLabel">'.$strDurr.'</span>';
                        echo $this->Form->input('end_time', array(
                            'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                            'type'=>'text',
                            'id'=>'eaEndTime'.$tid, 
                            'between'=>'',
                            'before'=>'<div class="input-group">',
                            'placeholder'=>'Choose a date',
                            'div'=>array(
                                'data-date-format' => 'Y-m-d H:i:s'),
                            'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                            'class'=>'form-control eaEndTime inputEndTime DTPdaytime',
                            )
                        );
                    ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <?php echo $this->Form->label('short_description', 'Short Description*');?>
                        <?php echo $this->Form->input('short_description', array(
                            'error' => array(
                                'attributes' => array(
                                    'wrap' => 'span', 
                                    'class' => 'help-inline text-danger bolder')),
                            'class' => 'form-control eaDescription inputDescription')); 
                        ?>
                    </div><!-- .form-group -->
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 sm-bot-marg">
                    <?php echo $this->Form->input('details', array(
                        'label'=>'Details', 
                        'id'=>'eaInputDetails'.$tid, 
                        'class'=>'input-details eaDetails form-control inputDetails', 
                        'type' => 'textarea')); 
                    ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                    <div class="well well-sm">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">                    
                                <div class="form-group">
                                    <span class="hiddenParId" style = "display:none;"><?php echo $task['Task']['parent_id'];?></span>
                                    <span class="hiddenTc" style = "display:none;"><?php echo $task['Task']['time_control'];?></span>
                                    <span class="hiddenTo" style = "display:none;"><?php echo $task['Task']['time_offset'];?></span>
                                    <?php echo $this->Form->label('parent_id', 'Linked Task'); ?>
                                    <div id="eaLinkedParentDiv<?php echo $tid;?>" class="linkedParentDiv">
                                    <?php
                                        if($linkable){
                                            echo $this->element('task/linkable_parents_list', array(
                                                'team'=>$task['Task']['team_id'],
                                                'current'=>$task['Task']['parent_id'],
                                                'child'=>$task['Task']['id']));    
                                        }
                                        else{
                                            echo '<div class="alert slim-alert alert-info" role="alert"> 
                                            Select a lead team first</div>';
                                        }
                                    ?>    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="advancedParent <?php echo (!$showAdvPid)? 'collapse':null?>">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <p><b>Synchronize</b> 
                                        <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Synchronize Tasks" data-content="Allow the <u>linked task</u> to control the start time of <u>your</u> task.  Your task moves automatically whenever the linked task moves.<br><br><b>Note:</b> When this is active, the start time is set automatically from the linked task. You will be unable to edit the start time, but may set an end time (duration) and offset."><i class="fa fa-question-circle text-info"></i></a>
                                    </p>  
                                    <div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
                                        <?php 
                                            $time_controlled = ($this->request->data('Task.time_control') == 0)? false: 'checked';
                                            echo $this->Form->input('Task.time_control', array(
                                                'type'=>'checkbox',
                                                'id'=>'eaTimeCtrl'.$tid,
                                                'class' => 'input-control eaTimeCtrl inputTC',
                                                'checked'=>$time_controlled,
                                                'div'=>false,
                                            )); 
                                        ?>
                                        <label for="eaTimeCtrl<?php echo $tid;?>">Linked task controls start time</label>
                                    </div>
                                    <span class="help-block">
                                        If selected, task moves automatically if the linked task moves.
                                    </span>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label>Offset (mm:ss)</label>
                                        <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Offset (For Synchronized Tasks)" data-content="The amount of time (mm:ss) to maintain between the start of <u>this task</u> and the <u>linked task</u>. You can choose to synchronyize the start of your task to before/after the start/end of the linked task. <br><br>When the linked task moves, this task moves automatically, such that the offset is preseved. <br><br>e.g. If you set an offset of 10 Minutes (10:00) <i>before the linked task starts</i>, your task will always start 10 minutes before the linked task, even if the linked task's start time changes. <br><br><b>Note:</b> You can only set an offset if the task is <b>synchronized</b> with the linked task.</b>">
                                            <i class="fa fa-question-circle text-info"></i>                    
                                        </a>
                                        <div class="form-inline">
                                        <?php
                                            echo $this->Form->input('Offset.minutes', array(
                                                'type'=>'number',
                                                'class'=>'form-control inputOffMin',
                                                'div'=>false,
                                                'size'=>2,
                                                'min'=> 0,
                                                'max'=> 720,
                                                'placeholder'=> '00 Min',
                                                'id'=>'inputOffMin'.$tid,
                                            ));
                                            echo '<b>:</b>';
                                            echo $this->Form->input('Offset.seconds', array(
                                                'type'=>'number',
                                                'class'=>'form-control inputOffSec',
                                                'div'=>false,
                                                'size'=>2,
                                                'min'=> -1,
                                                'max'=> 60,
                                                'placeholder'=>'00 Sec',
                                                'id'=>'inputOffSec'.$tid,
                                            ));
                                            echo '&nbsp;'; 
                                            echo $this->Form->input('Task.time_offset_type', array(
                                                'type'=>'select',
                                                'class'=>'form-control inputOffType',
                                                'id'=>'inputOffType'.$tid,
                                                'div'=>false,
                                                'empty'=>false,
                                                'default'=>-1,
                                                'options'=>array(
                                                    '-1' => "Before Linked Task STARTS",
                                                    '-2' => "Before Linked Task ENDS",
                                                    '1' => "After Linked Task STARTS",
                                                    '2'=>"After Linked Task ENDS"
                                                )
                                            ));
                                        ?>
                                        </div>
                                        <span id="eaOffsetHelpBlock" class="help-block">Time (mm:ss) between the start of your task and linked task.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--well -->
        </div>  
    </div><!--col-md-9-->
    
    <div class="col-md-3">
        <div class="row">
            <div class="col-sm-6 col-md-12">        
                <?php 
                    echo $this->Form->button('<i class="fa fa-save"></i>&nbsp; Save Task', array(
                        'type' => 'submit',
                        'id'=>'eaSubmitButton_'.$tid,
                        
                        'class' => 'btn btn-success btn-lg btn-block eaSubmitButton sm-bot-marg',
                        'escape' => false
                    ));
                ?>
            </div>
            <div class="col-sm-6 col-md-12">
                <?php
                    echo $this->Form->button('<i class="fa fa-close"></i>&nbsp;Cancel', array(
                        'class' => 'btn btn-danger btn-block btn-lg eaCancelBut sm-bot-marg',
                        'id'=>'eaCancelBut'.$tid,
                        'escape' => false
                    ));
    
                    echo '<span class="eaSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                    echo '<span class="tr_spin"><i class="fa fa-cog fa-spin"></i></span>';
                    echo '</span><br/>'; 
               ?>
           </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-dark">
                    <div class="panel-heading"><i class="fa fa-users"></i>&nbsp; Teams</div>
                        <div class="panel-body">
                            <div class="row sm-bot-marg">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?php echo $this->Form->label('team_id', 'Lead*');?>
                        
                                        <?php echo $this->Form->input('team_id', array(
                                            'empty'=>$team_input_empty,
                                            'readonly'=>$team_input_readonly,
                                            'options'=>$controlled_teams,
                                            'multiple'=>false, 
                                            'id'=>'eaLeadTeamSelect'.$tid, 
                                            'div'=>array(
                                                'class'=>'input-group'),
                                            'after'=>'<span class="input-group-addon"><i class="fa fa-users"></i></span>',
                                            'class' => 'eaLeadTeamSelect form-control inputLeadTeam')); ?>
                                    </div>
                                </div><!-- .form-group -->
                            </div>
                
                            <div class="row sm-bot-marg">
                                <div class="col-sm-12">
                                    <div class="teamsList eaTeamsList" id="eaTeamsList<?php echo $task['Task']['id']?>">
                                        <?php 
                                            echo $this->element('tasks_team/tt_signature',array(
                                                'teamsRoles'=>$allowTRoles
                                            ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div><!--panel body-->
                    </div><!--panel-->
                </div>
            </div>
        <div class="row">
            <div class="col-sm-6 col-md-12">
               <div class="panel panel-bdanger">
                    <div class="panel-heading"><i class="fa fa-flag"></i>&nbsp; Task Flags</div>
                    <div class="panel-body">
                        <div class="row">
                            <?php if($userRole >= 500):?>
                            <div class="col-xs-12">
                                <div class="form-group divInputAssignments">
                                    <?php echo $this->Form->label('Assignments', 'Assign Task'); ?>
    
                                    <?php echo $this->Form->input('Assignments', array(
                                        'empty'=>true,
                                        'id'=>'eaAssignSelect',
                                        'type'=>'select',
                                        'options'=>$roles,
                                        'selected'=>$assigned,
                                        'multiple'=>true,
                                        'div'=>array(
                                            'class'=>'input-group select2-bootstrap-append'
                                        ),
                                        'style'=>"width: 100%",
                                        'after'=>'<span class="input-group-addon"><i class="fa fa-id-badge"></i></span>',
                                        'class'=>'form-control inputAssignments',
                                        )); 
                                    ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?php echo $this->Form->label('due_date', 'Due Date'); ?>
                                    <?php echo $this->Form->input('due_date', array(
                                        'empty'=>true,
                                        'id'=>'eaDueDate'.$tid,
                                        'type'=>'text',
                                        'placeholder'=>'Set due date',
                                        'div'=>array(
                                            'class'=>'input-group'),
                                        'after'=>'<span class="input-group-addon"><i class="fa fa-bell-o"></i></span>',
                                        'class'=>'form-control eaDueDate')); ?>
                                </div>
                            </div>
                            <?php if($userRole >= 100): ?>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('actionable_type_id', 'Action Item');?>
                                        <?php echo $this->Form->input('actionable_type_id', array(
                                            'empty'=>true,
                                            'options'=>$actionableTypes, 
                                            'div'=>array(
                                                'class'=>'input-group'),
                                                'after'=>'<span class="input-group-addon"><i class="fa fa-flag"></i></span>',
                                                'class' => 'form-control'
                                            )); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div><!--panel body-->
                </div><!--panel--> 
            </div>
            <div class="col-sm-6 col-md-12">
                <div class="alert alert-danger text-center">
                    <button type="button" data-desc="<?php echo $task['Task']['short_description'];?>" data-tid="<?php echo $tid;?>" class="btn btn-block btn-danger eaTaskDeleteButton"><i class="fa fa-trash-o"></i> Delete Task</button>
                    <p class="sm-top-marg"><i class="fa fa-warning"></i>&nbsp;<b>Warning:</b> Cannot be undone</p>                               
                </div>
            </div>
        </div>
    </div><!--col-md-3-->
</div><!--row-->
        


<?php

    echo $this->Form->end(); 
    echo $this->Js->writeBuffer(); 
?> 
<!-- end edit--> 
