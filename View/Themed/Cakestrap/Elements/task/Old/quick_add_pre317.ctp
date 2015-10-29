
<?php
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
        $('body').on('submit','form.formAddTask', function(e){
            var subBut = $(this).find('.qaSubmitButton');
            var valCont = $(this).find('.qaValidationContent');
            var spinner = $(this).find('.qaSpinner');
            
            subBut.val('Saving...');
            spinner.fadeIn();

            $.post($(this).attr('action'), $(this).serialize(), function(data){
                    valCont.fadeOut('fast');                
            })
                .done(function(data) {
                    $('#qaForm').trigger('reset');
                    $('#qaAssistTeams').select2('val','');
                    $('#qaPushTeams').select2('val','');
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
            return false;
        });
        
        $('#qaAssistTeamAll').click(function(){
            var cur_lead = $('#qa_input-leadteam-select').val();
            var at_sel =  $('#qaPanelBody').find('.input-ateam-select');
            var at_sel_opts = $('#qaPanelBody').find('select.input-ateam-select option');
            var pt_sel =  $('#qaPanelBody').find('.input-pteam-select');
            var pt_sel_opts = $('#qaPanelBody').find('select.input-pteam-select option');
  
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
                $('#qaPushTeamAll').html('None');
            }
            else{
                at_sel.select2('val','');
                this_butt.html('All');
                
            }
        });

        $('#qaPushTeamAll').click(function(){
            var cur_lead = $('#qa_input-leadteam-select').val();
            var at_sel =  $('#qaPanelBody').find('.input-ateam-select');
            var at_sel_opts = $('#qaPanelBody').find('select.input-ateam-select option');
             
            var pt_sel =  $('#qaPanelBody').find('.input-pteam-select');
            var pt_sel_opts = $('#qaPanelBody').find('select.input-pteam-select option');
            var this_butt = $(this);
            
            if(this_butt.html() == 'All'){
                var selected = [];
                pt_sel.find('option').each(function(i,e){
                    if($(e).attr('value').length > 0  && $(e).attr('value') != cur_lead){
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
                $('#qaAssistTeamAll').html('All');
                
            }
        });

        $('#qaInputDetails').wysihtml5({
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
        
        $('#qaStartTime').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
	     
            minuteStep: 5,
            startDate:'2014-11-01',
            //forceParse:false,
	     showMeridian: true,            
            keyboardNavigation:false,
            endDate:'2015-03-31',
            linkField: 'qaEndTime',
            linkFormat: 'yyyy-mm-dd hh:ii:ss',
        });
    
        $('#qaEndTime').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            showMeridian: true,            
            keyboardNavigation:false,
            minuteStep: 5,
            startDate:'2014-11-01',
            forceParse:false,
            endDate:'2015-03-31',
        });
           
        $('#qaDueDate').datetimepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            keyboardNavigation:false,
            //minuteStep: 1,
            minView: 2,
            //showMeridian: true,
            startDate:'2014-11-01',
            forceParse:true,
            endDate:'2015-03-31',
        });
        
        $('#qaAssistTeams').select2({
            'width':'100%',
            'allowClear':true,
            'placeholder':'Assisting',
            'minimumResultsForSearch':-1,
            
             formatSelectionCssClass: function (data, container) { 
                return 'team-assist'; },
            })
            .on('change', function(e) {
                if(e.added){
                    var old = $('#qaPushTeams').select2('val');
                    if(old[0]==''){
                        old.shift();
                    }
                    old[old.length] = e.added.id;
                    $('#qaPushTeams').select2('val', old);
                }
            }
        );
        
        $('#qaPushTeams').select2({
            'width':'100%',
            'allowClear':true,
            'placeholder':'Pushed To',
            'minimumResultsForSearch':-1,
            formatSelectionCssClass: function (data, container) { 
                return 'team-push'; },
            })
            .on('change', function(e) {
                if(e.removed){
                    var old = $('#qaAssistTeams').select2('val');
                    var rid = e.removed.id;
                    var oindex = old.indexOf(rid);
                    
                    if(oindex > -1){
                        var nindex = old.splice(oindex,1);
                        $('#qaAssistTeams').select2('val', old);
                    }
                }
            }
        );
        
        

    ");
    
