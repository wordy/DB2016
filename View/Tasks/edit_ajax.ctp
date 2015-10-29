<?php
    //$this->extend('/Common/Task/view_task');
    //echo $this->Html->script('form-controls');
    
    if(!empty($task)){
        $this->request->data = $task;
    }
    
    $this->Js->buffer("
    
    $('#logteam').on('click', function(){
       var tt =[];
       var lt = [];
       var pt = [];
       var ort = [];
       var crt = [];
         
       $('.new_tt').each(function(){
            //tt[$(this).data('tr_id')][]= $(this).data('tr_id');  
            if ($(this).data('tr_id') == 1){
                lt[lt.length] = $(this).data('team_id');
            }
            else if ($(this).data('tr_id') == 2){
                pt[pt.length] = $(this).data('team_id');
            }
            else if ($(this).data('tr_id') == 3){
                ort[ort.length] = $(this).data('team_id');
            }
            
                         
       });
       tt.push(lt, pt, ort);
       //r tt = [lt pt ort];     
       console.log(tt); 
    });
    /*
    $('span.new_tt[data-tr_id=0]').addClass('btn-info');
    
    $('.new_tt').on('click', function(){
       $(this).toggleClass('btn-default btn-danger btn-success'); 
    });*/
    
    function hello(hi){
        console.log(hi);
    }
    
    $('body').on('click', '.new_tt', function(){
        hello('word');
        
        var trid = $(this).data('tr_id');
        
        
        if(trid == 0){
            
            if($(this).hasClass('btn-unselected')){
                $(this).removeClass('btn-unselected').addClass('btn-default').data('tr_id', 2);
            }
                
        }
        else if(trid == 2){
            
            if($(this).hasClass('btn-default')){
                $(this).removeClass('btn-default').addClass('btn-danger').data('tr_id', 3);    
            }
                
        }
        else if(trid == 3){
            
            if($(this).hasClass('btn-danger')){
                $(this).removeClass('btn-danger').addClass('btn-success').data('tr_id', 4);    
            }
                
        }
        else if(trid == 4){
            
            if($(this).hasClass('btn-success')){
                $(this).removeClass('btn-success').addClass('btn-unselected').data('tr_id', 0);    
            }
                
        }


            
        //$(this).removeClass('btn-default').addClass('btn-danger');
        //$(this).data('tr_id', 3);
    });
    
    
    $('.new_tt').on('click', function(){
        var tt2 = [];
        $('.new_tt').each(function(k, val){
            tt2[$(this).data('team_id')] = $(this).data('tr_id');
        });
        
        
        
        console.log(tt2);
        
    });
    
    
    /*
    $('.btn-unselected.new_tt').on('click', function(){
        $(this).removeClass('btn-unselected').addClass('btn-default');
        $(this).data('tr_id', 2);
    });

    $('body').on('click', 'btn-default.new_tt', function(){
        $(this).removeClass('btn-default').addClass('btn-danger');
        $(this).data('tr_id', 3);
    });
    
    $('.btn-danger.new_tt').on('click', function(){
        $(this).removeClass('btn-danger').addClass('btn-success');
        $(this).data('tr_id', 4);
    });
    
    $('.btn-success.new_tt').on('click', function(){
        $(this).removeClass('btn-success').addClass('btn-unselected');
        $(this).data('tr_id', 0);
    });

    */
    
    // Take over the submit action
    // attached to the body as the ajax call will replace the form 
    // and otherwise the onsubmit handler will be lost
    /*
    $('body').on('submit','.form-edit-task', function(e){

        // Update submit text to indicate something is happening
        $('#form-submit-button').val('Saving...');
        $('#spinner').fadeIn();

        // Post the form using the form's action and data
        $.post($(this).attr('action'), $(this).serialize())
        // called when post has finished
        .done(function(data) {
            //$('#spinner').fadeOut('fast');
            
                
            
            //console.log(data);
            // inject returned html into page
            $('#validation_content').html(data);
            
            
            
            
        })
        // called on failure
        .fail(function(data, textStatus) {
            //alert(data.responseText);
            //console.log(data);
            $('#validation_content').html(data.responseText);
        })
        
        .always(function(){
            $('#spinner').fadeOut('fast');
            $('#form-submit-button').val('Save Changes');
            
            
            
        });

    // return false to stop the page from posting normally
    return false;
    });
*/









    ");
        
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
<div class="panel panel-primary">
    <div class="panel-heading"><i class="fa fa-bookmark-o"></i><b>&nbsp;Edit Task (/v/t)</b></div>
    <div class="panel-body">
        

        
        
        
        
        <div class="row">
            <div class="col-md-9">
                <?php echo $this->Form->create('Task', array(
                    //'url' => array(
                      //  'controller' => 'tasks', 
                        //'action' => 'edit', $task['Task']['id']),
                    'action'=>'edit',
                    'type'=>'post', 
                    'class'=>'form-edit-task', 
                    'novalidate' => true,
                    'inputDefaults' => array(
                        'label' => false), 
                    'role' => 'form')); ?>
                <?php echo $this->Form->input('id', array('type'=>'hidden')); ?>
  
            <div class="panel panel-success panel-taskcolored">
                <div class="panel-heading" style="color:#000;"><b>Task Details</b></div>
                <div class="panel-body">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->label('task_type_id', 'Task Type*'); ?>
                                <?php echo $this->Form->input('task_type_id', array('class' => 'form-control', 'id'=>'input-tasktype-select', 'options'=>$taskTypes)); ?>
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
                                    'id'=>'edit_input-details', 
                                    'class'=>'input-details form-control', 
                                    'type' => 'textarea')); ?>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            
            
                    

            </div><!--col-md-9-->

            <div class="col-md-3">
                    <div class="panel panel-yh">
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
                                        'id'=>'edit_input-leadteam-select', 
                                        'class' => 'form-control',
                                        'error' => array('attributes' => array(
                                            'id'=>'lead-team-error-message',
                                            'wrap' => 'span', 
                                            'class' => 'help-inline text-danger bolder')
                                        ))); ?>
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
                                        'selected'=>$assist_id,
                                        'type'=>'select', 
                                        'placeholder'=>'Select Assisting Team(s)', 
                                        'multiple'=>true, 
                                        'class' => 'input-conteam-select')); ?>
                                </div><!-- .form-group -->
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('PushTeams', 'Notify (Nothing Needed)');?>
                                        <?php echo $this->Form->input('PushTeams', array(
                                            'empty'=>true, 
                                            'id'=>'e11', 
                                            'options'=>$teams,
                                            'selected'=>$push_id, 
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
            echo $this->Form->submit('Save Changes', array('id'=>'form-submit-button', 'div'=>false, 'class' => 'submit btn btn-large btn-success'));
            echo '&nbsp;&nbsp;';
            echo $this->Html->link('Cancel', array('action'=>'compile'), array('class'=>'btn btn-large btn-danger'));
            echo '&nbsp;&nbsp;';
                        echo '<span id="spinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                        //echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner_img', ));
                        echo $this->Html->image('ajax-loader_old.gif', array('id' => 'spinner_img', ));
                        
                        
                        echo '</span>';  
        ?>   
