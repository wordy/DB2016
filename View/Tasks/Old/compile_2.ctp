<?php
    if (AuthComponent::user('id')){
        //$controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }
        
    $show_details = $this->request->data('Task.show_details');
    $single_task = (isset($single_task))? $single_task : 0; 
    
    /*
    $tlist=array();
    foreach ($teams as $zone){
        foreach($zone as $tid=>$code){
            $tlist[$tid] = $code;
        } 
    }
    */
    
    //$teamIdCodeList = Hash::combine($zoneTeamList, '{n}.Team.id', '{n}.Team.code');
        
       
    $cs_teams = array();
    if(!empty($cSettings['Teams'])){
        foreach ($cSettings['Teams'] as $tid){
            $cs_teams[] = $teamIdCodeList[$tid];
        }
    }
    
    $this->Js->buffer("
    // Back To Top
    var offset = 420;
    var duration = 700;
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > offset) {
            $('#back-to-top').fadeIn(duration);
        } else {
            $('#back-to-top').fadeOut(duration);
        }
    });
                
    $('#back-to-top').on('click', function(){
        $('html, body').animate({scrollTop : 0}, duration);
        return false;
    });
    
    $('#taskListWrap').on('click', '.ban-edit', function(e){
        e.preventDefault();
        e.stopPropagation();
    });
    
    $('.helpTTs').popover({
        container: 'body',
        html:true,
        
    });
    
    // Accordion toggle for menu   
    function toggleCaChevron(e) {
        $(e.target)
            .prev('.panel-ctab')
            .find('i.cAindicator')
            .toggleClass('fa-chevron-down fa-chevron-up ');
    }
    $('#compActMenu').on('hidden.bs.collapse', toggleCaChevron);
    $('#compActMenu').on('shown.bs.collapse', toggleCaChevron);     
    

    $('.DTPdaytime').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD HH:mm:ss', 
    });
    
    $('.DTPday').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD', 
    });
    
    // TTButtons
    $('body').on('click','div.teamsList .tt-btn', function(){
        var trid = $(this).data('tr_id');
        if(trid == 0){
            if($(this).hasClass('btn-ttrid0')){
                $(this).removeClass('btn-ttrid0').addClass('btn-ttrid2').data('tr_id', 2);
            }
        }
        else if(trid == 2){
            if($(this).hasClass('btn-ttrid2')){
                $(this).removeClass('btn-ttrid2').addClass('btn-danger').data('tr_id', 3);    
            }
        }
        else if(trid == 3){
            if($(this).hasClass('btn-danger')){
                $(this).removeClass('btn-danger').addClass('btn-success').data('tr_id', 4);    
            }
        }
        else if(trid == 4){
            if($(this).hasClass('btn-success')){
                $(this).removeClass('btn-success').addClass('btn-ttrid0').data('tr_id', 0);    
            }
        }
    });
        
    // TT Button role changes from compile 
    $('span.btn-xxs:not(.ban-edit)').on('click', function(e){
        e.stopPropagation();
        var this_but = $(this);
        var tdheading_div = $(this).closest('div.task-panel-heading');
        var task_id = tdheading_div.data('tid');
        var team_id = this_but.data('teamid');
        var role_id = '';
            
        if(this_but.hasClass('openTeam')){
            role_id = 4;
            this_but.removeClass('btn-danger openTeam').addClass('btn-success closeTeam');
        }
        else if(this_but.hasClass('closeTeam')){
            role_id = 2;
            this_but.removeClass('btn-success closeTeam').addClass('btn-ttrid2 pushTeam');
        }    
        else if(this_but.hasClass('pushTeam')){
            role_id = 3;
            this_but.removeClass('btn-ttrid2 pushTeam').addClass('btn-danger openTeam');
        }    

        if((task_id!=null) && (team_id!=null) && (role_id!=null) ){                                            
            $.ajax( {
                url: '/tasks_teams/chgRole/',
                data: {'task':task_id, 'team':team_id, 'role':role_id},                
                type: 'post',
                dataType:'json',
                beforeSend:function () {
                    tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                },
                complete:function (XMLHttpRequest, textStatus) {
                    tdheading_div.find('.tr_spin').remove();
                },
                error: function(xhr, statusText, err){
                    var res_j = $.parseJSON(xhr.responseText);
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+res_j.message+'</div>';
                    $('#cErrorStatus').html(msg).fadeIn('fast').delay(7000).fadeOut();
                },
            });
        }
    });
        
    // Fetch task details
    $('#taskListWrap').on('click', '.task-panel-heading', function(event){
        var tdheading_div = $(this);
        var tid = $(this).attr('data-tid');
        var tbody_div = $(this).parent('.task-panel').find('.taskPanelBody');
        var tbd_c = 0;
        
        if(tbody_div.html()){
            tbd_c = tbody_div.html().length;
        }

        // Details haven't been fetched
        if(tbd_c < 100){
            $.ajax( {
            url: '/tasks/details/'+tid,
                beforeSend:function () {
                    tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                },
                success:function(data, textStatus) {
                    tbody_div.html(data).addClass('is_vis').slideDown(300);
                    var new_lpts = tbody_div.find('.linkableParentSelect');
                    bindToSelect2(new_lpts);
                   
                },
                complete:function (XMLHttpRequest, textStatus) {
                    tdheading_div.find('.tr_spin').remove();
                },
                error: function(xhr, statusText, err){
                    if(xhr.status == '401'){
                        var res_j = $.parseJSON(xhr.responseText);
                        var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>Weird, you aren\'t allowed to view the task details ('+res_j.message+') Please refresh the page.</div>';
                        $('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                    }
                    else{
                        var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                        $('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                    }
                },
                type: 'post',
                dataType:'html',
            });
        }  // Details were previously fetched, just show it again
        else if (tbd_c > 100 && !tbody_div.hasClass('is_vis')){
            tbody_div.addClass('is_vis').slideDown('slow');
        }  
        else {  //Details are visible, roll it up
            tbody_div.removeClass('is_vis').slideUp(300);
        }
        event.preventDefault();        
        return false;
    });

    // Edit Task
    $('body').on('submit','form.formEditTask', function(e){
        var subBut = $(this).find('.eaSubmitButton');
        var valCont = $(this).find('.eaValidationContent');
        var spinner = $(this).find('.eaSpinner');
        var pageNum = $('#pageNum').html();
        var thisform = $(this);
        e.preventDefault();
        
        // Grab state from teams' buttons; save as hidden inputs            
        $(this).find('.tt-btn').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            
            $('<input>').attr({
                type: 'hidden',
                name: 'data[TeamRoles]['+nteam+']',
                value: ntr,
            }).appendTo(thisform);
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
                spinner.fadeOut('fast');
                $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                // Refresh tasks list
                 
                $('#taskListWrap').load('/tasks/compile', function(response, status, xhr){
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
                subBut.val('Save Changes');
            },
        });
        return false;
    });
    
    function updateTcTo(update_div){
        
    }
    
    function updateLinkableParents(team, update_div, current, child){
        var p_label = update_div.parents('.form-group').find('label');

        $.ajax( {
            url: '/tasks/linkable/',
            data: {team:team, current:current, child:child},
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                p_label.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
            },
            success: function(data, textStatus){
                update_div.html(data).fadeIn('fast');
                var new_lps = update_div.find('.linkableParentSelect');
                bindToSelect2(new_lps);
                new_lps.trigger('change');
            },
            complete:function (XMLHttpRequest, textStatus) {
                p_label.find('.tr_spin').remove();
            },
        });
    }
    
    //Change lead
    $('#ajax-content-load').on('change','.inputLeadTeam', function(){
        var leadt = $(this).val();
        var thisT = $(this); 
        var lead_label = $(this).parents('div.form-group').find('label');
        var partask_label = $(this).parents('form').find('.linkedParentDiv').parents('div.form-group').find('label');
        var partask_list = $(this).parents('form').find('.linkedParentDiv');
        var ea_tlist = $(this).parents('form').find('.teamsList');
        var curPID =  $(this).parents('form').find('.hiddenParId').text();
        var curTID =  $(this).parents('form').find('.hiddenTaskId').text();    

        $.ajax( {
            url: '/tasks_teams/updateSig/',
            data: {team:leadt},
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                lead_label.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
            },
            success:function(data, textStatus) {
                ea_tlist.html(data).fadeIn('fast');
                
                if(leadt){
                    updateLinkableParents(leadt, partask_list, curPID, curTID);
                    console.log('updating due to EALTS change'); 
                }
            },
            error: function(xhr, statusText, err){
                if(xhr.status == 401){
                    var res_j = $.parseJSON(xhr.responseText);
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+res_j.message+'</div>';
                    $('#eaErrorStatus').html(msg).stop().fadeIn('fast').delay(3000).fadeOut('fast');
                }
                else{
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                    $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
                }
            },                
            complete:function (XMLHttpRequest, textStatus) {
                lead_label.find('.tr_spin').remove();
            },
        });
    });
    

    //Change Linked Parent
    $('#ajax-content-load').on('change','.linkableParentSelect', function(e){
        console.log('chg pid from compile');
        var this_sel = $(this);
        var sel_par = $(this).val();
        var tc = $(this).parents('form').find('.inputTC');
        var start = $(this).parents('form').find('.inputStartTime');
        var stHelp = $(this).parents('form').find('.stHelpWhenTC');
        var to = $(this).parents('form').find('.inputTO');
        var advpid = $(this).parents('form').find('.advancedParent');
        var curTID =  $(this).parents('form').find('.hiddenTaskId').text();
        var partask_label = $(this).parents('.form-group').find('label');
        
        resetTaskForm(e);
        if(!sel_par){
            advpid.addClass('collapse');
            start.prop('readonly',false);
            stHelp.addClass('collapse');
            
            
        }
        else{
            advpid.removeClass('collapse');
            start.prop('readonly', false);
            stHelp.addClass('collapse');
            var no_tc_html = '<div class=\"alert alert-danger slim-alert par_disallow\"><i class=\"fa fa-exclamation-triangle\"></i><b>Time Link Not Allowed: </b>You may link to this task, but <b>time linking</b> to the selected task is not allowed. <a class=\"helpTTs\" tabindex=\"0\" role=\"button\" data-toggle=\"popover\" data-trigger=\"focus\" title=\"Why Can\'t I Time Link to This?\" data-content=\"<p>The task you\'re trying to link to already links back to your task -- possibly through intermediate tasks.</p><p>This would potentially create an infinite loop. We prevent the loop by preventing you from time linking to this task. <p><p>You can still link to it, but it <b>cannot</b> control your task start time.</p>\"><i class=\"fa fa-question-circle text-info\"></i></a></div>';
            tc.prop('checked', false);
            to.val(0).prop('disabled', true);
            
            //    console.log(advpid);
            //advpid.find('.par_disallow').remove();

            
            if(sel_par && curTID){
                $.ajax( {
                    url: '/tasks/checkPid/',
                    data: {task:curTID, parent:sel_par},
                    type: 'post',
                    dataType:'json',
                    beforeSend:function () {
                        partask_label.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                    },            
                    success:function(data, textStatus) {
                        if(data.allow_parent == false){
                            tc.prop('checked', false);
                            tc.prop('disabled', true);
                            to.prop('disabled', true);
                            advpid.append(no_tc_html);
                            
                            $('.helpTTs').popover({
                                container: 'body',
                                html:true,
                            });
                        }
                        else{
                            advpid.find('.par_disallow').remove();
                            tc.prop('disabled', false);
                            to.prop('disabled', true);
                            tc.prop('checked', false);
                            to.val(0);
                        }
                    },
                    error: function(xhr, statusText, err){
                        /*    
                        if(xhr.status == 401){
                        }
                        else{
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                            $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
                        }
                        */
                    },                
                    complete:function (XMLHttpRequest, textStatus) {
                        partask_label.find('.tr_spin').remove();
                    },
                });
            }
    }
        
        
    });


    //Time Ctrl
    $('#ajax-content-load').on('change', '.inputTC', function(){
        var startTime = $(this).parents('form').find('.inputStartTime');
        var endTime = $(this).parents('form').find('.inputEndTime');
        var timeOffset = $(this).parents('form').find('.inputTO');
        var toVal = 60*timeOffset.val();
        
        var ost = moment(startTime.val());
        var parentTask = $(this).parents('form').find('.linkableParentSelect');
        var pt_sel = parentTask.find('option:selected');
        
        if(pt_sel.data('stime')){
            var pt_start_val = pt_sel.data('stime');    
        }
        else{
            var pt_start_val = null;
        }

        var ost = moment(startTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var oet = moment(endTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var odur = oet.diff(ost)
        
        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        var stHelp = $(this).parents('form').find('.stHelpWhenTC');
        
        if($(this).prop('checked') && pt_start_val){
            startTime.prop('readonly', true);
            startTime.data('DateTimePicker').date(moment(nst));
            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
            timeOffset.prop('readonly',false);
            timeOffset.prop('disabled',false);
            stHelp.removeClass('collapse');
        }
        else{
            timeOffset.val(0);
            timeOffset.prop('disabled',true);
            startTime.prop('readonly', false);
            stHelp.addClass('collapse');
        }
    });

    // Offset
    $('#ajax-content-load').on('change', '.inputTO', function(){
        var timeCtrl = $(this).parents('form').find('.inputTC');
        var timeOffset = $(this);
        var startTime = $(this).parents('form').find('.inputStartTime');
        var endTime = $(this).parents('form').find('.inputEndTime');
        var toVal = 60*timeOffset.val();
        var parentTask = $(this).parents('form').find('.linkableParentSelect');
        var pt_sel = parentTask.find('option:selected');
        var pt_start_val = pt_sel.data('stime');
        var ost = moment(startTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var oet = moment(endTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var odur = oet.diff(ost);
        var opst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss');
        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        var isTc = timeCtrl.prop('checked');
        
        if(isTc){
            startTime.data('DateTimePicker').date(moment(nst));
            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
        }
    });
      
    //Delete Task  
    $('#taskListWrap').on('click', '.eaTaskDeleteButton', function(){
        var tid = $(this).data('tid');
        //$('#myModal1').modal('show');
        
        var result = confirm('Are you sure you want to delete this?');
            
        if(result){
            $.ajax( {
                url: '/tasks/delete/'+tid,
                beforeSend:function () {
                    $('#ajaxProgress').fadeIn('fast');
                },
                success:function(data, textStatus) {
                    var msg_html = '<div class=\"alert alert-success\" role=\"alert\">'+data.message+'</div>';
                    // Refresh tasks list
                    $('#taskListWrap').load('/tasks/compile', function(response, status, xhr){
                        if(status == 'success'){
                            $('#taskListWrap').html(response);
                        }
                    });
                    $('#cErrorStatus').html(msg_html).fadeIn().delay(3000).fadeOut('fast');
                },
                complete:function (XMLHttpRequest, textStatus) {
                    $('#ajaxProgress').fadeOut('fast');
                }, 
                type: 'post',
                dataType:'json',
            });
        }
    });

    // Time Shifting
    $('#taskListWrap').on('click', 'input.tsCheck', function(event){
        var this_check = $(this);
        var tid = $(this).data('tid');
        var act = 'addShift';
        
        if($(this).hasClass('checked')){
            act = 'remShift';
        }

        $.ajax( {
            url: '/tasks/'+act,
            type: 'post',
            dataType:'json',
            data: {task: +tid},
            success:function(data, textStatus) {
                if(data.success){
                    var msg = '<div class=\"alert alert-success\" role=\"alert\"><b>Success: </b>'+data.message+'</div>';
                    $('#cErrorStatus').html(msg).stop().fadeIn('fast').delay(3000).fadeOut('fast');
                    $('#userTimeshiftCount').html(data.ts_count);
                    this_check.toggleClass('checked');    
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
            }
        });
    });
    
    $('div.flash-success').delay(3000).fadeOut();
    
    /*    
    $('div.astHeading').on('click', '.task_view_button', function(e){
        e.stopPropagation();
    });
    */
    /*
    $('#taskListWrap').on('click', '.taskTs', function(event){
        event.stopPropagation();
    });
    */
    // Show details right away if viewing single task
    var showing_single_task = ".$single_task.";
    if(showing_single_task==1){
        $('#taskListWrap').find('.task-panel-heading').trigger('click').delay(2000)
//        .find('.eaLeadTeamSelect')
//        .trigger('change');
    }
    
    
    
    
    
    
    
    
    
    
        
    ");
    

    $this->Js->buffer("
    
    $('.actTypeDD').dropdown();
    
    $('.actTypeDD').on('click', 'li a', function(){
        $(this).parents('.btn-group').find('.btn').text($(this).text());
        $(this).parents('.btn-group').find('.btn').val($(this).text()); 
    });

    /*
    $('.actTypeDD').on('click', function(e){
        console.log('got li click');
        //$(this).dropdown();
        
        e.stopPropagation();
  
    });
    */
    
    // Prevents buttons & links from triggering the details slidedown
    $('.task-panel-heading').find('button, a').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();
    });
    

        ");
    
    if(!$single_task){
        
    }
    
    
    // If results are from a user search, highlight the term
    if(isset($search_term)){
        $this->Js->buffer("
            $('body').highlight('".$search_term."');
        ");
    }


?>

    <div class="row">
        <h2>Compile Tasks</h2>
    </div>
    <div class="row">
    <!-- Accordion Credit: http://jsfiddle.net/d2p17qj7/ -->    
        <div class="panel-group" id="compActMenu">
            <div class="panel panel-bsuccess">
                <div id="colAddHeading" class="panel-heading panel-ctab accordion-toggle" data-toggle="collapse" data-parent="#compActMenu" href="#colAdd">
                    <h4 class="panel-title">          
                        <i class="fa fa-plus"></i>  &nbsp;&nbsp;Add Task 
                        <i class="cAindicator fa fa-chevron-down pull-right"></i>
                    </h4>
                </div>
                <div id="colAdd" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php echo $this->element('task/quick_add'); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-bdanger">
                <div class="panel-heading panel-ctab accordion-toggle" data-toggle="collapse" data-parent="#compActMenu" href="#colUrgent">
                    <h4 class="panel-title">
                        <i class="fa fa-clock-o"></i>  &nbsp;&nbsp;Upcoming Tasks
                        <i class="cAindicator fa fa-chevron-down pull-right"></i>
                    </h4>
                </div>
                <div id="colUrgent" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php echo $this->element('task/urgent_by_user_settings'); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-bsteel">
                <div class="panel-heading panel-ctab accordion-toggle" data-toggle="collapse" data-parent="#compActMenu" href="#colCompOpts">
                    <h4 class="panel-title">
                        <i class="fa fa-cog"></i>  &nbsp;&nbsp;Compile Options
                        <i class="cAindicator fa fa-chevron-down pull-right"></i>
                    </h4>
                </div>
                <div id="colCompOpts" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php echo $this->element('task/compile_options'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="page-content" class="row">
        <div id="taskListWrap">
            <?php 
                echo $this->element('task/compile_screen_2',
                    array(
                        'tasks'=>$tasks,
                        'show_details'=>$show_details
                    )
                );
            ?>
        </div>

        <?php 
            echo $this->element('task/task_legend');

        ?>
    </div>

   

<?php 
    echo $this->Js->writeBuffer(); 
?>