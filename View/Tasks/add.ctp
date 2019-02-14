
<?php
    echo $this->Html->script('form-controls');
              
    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
    }
    
    $control_team_count = count($controlled_teams, COUNT_RECURSIVE) - count($controlled_teams);
    
    //Form field defaults
    $readonly = false;
    $team_input_readonly = false;
    $task_color_selected = $this->request->data('Task.Color');
    
    if($control_team_count >=2){
        $team_input_empty = true;
    }

    elseif($control_team_count == 1){
        $team_input_readonly = 'readonly';
        $team_input_empty = false;
    }
    else{ $team_input_empty = false;}
    
    if (!empty($task_color_selected)){
        $tcol = $task_color_selected;
        $to_be_buff = '
            $(".panel-taskcolored").css("border-color","#ccc");
            $(".panel-taskcolored > .panel-heading").css({"color":"'.$tcol.'", "background-color":"'.$tcol.'","border-color":"#ccc"});
            $(".panel-taskcolored > .panel-heading").css({"color":"#000", "background-color":"'.$tcol.'","border-color":"#ccc"});
            $(".panel-taskcolored > .panel-heading + .panel-collapse .panel-body").css("border-top-color","#ccc");
            $(".panel-taskcolored > .panel-footer + .panel-collapse .panel-body").css("border-bottom-color","#ccc");
        ';
        
        $this->Js->buffer($to_be_buff);
    }
    
    $this->Js->buffer("
    $('#e10').select2({
        placeholder: 'Select report type',
        width:'100%',
        allowClear: true,
        })
        .on('change', 
            function(e) {
                if(e.added){
                    var old = $('#e11').select2('val');
                    //console.log('old e11: ' +old);
                    //alert('hi');
                    //console.log($('#e10').select2('val'));
                    old[old.length] = e.added.id;
                    //old[] = e.added.id;
                    $('#e11').select2('val', old);
                    //console.log('after adding: ' + old);
                    //console.log($('#e10').select2('val'));    
                }
            }
        );
        
    $('#e11').select2({
        placeholder: 'Select report type',
        width:'100%',
        allowClear: true,
        })
        .on('change', 
            function(e) {
                if(e.removed){
                    var old = $('#e10').select2('val');
                    //console.log('old e10: '+old);
                    var rid = e.removed.id;
                    
                    var oindex = old.indexOf(rid);
                    //console.log('oindex: ' +oindex);
                    
                    if(oindex > -1){
                        var nindex = old.splice(oindex,1);
                        $('#e10').select2('val', old);
                        //console.log('new: ' + $('#e10').select2('val'));    
                    }
                    
                }
                //$('#e11').select2('val', e.val);
            }
        );        
        
    
        $('#add_spinner').css({'display':'none'});
        
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
                        <div class="col-md-4">
                            <div class="form-group has-success">

                            <?php //echo $this->Form->label('task_type_id', 'Type*'); ?>
                            <?php echo $this->Form->input('task_type_id', array(
                                'label'=> array(
                                    'text'=>'Type*',
                                    //'class'=>'control-label'
                                    ),
                                'options'=>$taskTypes, 
                                'class' => 'form-control')); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group has-success">
                                <?php echo $this->Form->input('start_time', array(
                                    'format' => array(
                                        'label', 'between', 'before', 'input', 'after', 'error'),
                                    'type'=>'text',
                                    'label'=>array(
                                        'text'=>'Start Time*',
                                        //'class'=>'control-label'
                                        ),
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
                        </div>
                        
                        <div class="col-md-4">   
                            <?php echo $this->Form->input('end_time', array(
                                'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                                'type'=>'text',
                                'label'=>'End Time*',
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                    'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                    'class'=>'form-control datetimepicker-etime',
                                    'error' => array(
                                        'attributes' => array(
                                            'id'=>'etime-error-msg', 
                                            'wrap' => 'span', 
                                            'class' => 'help-inline text-danger bolder')
                                    )));
                            ?>           
                        </div>

                    </div>

                    <div class="row xsm-top-marg">
                        <div class="col-md-12">
                            <div class="form-group has-success">
                                <?php //echo $this->Form->label('short_description', 'Short Description*');?>
                                <?php echo $this->Form->input('short_description', array(
                                    'label'=> array(
                                        'text'=>'Short Description*',
                                        //'class'=>'control-label'
                                        ),
                                    'error' => array('attributes' => array(
                                        'wrap' => 'span', 
                                        'class' => 'help-inline text-danger bolder')),
                                    'class' => 'form-control')); ?>
                            </div><!-- .form-group -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?php echo $this->Form->input('details', array('label'=>'Details', 'id'=>'add_input-details', 'class'=>'input-details form-control', 'type' => 'textarea')); ?>
                            <p class="help-block">(Optional) Use this to store extra details about this task</p>   
                        </div>
                    </div>    

                    <div class="row sm-top-marg">
                        <div class="col-md-12">
                            <?php echo $this->Form->submit('Add Task', array('class' => 'btn btn-large btn-success')); ?>
                        </div>
                    </div>
                </div><!--leftcol-->
                
                <div class="col-md-3">
                    <div class="panel panel-yh">
                        <div class="panel-heading"><b>Teams</b></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group has-success">
                                        <?php //echo $this->Form->label('team_id', 'Lead Team*');?>
                                        <?php echo $this->Form->input('team_id', array(
                                            'empty'=>$team_input_empty,
                                            'readonly'=>$team_input_readonly, 
                                            'div'=>false, 
                                            'multiple'=>false, 
                                            'options'=>$controlled_teams, 
                                            'id'=>'add_input-leadteam-select', 
                                            'class' => 'form-control',
                                            'label'=>array(
                                                'text'=>'Lead Team*',
                                                //'class'=>'control-label'
                                                ),
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
                                        <?php echo $this->Form->label('AssistTeams', 'Request Response From');?>
                                        <?php echo $this->Form->input('AssistTeams', array(
                                            'empty'=>true, 
                                            'id'=>'e10', 
                                            'options'=>$teams, 
                                            'type'=>'select', 
                                            'placeholder'=>'Select Assisting Team(s)', 
                                            'multiple'=>true, 
                                            'class' => 'input-conteam-select')); ?>
                                    </div><!-- .form-group -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                  <!-- <p><input type="hidden" id="e11"/></p>-->
                                
                                 <div class="form-group">
                                        <?php echo $this->Form->label('PushTeams', 'Notify (Nothing Needed)');?>
                                        <?php echo $this->Form->input('PushTeams', array(
                                            'empty'=>true, 
                                            'id'=>'e11', 
                                            'options'=>$teams, 
                                            'type'=>'select', 
                                            'placeholder'=>'Select Pushed Team(s)', 
                                            'multiple'=>true, 
                                            'class' => 'input-conteam-select')); ?>
                                    </div> <!--.form-group--> 
                                </div>
                            </div>
                        </div><!--panel body-->
                    </div><!--panel-->
                    
                    <div class="panel panel-danger">
                        <div class="panel-heading"><b>Dates &amp; Statuses</b></div>
                        <div class="panel-body">
                                                    <div class="row">
                            <div class="form-group">
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
                        </div>
                            
                            
                            
                            <?php if($user_role >= 200): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('actionable_type_id', 'Action Item Status');?>
                                        <?php echo $this->Form->input('actionable_type_id', array(
                                            //'id'=>'input-actionabletype-select',
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
                </div><!-- left col-->
            </div>
        </div><!--main panel body-->
    </div><!--panel-->
</div>
    
    

<?php 
    echo $this->Form->end(); 
    //echo $this->Js->writeBuffer();
?>





