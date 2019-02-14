<?php
    //echo $this->Html->script('compile');
    //echo $this->Html->script('add_task');
    
    if (AuthComponent::user('id')) {
        $userRole = AuthComponent::user('user_role_id');
        $userTeams = AuthComponent::user('Teams');
        $userTeamList = AuthComponent::user('TeamsList');
        $userTeamByZone = AuthComponent::user('TeamsByZone');
    }
    
    if(isset($assignments)){
        $this->request->data['Assignments'] = $assignments;
    }

    // Figure out # of controlled teams a user has. Show team selection as readonly if control = 1
    $control_team_count = count($userTeamList);
    $readonly = $team_input_readonly = $singleTeamControl = false;
    $singleTeamControlled = null;
    
    if ($control_team_count >= 2) {
        $team_input_empty = true;
    }
    elseif ($control_team_count == 1) {
        $team_input_readonly = 'readonly';
        $team_input_empty = false;
        $ar_k = array_keys($userTeamList);
        $singleTeamControlled = $ar_k[0];
        $singleTeamControl = true;
    }
    else {
        $team_input_empty = false;
    }
    

    $this->Js->buffer("
        //bindToSelect2('#qaTaskTypeInput, #qaLeadTeamSelect');
        //bindToSelect2($('#qaActionItemSelect'),'Mark as Action Item');
        bindStartEndToDTP($('.inputStartTime, .inputEndTime'));
        bindDateOnlyToDTP($('#qaDueDate'));
        bindSummernoteStd($('#qaInputDetails'));
        
        $('.helpTTs').popover({container: 'body',html:true, trigger:'click'});
        
        // TRIGGERS
        //$('#qaReqAllBut, #qaPushAllBut').trigger('change');
        $('#qaStartTime').trigger('dp.change');
        //$('#qaLeadTeamSelect').trigger('select2:select');
                       
        // Stays in view
        $('#qaAssignSelect').select2({
            theme:'bootstrap',
            multiple:true,    
            //allowClear: true,
            placeholder: 'Select role',
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
        
        
        
        
        
        
    ");

    // Default start/end is current hour
    $now_min = date('Y-m-d H:00:00');
    $this->request->data('Task.start_time', $now_min);
    $this->request->data('Task.end_time', $now_min);
    
    echo $this->Form->create('Task', 
        array(
            'class' => 'formAddTask', 
            'id' => 'qaForm',
            'data-tid'=> null,  
            'url' => array('action' => 'add'), 
            'novalidate' => true, 
            'inputDefaults' => array('label' => false), 
            'role' => 'form')
        );
?>
<style>

.select2-results__group{
    font-weight: bolder !important;
    font-size: 1.1em !important;
    background-color: #00816C !important;
    color: #fff !important;
}
</style>

<div class="row">
    <div class="col-xs-12"><span class="h2"><i class="fa fa-plus-circle"></i> Add Task</span>
        <div class="pull-right">
            <button data-lead=69 data-action="addAndClose" class="btn btn-md btn-success margin1 qaSubmitButton submit"><i class="fa fa-plus-circle"></i> Save Task</button> <button data-action = "addAndReset" class="btn btn-md btn-success margin1 qaSubmitButton submit"><i class="fa fa-plus-circle"></i> Save &amp; Add Another</button> <button class="btn btn-md btn-danger margin1 qaCancelButton"><i class="fa fa-close"></i> Cancel</button></p>
        </div>
    </div>
</div>
    
<div class="row">
    <div class="col-xs-12">
        <hr class="sm-top-marg sm-bot-marg"/>
    </div>
</div>
    
<div id="qaPanelBody">
	<div class="row">
		<div class="col-xs-12">
			<div class="qaValidationContent"></div>
		</div>
	</div>

    <div class="row" id="qaLinkedTaskArea">
        <div class="col-sm-12 col-xs-12">

            <div class="row">
                <div class="col-xs-12">
                    <div class="well well-sm well-small" style="padding-bottom: 0px;">
                        <div class="form-group">
                            <?php echo $this -> Form -> label('parent_id', 'Link To Task'); ?> <?php echo $this->Ops->helpPopover('linked_task');?>
                            <div id="qaLinkedParentDiv" class="linkedParentDiv">
                                <?php
                                    if ($singleTeamControl) {
                                        //echo $singleTeamControlled;
                                        echo $this -> element('task/linkable_parents_list', array('team' => $singleTeamControlled));
                                    }
                                    else {
                                        echo '<span class="text-info">Select a lead team first.</span>';
                                    }
                                ?>
                            </div>
                        </div>

                        <div class="advancedParent collapse">
                            <div class="row" id="qaAdvancedLinked">
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <p><b><i class="fa fa-history"></i> Synchronize</b> <?php echo $this->Ops->helpPopover('synchronize')?> </p>
                                    <div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
                                        <?php echo $this -> Form -> input('Task.time_control', array('type' => 'checkbox', 'id' => 'qaTimeCtrl', 'class' => 'inputTC', 'div' => false, 'checked' => false, )); ?>
                                        <label for="qaTimeCtrl">Linked task controls start time</label>
                                    </div>
                                </div>

                                <div class="col-xs-5 col-sm-3 col-md-3">
                                    <?php echo $this -> Form -> label('Offset.minutes', 'Offset (mins)'); ?> <?php echo $this->Ops->helpPopover('offset');?>
                                    <div class="form-group">
                                        <?php
                                            echo $this -> Form -> input('Offset.minutes', array('type' => 'number', 'class' => 'form-control inputOffMin', 
                                            'div' => false, 'size' => 2, 'min' => 0, 'max' => 720, 'placeholder' => '0 Min', 'id' => 'qaInputOffMin', ));
                                        ?>
                                    </div>
                                </div>

                                <div class="col-xs-7 col-sm-5 col-md-5">
                                    <label>Offset Type</label> <?php echo $this->Ops->helpPopover('offset_type');?>
                                    <div class="form-group">
                                    <?php
                                        echo $this -> Form -> input('Task.time_offset_type', array('type' => 'select', 'class' => 'form-control inputOffType', 'id' => 'qaInputOffType', 'div' => false, 
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
            <div class="col-sm-5 col-md-4">
                <div class="form-group">
                    <?php echo $this -> Form -> label('team_id', 'Lead Team*'); ?> <?php echo $this->Ops->helpPopover('teams');?>
                    <?php echo $this -> Form -> input('team_id', array(
                        'empty'=> $team_input_empty,
                        'readonly' => $team_input_readonly, 
                        'options' => $userTeamByZone, 
                        'multiple' => false, 
                        'id' => 'qaLeadTeamSelect', 
                        'div' => array('class' => 'input-group'), 
                        'after' => '<span class="input-group-addon"><i class="fa fa-users"></i></span>',
                        'class' => 'form-control inputTaskLeadTeam inputLeadTeam')); 
                    ?>
                </div>
            </div>
            <div class="col-sm-7 col-md-8">
                <div id="qaNewTeamsList" class="teamsList sm-bot-marg xs-top-marg">
                <?php
                    if ($control_team_count > 1) {
                        //echo '<div class="alert slim-alert alert-info" role="alert"><i class="fa fa-info-circle"></i> Select a lead team first</div>';
                    }
                    else {
                        $new_teams = array();
                        if ($control_team_count == 1) {
                            $uteam = $userTeams[0];
                            $new_teams = $this -> requestAction(array('controller' => 'tasks_teams', 'action' => 'updateSig', $uteam));
                        }
                    }
                ?>
                </div>
            </div>
        </div>
    </div>

        <div class="col-xs-12 col-sm-6 col-md-4">        
            <div class="form-group">
                <?php echo $this -> Form -> label('Task.start_time', 'Start Time*'); ?>
                <?php echo $this -> Form -> input('Task.start_time', array(
                    'type' => 'text', 
                    'id' => 'qaStartTime',
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

        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="form-group">
                <?php
                    echo $this -> Form -> label('Task.end_time', 'End Time*');
                    echo '<span class="endTimeLabel"></span>';
                    echo $this -> Form -> input('Task.end_time', array('type' => 'text', 'id' => 'qaEndTime', 'placeholder' => 'Choose a date', 'div' => array('class' => 'input-group', 'data-date-format' => 'Y-m-d H:i:s'), 'after' => '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>', 'class' => 'form-control inputEndTime required', ));
                ?>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 col-md-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?php echo $this -> Form -> label('Task.task_type_id', 'Task Type*'); ?>
                        <?php echo $this -> Form -> input('Task.task_type_id', array('id'=>'qaTaskTypeInput', 'options' => $taskTypes, 'div' => array('class' => 'input-group'), 'class' => 'form-control input-md inputTaskType')); ?>
                    </div>                    
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <?php echo $this -> Form -> input('Task.short_description', array('class' => 'form-control', 'label' => 'Description*', )); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?php echo $this -> Form -> input('Task.details', array('id' => 'qaInputDetails', 'label' => 'Details', 'class' => 'input-details form-control')); ?>
                    </div>
                </div>
            </div>
        </div>
    
        <div class = "col-xs-12 col-sm-12 col-md-4">
            <div class="well well-sm">
                <div class="form-group">
                    <?php echo $this->Form->label('Assignments', 'Assign Task'); ?> <?php echo $this->Ops->helpPopover('assign_task');?>
                    <?php echo $this->Form->input('Assignments', array(
                         //'empty'=>true,
                         'id'=>'qaAssignSelect',
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
                <div class="form-group">
                    <?php echo $this -> Form -> label('due_date', 'Due Date'); ?>
                    <?php echo $this -> Form -> input('due_date', array('empty' => true, 'id' => 'qaDueDate', 'type' => 'text', 'placeholder' => 'Set due date', 'div' => array('class' => 'input-group'), 'after' => '<span class="input-group-addon"><i class="fa fa-bell-o"></i></span>', 'class' => 'form-control input-date-notime')); ?>
                </div>
    
            <?php if($userRole >= 100): ?>
                <div class="form-group">
                    <?php echo $this -> Form -> label('actionable_type_id', 'Action Item'); ?>
                    <?php echo $this -> Form -> input('actionable_type_id', array(
                        'id'=>'qaActionItemSelect', 
                        'empty' => true, 
                        'empty'=>'<Mark as Action Item>', 
                        'options' => $actionableTypes, 
                        'div' => array('class' => 'input-group'), 
                        'after' => '<span class="input-group-addon"><i class="fa fa-flag"></i></span>', 
                        'class' => 'form-control')
                    ); ?>
                </div>
            <?php endif; ?>
            </div><!-- well-->
        </div>
    </div>
</div><!--panel body-->


<?php
    echo $this -> Form -> end();
    echo $this -> Js -> writeBuffer();
?>

