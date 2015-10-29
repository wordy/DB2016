<?php
    if(!empty($task)){
        $this->request->data = $task;
    }
    
    $tid = $task['Task']['id'];
    
    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
        $user_teams = AuthComponent::user('TeamsList');
    }
    
    //If user controls >2 teams, force empty input so they can't forget and accidently set an incorrect team
    $team_input_readonly = false;
    
    if(count($controlled_teams) == 1){
        $team_input_empty = false;
        $team_input_readonly = 'readonly';
    }
    elseif(count($controlled_teams)>=2){
        $team_input_empty = true;
    }
    
    $this->Js->buffer("
        
    
        $('#eaAssistTeams".$tid."').select2({
            'width':'100%',
            'allowClear':true,
            'minimumResultsForSearch':-1,
            
             formatSelectionCssClass: function (data, container) { 
                return 'team-assist'; },
            })
            .on('change', function(e) {
                if(e.added){
                    var old = $('#eaPushTeams".$tid."').select2('val');
                    if(old[0]==''){
                        old.shift();
                    }
                    old[old.length] = e.added.id;
                    $('#eaPushTeams".$tid."').select2('val', old);
                }
            }
        );
        
        $('#eaPushTeams".$tid."').select2({
            'width':'100%',
            'allowClear':true,
            'minimumResultsForSearch':-1,
            formatSelectionCssClass: function (data, container) { 
                return 'team-push'; },
            })
            .on('change', function(e) {
                if(e.removed){
                    var old = $('#eaAssistTeams".$tid."').select2('val');
                    if(old[0]==''){
                        //old = [];
                        old.shift();
                    }
                    
                    var rid = e.removed.id;
                    var oindex = old.indexOf(rid);
                    
                    if(oindex > -1){
                        var nindex = old.splice(oindex,1);
                        $('#eaAssistTeams".$tid."').select2('val', old);
                    }
                }
            }
        );
        
        $('#eaInputDetails".$tid."').wysihtml5({
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
        
        $('#eaAssistTeamAll".$tid."').click(function(){
            var at_sel = $('#eaAssistTeams".$tid."');
            var at_sel_opts = at_sel.find('option');
            var pt_sel = $('#eaPushTeams".$tid."');
            var pt_sel_opts = pt_sel.find('option');
            var this_butt = $(this);
  
            console.log(at_sel_opts);
            console.log(pt_sel_opts);
            
            if(this_butt.html() == 'All'){
                var selected = [];
                at_sel.find('option').each(function(i,e){
                    if($(e).attr('value').length > 0){
                        selected[selected.length]=$(e).attr('value');    
                    }
                });
                
                var selected2=[];
                pt_sel.find('option').each(function(i,e){
                    if($(e).attr('value').length > 0){
                        selected2[selected2.length]=$(e).attr('value');
                    }
                });
                
                at_sel.select2('val', selected);
                pt_sel.select2('val', selected2);
                this_butt.html('None');
                $('#eaPushTeamAll".$tid."').html('None');
            }
            else{
                at_sel.select2('val','');
                this_butt.html('All');
                
            }
        });

        $('#eaPushTeamAll".$tid."').click(function(){
            var at_sel = $('#eaAssistTeams".$tid."');
            var at_sel_opts = at_sel.find('option');
            var pt_sel = $('#eaPushTeams".$tid."');
            var pt_sel_opts = pt_sel.find('option');
            var this_butt = $(this);
            
            if(this_butt.html() == 'All'){
                var selected = [];
                pt_sel.find('option').each(function(i,e){
                    if($(e).attr('value').length > 0){
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
                $('#eaAssistTeamAll".$tid."').html('All');
                
            }
        });
        
        $('#eaStartTime".$tid."').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            minuteStep: 5,
            startDate:'2014-11-01',
            //forceParse:false,
            endDate:'2015-03-31',
            linkField: 'eaEndTime".$tid."',
            linkFormat: 'yyyy-mm-dd hh:ii:ss',
        });
    
        $('#eaEndTime".$tid."').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            minuteStep: 5,
            startDate:'2014-11-01',
            forceParse:false,
            endDate:'2015-03-31',
        });
           
        $('#eaDueDate".$tid."').datetimepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            //minuteStep: 1,
            minView: 2,
            //showMeridian: true,
            startDate:'2014-11-01',
            forceParse:true,
            endDate:'2015-03-31',
        });
        
        
    ");


    //Figure out current team contributions
    if(!empty($task['TasksTeam'])){
        $tt = $task['TasksTeam'];
        $lead_id = Hash::extract($tt, '{n}[task_role_id=1].team_id');
        $push_id = Hash::extract($tt, '{n}[task_role_id=2].team_id');
        $assist_id = Hash::extract($tt, '{n}[task_role_id=3].team_id');
    }
