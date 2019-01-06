<?php
    //echo $this->Html->script('compile');
    
    if (AuthComponent::user('id')) {
        $userRole = AuthComponent::user('user_role_id');
        $userTeams = AuthComponent::user('Teams');
        $userTeamList = AuthComponent::user('TeamsList');
        $userTeamByZone = AuthComponent::user('TeamsByZone');
    }
    
    // Figure out # of controlled teams a user has. Show team selection as readonly if control = 1
    $control_team_count = count($userTeamList);
    //$controlled_teams = Hash::extract($controlled_teams,'{s}');
    
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
    
    if(isset($assignments)){
        //$test = array_intersect($assignments, $userTeamList);
        //debug($assignments);debug($userTeamList);
        //debug($test);
        $this->request->data['Assignments'] = $assignments;
    }

    $this -> Js -> buffer("
        bindToSelect2($('.linkableParentSelect'));

        // Task Duration
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
                $(this).parents('form').find('.endTimeLabel').html('&nbsp;&nbsp;(<b>Duration: \>1 Day</b>)');
            }
            else{
                $(this).parents('form').find('.endTimeLabel').html('&nbsp;&nbsp;(<b>Duration: None</b>)');
            }
        });

        // TRIGGERS
        $('#qaReqAllBut, #qaPushAllBut').trigger('change');
        $('#qaStartTime').trigger('dp.change');
       
        // EVENTS
        $('#qaStartTime').on('click', 'li, a', function (e) {
            trigger('dp.change');
            //console.log('got change');
        });

        $('#qaStartTime').on('dp.change', function (e) {
            var inStart = $(this).parents('form').find('.inputStartTime');
            var inEnd = $(this).parents('form').find('.inputEndTime');
            var startTime = inStart.data('DateTimePicker').date();
            var endTime = inEnd.data('DateTimePicker').date();
            var diff = endTime.diff(startTime);
            var old_start = e.oldDate;
            var new_start = e.date;
            //var delta = new_start.diff(old_start);
            var delta = e.date.diff(e.oldDate);
            var old_dur = endTime.diff(old_start);

            // Enforce end after start
            inEnd.data('DateTimePicker').minDate(startTime);

            if(old_dur != 0){
                //console.log('olddur ' + old_dur);
                inEnd.data('DateTimePicker').date(endTime.add(delta));
                inEnd.data('DateTimePicker').minDate(startTime);
            }

            if(delta != 0){
                //inEnd.data('DateTimePicker').date(endTime.add(delta));
            }
        });

        $('#qaEndTime').on('dp.change', function (e) {
            var inStart = $(this).parents('form').find('.inputStartTime');
            var inEnd = $(this).parents('form').find('.inputEndTime');
            var startTime = inStart.data('DateTimePicker').date();
            var endTime = inEnd.data('DateTimePicker').date();
            var diff = endTime-startTime;
            var old_end = e.oldDate;
            var new_end = e.date;
            var delta = new_end.diff(old_end);
            //var delta_s = delta.seconds();
            var old_dur = e.oldDate.diff(startTime);

            if(old_dur != 0){
                //inStart.data('DateTimePicker').date(startTime.add(delta));
                inEnd.data('DateTimePicker').minDate(startTime);      
            }

            // Enforce end after start
            var new_dur = startTime - e.date;
            //taskStartEnd(inStart, startTime, inEnd, endTime, 'end', old_dur);

            // New end minus old start -- original duration
            //var old_dur = e.date.subtract(startTime); 
            //console.log(startTime.diff(e.date));

            if(startTime.diff(e.date) > 0){
                //console.log('start after end');
            }
        });

        $('#qaAssignSelect').select2({
            theme:'bootstrap',
            multiple:true,    
            allowClear: true,
            placeholder: 'Select role',
            minimumResultsForSearch: Infinity,
        })
    ");

    $now_min = date('Y-m-d H:00:00');
    
    $this -> request -> data('Task.start_time', $now_min);
    $this -> request -> data('Task.end_time', $now_min);
    
    echo $this -> Form -> create('Task', array('class' => 'formAddTask', 'id' => 'qaForm', 'url' => array('action' => 'add'), 'novalidate' => true, 'inputDefaults' => array('label' => false), 'role' => 'form'));
?>

