<?php
    if (AuthComponent::user('id')){
        $userRole = AuthComponent::user('user_role_id');
        $userTeamList = AuthComponent::user('TeamsList');
    }

    if(!empty($task)){
        $this->request->data = $task;
        $tid = $task['Task']['id'];
    }
    
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
    
    //$cpar = (!empty($task['Task']['parent_id'])) ? $task['Task']['parent_id']: 0;

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
    
        $('#eaStartTime".$tid."').datetimepicker({
            sideBySide: true,
            showTodayButton: true,
            allowInputToggle: true,
            format: 'YYYY-MM-DD HH:mm:ss', 
        });
        
        $('#eaEndTime".$tid."').datetimepicker({
            sideBySide: true,
            showTodayButton: true,
            allowInputToggle: true,
            format: 'YYYY-MM-DD HH:mm:ss', 
        });

        $('#eaDueDate".$tid."').datetimepicker({
            sideBySide: true,
            showTodayButton: true,
            allowInputToggle: true,
            format: 'YYYY-MM-DD', 
        });
        
        $('#eaInputDetails".$tid."').summernote({
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['misc', ['undo','redo','help']],
            ]
        });

        /*        
        $('.helpTTs').popover({
            container: 'body',
            html:true,
        });

        */
        $('.inputStartTime').on('dp.change', function(){
            //console.log('got dp-dp.change change from tab_edit');
            var startTime = $(this).data('DateTimePicker').date();
            var endTime = $(this).parents('form').find('.inputEndTime');
            var emom = endTime.data('DateTimePicker').date();
            if(emom < startTime){
                endTime.data('DateTimePicker').date(startTime);
            }
        });

        $('.inputEndTime').on('dp.change', function(){
            var endTime = $(this).data('DateTimePicker').date();
            var startTime = $(this).parents('form').find('.inputStartTime');
            var smom = startTime.data('DateTimePicker').date();
            if(smom>endTime){
                startTime.data('DateTimePicker').date(endTime);
            }
        });

        
        $('.inputStartTime, .inputEndTime').on('dp.change', function(){
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
        'action'=>'edit',
        'class'=>'formEditTask',
        'type'=>'post',
        'data-tid'=> $tid, 
        'id'=>'eaEdit'.$tid, 
        'novalidate' => true,
        'inputDefaults' => array(
            'label' => false), 
        'role' => 'form')); 
    ?>
    <div class="row" id="eaTask<?php echo $tid;?>">
        <div class="col-md-9">
            
            <div>
                <?php echo $this->Form->input('id', array(
                    'id'=>'input-task-id_'.$tid,
                    'type'=>'hidden')); ?>

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
                            )
                        ); 
                        ?>
                        <div class="alert alert-info slim-alert stHelpWhenTC collapse">
                            <i class="fa fa-clock-o"></i> <b>Time Link: </b> Start time is controlled by the linked task &amp; Offset.
                        </div>

                        </div>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <div class="form-group">
                        <?php echo $this->Form->label('end_time', 'End Time*&nbsp; '); 
                       echo '<span class="endTimeLabel">'.$strDurr.'</span>';
                       
                        ?>
                        
                        <?php 
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
                                        <?php echo $this->Form->label('parent_id', 'Link To Task'); ?>
                                        <div id="eaLinkedParentDiv<?php echo $tid;?>" class="linkedParentDiv">
                                        <?php
                                            if($linkable){
                                                echo $this->element('task/linkable_parents_list', array(
                                                    //'linkable'=>$linkable,
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
                                        <p><b>Time Link</b> <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Allow Linked Task to Control Start Time (Time Link)" data-content="The <u>linked task</u> controls the start time of <u>your</u> task.  Your task moves automatically whenever the linked task moves.<br><br><b>Note:</b> When this is active, the start time is set automatically from the linked task. You will be unable to edit the start time, but may set an end time (i.e. duration) and offset.">
                                                <i class="fa fa-question-circle text-info"></i>                    
                                            </a></p>  
                                        <div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
                                        <?php 
                                            $time_controlled = ($this->request->data('Task.time_control') == 0)? false: 'checked';
                                            echo $this->Form->input('Task.time_control', array(
                                                'type'=>'checkbox',
                                                'id'=>'eaTimeCtrl'.$tid,
                                                'class' => 'input-control eaTimeCtrl inputTC',
                                                'checked'=>$time_controlled,
                                                //'checked'=>true,
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
                <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Offset (Linked Tasks)" data-content="The offset is the amount of time in mm:ss between when your task starts and when the linked task starts. <br><br>If your task is moved automatically due to the time linked task moving, the <b>offset</b> will always be maintained. <br><br>i.e. If you set an offset of 10 Minutes (10:00) Before the linked task, your task will <b>always</b> start 10 minutes before the linked task, even if the linked task's start time changes in the future. <br><br><b>Note:</b> You can only set an offset if the task is <b>time linked.</b>">
                    <i class="fa fa-question-circle text-info"></i>                    
                </a>
                <?php 
                
                   // if($task['Task']['time_control'])
                   
                   
                
                ?>
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
                    echo $this->Form->input('Offset.sign', array(
                        'type'=>'select',
                        'class'=>'form-control inputOffSign',
                        'id'=>'inputOffSign'.$tid,
                        'div'=>false,
                        'options'=>array(
                            '-' => "Before Linked Task",
                            '+'=>"After Linked Task"
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
                <div class="panel-heading"><i class="fa fa-flag"></i>&nbsp; Dates &amp; Statuses</div>
                <div class="panel-body">
                    <div class="row">
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
                                                    'class' => 'form-control')); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                    <?php if($userRole >= 200000): ?>

                        <div class="col-xs-6 col-md-12 admin_actionable">
                                        <?php echo $this->Form->label('actionable_type_id', 'Action Item');?>
<br/>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-danger dropdown-toggle atype_sel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-flag"></i>&nbsp; 
    <?php
      $curAtId = $this->request->data('Task.actionable_type_id');
      
      if($curAtId >0){
          echo $actionableTypes[$curAtId];
      }
      else{
          echo 'None';
      }
    ?>
    
      <span class="caret"></span>
  </button>
  <ul class="dropdown-menu atid_dropdown">
      <li><a href="#">None</a></li>
      <?php 
      
        
        foreach ($actionableTypes as $atid => $atlab){
        echo '<li value="';
        echo $atid.'" ';
        echo ($atid == $curAtId)?'class="active" selected':'';
        echo '><a href="#">'.$atlab.'</a></li>';
            
        }
      
      ?>
  </ul>
</div>
                            <div class="form-group">
                                <?php echo $this->Form->input('actionable_type_id', array(
                                    'id'=>'input-actionabletype-select_'.$tid,
                                    'empty'=>true,
                                    'type'=>'hidden',
                                    'value'=>$this->request->data['Task']['actionable_type_id'],
                                    'options'=>$actionableTypes, 
                                    'div'=>array(
                                        'class'=>'input-group'),
                                        //'label'=>'Actionable Type',        
                                    'after'=>'<span class="input-group-addon"><i class="fa fa-flag"></i></span>',
                                    'class' => 'ActionableTypeId form-control')); ?>
                            </div><!-- .form-group -->
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
            
            
            
        <!-- col wrap-->
        
        </div><!--col-md-3-->
    </div><!--row-->
        <div class="panel-footer">
            <div class="row">
                    <div class="col-xs-12">
                        <div class="eaValidationContent" id="validation_content_<?php echo $tid?>"></div>
                    </div>
                </div>
            <div class="row">
        <div class="col-sm-12">
            <?php 
            
            
                //echo '<span class="pull-right">';
                /*
                echo $this->Form->submit('<i class="fa fa-save"></i> Save Changes', array(
                    'id'=>'eaSubmitButton_'.$tid, 
                    'div'=>false, 
                    'escape'=>false,
                    'class' => 'eaSubmitButton submit btn btn-large btn-success'));
            
            */
             

            echo $this->Form->button('<i class="fa fa-save"></i>&nbsp; Save Task', array(
                'type' => 'submit',
                'id'=>'eaSubmitButton_'.$tid,
                
                'class' => 'btn btn-yh',
                'escape' => false
            ));
            
            
                echo '&nbsp;&nbsp;';

            echo $this->Form->button('<i class="fa fa-close"></i>&nbsp;Cancel', array(
                'class' => 'btn btn-default eaCancelBut',
                'id'=>'eaCancelBut'.$tid,
                'escape' => false
            ));


/*


                echo $this->Html->link('<i class="fa fa-close"></i>&nbsp; Cancel', array('action'=>'compile'), array('escape'=>false, 'class'=>'btn btn-large btn-danger'));
                echo '&nbsp;&nbsp;';*/
                echo '<span class="eaSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                //echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner_img', ));
                echo $this->Html->image('ajax-loader_old.gif');
                echo '</span>'; 
           ?>
        </div>
        </div>
        </div>


<?php

    echo $this->Form->end(); 
    echo $this->Js->writeBuffer(); 
?> 
<!-- end edit--> 