?>



    <?php 
        echo $this->Form->create('Task', array(
            'class'=>'formAddTask',
            'id'=>'qaForm',
            'action'=>'add',
            'novalidate' => true,
            'inputDefaults' => array(
                'label' => false), 
                'role' => 'form'));
    ?>

    <div class="panel-body" id="qaPanelBody">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <?php echo $this->Form->input('team_id', array(
                        'empty'=>$team_input_empty,
                        'readonly'=>$team_input_readonly,
                        'label'=>'Lead Team*',
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
            <div class="col-md-2">
                <?php echo $this->Form->input('task_type_id', array(
                    'options'=>$taskTypes,
                    'label'=>'Type*', 
                    'class' => 'form-control')); 
                ?>
            </div>

            <div class="col-md-3">
                <?php echo $this->Form->input('start_time', array(
                    'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                    'type'=>'text',
                    'id'=>'qaStartTime',
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
             <div class="col-md-3">   
                <?php echo $this->Form->input('end_time', array(
                    'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                    'type'=>'text',
                    'id'=>'qaEndTime',
                    'label'=>'End Time*',
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
                <b>Add</b><br/>
                <button type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#details">Task Details</button>
                <button type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#teamStatus">Teams & Statuses</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Form->input('short_description', array(
                    'class' => 'form-control',
                    'label'=>'Short Description*',
                    'error' => array(
                        'attributes' => array(
                        'wrap' => 'span', 
                        'class' => 'help-inline text-danger bolder')))); 
                ?>
            </div>
        </div>

        <div class="row collapse sm-top-marg" id="details">
            <div class="col-md-12">
                <?php echo $this->Form->input('details', array(
                    'id'=>'qaInputDetails',
                    'label'=>'Details',
                    'class' => 'input-details form-control')); 
                ?>
            </div>
        </div>

        <div class="row collapse sm-top-marg" id="teamStatus">
            <div class="col-md-9">
                <div class="panel panel-dark">
                    <div class="panel-heading"><i class="fa fa-users"></i>&nbsp;&nbsp;Teams</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <?php echo $this->Form->input('AssistTeams', array(
                                        'empty'=>true,
                                        'id'=>'qaAssistTeams', 
                                        'options'=>$teams,
                                        'label'=> 'Assisting <small>(Response Required)</small>', 
                                        'type'=>'select', 
                                        'placeholder'=>'Select Teams', 
                                        'multiple'=>true,
                                        'class' => 'input-ateam-select',
                                        'id'=>'qaAssistTeams')); 
                                    ?>
                                    <span class="input-group-btn">
                                        <button style="margin-top: 23px;" class="btn btn-sm btn-primary ateamAll" id="qaAssistTeamAll" type="button">All</button>
                                    </span>
                                </div><!-- .input-group -->
                            </div>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <?php echo $this->Form->input('PushTeams', array(
                                        'empty'=>true, 
                                        'options'=>$teams,
                                        'id'=>'qaPushTeams',
                                        'label'=>'Pushed To Teams',
                                        'type'=>'select', 
                                        'placeholder'=>'Select Teams', 
                                        'multiple'=>true, 
                                        'class' => 'input-pteam-select',
                                        'id'=> 'qaPushTeams')); 
                                    ?>
                                    <span class="input-group-btn">
                                        <button style="margin-top: 23px;" class="btn btn-sm btn-darkgrey pteamAll" id="qaPushTeamAll" type="button">All</button>
                                    </span>
                                </div><!-- .input-group -->
                            </div>
                        </div>
                    </div><!--panel body-->
                </div><!--panel-->
            </div>
            <div class="col-md-3">
                <div class="panel panel-bdanger">
                <div class="panel-heading"><i class="fa fa-flag"></i>&nbsp;&nbsp;Dates &amp; Statuses</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $this->Form->label('due_date', 'Due Date'); ?>
                                <?php echo $this->Form->input('due_date', array(
                                    'empty'=>true,
                                    'id'=>'qaDueDate',
                                    'type'=>'text',
                                    'placeholder'=>'Set due date',
                                    'div'=>array(
                                        'class'=>'input-group'),
                                    'after'=>'<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>',
                                    'class'=>'form-control input-date-notime')); ?>
                            </div>
                        </div>

                    <?php if($user_role >= 100): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo $this->Form->label('actionable_type_id', 'Action Item Status');?>
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
                    </div><!--panel body-->
                </div><!--panel-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-md-2">
                <?php 
                    echo $this->Form->submit('Add Task', array(
                        'div'=>false, 
                        'class' => 'qaSubmitButton submit btn btn-large btn-yh'));
                    echo '&nbsp;&nbsp;';
                    echo '<span class="qaSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                    //echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner_img', ));
                    echo $this->Html->image('ajax-loader_old.gif');
                    echo '</span>'; 
               ?>
            </div>
        <div class="col-md-6">
            <div class="qaValidationContent"></div>
        </div>
    </div>
        
        
    </div><!--main panel body-->

    <?php echo $this->Form->end(); ?>
    <?php echo $this->Js->writeBuffer();?>
