<?php
    if (AuthComponent::user('id')){
        $userRole = AuthComponent::user('user_role_id');
        $userTeamList = AuthComponent::user('TeamsList');
    }
    //$this->log($task);

    if(!empty($task)){
        $this->request->data = $task;
        $tid = $task['Task']['id'];
    }

    $singleTeamControl = false;
    $assigned = (!empty($task['Assignment']))? Hash::extract($task['Assignment'],'{n}.role_id'): array();
    $showAdvPid = (!empty($task['Task']['parent_id']))? TRUE:FALSE;
    
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
    
    $this->Js->buffer("
        //bindToSelect2($('#eaLeadTeamSelect".$tid."'));
        //bindToSelect2($('#eaActionItemSelect".$tid."'),'Mark as Action Item');
        bindStartEndToDTP($('#eaStartTime".$tid.", #eaEndTime".$tid."'));
        bindDateOnlyToDTP($('#eaDueDate".$tid."'));
        bindSummernoteStd($('#eaInputDetails".$tid."'));

        // Stays in view
        $('#eaAssignSelect".$tid."').select2({
            theme:'bootstrap',
            placeholder: 'Assign To',
            multiple: true,
            minimumResultsForSearch: Infinity,
        }).on('change', function(e){
            tsel = $(this);
            var clrs = tsel.parent('div').find('li.select2-selection__choice span.select2-selection__choice__remove');
            var sels = tsel.parent('div').find('span.select2 li.select2-selection__choice');
            $.each(clrs, function(){
                $(this).css('color','#fff');
            });
            $.each(sels, function(i,val){
                $(this).css({'background-color':'#ff751a','color':'#fff'});
            });
        });
        
        // Triggers
        $('#eaStartTime".$tid."').trigger('dp.change');
        $('#eaTimeCtrl".$tid."').trigger('change');
        $('#eaAssignSelect".$tid."').trigger('change');
        $('select.linkableParentSelect').trigger('change.select2');
        
        $('.helpTTs').popover({container: 'body',html:true, trigger:'click'});
        
 
        $('.eaCancelEditBut').on('click', function(){
            var tph = $(this).parents('.task-panel').find('.task-panel-heading');
            tph.trigger('click');
        });
        
        
    
    ");

    //Figure out current team contributions
/*    if(!empty($task['TasksTeam'])){
        $tt = $task['TasksTeam'];
        $lead_id = Hash::extract($tt, '{n}[task_role_id=1].team_id');
        $push_id = Hash::extract($tt, '{n}[task_role_id=2].team_id');
        $ot_id = Hash::extract($tt, '{n}[task_role_id=3].team_id');
        $ct_id = Hash::extract($tt, '{n}[task_role_id=4].team_id');
        $non_lead = Hash::extract($tt, '{n}[task_role_id!=1].team_id');
    }
*/
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


<div class="row">    
    <div class="col-xs-6 col-sm-6 col-md-3 sm-bot-marg"><h3><i class="fa fa-pencil"></i> Edit Task</h3></div>          

    <div class="col-xs-6 col-sm-6 col-md-3 col-md-push-3 sm-bot-marg">        
        <?php 
            echo $this->Form->button('<i class="fa fa-save"></i>&nbsp; Save Task', array(
                'type' => 'submit',
                'id'=>'eaSubmitButton_'.$tid,
                'class' => 'btn btn-success btn-block eaSubmitButton',
                'escape' => false
            ));
        ?>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-3 col-md-push-3 sm-bot-marg">
        <span id="eaCancelEditBut<?php echo $tid;?>" class="btn btn-danger btn-block eaCancelEditBut"><i class="fa fa-close"></i>&nbsp;Cancel</span>

   </div>
</div>

<hr class="xs-top-marg"/>    

        
<?php 
    echo $this->Form->input('id', array('id'=>'input-task-id_'.$tid, 'type'=>'hidden')); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="eaValidationContent" id="validation_content_<?php echo $tid?>"></div>
    </div>
</div>


<div class="row" id="eaLinkedTaskArea">
    <div class="col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="well well-sm well-small" style="padding-bottom: 0px;">
                    <div class="form-group">
                        <?php echo $this -> Form -> label('parent_id', 'Link To Task'); ?> <?php echo $this->Ops->helpPopover('linked_task');?>
                        <span class="hiddenParId" style = "display:none;"><?php echo $task['Task']['parent_id'];?></span> <span class="hiddenTc" style = "display:none;"><?php echo $task['Task']['time_control'];?></span> <span class="hiddenTo" style = "display:none;"><?php echo $task['Task']['time_offset'];?></span>
                        <div id="eaLinkedParentDiv<?php echo $tid;?>" class="linkedParentDiv">
                        <?php
                            if($linkable){
                                echo $this->element('task/linkable_parents_list', array(
                                    'team'=>$task['Task']['team_id'],
                                    'current'=>$task['Task']['parent_id'],
                                    'child'=>$task['Task']['id']));    
                            }
                            else{
                                echo '<div class="alert slim-alert alert-info" role="alert">Select a lead team first</div>';
                            }
                        ?>    
                        </div>
                    </div>

                    <div class="advancedParent <?php echo (!$showAdvPid)? 'collapse':null?>">
                        <div class="row" id="eaAdvancedLinked<?php echo $tid;?>">
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <p><b><i class="fa fa-history"></i> Synchronize</b> <?php echo $this->Ops->helpPopover('synchronize')?> </p>
                                <div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
                                            
                                    <?php 
                                    $time_controlled = ($this->request->data('Task.time_control') == 0)? false: 'checked';
                                    
                                    echo $this->Form->input('Task.time_control', array(
                                        'type'=>'checkbox', 
                                        'id'=>'eaTimeCtrl'.$tid, 
                                        'class' => 'inputTC', 
                                        'div' => false, 
                                        'checked' =>$time_controlled, )); 
                                    ?>
                                    <label for="eaTimeCtrl<?php echo $tid;?>">Linked task controls start time</label>
                                </div>
                            </div>
                            <div class="col-xs-5 col-sm-3 col-md-3">
                                <?php echo $this -> Form -> label('Offset.minutes', 'Offset (mins)'); ?> <?php echo $this->Ops->helpPopover('offset');?>
                                <div class="form-group">
                                    <?php echo $this -> Form -> input('Offset.minutes', array(
                                        'type' => 'number', 
                                        'class' => 'form-control inputOffMin', 
                                        'div' => false, 
                                        'size' => 2, 
                                        'min' => 0, 
                                        'max' => 720, 
                                        'placeholder' => '0 Min', 
                                        'id' => 'eaInputOffMin'.$tid));
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-7 col-sm-5 col-md-5">
                                <label>Offset Type</label> <?php echo $this->Ops->helpPopover('offset_type');?>
                                <div class="form-group">
                                    <?php echo $this -> Form -> input('Task.time_offset_type', array(
                                        'type' => 'select', 
                                        'class' => 'form-control inputOffType', 
                                        'id' => 'eaInputOffType'.$tid, 
                                        'div' => false, 
                                        'options' => array(
                                            '-1' => "Before Linked Task STARTS", 
                                            '-2' => "Before Linked Task ENDS", 
                                            '1' => "After Linked Task STARTS", 
                                            '2' => "After Linked Task ENDS"))); 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div><!--collapse-->

                </div><!--well -->
            </div>
        </div>
    </div><!-- /end left col-->
</div>

<div class="row">

    <div class="col-sm-12 col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
            <?php echo $this -> Form -> label('team_id', 'Lead Team*'); ?> <?php echo $this->Ops->helpPopover('teams');?>
            <?php echo $this -> Form -> input('team_id', array(
                'empty'=>$team_input_empty,
                'readonly'=>$team_input_readonly,
                'options'=>$controlled_teams,
                'multiple'=>false, 
                'id'=>'eaLeadTeamSelect'.$tid, 
                'div' => array('class' => 'input-group'), 
                'after' => '<span class="input-group-addon"><i class="fa fa-users"></i></span>',
                'class' => 'form-control inputTaskLeadTeam inputLeadTeam')); 
            ?>
        </div>
            </div>
            <div class="col-md-8">
                <div class="teamsList eaTeamsList sm-bot-marg" id="eaTeamsList<?php echo $tid;?>">
            <?php 
                echo $this->element('tasks_team/tt_signature',array(
                    'teamsRoles'=>$allowTRoles
                ));
            ?>
        </div>
            </div>
        </div>
       
    </div>

    <div class="col-xs-12 col-sm-6 col-md-5">        
        <div class="form-group">
            <?php echo $this -> Form -> label('Task.start_time', 'Start Time*'); ?>
            <?php echo $this -> Form -> input('Task.start_time', array(
                'type' => 'text', 
                'id'=>'eaStartTime'.$tid, 
                'placeholder' => 'Choose a date', 
                'div' => array(
                    'class' => 'input-group', 
                    'id' => 'inputStartTime', 
                    'data-date-format' => 'Y-m-d H:i:s'
                ), 
                'after' => '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>', 
                'class' => 'form-control inputStartTime'));
            ?>
            <div class="alert alert-info slim-alert stHelpWhenTC collapse xs-top-marg">
                <i class="fa fa-history"></i> <b>Sync: </b> Start time controlled by linked task &amp; Offset.
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6 col-md-5">
        <div class="form-group">
            <?php
                echo $this -> Form -> label('Task.end_time', 'End Time*');
                echo '<span class="endTimeLabel"></span>';
                echo $this -> Form -> input('Task.end_time', array('type' => 'text', 'id'=>'eaEndTime'.$tid, 'placeholder' => 'Choose a date', 'div' => array('class' => 'input-group', 'data-date-format' => 'Y-m-d H:i:s'), 'after' => '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>', 'class' => 'form-control inputEndTime required', ));
            ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-8">
        <div class="row">
            <div class="col-sm-3 col-md-4">
                <div class="form-group">
                    <?php echo $this -> Form -> label('Task.task_type_id', 'Task Type*'); ?>
                    <?php echo $this -> Form -> input('Task.task_type_id', array('id'=>'eaTaskTypeInput'.$tid, 'options' => $taskTypes, 'div' => array('class' => 'input-group'), 'class' => 'form-control input-md inputTaskType')); ?>
                </div>                    
            </div>
            <div class="col-xs-12 col-sm-9 col-md-8">
                <div class="form-group">
                    <?php echo $this -> Form -> input('Task.short_description', array('class' => 'form-control', 'label' => 'Description*', )); ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo $this -> Form -> input('Task.details', array('id'=>'eaInputDetails'.$tid, 'label' => 'Details', 'class' => 'input-details form-control')); ?>
                </div>
            </div>
        </div>
    </div>
                
    <div class = "col-xs-12 col-sm-12 col-md-4">
        <div class="well well-sm">
            <div class="row">
                <div class="col-sm-4 col-md-12">
                    <div class="form-group">
                        <?php echo $this->Form->label('Assignments', 'Assign Task'); ?> <?php echo $this->Ops->helpPopover('assign_task');?>
                        <?php echo $this->Form->input('Assignments', array(
                             'empty'=>true,
                             'id'=>'eaAssignSelect'.$tid,
                             'selected'=>$assigned,
                             'type'=>'select',
                             'options'=>$roles,
                             'multiple'=>true,
                             'div'=>array('class'=>'input-group select2-bootstrap-append'),
                             'style'=>"width: 100%",
                             'after'=>'<span class="input-group-addon"><i class="fa fa-id-badge"></i></span>',
                             'class'=>'form-control inputAssignments',
                             ));
                         ?>
                    </div>
                </div>
                
                <div class="col-xs-12 col-sm-4 col-md-12">
                    <div class="form-group">
                        <?php echo $this -> Form -> label('due_date', 'Due Date'); ?>
                        <?php echo $this -> Form -> input('due_date', array('empty' => true, 'id'=>'eaDueDate'.$tid, 'type' => 'text', 'placeholder' => 'Set due date', 'div' => array('class' => 'input-group'), 'after' => '<span class="input-group-addon"><i class="fa fa-bell-o"></i></span>', 'class' => 'form-control input-date-notime')); ?>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-4 col-md-12">
                <?php if($userRole >= 100): ?>
                    <div class="form-group">
                        <?php echo $this -> Form -> label('actionable_type_id', 'Action Item'); ?>
                        <?php echo $this -> Form -> input('actionable_type_id', array(
                            'id'=>'eaActionItemSelect'.$tid,
                            'empty' => true, 
                            'empty'=>'<Mark as Action Item>', 
                            'options' => $actionableTypes, 
                            'div' => array('class' => 'input-group'), 
                            'after' => '<span class="input-group-addon"><i class="fa fa-flag"></i></span>', 
                            'class' => 'form-control')
                        ); ?>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div><!-- well-->
    </div>
</div>

<?php
    echo $this->Form->end(); 
    echo $this->Js->writeBuffer(); 
?> 
<!-- end edit--> 