?>
    <?php echo $this->Form->create('Task', array(
        'action'=>'edit',
        'class'=>'formEditTask',
        'type'=>'post',
        'data-tid'=> $tid, 
        'id'=>'eaEditForm_'.$tid, 
        'novalidate' => true,
        'inputDefaults' => array(
            'label' => false), 
        'role' => 'form')); 
    ?>
    <div class="row" id="eaTask<?php echo $tid;?>">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit Task</div>
                <div class="panel-body">
                    <?php echo $this->Form->input('id', array(
                        'id'=>'input-task-id_'.$tid,
                        'type'=>'hidden')); ?>

                <div class="row">
                    <div class="col-md-1">
                        <div class="form-group">
                            <?php echo $this->Form->label('id', 'ID'); ?>
                            <?php echo $task['Task']['id']; 
                            ?>
                        </div><!-- .form-group -->
                    </div>
    
                    <div class="col-md-3">
                        <div class="form-group">
                            <?php echo $this->Form->label('task_type_id', 'Task Type*'); ?>
                            <?php echo $this->Form->input('task_type_id', array(
                                'class' => 'form-control', 
                                'id'=>'input-tasktype-select_'.$tid, 
                                'options'=>$taskTypes)); 
                            ?>
                        </div><!-- .form-group -->
                    </div>
                    <div class="col-md-4">
                        <?php echo $this->Form->input('start_time', array(
                            'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                            'type'=>'text',
                            'id'=>'eaStartTime'.$tid, 
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
                            'id'=>'eaEndTime'.$tid, 
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
                    <div class="col-sm-12">
                        <div class="linkableParents">
                            <?php
                                echo $this->element('task/linkable_parents', array('curParent'=>$task['Task']['parent_id']));
                            ?>    
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php echo $this->Form->label('short_description', 'Short Description*');?>
                            <?php echo $this->Form->input('short_description', array(
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
                            'id'=>'eaInputDetails'.$tid, 
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
                                    'options'=>$user_teams, 
                                    'id'=>'eaLeadTeam'.$tid, 
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
                                <?php echo $this->Form->label('AssistTeams', 'Assisting <small>(Response Required)</small>');?>
                                <?php echo $this->Form->input('AssistTeams', array(
                                    'empty'=>true, 
                                    'id'=>'eaAssistTeams'.$tid, 
                                    'options'=>$teams, 
                                    'selected'=>$assist_id,
                                    'type'=>'select', 
                                    'placeholder'=>'Assisting Team(s)', 
                                    'multiple'=>true,
                                    'class' => 'input-ateam-select')); ?>
                                <span class="input-group-btn">
                                    <button style="margin-top: 23px;" class="btn btn-sm btn-primary ateamAll" id="eaAssistTeamAll<?php echo $tid;?>" type="button">All</button>
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
                                    'id'=>'eaPushTeams'.$tid, 
                                    'options'=>$teams,
                                    'selected'=>$push_id, 
                                    'type'=>'select', 
                                    'placeholder'=>'Push To', 
                                    'multiple'=>true, 
                                    'class' => 'input-pteam-select')); ?>
                                <span class="input-group-btn">
                                    <button style="margin-top: 23px;" class="btn btn-sm btn-darkgrey pteamAll" id="eaPushTeamAll<?php echo $tid;?>" type="button">All</button>
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
									'id'=>'eaDueDate'.$tid,
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
                                        'id'=>'input-actionabletype-select_'.$tid,
                                        'empty'=>true,
                                        'value'=>$this->request->data['Task']['actionable_type_id'],
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
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-danger">
                      <div class="panel-body">
                        <button type="button" data-tid="<?php echo $tid;?>" class="btn btn-danger eaTaskDeleteButton">Delete Task</button>
                        &nbsp;&nbsp;
                            <i class="fa fa-lg fa-exclamation-circle"></i><b> This cannot be undone</b>                               
                      </div>
                    </div>
                </div>
            </div>
        </div><!--col-md-3-->
    </div><!--row-->
    <div class="row">
        <div class="col-sm-4">
            <?php 
                //echo '<span class="pull-right">';
                echo $this->Form->submit('Save Changes', array(
                    'id'=>'eaSubmitButton_'.$tid, 
                    'div'=>false, 
                    'class' => 'eaSubmitButton submit btn btn-large btn-success'));
            
                echo '&nbsp;&nbsp;';
                echo $this->Html->link('Cancel', array('action'=>'compile'), array('class'=>'btn btn-large btn-danger'));
                echo '&nbsp;&nbsp;';
                echo '<span class="eaSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                //echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner_img', ));
                echo $this->Html->image('ajax-loader_old.gif');
                echo '</span>'; 
	       ?>
        </div>
        <div class="col-sm-8">
            <div class="eaValidationContent" id="validation_content_<?php echo $tid?>"></div>
        </div>
    </div>

<?php
    echo $this->Form->end(); 
    echo $this->Js->writeBuffer(); 
?>  