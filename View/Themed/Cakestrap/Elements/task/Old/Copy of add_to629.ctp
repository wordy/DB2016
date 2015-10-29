<?php


//$this->log($aInControlled);
    //if(!empty($task)){
    //    $this->request->data = $task;
    //}
    
    //$this->log($this->request->data);
    //$this->log($ateams);
    
        $task = $this->request->data('Task');
        
        $this->request->data('Task', null);
        $this->request->data('TasksTeam', null);
        
        
        // Team that owns the task we're responding to. Push to them by default.
        $push_id = $task['team_id'];
        
        // Arbitrarily sets the "linked" task to be 1s after the one they're responding to
        $par_task_stime = $task['start_time'];
        $new_task_stime = date('Y-m-d H:i:s', strtotime($par_task_stime) +1);
        
        $this->request->data('Task.start_time', $new_task_stime);
        $this->request->data('Task.end_time', $new_task_stime);

           //$this->log($this->request->data);
        
//        $tt = $this->request->data('TasksTeam');
    
    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
        $user_teams = AuthComponent::user('TeamsList');
    }
    
    //If user controls >2 teams, force empty input so they can't forget and accidently set an incorrect team
    $team_input_readonly = false;
    $team_input_empty = false;
    
    
    if(count($controlled_teams) == 1){
        $team_input_empty = false;
        $team_input_readonly = 'readonly';
    }
    elseif(count($controlled_teams)>=2){
        $team_input_empty = true;
    }
    
    $this->Js->buffer("
        $('body').on('submit','#atAddTo".$tid."', function(e){
            var subBut = $(this).find('.atSubmitButton');
            var valCont = $(this).find('.atValidationContent');
            var spinner = $(this).find('.atSpinner');
            
            subBut.val('Saving...');
            spinner.fadeIn();
    
            $.post($(this).attr('action'), $(this).serialize(), function(data){
                    valCont.fadeOut('fast');                
            })
            .done(function(data) {
                $('#atAddTo".$tid."').trigger('reset');
                $('#atOpenTeams".$tid."').select2('val','');
                $('#atPushTeams".$tid."').select2('val','');
                spinner.fadeOut('fast');
                //valCont.html(data).fadeIn('fast').delay(3000).fadeOut();
                $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                
                // Refresh tasks list
                //var top_stime = $('div.task-panel').eq(0).data('stime');
                //var bot_stime = $('div.task-panel').eq(-1).data('stime');
                
                
                $('#taskListWrap').load('/tasks/compileUser', function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });
            })
            .fail(function(data, textStatus) {
                valCont.html(data.responseText).fadeIn('fast');
            })
            .always(function(){
                spinner.fadeOut('fast');
                subBut.val('Add Task');
            });
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });
    
        $('#atClosedTeams".$tid."').select2({
            'width':'100%',
            'allowClear':true,
            'minimumResultsForSearch':-1,
            
             formatSelectionCssClass: function (data, container) { 
                return 'team-closed'; 
             },
        })
        .on('change', function(e) {
            if(e.added){
                var old = $('#atPushTeams".$tid."').select2('val');
                if(old[0]==''){
                    old.shift();
                }
                old[old.length] = e.added.id;
                $('#atPushTeams".$tid."').select2('val', old);
            }
        });
        
        $('#atOpenTeams".$tid."').select2({
            'width':'100%',
            'allowClear':true,
            'minimumResultsForSearch':-1,
            
             formatSelectionCssClass: function (data, container) { 
                return 'team-open'; 
             },
        })
        .on('change', function(e) {
            if(e.added){
                var old = $('#atPushTeams".$tid."').select2('val');
                if(old[0]==''){
                    old.shift();
                }
                old[old.length] = e.added.id;
                $('#atPushTeams".$tid."').select2('val', old);
            }
        });
        
        $('#atPushTeams".$tid."').select2({
            'width':'100%',
            'allowClear':true,
            'minimumResultsForSearch':-1,
            formatSelectionCssClass: function (data, container) { 
                return 'team-push'; },
            })
            .on('change', function(e) {
                if(e.removed){
                    var old = $('#atAssistTeams".$tid."').select2('val');
                    if(old[0]==''){
                        old = [];
                    }
                    
                    var rid = e.removed.id;
                    var oindex = old.indexOf(rid);
                    
                    if(oindex > -1){
                        var nindex = old.splice(oindex,1);
                        $('#atAssistTeams".$tid."').select2('val', old);
                    }
                }
            }
        );
        
        $('#atAssistTeamAll".$tid."').click(function(){
            var cur_lead = $('#atLeadTeam".$tid."').val();
            var at_sel =  $('#atTask".$tid."').find('.input-ateam-select');
            var at_sel_opts = $('#atTask".$tid."').find('select.input-ateam-select option');
            var pt_sel =  $('#atTask".$tid."').find('.input-pteam-select');
            var pt_sel_opts = $('#atTask".$tid."').find('select.input-pteam-select option');
            var this_butt = $(this);
            
            if(this_butt.html() == 'All'){
                var selected = [];
                at_sel.find('option').each(function(i,e){
                    if($(e).attr('value').length > 0 && $(e).attr('value') != cur_lead){
                        selected[selected.length]=$(e).attr('value');    
                    }
                });
                
                var selected2=[];
                pt_sel.find('option').each(function(i,e){
                    if($(e).attr('value').length > 0 && $(e).attr('value') != cur_lead){
                        selected2[selected2.length]=$(e).attr('value');
                    }
                });
                
                at_sel.select2('val', selected);
                pt_sel.select2('val', selected2);
                this_butt.html('None');
                $('#atPushTeamAll".$tid."').html('None');
            }
            else{
                at_sel.select2('val','');
                this_butt.html('All');
                
            }
        });

        $('#atPushTeamAll".$tid."').click(function(){
            var cur_lead = $('#atLeadTeam".$tid."').val();
            var at_sel =  $('#atTask".$tid."').find('.input-ateam-select');
            var at_sel_opts = $('#atTask".$tid."').find('select.input-ateam-select option');
            var pt_sel =  $('#atTask".$tid."').find('.input-pteam-select');
            var pt_sel_opts = $('#atTask".$tid."').find('select.input-pteam-select option');
            var this_butt = $(this);
            
            if(this_butt.html() == 'All'){
                var selected = [];
                pt_sel.find('option').each(function(i,e){
                    if($(e).attr('value').length > 0 && $(e).attr('value') != cur_lead){
                        selected[selected.length]=$(e).attr('value');    
                    }
                });
                pt_sel.select2('val', selected);
                this_butt.html('None');
            }
            else{
                at_sel.select2('val','');
                pt_sel.select2('val','');
                this_butt.html('All');
                $('#atAssistTeamAll".$tid."').html('All');
                
            }
        });
        
        $('#atStartTime".$tid."').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            showMeridian: true,            
            keyboardNavigation:false,
            minuteStep: 5,
            startDate:'2014-11-01',
            //forceParse:false,
            endDate:'2015-03-31',
            linkField: 'atEndTime".$tid."',
            linkFormat: 'yyyy-mm-dd hh:ii:ss',
        });
    
        $('#atEndTime".$tid."').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
            todayBtn: 'linked',
            showMeridian: true,            
            keyboardNavigation:false,
            todayHighlight: true,
            minuteStep: 5,
            startDate:'2014-11-01',
            forceParse:false,
            endDate:'2015-03-31',
        });
           
        $('#atDueDate".$tid."').datetimepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            showMeridian: true,            
            keyboardNavigation:false,
            todayBtn: 'linked',
            todayHighlight: true,
            //minuteStep: 1,
            minView: 2,
            //showMeridian: true,
            startDate:'2014-11-01',
            forceParse:true,
            endDate:'2015-03-31',
        });
        
        $('#atInputDetails".$tid."').wysihtml5({
            toolbar: {
                'fa': true,
                'link': true, 
                'image': false, 
                'lists': true,
                'html': false,
                'color': false,
                'blockquote': false,
            }    
        });

    ");


