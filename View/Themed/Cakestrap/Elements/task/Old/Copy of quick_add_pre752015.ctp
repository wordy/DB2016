
<?php
    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
        $userTeamList = AuthComponent::user('TeamsList');
    }
    
    // Figure out # of controlled teams a user has. Show team selection as readonly
    // if they only control a single team
    
    $control_team_count = count($controlled_teams, COUNT_RECURSIVE) - count($controlled_teams);
    //$controlled_teams = Hash::extract($controlled_teams,'{s}');
    
    $readonly = false;
    $team_input_readonly = false;
    $singleTeamControl = false;
    $singleTeamControlled = null;
    
    if($control_team_count >=2){
        $team_input_empty = true;
    }
    elseif($control_team_count == 1){
        $team_input_readonly = 'readonly';
        $team_input_empty = false;
        $ar_k = array_keys($userTeamList);
    
        $singleTeamControlled = $ar_k[0];
        $singleTeamControl = true;
    
    }
    else{ $team_input_empty = false;}
    
    $zbuts = array();
        $i=1;
        
        foreach($teams as $k=>$zone){
            foreach($zone as $k=>$tcode){
                $zbuts[$i][]='<span class="btn btn-ttrid0 btn-sm new_tt" data-team_id = "'.$k.'" data-tr_id = "0">'.$tcode.'</span>';
            }
            $i++;        
        }
    
    $this->Js->buffer("
    
    $('#qaStartTime').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD HH:mm:ss', 
    });
    
    $('#qaEndTime').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD HH:mm:ss', 
    });
    
    $('#qaDueDate').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD', 
    });    
    
    $('#qaInputDetails').summernote({
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['picture','link']],
            ['misc', ['undo','redo','help']],
        ]
    });
    
    $('#qaForm').on('submit', function(e){
        var subBut = $(this).find('.qaSubmitButton');
        var valCont = $(this).find('.qaValidationContent');
        var spinner = $(this).find('.qaSpinner');
        e.preventDefault();
                
        $('#qaNewTeamsList').find('.tt-btn').each(function(){
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
            dataType:'html',
            beforeSend:function () {
                subBut.val('Saving...');
                valCont.fadeOut('fast');                
                spinner.fadeIn();
            },
            success:function(data, textStatus) {
                $('#qaForm').trigger('reset');
                $('#qaLeadTeamSelect').trigger('change');  
                spinner.fadeOut('fast');
                $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');

                // Refresh tasks list
                $('#taskListWrap').load('/tasks/compileUser', function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
                subBut.val('Add Task');
            },
        });

        //return false;
    });
            
    $('#qaNewTeamsList').on('click', '.tt-btn', function(){
        var trid = $(this).data('tr_id');
        if(trid == 0){
            if($(this).hasClass('btn-ttrid0')){
                $(this).removeClass('btn-ttrid0').addClass('btn-ttrid2').data('tr_id', 2);}
        } 
        else if(trid == 2){
            if($(this).hasClass('btn-ttrid2')){
                $(this).removeClass('btn-ttrid2').addClass('btn-danger').data('tr_id', 3);}
        }
        else if(trid == 3){
            if($(this).hasClass('btn-danger')){
                $(this).removeClass('btn-danger').addClass('btn-success').data('tr_id', 4);}
        }
        else if(trid == 4){
            if($(this).hasClass('btn-success')){
                $(this).removeClass('btn-success').addClass('btn-ttrid0').data('tr_id', 0);}
        }
    });
    
    $('#qaPushAllBut').on('click', function(){
        if($(this).text() == 'Push ALL'){
            $('#qaReqAllBut').text('Request ALL');
            $(this).text('Push NONE');
                $(this).parents('div.teams-panel').find('.tt-btn').each(function(k, val){
                    $(this).data('tr_id', 2).removeClass('btn-danger').removeClass('btn-success').removeClass('btn-ttrid0').addClass('btn-default');
                });
        }
        else {
            $(this).text('Push ALL');
            $('#qaReqAllBut').text('Request ALL');
            $(this).parents('div.teams-panel').find('.tt-btn').each(function(k, val){
                $(this).data('tr_id', 0).removeClass('btn-danger').removeClass('btn-success').removeClass('btn-default').addClass('btn-ttrid0');
            });
        }
    });
        
    $('#qaReqAllBut').on('click', function(){
        if($(this).text() == 'Request ALL'){
            $(this).text('Request NONE');
            $('#qaPushAllBut').text('Push ALL');
            $(this).parents('div.teams-panel').find('.tt-btn').each(function(k, val){
                    $(this).data('tr_id', 3).removeClass('btn-default').removeClass('btn-success').removeClass('btn-ttrid0').addClass('btn-danger');
                });
        }
        else {
            $(this).text('Request ALL');
            $(this).parents('div.teams-panel').find('.tt-btn').each(function(k, val){
                $(this).data('tr_id', 0).removeClass('btn-danger').removeClass('btn-success').removeClass('btn-default').addClass('btn-ttrid0');
            });
        }
    });

    $('#qaLeadTeamSelect').on('change', function(){
        var leadt = $(this).val();
        var lead_label = $(this).parents('div.form-group').find('label');
        var partask_label = $('#qaLinkedParentDiv').parents('div.form-group').find('label');
    
        $.ajax( {
            url: '/tasks_teams/allowedAssistTeams/'+leadt,
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                lead_label.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
            },
            success:function(data, textStatus) {
                $('#qaNewTeamsList').html(data).fadeIn('fast');
                if(leadt > 0){
                      $.ajax( {
                        url: '/tasks/linkableParentsByTeam/'+leadt,
                        type: 'post',
                        dataType:'html',
                        beforeSend:function () {
                            partask_label.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                        },
                        success: function(data, textStatus){
                            $('#qaLinkedParentDiv').html(data).fadeIn('fast');
                            $('#qaLinkedParent').trigger('change');
                        },
                        complete:function (XMLHttpRequest, textStatus) {
                            partask_label.find('.tr_spin').remove();
                        },
                      });
                }
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
        
        // Enables or Disables PushALL/RequestALL buttons if a lead is set.
        $('#qaReqAllBut, #qaPushAllBut').trigger('change');    
        
        
        
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
    






 /* TESTS */
 

    function resetQaLink(){
        var lead = $('#qaLeadTeamSelect');
        var par = $('#qaLinkedParent');
        var tc = $('#qaTimeCtrl');
        var to = $('#qaTimeOffset');
        
        if(!lead.val() || par.val()<1){
            console.log('not lead or par val less than 1');
        }
    }
    
    $('#qaPanelBody').on('change', '#qaLinkedParent' , function(){
        //if($(this).val())
        //alert('got chg');
        
        if(!$(this).val()){
            //alert($(this).val());
            $('#qaTimeCtrl').prop('disabled', 'disabled');
            
            
        }
        
        
    });
    
    var lpval= $('#qaLinkedParent').val();
    var tcval= $('#qaTimeCtrl').prop('checked');
    
    if (!lpval){
        $('#qaTimeCtrl').prop('disabled',true);
        $('#qaTimeOffset').prop('disabled',true);
    }
    else if((lpval>0) && tcval){
        $('#qaTimeOffset').prop('disabled', false);
    }
    
    $('#qaPanelBody').on('change', '#qaLinkedParent', function(){
        if(!$(this).val()){
            $('#qaTimeCtrl').prop('disabled',true);
            $('#qaTimeOffset').prop('disabled',true);
        }
        else{
            $('#qaTimeCtrl').prop('disabled', false);
            $('#qaTimeOffset').prop('disabled', false);
            
        }
        
    });
    
    $('#qaTimeCtrl').on('change',function(){
        if(($('#qaLinkedParent').val() > 0) && ($(this).prop('checked'))){
            $('#qaStartTime').attr('readonly', true);    
        }
        else{
            $('#qaStartTime').attr('readonly', false);
        }
        
    });
    
    

    
    $('#qaStartTime').on('dp.change', function (e) {
        //alert('e.date=' + e.date);
        //console.log(e);
        
        console.log($(this).val());
        $('#qaEndTime').data('DateTimePicker').minDate(moment($(this).val()));
        
    });
    
    
    
    
    
    
    
    /*
    $('#qaStartTime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 5,
        startDate:'".Configure::read('CompileStart')."',
        showMeridian: true,
        forceParse: true,            
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
    */
    // Disables PushALL and RequestAll buttons when no lead team is selected
    $('#qaReqAllBut, #qaPushAllBut').on('change', function(){
        lteam = $('#qaLeadTeamSelect').val();
        
        if(!lteam){
            $(this).attr('disabled', 'disabled');
        }
        else{
            $(this).removeAttr('disabled');
        }
    });
    
    $('#qaReqAllBut, #qaPushAllBut').trigger('change');
    $('#qaLeadTeamSelect').trigger('change');
        
        
    //$('#colAddHeading').trigger('click');
      

    $('#qaPanelBody').on('change', '#qaLinkedParent', function(e){
        var qa_pid = $('#qaLinkedParent').val(); 

        console.log(qa_pid);

        if(qa_pid>0){
            $('#qaAdvancedLinked').removeClass('collapse');    
        }
        else{
            $('#qaAdvancedLinked').addClass('collapse');
        }
    });
        
        $('#qaTimeCtrl').on('change', function(){
            var parent_start_time = '';
            //alert(parent_start_time);
            
            // Get LPT_id
            var qa_pid = $('#qaLinkedParent').val(); 
            
            // GET LPT_st
            
            $.get('/tasks/getStartTimeByTask/'+qa_pid, function( data ) {
                    
                if(data && $('#qaTimeCtrl').val() == 1){
                    $('#qaStartTime').data('DateTimePicker').date(moment(data));
                    $('#qaEndTime').data('DateTimePicker').date(moment(data));
                    
                    //$('#qaEndTime').data('DateTimePicker').date(moment(data));    
                }
                
            }); 
            
            // Set Task_st
            
            
                        
            
            
            
        });
        
        
      $('#qaTimeOffset').on('change', function(){
          
          var mySQLDate = '2014-12-31 11:59:51';

            date_str = new Date(mySQLDate);
            date_val = date_str.valueOf() + 10;
            
            new_date = Date.parse(date_val);
            
            
            var d1 = Date.createFromMysql();
            var d2 = Date.createFromMysql('2011-02-20 17:16:00');
            //alert('d1 year = ' + d1.getFullYear());
            

            //date_str = new Date(Date.parse(mySQLDate.replace('-','/','g')));
            
            //console.log(date_str.valueOf());
            
            //new_date = date_str+10;
          //console.log(new_date);
          
          
          
            var parent_start_time = '';
            //alert(parent_start_time);
            var shift_amt = $(this).val();
            var qa_pid = $('#qaLinkedParent').val();
            var is_par_ctrl = $('#qaTimeCtrl').val();
            
            if((is_par_ctrl == 1) && (qa_pid > 0) && shift_amt){
                $('#qaStartTime').prop('readonly', true);
                $('#qaStartTime').datetimepicker('hide');
                
                //$('#qaStartTime').attr('disabled', true);
                
                $.get('/tasks/getStartTimeByTask/'+qa_pid, function(data){
                    parent_start_time = data;
                    cur_par_start = Date.createFromMysql(data);
                   
                    if(shift_amt != 0){
                        new_task_start = cur_par_start.valueOf()+shift_amt*60*1000;
                        new_start_str = new Date(new_task_start);
                        new_start_date = new_start_str.getMySQL();
                        
                        $('#qaStartTime').data('DateTimePicker').date(new_start_date);
                    }
                });
            }
        });
      
      Date.createFromMysql = function(mysql_string){ 
        if(typeof mysql_string === 'string'){
            var t = mysql_string.split(/[- :]/);
        
            return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
        }
        
        return null;   
    }
    
        

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
    <style>
    
    <?php /*
        .form-group input[type="checkbox"] {
    display: none;
}

.form-group input[type="checkbox"] + .btn-group > label span {
    width: 20px;
}

.form-group input[type="checkbox"] + .btn-group > label span:first-child {
    display: none;
}
.form-group input[type="checkbox"] + .btn-group > label span:last-child {
    display: inline-block;   
}

.form-group input[type="checkbox"]:checked + .btn-group > label span:first-child {
    display: inline-block;
}
.form-group input[type="checkbox"]:checked + .btn-group > label span:last-child {
    display: none;   
}
        */?>
    </style>
    

    <div id="qaPanelBody">

        <div class="row">
            
            <div class="col-md-9 col-xs-12">
                        
                
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-4">
                        <div class="form-group">

                        <?php echo $this->Form->label('Task.task_type_id', 'Type*'); ?>

                        <?php echo $this->Form->input('Task.task_type_id', array(
                            'options'=>$taskTypes,
                            'div'=>array(
                                'class'=>'input-group'),
                            'after'=>'<span class="input-group-addon"><i class="fa fa-tag"></i></span>',
                            'class' => 'form-control')); 
                        ?>  
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-4">
                        <div class="form-group">
                            <?php echo $this->Form->label('Task.start_time', 'Start Time*'); ?>

                            <?php echo $this->Form->input('Task.start_time', array(
                                'type'=>'text',
                                'id'=>'qaStartTime',
                                //'readonly'=>'readonly',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'class'=>'input-group',
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span>',
                                'class'=>'form-control datetimepicker-stime',
                            )); ?>            
                        </div>
                    </div>    
              
                    <div class="col-xs-6 col-md-4">
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
                    <div class="col-xs-12">
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
                    <div class="col-xs-12">
                        <div class="form-group">
        
                        <?php echo $this->Form->input('Task.details', array(
                            'id'=>'qaInputDetails',
                            'label'=>'Details',
                            'class' => 'input-details form-control')); 
                        ?>
                        </div>
                    </div>
                </div>
                
                <div class="well well-sm">
                    <div class="row">
                        <div class="col-xs-12">
                <div class="row">
            <div class="col-xs-12 col-md-12">                    

                <div class="form-group">

                <?php 
                    echo $this->Form->label('parent_id', 'Link To Task');
                    echo '<span id="qaParentTaskSpinner" class="csSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                    echo $this->Html->image('ajax-loader_old.gif');
                    echo '</span>'; 
                ?>
                    
                <div id="qaLinkedParentDiv" class="linkableParents">
                    <?php
                    
                        if($singleTeamControl){
                            echo $this->element('task/linkable_parents_on_add', array('team'=>$singleTeamControlled));    
                        }
                        else{
                            echo '<div class="alert slim-alert alert-info" role="alert"> 
                            Select a lead team first</div>';
                        }
                        
                        //echo $this->element('task/linkable_parents', array('tid'=>$task['Task']['id']));
                        //echo $this->element('task/linkable_parents_on_add');
                    ?>    
                </div>
            </div>
        </div>
        </div>
        <div class="row" id="qaAdvancedLinked">
            <div class="col-xs-12 col-sm-8 col-md-6">
  
                  <p><b>Sync Start Times</b></p>  
                  <div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
          
                <?php 
                
                    echo $this->Form->input('Task.time_control', array(
                        'type'=>'checkbox',
                        //'name'=>'fancy-checkbox-default',
                        'id'=>'qaTimeCtrl',
                        //'class' => 'input-control',
                        'div'=>false,
                        'default'=>0,
                        //'label'=>'Linked task controls start time',
                        )); 
                ?>
                <label for="qaTimeCtrl">Linked task controls start time</label></div>

                <span id="helpBlock" class="help-block">
                    If selected, task moves automatically if the linked task moves.
                </span>
            
                        
                
                
            <!--
            <input type="checkbox" id="qaTCheckbox" class="tsCheck"/>
                    <div class="[ btn-group ]">
                        <label for="qaTimeCtrl" class="[ btn btn-sm btn-danger ]">
                            <span class="[ fa fa-check ]"></span>
                            <span> </span>
                        </label>
                        <label for="qaTimeCtrl" class="[ btn btn-sm btn-danger active ]">
                            Linked Controls Start Time
                        </label>
                    </div>-->
            </div>
        <div class="col-sm-12 col-md-6">
            <div class="form-group">


            <?php 
                echo $this->Form->label('Task.time_offset', 'Offset (Minutes)');
                echo $this->Form->input('Task.time_offset', array(
                    'type'=>'number',
                    'min'=>"-1440",
                    'placeholder'=>'0 Minutes',
                    'max'=>'720',
                    'default'=>0,
                    'id'=>'qaTimeOffset',
                    'class' => 'form-control',
                    //'label'=>'Offset (Minutes)',
                    'div'=>array(
                        'class'=>'input-group'),
                    'after'=>'<span class="input-group-addon">Minutes</span>',
                    ));
             ?>
             </div>
<span id="helpBlock" class="help-block">Eg. -10 if your task occurs 10 minutes <em>before</em> linked task.</span>
        </div>


    </div></div>
                    </div>
</div><!--well -->
            </div>
            <div class="col-md-3 col-xs-12">
                
                
                <div class="row sm-top-marg" id="teamStatus">
                    <div class="col-md-12">
                        <div class="panel panel-dark panel-qa">
                            <div class="panel-heading"><i class="fa fa-users"></i>&nbsp;&nbsp;Teams
                            </div>
                            <div class="panel-body teams-panel">
                                <div class="row sm-bot-marg">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                                <?php echo $this->Form->label('team_id', 'Lead*');?>
                                            <?php echo $this->Form->input('team_id', array(
                                                'empty'=>$team_input_empty,
                                                'readonly'=>$team_input_readonly,
                                            //'options'=>$actionableTypes, 
                                            'options'=>$controlled_teams,
                                            'multiple'=>false, 
                                            'id'=>'qaLeadTeamSelect', 
                                            'div'=>array(
                                                'class'=>'input-group'),
                                            'after'=>'<span class="input-group-addon"><i class="fa fa-users"></i></span>',
                                            'class' => 'form-control')); ?>
                                        </div>
                                    </div><!-- .form-group -->
                                </div>
                                <div class="row sm-bot-marg">
                                    <div class="col-sm-12">
                                        <div id='qaNewTeamsList' class="tt_list_div">
                                            <?php 
                                            
                                            if(count($user_controls) > 0){
     
        echo '<div class="alert slim-alert alert-info" role="alert"> 
        Select a lead team first</div>';
    
                                            }
                                            else{
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
                            <div class="panel-heading"><i class="fa fa-flag"></i>&nbsp;&nbsp;Due Date &amp; Status
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
