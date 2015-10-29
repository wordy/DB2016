<?php
    //echo $this->Html->css('bootstrap-wysihtml5', array('inline'=>false));
    //echo $this->Html->script('bootstrap3-wysihtml5', array('inline'=>false));

    if (AuthComponent::user('id')){
        //$controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }
        
    $show_details = $this->request->data('Task.show_details');
    
    
    $single_task = (isset($single_task))? $single_task : 0; 
    
    //$tlist = Hash::combine($teams,'{s}.{n}','{s}.{n}');
    
    $tlist=array();
    foreach ($teams as $zone){
        foreach($zone as $tid=>$code){
            $tlist[$tid] = $code;
        } 
    }
    
    //debug($cSettings);
    
    $cs_teams=array();
    
    if(!empty($cSettings['Teams'])){
        foreach ($cSettings['Teams'] as $tid){
            $cs_teams[] = $tlist[$tid];
        }
    }
    
    //debug($tlist3);
    
        $this->Js->buffer("
        /*
        $('span.btn-xxs').on('click', function(e){
            return false;
        });
        */
        $('span.btn-xxs').on('click', function(e){
            //tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
            //var spin = tdheading_div.find('span.tr_spin');
            //spin.delay(3000).fadeOut();
            
            e.stopPropagation();
            
            
                
            var tdheading_div = $(this).closest('div.task-panel-heading');
            var task_id = tdheading_div.data('tid');
            var team_id = $(this).data('teamid');
            var role = '';
                
            if($(this).hasClass('openTeam')){
                role_id = 4;
                $(this).removeClass('btn-danger openTeam').addClass('btn-success closeTeam');
            }
             
            else if($(this).hasClass('closeTeam')){
                role_id = 2;
                $(this).removeClass('btn-success closeTeam').addClass('btn-ttrid2 pushTeam');
            }    

            else if($(this).hasClass('pushTeam')){
                role_id = 3;
                $(this).removeClass('btn-ttrid2 pushTeam').addClass('btn-danger openTeam');
            }    
            
            if(task_id && team_id && role_id){                                            
                $.ajax( {
                    url: '/tasks_teams/chgRole/',
                    data: {'task':task_id, 'team':team_id, 'role':role_id},
                    beforeSend:function () {
                        console.log(team_id);
                        console.log(role_id);
                        tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                    },
                    //success:function(data, textStatus) {
                        //var msg_html = '<div class=\"alert alert-success\" role=\"alert\">'+data.message+'</div>';
                        //$('#cErrorStatus').html(msg_html).fadeIn().delay(3000).fadeOut('fast');
                    //},
                    complete:function (XMLHttpRequest, textStatus) {
                        tdheading_div.find('.tr_spin').remove();
                    },
                    error: function(xhr, statusText, err){
                        if(xhr.status == '401'){
                            var res_j = $.parseJSON(xhr.responseText);
                            //var res_j = xhr.responseText;
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>' +res_j.message+ ' Please try again.</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(7000).fadeOut();
                        }
                        else{
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(7000).fadeOut();
                        }
                    },
                    type: 'post',
                    dataType:'json',
                });
            }
        });
        
        
    ");

    
    
        
    $this->Js->buffer("
       $('body').on('submit','form.formEditTask', function(e){
            var subBut = $(this).find('.eaSubmitButton');
            var valCont = $(this).find('.eaValidationContent');
            var spinner = $(this).find('.eaSpinner');
            var pageNum = $('#pageNum').html();
            var thisform = $(this);
    //console.log(thisform);return false;        
        e.preventDefault();
                    
        $(this).find('.tt-btn').each(function(){
            var nteam = $(this).data('team_id');
            console.log(nteam);
            var ntr = $(this).data('tr_id');
            console.log(ntr);
            
            $('<input>').attr({
                type: 'hidden',
                name: 'data[TeamsRoles]['+nteam+']',
                value: ntr,
            }).appendTo(thisform);
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
                //$('#qaForm').trigger('reset');  
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
                subBut.val('Save Changes');
            },
        });
                        
            
            return false;
        });
        
        $('#taskListWrap').on('click', '.task-panel-heading', function(event){
            //console.log('heading cloic');
            var tdheading_div = $(this);
            var tid = $(this).attr('data-tid');
            var tbody_div = $(this).parent('.task-panel').find('.taskPanelBody');
            var tbd_c = 0;
            
            if(tbody_div.html()){
                console.log(tbody_div.html());
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
        
        $('#taskListWrap').on('click', '.astRow', function(event){
            var tid = $(this).attr('data-tid');
            var astRow = $(this);
            var astHeadingDiv = $(this).find('.astHeading');    
            var astBodyDiv = $(this).find('.astBody');
            var astBodyCont = 0;
            
            if(astBodyDiv.html()){
                astBodyCont = astBodyDiv.html().length;
            }
                
            // Details haven't been fetched
            if(astBodyCont < 100){
                $.ajax( {
                    url: '/tasks/details/'+tid,
                    beforeSend:function () {
                        astRow.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                    },
                    success:function(data, textStatus) {
                        astBodyDiv.html('<br>'+data).addClass('is_vis').slideDown(300);
                    },
                    complete:function (XMLHttpRequest, textStatus) {
                        astRow.find('.tr_spin').remove();
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
            else if (astBodyCont > 100 && !astBodyDiv.hasClass('is_vis')){
                astBodyDiv.addClass('is_vis').slideDown('slow');
            }  
            else {  //Details are visible, roll it up
                astBodyDiv.removeClass('is_vis').slideUp(300);
            }
            event.preventDefault();        
            return false;
        });
        
        
        
        
      $('#taskListWrap').on('click', '.eaTaskDeleteButton', function(){
            var tid = $(this).data('tid');
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
                            $('#taskListWrap').load('/tasks/compileUser', function(response, status, xhr){
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

        $('#taskListWrap').on('click', '.taskTs', function(event){
            event.stopPropagation();
        });

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
                /*
                complete:function (XMLHttpRequest, textStatus) {
                    tdheading_div.find('.tr_spin').remove();
                    console.log(textStatus);
                    console.log(XMLHttpRequest);
                },
                */ 
            });
        });
        
        $('div.flash-success').delay(3000).fadeOut();
        
        
        $('div.astHeading').on('click', '.task_view_button', function(e){
            e.stopPropagation();
        });
        
        
        
            
        var showing_single_task = ".$single_task.";
        if(showing_single_task==1){
                $('#taskListWrap').find('.task-panel-heading').trigger('click');
        }
        
        $('#ajax-content-load').on('click', '.panel-heading span.clickable', function(e){
            console.log('got p-h click from compile');
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
        
        
   function toggleCaChevron(e) {
       console.log($(e.target));
       console.log($(e));
       
    $(e.target)
        .prev('.panel-ctab')
        .find('i.cAindicator')
        .toggleClass('fa-chevron-down fa-chevron-up ');
    }
    $('#compActMenu').on('hidden.bs.collapse', toggleCaChevron);
    $('#compActMenu').on('shown.bs.collapse', toggleCaChevron);     
        
        
        
        
        
    ");
    
    if(isset($search_term)){
        $this->Js->buffer("
            $('body').highlight('".$search_term."');
            
        
        ");
        
    }


?>

    <div class="row">
        <h2>
            Compile Tasks
            <span id="ajaxProgress" style="display: none; margin-left: 10px; vertical-align: top;">
                <?php echo $this->Html->image('ajax-loader_old.gif'); ?>                    
            </span>
        </h2>
    </div>
<div class="row">
    
<div class="panel-group" id="compActMenu">
  <div class="panel panel-bsuccess">
    <div class="panel-heading panel-ctab accordion-toggle" data-toggle="collapse" data-parent="#compActMenu" href="#colAdd">
      <h4 class="panel-title">          
        <i class="fa fa-plus"></i>&nbsp;Add Task 
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
       <i class="fa fa-clock-o"></i>&nbsp;Upcoming Tasks
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
        <i class="fa fa-cog"></i>&nbsp;Compile Options
        <i class="cAindicator fa fa-chevron-down pull-right"></i>
      </h4>
    </div>
    <div id="colCompOpts" class="panel-collapse collapse">
      <div class="panel-body">
        <?php echo $this->element('task/compile_options'); ?>
      </div>
    </div>
  </div>
</div></div>
    <?php 
    /*
    <div class="row">
        <div class="panel with-nav-tabs panel-yh">
            <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#compileAdd" data-toggle="tab"><i class="fa fa-plus"></i>&nbsp;Add Task</a></li>
                        <li><a href="#urgentByUser" data-toggle="tab"><i class="fa fa-clock-o"></i>&nbsp;Upcoming Tasks</a></li>
                        <li><a href="#compileOptions" data-toggle="tab"><i class="fa fa-cog"></i>&nbsp;Compile Options</a></li>
                    </ul>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="compileAdd">
                        <?php echo $this->element('task/quick_add'); ?>
                    </div>
                    <div class="tab-pane fade" id="urgentByUser">
                        <?php echo $this->element('task/urgent_by_user_settings'); ?>
                    </div>
                    <div class="tab-pane fade" id="compileOptions">
                        <?php echo $this->element('task/compile_options'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    */?>


    <div class="row">
        <div class="col-md-8 col-md-offset-2" id="cErrorStatus">
            <?php 
                echo $this->Session->flash('compile');
            ?>
        </div>
    </div>
          <?php
        //<div class="row">
          //  <div class="col-md-12">
                
                
           
             
                //echo $this->element('task/urgent_by_user_settings');
             
                //$tc = $this->request->params['paging']['Task']['count'];
                
                //echo $tc;
             
                //debug($this->request->paging['Task']);
                //debug($this->request->query);
             
                //debug($this->params);
          
                //echo 'Showing '.$this->request->paging['Task']['current'].' of '.$this->request->paging['Task']['count'].' tasks for '; 
                //echo implode(', ', $cs_teams);
                //echo ' from ' .date('M d Y', strtotime($cSettings['start_date'])). ' to '.date('M d Y', strtotime($cSettings['end_date']));
                
            //</div>
        //</div>
 ?>
    <div id="page-content" class="row">
        <div id="taskListWrap">
            <?php 
                echo $this->element('task/compile_screen',array(
                    'tasks'=>$tasks,
                    'show_details'=>$show_details
                    ));
            ?>
        </div>
        
                <?php 
            echo $this->element('task/task_legend');
        ?>
            
        </div>
        
        
    </div>

   

<?php echo $this->Js->writeBuffer(); ?>