
<?php
    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
        //$controlled_teams = AuthComponent::user('TeamsList');
    }
    
    $control_team_count = count($controlled_teams, COUNT_RECURSIVE) - count($controlled_teams);
    //$controlled_teams = Hash::extract($controlled_teams,'{s}');
    
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
    
    $zbuts = array();
        $i=1;
        
        foreach($teams as $k=>$zone){
            foreach($zone as $k=>$tcode){
                $zbuts[$i][]='<span class="btn btn-unselected btn-sm new_tt" data-team_id = "'.$k.'" data-tr_id = "0">'.$tcode.'</span>';
            }
            $i++;        
        }
    
    $this->Js->buffer("
    
    $('#ajax-content-load').on('click', '.panel-heading span.clickable', function(e){
        if(!$(this).hasClass('panel-collapsed')) {
            $(this).parents('.panel-qa').find('.panel-body').slideUp();
            $(this).addClass('panel-collapsed');
            $(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $(this).parents('.panel-qa').find('.panel-body').slideDown();
            $(this).removeClass('panel-collapsed');
            $(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });
    
    
    $('#qaForm').on('submit', function(e){
        var subBut = $(this).find('.qaSubmitButton');
        var valCont = $(this).find('.qaValidationContent');
        var spinner = $(this).find('.qaSpinner');
            
        e.preventDefault();
                
        $('.new_tt').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            
            $('<input>').attr({
                type: 'hidden',
                name: 'data[TeamRoles]['+nteam+']',
                value: ntr,
            }).appendTo('#qaForm');
        });
            
        $.ajax( {
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            dataType:'json',
            beforeSend:function () {
                subBut.val('Saving...');
                valCont.fadeOut('fast');                
                spinner.fadeIn();
            },
            success:function(data, textStatus) {
                $('#qaForm').trigger('reset');  
                spinner.fadeOut('fast');
                //$('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                // Refresh tasks list
                /*    
                $('#taskListWrap').load('/tasks/compileUser', function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });*/
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
                subBut.val('Add Task');
            },
        });

        return false;
    });
            
    $('#qaNewTeamsList').on('click', '.new_tt', function(){
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
    });
    
    $('#qaPushAllBut').on('click', function(){
        if($(this).text() == 'Push ALL'){
            $('#qaReqAllBut').text('Request ALL');
            
            $(this).text('Push NONE');
                $('.new_tt').each(function(k, val){
                    $(this).data('tr_id', 2).removeClass('btn-danger').removeClass('btn-success').removeClass('btn-unselected').addClass('btn-default');
                });
        }
        else {
            $(this).text('Push ALL');
            $('#qaReqAllBut').text('Request ALL');

            $('.new_tt').each(function(k, val){
                $(this).data('tr_id', 0).removeClass('btn-danger').removeClass('btn-success').removeClass('btn-default').addClass('btn-unselected');
            });
        }
    });
        
    $('#qaReqAllBut').on('click', function(){
        if($(this).text() == 'Request ALL'){
            $(this).text('Request NONE');
            $('#qaPushAllBut').text('Push ALL');
            
                $('.new_tt').each(function(k, val){
                    $(this).data('tr_id', 3).removeClass('btn-default').removeClass('btn-success').removeClass('btn-unselected').addClass('btn-danger');
                });
        }
        else {
            $(this).text('Request ALL');

            $('.new_tt').each(function(k, val){
                $(this).data('tr_id', 0).removeClass('btn-danger').removeClass('btn-success').removeClass('btn-default').addClass('btn-unselected');
            });
        }
    });

    $('#qa_input-leadteam-select').on('change', function(){
        var leadt = $(this).val();
        var lead_label = $(this).parents('div.form-group').find('label');
    
        $.ajax( {
            url: '/tasks_teams/allowedAssistTeams/'+leadt,
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                lead_label.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
            },
            success:function(data, textStatus) {
                $('#qaNewTeamsList').html(data).fadeIn('fast');
            },
            error: function(xhr, statusText, err){
                if(xhr.status == 401){
                    var res_j = $.parseJSON(xhr.responseText);
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+res_j.message+'</div>';
                    $('#cErrorStatus').html(msg).stop().fadeIn('fast').delay(3000).fadeOut('fast');
                }
                else{
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                    $('#cErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
                }
            },                
            complete:function (XMLHttpRequest, textStatus) {
                lead_label.find('.tr_spin').remove();
            },
        });
    });
      
    $('#qaDueDate').on('change', function(){
        $('#qaModalDueChangeEnd').modal('show');
    });
      
    $('#qaDueAndEndButton').on('click',function(){
        t_due = $('#qaDueDate').val();
        t_due_endday = t_due+' 11:59:59';
          
        $('#qaEndTime').datetimepicker('update', t_due_endday);
        $('#qaModalDueChangeEnd').modal('hide');
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
        startDate:'".Configure::read('CompileStart')."',
        showMeridian: true,            
        keyboardNavigation:false,
        endDate:'".Configure::read('CompileEnd')."',
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
        startDate:'".Configure::read('CompileStart')."',
        forceParse:false,
        endDate:'".Configure::read('CompileEnd')."',
    });
           
    $('#qaDueDate').datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        keyboardNavigation:false,
        minView: 2,
        startDate:'".Configure::read('CompileStart')."',
        forceParse:true,
        endDate:'".Configure::read('CompileEnd')."',
    });
      
      
            
                
        

    ");
    