<div id="qaPanelBody">
	<div class="row">
		<div class="col-xs-12">
			<div class="qaValidationContent"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-9 col-xs-12">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-4">
					<div class="form-group">
						<?php echo $this -> Form -> label('Task.task_type_id', 'Type*'); ?>
						<?php echo $this -> Form -> input('Task.task_type_id', array('options' => $taskTypes, 'div' => array('class' => 'input-group'), 'after' => '<span class="input-group-addon"><i class="fa fa-tag"></i></span>', 'class' => 'form-control input-md')); ?>
					</div>
				</div>

				<div class="col-xs-6 col-sm-6 col-md-4">
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
						<div class="alert alert-info slim-alert stHelpWhenTC collapse">
							<i class="fa fa-clock-o"></i> <b>Time Sync: </b> Start time is controlled by the linked task &amp; Offset.
						</div>
					</div>
				</div>

				<div class="col-xs-6 col-md-4">
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
				<div class="col-xs-12">
					<div class="form-group">
						<?php echo $this -> Form -> input('Task.short_description', array('class' => 'form-control', 'label' => 'Short Description*', )); ?>
					</div>
				</div>
			</div>

			<div class="row" id="details">
				<div class="col-xs-12">
					<div class="form-group">
						<?php echo $this -> Form -> input('Task.details', array('id' => 'qaInputDetails', 'label' => 'Details', 'class' => 'input-details form-control')); ?>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-sm-12 col-md-12">
							<div class="well well-sm">
								<div class="form-group">
									<?php echo $this -> Form -> label('parent_id', 'Link To Task'); ?>
									<div id="qaLinkedParentDiv" class="linkedParentDiv">
										<?php
                                            if ($singleTeamControl) {
                                                echo $this -> element('task/linkable_parents_list', array('team' => $singleTeamControlled));
                                            }
                                            else {
                                                echo '<div class="alert alert-info" role="alert"><i class="fa fa-info-circle"></i> Select a lead team first</div>';
                                            }
										?>
									</div>
								</div>

								<div class="advancedParent collapse">
									<div class="row" id="qaAdvancedLinked">
										<div class="col-xs-12 col-sm-8 col-md-6">
											<p>
												<b>Synchronize </b><a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Synchronize Tasks" data-content="Allow the <b>linked task</b> to control the start time of <b>your</b> task.  Your task moves automatically whenever the linked task moves.<br><br><b>Note:</b> When this is active, the start time is set automatically from the linked task. You will be unable to edit the start time, but may set an end time (duration) and offset."><i class="fa fa-question-circle text-info"></i></a>
											</p>

											<div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
												<?php
                                                    echo $this -> Form -> input('Task.time_control', array('type' => 'checkbox', 'id' => 'qaTimeCtrl', 'class' => 'inputTC', 'div' => false, 'checked' => false, ));
												?>
												<label for="qaTimeCtrl">Linked task controls start time</label>
											</div>

											<span class="help-block"> If selected, task moves automatically if the linked task moves. </span>
										</div>

										<div class="col-sm-12 col-md-6">
											<div class="form-group">
												<label>Offset (mm:ss)</label>
                                                <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Offset (For Synchronized Tasks)" data-content="The amount of time (mm:ss) to maintain between the start of <u>this task</u> and the <u>linked task</u>. You can choose to synchronyize the start of your task to before/after the start/end of the linked task. <br><br>When the linked task moves, this task moves automatically, such that the offset is preseved. <br><br>e.g. If you set an offset of 10 Minutes (10:00) <i>before the linked task starts</i>, your task will always start 10 minutes before the linked task, even if the linked task's start time changes. <br><br><b>Note:</b> You can only set an offset if the task is <b>synchronized</b> with the linked task.</b>">
                                                    <i class="fa fa-question-circle text-info"></i>                    
                                                </a>
												<div class="form-inline">
													<?php
                                                        echo $this -> Form -> input('Offset.minutes', array('type' => 'number', 'class' => 'form-control inputOffMin', 'div' => false, 'size' => 2, 'min' => 0, 'max' => 720, 'placeholder' => '0 Min', 'id' => 'qaInputOffMin', ));
                                                        echo '<b>:</b>';
                                                        echo $this -> Form -> input('Offset.seconds', array('type' => 'number', 'class' => 'form-control inputOffSec', 'div' => false, 'size' => 2, 'min' => -1, 'max' => 60, 'placeholder' => '0 Sec', 'id' => 'qaInputOffSec', ));
                                                        echo '&nbsp;';
                                                        echo $this -> Form -> input('Task.time_offset_type', array('type' => 'select', 'class' => 'form-control inputOffType', 'id' => 'qaInputOffType', 'div' => false, 
                                                            'options' => array(
                                                                '-1' => "Before Linked Task STARTS", 
                                                                '-2' => "Before Linked Task ENDS",
                                                                '1' => "After Linked Task STARTS", 
                                                                '2' => "After Linked Task ENDS")
                                                            )
                                                        );
													?>
												</div>
												<span id="qaOffsetHelpBlock" class="help-block">Time (mm:ss) between the start of your task and linked task.</span>
											</div>
										</div>
									</div>
								</div>
							</div><!--well -->
						</div>
					</div>
				</div>
			</div>
		</div><!-- /end left col-->

		<div class="col-md-3 col-xs-12">
			<div class="row">
				<div class="col-sm-12 lg-bot-marg">
					<button class="btn btn-success btn-lg btn-block qaSubmitButton submit">
						<i class="fa fa-plus"></i> Add New Task
					</button>
				</div>
			</div>

			<div class="row sm-top-marg" id="teamStatus">
				<div class="col-md-12">
					<div class="panel panel-dark panel-qa">
						<div class="panel-heading">
							<i class="fa fa-users"></i>&nbsp;&nbsp;Teams
						</div>
						<div class="panel-body teams-panel">
							<div class="row sm-bot-marg">
								<div class="form-group">
									<div class="col-sm-12">
										<?php echo $this -> Form -> label('team_id', 'Lead*'); ?>
										<?php echo $this -> Form -> input('team_id', array('empty' => $team_input_empty, 'readonly' => $team_input_readonly, 'options' => $userTeamByZone, 'multiple' => false, 'id' => 'qaLeadTeamSelect', 'div' => array('class' => 'input-group'), 'after' => '<span class="input-group-addon"><i class="fa fa-users"></i></span>', 'class' => 'form-control inputLeadTeam')); ?>
									</div>
								</div><!-- .form-group -->
							</div>

							<div class="row sm-bot-marg">
								<div class="col-sm-12">
									<div id="qaNewTeamsList" class="teamsList">
										<?php
                                            if ($control_team_count > 1) {
                                                echo '<div class="alert slim-alert alert-info" role="alert"><i class="fa fa-info-circle"></i> Select a lead team first</div>';
                                            }
                                            else {
                                                $new_teams = array();
                                                if ($control_team_count == 1) {
                                                    $uteam = $userTeams[0];
                                                    $new_teams = $this -> requestAction(array('controller' => 'tasks_teams', 'action' => 'updateSig', $uteam));
                                                }
   
                                                echo $this -> element('tasks_team/new_team_list', array('new_teams' => $new_teams, ));
                                            }
										?>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<a id="qaPushAllBut" class="btn btn-sm btn-default">Push ALL</a>
									<a id="qaReqAllBut" class="btn btn-sm btn-danger">Request ALL</a>
								</div>
							</div>
						</div>
					</div>
				</div><!--panel body-->
			</div><!--row-->

			<div class="row">
				<div class="col-md-12 sm-top-marg">
					<div class="panel panel-bdanger panel-qa">
						<div class="panel-heading">
							<i class="fa fa-flag"></i>&nbsp;&nbsp;Task Flags
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-6 col-sm-6 col-md-12">
                                     <div class="form-group">
                                    <?php
                                     echo $this->Form->label('Assignments', 'Assign Task');
                                     echo $this->Form->input('Assignments', array(
                                     //'empty'=>true,
                                     'id'=>'qaAssignSelect',
                                     'type'=>'select',
                                     //'placeholder'=>'alskd alskdjlka',
                                     'options'=>$roles,
                                     'multiple'=>true,
                                     'div'=>array(
                                        'class'=>'input-group select2-bootstrap-append'
                                     ),
                                     'style'=>"width: 100%",
                                     'after'=>'<span class="input-group-addon"><i class="fa fa-id-badge"></i></span>',
                                     'class'=>'form-control inputAssignments',
                                     ));?>
                                     </div>
									<div class="form-group">
										<?php echo $this -> Form -> label('due_date', 'Due Date'); ?>
										<?php echo $this -> Form -> input('due_date', array('empty' => true, 'id' => 'qaDueDate', 'type' => 'text', 'placeholder' => 'Set due date', 'div' => array('class' => 'input-group'), 'after' => '<span class="input-group-addon"><i class="fa fa-bell-o"></i></span>', 'class' => 'form-control input-date-notime')); ?>
									</div>
								</div>

								<?php if($userRole >= 100):	?>
    								<div class="col-xs-6 col-sm-6 col-md-12">
    									<div class="form-group">
    										<?php echo $this -> Form -> label('actionable_type_id', 'Action Item'); ?>
    										<?php echo $this -> Form -> input('actionable_type_id', array('empty' => true, 'options' => $actionableTypes, 'div' => array('class' => 'input-group'), 'after' => '<span class="input-group-addon"><i class="fa fa-flag"></i></span>', 'class' => 'form-control')); ?>
    									</div>
    								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--panel body-->

<div id="qaModalDueChangeEnd" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Change Task End Time?</h4>
			</div>

			<div class="modal-body">
				<p>You set a new due date. Would you like to change the task <b>end time</b> to the <b>new due date?</b></p>
				<p>Default: Tasks run from when they're created until they're due</p>
			</div>

			<div class="modal-footer">
				<button type="button" id="qaDueOnlyButton" class="btn btn-default" data-dismiss="modal">Change ONLY Due Date</button>
				<button type="button" id="qaDueAndEndButton" class="btn btn-primary">Change End Time AND Due Date</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
    echo $this -> Form -> end();
    echo $this -> Js -> writeBuffer();
?>