?>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                <b>Linking Tasks</b><br/>
                <ul>
                    <li>Creates a task with start time directly after the one you're linking to. Change this if your task occurs at a specific time (i.e. cue on event day)</li>
                    <li>Notifies the other team that you responded (if your team is listed as assisting)</li>
                    <li>Any teams listed as Assisting or Pushed can link to the task.</li>

                </ul> 
            </div>
        </div>
    </div>
    <?php echo $this->Form->create('Task', array(
        'action'=>'addTo',
        'class'=>'formAddToTask',
        'type'=>'post',
        'data-tid'=> $tid, 
        'id'=>'atAddTo'.$tid, 
        'novalidate' => true,
        'inputDefaults' => array(
            'label' => false), 
        'role' => 'form')); 
    ?>

            
    <div class="row" id="atTask<?php echo $tid;?>">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-bookmark-o"></i>&nbsp;&nbsp;Task Details</div>

                <div class="panel-body">
                    <?php echo $this->Form->input('parent_id', array(
                        'value'=>$tid,
                        'id'=>'parId'.$tid,
                        'type'=>'hidden')); ?>
                    <?php echo $this->Form->input('parent_task', array(
                        'value'=>$tid,
                        'id'=>'parTask'.$tid,
                        'type'=>'hidden')); ?>
                    <?php echo $this->Form->input('parent_team', array(
                        'value'=>$push_id,
                        'id'=>'parTeam'.$tid,
                        'type'=>'hidden')); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->label('task_type_id', 'Task Type*'); ?>
                                <?php echo $this->Form->input('task_type_id', array(
                                    'class' => 'form-control', 
                                    'id'=>'atinput-tasktype-select_'.$tid, 
                                    'options'=>$taskTypes)); 
                                ?>
                            </div><!-- .form-group -->
                        </div>
                        <div class="col-md-4">
                            <?php echo $this->Form->input('start_time', array(
                                'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                                'type'=>'text',
                                'id'=>'atStartTime'.$tid, 
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
                                'id'=>'atEndTime'.$tid, 
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                'class'=>'form-control datetimepicker-etime',
                                'error' => array(
                                    'attributes' => array(
                                        'wrap' => 'span', 
                                        'class' => 'help-inline text-danger bolder')))); 
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <p><strong>Linked To</strong></p>
                                    <?php echo $this->Ops->miniSubtaskRow($task); ?>
                            </div><!-- .form-group -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php echo $this->Form->label('short_description', 'Short Description*');?>
                                <?php echo $this->Form->input('short_description', array(
                                    'id'=>'atShortDesc'.$tid,
                                    'error' => array(
                                        'attributes' => array(
                                            'wrap' => 'span', 
                                            'class' => 'help-inline text-danger bolder')),
                                    'class' => 'form-control')); 
                                ?>
                            </div><!-- .form-group -->
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-12 sm-bot-marg">
                            <?php echo $this->Form->input('details', array(
                                'label'=>'Details', 
                                'id'=>'atInputDetails'.$tid, 
                                'class'=>'input-details form-control', 
                                'type' => 'textarea')); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
   
        </div><!--col-md-9-->
        <div class="col-md-4">
            <div class="panel panel-dark">
                <div class="panel-heading"><i class="fa fa-users"></i>&nbsp;&nbsp;Teams</div>
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
                                    'options'=>$aInControl, 
                                    'id'=>'atLeadTeam'.$tid, 
                                    'class' => 'form-control',
                                    'error' => array('attributes' => array(
                                        'wrap' => 'span', 
                                        'class' => 'help-inline text-danger bolder')
                                    ))); 
                                ?>
                            </div><!-- .form-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <?php echo $this->Form->label('OpenTeams', 'Open Request <small>(Response Required)</small>');?>
                                <?php echo $this->Form->input('OpenTeams', array(
                                    'empty'=>true, 
                                    'id'=>'atOpenTeams'.$tid, 
                                    'options'=>$teams, 
                                    //'selected'=>$assist_id,
                                    'type'=>'select', 
                                    'placeholder'=>'Open Request to', 
                                    'multiple'=>true,
                                    'class' => 'input-ateam-select')); ?>
                                <span class="input-group-btn">
                                    <button style="margin-top: 23px;" class="btn btn-sm btn-primary ateamAll" id="atAssistTeamAll<?php echo $tid;?>" type="button">All</button>
                                </span>
                                
                            </div><!-- .input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <?php echo $this->Form->label('ClosedTeams', 'Completed Request');?>
                                <?php echo $this->Form->input('ClosedTeams', array(
                                    'empty'=>true, 
                                    'id'=>'atClosedTeams'.$tid, 
                                    'options'=>$teams, 
                                    //'selected'=>$assist_id,
                                    'type'=>'select', 
                                    'placeholder'=>'Closed Request to', 
                                    'multiple'=>true,
                                    'class' => 'input-ateam-select')); ?>
                                <span class="input-group-btn">
                                    <button style="margin-top: 23px;" class="btn btn-sm btn-primary ateamAll" id="atAssistTeamAll<?php echo $tid;?>" type="button">All</button>
                                </span>
                                
                            </div><!-- .input-group -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <?php echo $this->Form->label('PushTeams', 'Pushed');?>
                                <?php echo $this->Form->input('PushTeams', array(
                                    'empty'=>true, 
                                    'id'=>'atPushTeams'.$tid, 
                                    'options'=>$teams,
                                    'selected'=>$push_id, 
                                    'type'=>'select', 
                                    'placeholder'=>'Push To', 
                                    'multiple'=>true, 
                                    'class' => 'input-pteam-select')); ?>
                                <span class="input-group-btn">
                                    <button style="margin-top: 23px;" class="btn btn-sm btn-darkgrey pteamAll" id="atPushTeamAll<?php echo $tid;?>" type="button">All</button>
                                </span>
                               
                            </div><!-- .input-group -->
                        </div>
                    </div>
                </div><!--panel body-->
            </div><!--panel-->
            
            <div class="panel panel-bdanger">
                <div class="panel-heading"><i class="fa fa-flag"></i>&nbsp;&nbsp;Dates &amp; Statuses</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php echo $this->Form->label('due_date', 'Due Date'); ?>
                                <?php echo $this->Form->input('due_date', array(
                                    'empty'=>true,
									'id'=>'atDueDate'.$tid,
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
                                        'id'=>'atinput-actionabletype-select_'.$tid,
                                        'empty'=>true,
                                        //'value'=>$this->request->data['Task']['actionable_type_id'],
                                        'options'=>$actionableTypes, 
                                        'div'=>array(
                                            'class'=>'input-group'),
                                            //'label'=>'Actionable Type',        
                                        'after'=>'<span class="input-group-addon"><i class="fa fa-flag"></i></span>',
                                        'class' => 'form-control')); ?>
                                </div><!-- .form-group -->
                            </div>
                        </div>
                    <?php endif; ?>
                </div><!--panel body-->
            </div><!--panel-->

        </div><!--col-md-3-->
    </div><!--row-->
    <div class="row">
        <div class="col-sm-4">
            <?php 
                //echo '<span class="pull-right">';
                echo $this->Form->submit('Save Changes', array(
                    'id'=>'atSubmitButton_'.$tid, 
                    'div'=>false, 
                    'class' => 'atSubmitButton submit btn btn-large btn-success'));
            
                echo '&nbsp;&nbsp;';
                echo $this->Html->link('Cancel', array('action'=>'compile'), array('class'=>'btn btn-large btn-danger'));
                echo '&nbsp;&nbsp;';
                echo '<span class="atSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                //echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner_img', ));
                echo $this->Html->image('ajax-loader_old.gif');
                echo '</span>'; 
	       ?>
        </div>
        <div class="col-sm-8">
            <div class="atValidationContent" id="atvalidation_content_<?php echo $tid?>"></div>
        </div>
    </div>

<?php

debug ($aInControl);
    echo $this->Form->end(); 
    echo $this->Js->writeBuffer(); 
?>  