?>



    <?php 
    
        
        $new_task_stime = date('Y-m-d H:i:00');
        
        $this->request->data('Task.start_time', $new_task_stime);
        $this->request->data('Task.end_time', $new_task_stime);
        echo $this->Form->create('Task', array(
            'class'=>'formAddTask',
            'id'=>'qaForm',
            'action'=>'add',
            'novalidate' => true,
            'inputDefaults' => array(
                'label' => false), 
                'role' => 'form'));
    ?>

    <div id="qaPanelBody">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-4">
                        <?php echo $this->Form->label('Task.task_type_id', 'Type*'); ?>

                        <?php echo $this->Form->input('Task.task_type_id', array(
                            'options'=>$taskTypes,
                            'div'=>array(
                                'class'=>'input-group'),
                            'after'=>'<span class="input-group-addon"><i class="fa fa-tag"></i></span>',
                            'class' => 'form-control')); 
                        ?>  
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-4">
                        <div class="form-group">
                            <?php echo $this->Form->label('Task.start_time', 'Start Time*'); ?>

                            <?php echo $this->Form->input('Task.start_time', array(
                                'type'=>'text',
                                'id'=>'qaStartTime',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'class'=>'input-group',
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span>',
                                'class'=>'form-control datetimepicker-stime',
                            )); ?>            
                        </div>
                    </div>    
              
                    <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                            <?php echo $this->Form->label('Task.end_time', 'End Time*'); ?>

                            <?php echo $this->Form->input('Task.end_time', array(
                                'type'=>'text',
                                'id'=>'qaEndTime',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'class'=>'input-group',
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span>',
                                'class'=>'form-control datetimepicker-stime',
                                ));
                            ?>    
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
    
                            <?php echo $this->Form->input('Task.short_description', array(
                                'class' => 'form-control',
                                'label'=>'Short Description*',
                                )); 
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row" id="details">
                    <div class="col-md-12">
                        <div class="form-group">
        
                        <?php echo $this->Form->input('Task.details', array(
                            'id'=>'qaInputDetails',
                            'label'=>'Details',
                            'class' => 'input-details form-control')); 
                        ?>
                        </div>
                    </div>
                </div>
                <div class="row" id="details">
                    <div class="col-md-12">
                    </div>
                </div>
        

        
                
            </div><!--left col -->
            
            <div class="col-md-3">
                
                
                <div class="row sm-top-marg" id="teamStatus">
                    <div class="col-md-12">
                        <div class="panel panel-dark panel-qa">
                            <div class="panel-heading"><i class="fa fa-users"></i>&nbsp;&nbsp;Teams
                                <span class="pull-right clickable"><i class="fa fa-chevron-up"></i></span>
                            </div>
                            <div class="panel-body">
                                <div class="row sm-bot-marg">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                                <?php echo $this->Form->label('team_id', 'Lead*');?>
                                            <?php echo $this->Form->input('team_id', array(
                                                'empty'=>$team_input_empty,
                                                'readonly'=>$team_input_readonly,
                                            'options'=>$actionableTypes, 
                                            'options'=>$controlled_teams,
                                            'multiple'=>false, 
                                            'id'=>'qa_input-leadteam-select', 
                                            'div'=>array(
                                                'class'=>'input-group'),
                                            'after'=>'<span class="input-group-addon"><i class="fa fa-users"></i></span>',
                                            'class' => 'form-control')); ?>
                                        </div>
                                    </div><!-- .form-group -->
                                </div>
                                <div class="row sm-bot-marg">
                                    <div class="col-sm-12">
                                        <div id='qaNewTeamsList'>
                                            <?php 
                                                $new_teams = array();
                                    
                                                if($control_team_count == 1){
                                        
                                                    $uteam = $user_controls[0];
                                                    $new_teams = $this->requestAction(
                                                        array(
                                                            'controller' => 'tasks_teams', 
                                                            'action' => 'allowedAssistTeams'),
                                                        array('pass'=>array($uteam)));
                                                }
                                    
                                                echo $this->element('tasks_team/new_team_list',array(
                                                    'new_teams'=>$new_teams,
                                                ));
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
                    <div class="col-md-12">
                        <div class="panel panel-bdanger panel-qa">
                            <div class="panel-heading"><i class="fa fa-flag"></i>&nbsp;&nbsp;Dates &amp; Status
                                <span class="pull-right clickable"><i class="fa fa-chevron-down"></i></span>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-12">
                                                                            <div class="form-group">

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
                                    <div class="col-xs-6 col-sm-6 col-md-12">
                                                                            <div class="form-group">

                                        <?php echo $this->Form->label('actionable_type_id', 'Action Item');?>
                                        <?php echo $this->Form->input('actionable_type_id', array(
                                            'empty'=>true,
                                            'options'=>$actionableTypes, 
                                            'div'=>array(
                                                'class'=>'input-group'),
                                                'after'=>'<span class="input-group-addon"><i class="fa fa-flag"></i></span>',
                                                'class' => 'form-control')); ?>
                                    </div></div>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>               
            </div>
            
        </div>
        <div class="row">
                    <div class="col-sm-2">
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
                    <div class="col-sm-10">
                        <div class="qaValidationContent"></div>
                    </div>
                </div>
    </div><!--panel body-->
    
<div id="qaModalDueChangeEnd" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Change Task End Time?</h4>
      </div>
      <div class="modal-body">
        <p>You set a new due date. Would you like to change the task <b>end time</b> to the <b>new due date?</b></p>
        <p>Default: Tasks run from when they're created until they're due</p>
      </div>
      <div class="modal-footer">
        <button type="button" id="qaDueOnlyButton"class="btn btn-default" data-dismiss="modal">Change ONLY Due Date</button>
        <button type="button" id="qaDueAndEndButton" class="btn btn-primary">Change End Time AND Due Date</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
    <?php   echo $this->Form->end(); ?>
    <?php   echo $this->Js->writeBuffer();?>
