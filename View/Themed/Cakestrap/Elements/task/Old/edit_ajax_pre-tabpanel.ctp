
<?php
    //$this->extend('/Common/Task/view_task');
    //echo $this->Html->script('task_details', array('inline'=>false));
    //echo $this->Html->script('form-controls', array('inline'=>false));
    
    if(!empty($task)){
        $this->request->data = $task;
    }
    
    $tid = $task['Task']['id'];
    
    //$this->log($tid);
    

    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
        $user_teams = AuthComponent::user('TeamsList');
    }
    
        $this->Js->buffer("
        
        $('body').on('submit','form.formEditTask', function(e){
            var subBut = $(this).find('.eaSubmitButton');
            var valCont = $(this).find('.eaValidationContent');
            var spinner = $(this).find('.eaSpinner');
            

        // Update submit text to indicate something is happening
        subBut.val('Saving...');
        spinner.fadeIn();

        // Post the form using the form's action and data
        $.post($(this).attr('action'), $(this).serialize())
        // called when post has finished
        .done(function(data) {
            spinner.fadeOut('fast');
            
                
            
            //console.log(data);
            // inject returned html into page
            valCont.html(data);
            
            
            
            
        })
        // called on failure
        .fail(function(data, textStatus) {
            //alert(data.responseText);
            //console.log(data);
            valCont.html(data.responseText);
        })
        
        .always(function(){
            spinner.fadeOut('fast');
            subBut.val('Save Changes');
            
            
            
        });

    // return false to stop the page from posting normally
    return false;
    });
        
        
        
        
         
         /*
         $('form.form_task_edit').submit(function(){
            alert('submitting2');
            return false;
        });       
  

        $(document).on('submit', 'form.form_task_edit', function(){
            alert('submitting from e_a');
            

            
            
            
            
                        
            return false;
        });
           */

        
    $('select.input-ateam-select').select2({
        'width':'100%',
        'allowClear':true,
        'placeholder':'Assisting',
        'minimumResultsForSearch':-1,
        
        
        
        
         formatSelectionCssClass: function (data, container) { 
            return 'team-assist'; },
        /*
        formatSelection: function (referencia) {
            return referencia.text;
        },
        formatResultCssClass: function(object){
            return 'highlight';},*/
        
        });
    
    $('select.input-pteam-select').select2({
        'width':'100%',
        'allowClear':true,
        'placeholder':'Pushed To',
        'minimumResultsForSearch':-1,
        
        
        formatSelection: function(item) {
        // Debugging -- open the developer console to see what you can access from the item object
        console.dir(item);

        return '<strong>' + item.text + '</strong>';
    },
          /*
         formatSelectionCssClass: function (data, container) { 
            return 'team-assist'; },
       
        
        formatResultCssClass: function(object){
            return 'highlight';},
       
        formatSelection: function (referencia) {
            return referencia.text;
        }*/
        
        });
        
    $('.datetimepicker-stime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        showMeridian: true,
        startDate:'2014-11-01',
        //forceParse:false,
        endDate:'2015-03-31',
        linkField: 'TaskEndTime',
        linkFormat: 'yyyy-mm-dd hh:ii:ss',
    });

    $('.datetimepicker-etime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        showMeridian: true,
        startDate:'2014-11-01',
        forceParse:false,
        endDate:'2015-03-31',
    });
       
    $('.input-date-notime').datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        minView: 2,
        showMeridian: true,
        startDate:'2014-11-01',
        forceParse:true,
        endDate:'2015-03-31',
    });
    
    $('.input-details').wysihtml5({
        html: false, //Button which allows you to edit the generated HTML. Default false
        link: true, //Button to insert a link. Default true
        image: false, //Button to insert an image. Default true,
        color: false, //Button to change color of font
        lists: true,  
        
    });        

        
        
        
        
        
        
        
        ");
        
    
    //If user controls >2 teams, force empty input so they can't forget and accidently set an incorrect team
    $team_input_readonly = false;
    
    if(count($controlled_teams) == 1){
        $team_input_empty = false;
        $team_input_readonly = 'readonly';
    }
    
    elseif(count($controlled_teams)>=2){
        $team_input_empty = true;
    }

    if (!empty($task['Task']['task_color_code'])){
        $tcol = $task['Task']['task_color_code'];
        $this->Js->buffer("
            $('.panel-taskcolored').css('border-color','#ccc');
            $('.panel-taskcolored > .panel-heading').css({'color':'".$tcol."', 'background-color':'".$tcol."','border-color':'#ccc'});
            $('.panel-taskcolored > .panel-heading').css({'color':'#000', 'background-color':'".$tcol."','border-color':'#ccc'});
            $('.panel-taskcolored > .panel-heading + .panel-collapse .panel-body').css('border-top-color','#ccc');
            $('.panel-taskcolored > .panel-footer + .panel-collapse .panel-body').css('border-bottom-color','#ccc');
        ");
    }
    
    //Figure out current team contributions
    
    if(!empty($task['TasksTeam'])){
        $tt = $task['TasksTeam'];
        $lead_id = Hash::extract($tt, '{n}[task_role_id=1].team_id');
        $push_id = Hash::extract($tt, '{n}[task_role_id=2].team_id');
        $assist_id = Hash::extract($tt, '{n}[task_role_id=3].team_id');
    
    }
    
    //debug($push_id);
    
    
    
?>
<div class="row">
    <?php echo $this->Form->create('Task', array(
                    //'url' => array(
                      //  'controller' => 'tasks', 
                        //'action' => 'edit', $task['Task']['id']),
                    'action'=>'edit',
                    'class'=>'formEditTask',
                    'type'=>'post',
                    'data-tid'=> $tid, 
                    'id'=>'eaEditForm_'.$tid, 
                    'novalidate' => true,
                    'inputDefaults' => array(
                        'label' => false), 
                    'role' => 'form')); ?>
<div class="col-sm-12">
<div class="panel panel-yh">
    <div class="panel-heading"><i class="fa fa-edit"></i><b>&nbsp;Edit Task (#<?php echo $task['Task']['id'];?>)</b></div>
    <div class="panel-body">
        

        
        
        
        
        <div class="row">
            <div class="col-md-9">
                
                <?php echo $this->Form->input('id', array(
                    'id'=>'input-task-id_'.$tid,
                    'type'=>'hidden')); ?>
  <!--
            <div class="panel panel-success">
                <!--<div class="panel-heading" style="color:#000;"><b>Task Details</b></div>-->
               <!-- <div class="panel-body">-->
                    <div class="row">

                        <div class="col-md-4">
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
                                'label'=>'End Time',
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                'class'=>'form-control datetimepicker-etime',
                                'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')))); ?>
                        </div>
      

      
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php echo $this->Form->label('short_description', 'Short Description*');?>
                                <?php echo $this->Form->input('short_description', array(
                                    'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')),
                                    'class' => 'form-control')); ?>
                            </div><!-- .form-group -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 sm-bot-marg">
                            <?php echo $this->Form->input('details', array(
                                'label'=>'Details', 
                                'id'=>'edit_input-details_'.$tid, 
                                'class'=>'input-details form-control', 
                                'type' => 'textarea')); ?>
                        </div>
                    </div>
                    
                    
                </div>
           <!-- </div>
            </div><!--col-md-9-->

            <div class="col-md-3">
                <div class="panel panel-dark">
                    <div class="panel-heading"><b>Teams</b></div>
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
                                        'id'=>'ea_input-leadteam-select_'.$tid, 
                                        'class' => 'form-control',
                                        'error' => array('attributes' => array(
                                            //'id'=>'lead-team-error-message',
                                            'wrap' => 'span', 
                                            'class' => 'help-inline text-danger bolder')
                                        ))); ?>
                                </div><!-- .form-group -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <?php echo $this->Form->label('AssistTeams', 'Assisting');?>
                                    <?php echo $this->Form->input('AssistTeams', array(
                                        'empty'=>true, 
                                        'id'=>'ea_input-ateam-select_'.$tid, 
                                        'options'=>$teams, 
                                        'selected'=>$assist_id,
                                        'type'=>'select', 
                                        'placeholder'=>'Assisting Team(s)', 
                                        'multiple'=>true,
                                        'class' => 'form-control input-ateam-select')); ?>
                                    <span class="input-group-btn">
                                        <button style="margin-top: 23px;" class="btn btn-sm btn-primary" type="button">All</button>
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
                                        'id'=>'ea_input-pteam-select_'.$tid, 
                                        'options'=>$teams,
                                        'selected'=>$push_id, 
                                        'type'=>'select', 
                                        'placeholder'=>'Push To', 
                                        'multiple'=>true, 
                                        'class' => 'input-pteam-select')); ?>
                                    <span class="input-group-btn">
                                        <button style="margin-top: 23px;" class="btn btn-sm btn-darkgrey" type="button">All</button>
                                    </span>
                                </div><!-- .input-group -->
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
    									'id'=>'ea_input-duedate_'.$tid,
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
            </div><!--col-md-3-->
        </div><!--row-->
    </div><!--outer panel body-->
    <div class="panel-footer">
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
    </div>
</div><!--outer panel-->
</div>
                        <?php echo $this->Form->end(); ?>

</div>

        
<?php 
            

echo $this->Js->writeBuffer(); ?>  