<?php echo $this->Form->end(); ?>
		
            </div>
            <div class="col-sm-8">
                <div id="validation_content"></div>
            </div>
            
            
                    </div>
        
        </div>

</div><!--outer panel-->
</div>

        
<?php 


//


$tt1 = Hash::combine($tt, '{n}.team_id','{n}','{n}.task_role_id');

//debug($tt);

//debug($tt);


//$tt2 = array_values($tt1);

//debug($tt);


echo '<br/><a id="logtr" class="btn btn-default">Log</a>';




?>





<?php

//debug($tt2);
/*
$buttons1=$buttons2 = '';
foreach ($tt as $role){
    if ($role['task_role_id'] == 1){
        $buttons1.= '<span class="btn btn-leadt btn-xxs new_tt" data-team_id = "'.$role['team_id'].'" data-tr_id = "'.$role['task_role_id'].'">'.$role['team_code'].'</span>';    
    }
    elseif ($role['task_role_id']==2){
        $buttons2.= '<span class="btn btn-unselected btn-xxs new_tt" data-team_id = "'.$role['team_id'].'" data-tr_id = "'.$role['task_role_id'].'">'.$role['team_code'].'</span>';    
    }
    elseif ($role['task_role_id']==3){
        $buttons1.= '<span class="btn btn-danger btn-xxs new_tt" data-team_id = "'.$role['team_id'].'" data-tr_id = "'.$role['task_role_id'].'">'.$role['team_code'].'</span>';    
    }
    elseif ($role['task_role_id']==4){
        $buttons1.= '<span class="btn btn-success btn-xxs new_tt" data-team_id = "'.$role['team_id'].'" data-tr_id = "'.$role['task_role_id'].'">'.$role['team_code'].'</span>';    
    }
}
echo $buttons1.$buttons2;

echo '<br/><a id="logteam" class="btn btn-default">Log</a>';


*/









echo $this->Js->writeBuffer(array('inline'=>true)); ?>  