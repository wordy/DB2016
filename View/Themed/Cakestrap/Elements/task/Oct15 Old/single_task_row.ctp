<?php
    if (AuthComponent::user('id')){
        $userTeams = AuthComponent::user('Teams');
    }
    $user_controls = $this->Session->read('Auth.User.Teams');
    $show_details = $this->Session->read('Auth.User.Compile.show_details');

    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  

    
    
    $this->Js->buffer("
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
      
        $('.task-panel-heading').find('button').on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
        });
        
    $('.task-panel-heading').on('mouseenter', function(e){
        var ucon_inv = $(this).data('uconinv');
        var addHtml = $('<span class=\"actButs\"><button style=\"margin:0px 5px 2px 5px;\" class=\"btn btn-yh btn-sm addTask\"><i class=\"fa fa-plus-circle\"> </i> &nbsp;Add</button></span>');
        var linkHtml = $('<span class=\"actButs\"><button style=\"margin:0px 0px 2px 2px;\" class=\"btn btn-yh btn-sm addLink\"><i class=\"fa fa-link\"> </i> &nbsp;Link</button></span>');
        //var tsDiv = $(this).find('.taskTs').parent();
        var tsDiv = $(this).find('.taskTs').parent();
        addHtml.hide().appendTo(tsDiv).fadeIn('fast');
        if(ucon_inv){
            linkHtml.hide().appendTo(tsDiv).fadeIn('fast'); 
        }
        
    });        
        
    $('.task-panel-heading').on('mouseleave', function(){
       $(this).parents('.task-panel').find('.actButs').remove();
        
    });
    
    $('.task-panel-heading').on('click', 'button.addTask', function(e){
        //e.stopPropagation();
        var stime = $(this).parents('.task-panel-heading').data('stime');
        var s_mom = moment(stime);
        addNewTask(stime);
    });
    
    $('.task-panel').on('click', 'button.addLink, button.addTask', function(e){
        e.stopPropagation();
        e.preventDefault();
    });
      

    $('.task-panel-heading').on('click', 'button.addLink', function(e){
        //e.stopPropagation();
        var tid = $(this).parents('.task-panel').find('.task-panel-heading').data('tid');
        var tm_id = $(this).parents('.task-panel').find('.task-panel-heading').data('team_id');
        var jsonCIN = $(this).parents('.task-panel').find('.jsonCIN').html();
        var jqCIN = $.parseJSON(jsonCIN);
        var opts = '';

        $('#teamAddLinkedModal').find('span.hiddenParentId').html(tm_id);
        $('#teamAddLinkedModal').find('span.hiddenTid').html(tid);
        
        // TODO: This apparently fails in IE<9
        var numCIN = Object.keys(jqCIN).length;

        if(numCIN == 1){
            var from_team = Object.keys(jqCIN)[0];
            addNewLinkedTask(tid, from_team, tm_id); 
        }      
        else{
            $.each(jqCIN, function(i,e){
                opts += '<option value=\"'+i+'\">'+e+'</option>';
            });
        
            $('#selectLinkedTeam').html(opts);
            $('#teamAddLinkedModal').modal('show');
        }
    });
    ");

    if (!empty($tasks)){ 
    ?>
        <div class="tasks index">
<?php  

    // Hold days of tasks
    $cur_t_day = '';
    $prev_t_day = '';
    $last_t_day = '';
    $last_t_hr = '';
    $curr_t_day = '';            
    $curr_t_hr = '';
    $last_c_day = '';
    
    // START of FOREACH $tasks
    foreach ($tasks as $k => $task):
        $uControlsInvolved = false;
        
      
        
        $teamsInvolved = Hash::extract($task['TasksTeam'],'{n}.team_id');
        $controlledInInvolved = array_intersect($teamsInvolved, $userTeams);
          //print_r($controlledInInvolved);
        //print_r($userTeams);
        $jsCIN = array();
        foreach($controlledInInvolved as $k => $team_id){
            $jsCIN[$team_id] = $teamIdCodeList[$team_id];
        }
        $jsonCIN = json_encode($jsCIN);
        
        
        if(!empty($controlledInInvolved)){
            $uControlsInvolved = true;
        }

        $curr_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        $curr_t_hr = date('H', strtotime($task['Task']['start_time']));
        $curr_c_day = date('Y-m-d', strtotime($task['Task']['created']));
        $isPastDue = false;
        $isTimeControlled = false;

        if($last_t_day == $curr_t_day){
            $daysAreSame = true;
        }
        if($last_c_day == $curr_c_day){
            $cDaysAreSame = true;
        }
        if($curr_t_day == $eday){
            $onEday = true;
        }
        if($curr_t_hr == $last_t_hr){
            $hoursAreSame = true;
        }
        
        $tid = $task['Task']['id']; 

        //Hide/show elements based on permissions.
        $userControls = false;
        if(in_array($task['Task']['team_id'], $user_controls)){ $userControls = true; }
        
        $inUsrShift = false;
        if(in_array($task['Task']['id'], $user_shift)){ $inUsrShift = true; }
        
        $taskTO = 0; 
        $hasComment = $commentCount = $hasDueDate = false; $hasDueSoon = false; $hasActionable = false; $hasChange = false; $hasNewChange = false;
            
        if(!empty($task['Task']['due_date'])){
            $dueString = strtotime($task['Task']['due_date']);
            $hasDueDate = true;
        
            // Highlights due 1 week from now
            if(($dueString >= $today_str) && ($dueString < $owfn)){
                 $hasDueSoon = true; 
            }
            // Highlights past due tasks
            if($dueString < $today_str) {
                $hasDueSoon = true;
                $isPastDue = true; 
            }
        }
        if(!empty($task['Task']['actionable_type'])){
             $hasActionable = true; 
        }
        if(!empty($task['Task']['time_control']) && (isset($task['Task']['time_offset']))){
            $isTimeControlled = true; 
            $taskTO = $task['Task']['time_offset'];
        }
        if(!empty($task['Comment'])){
            $hasComment = true;
            $commentCount = count($task['Comment']); 
        }
        if (!empty($task['Change'])){
            $hasChange = true;
            $numChange = 0;
            
            // Count for recent changes    
            foreach ($task['Change'] as $chg){
                if (strtotime($chg['created'])  > $owa){
                    $hasNewChange = true;
                    $numChange++;
                }
            }  
        }
    ?>
    
    <div class="row">
        <div class="col-md-12">
            <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" class="panel panel-default task-panel" style="border-left: 7px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
                <div class="panel-heading task-panel-heading" 
                    data-team_id = "<?php echo $task['Task']['team_id'];?>"
                    data-stime="<?php echo $task['Task']['start_time'];?>" 
                    data-uconinv="<?php echo ($uControlsInvolved)?'true':'false';?>" 
                    data-tid="<?php echo ($task['Task']['id']); ?>">
                    <span class="collapse jsonCIN" style="visibility: hidden;"><?php echo $jsonCIN; ?></span>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2">
                            <div class="taskTs checkbox facheckbox xs-bot-marg facheckbox-circle facheckbox-success">
                                <input type="checkbox"
                                    class="tsCheck <?php if($inUsrShift){echo 'checked';} ?>" 
                                    id="hide<?php echo $tid;?>"
                                    <?php if(!$userControls){echo 'disabled="disabled"';} ?>
                                    <?php if($inUsrShift){echo 'checked="checked"';} ?> 
                                    data-stime="<?php echo strtotime($task['Task']['start_time']); ?>" 
                                    data-etime="<?php echo strtotime($task['Task']['end_time']); ?>" 
                                    data-tid="<?php echo $task['Task']['id']?>" 
                                />
                                
                                <label class="taskTimeshift" for="hide<?php echo $tid;?>">
                                    <?php
                                        
                                            $t1 = date('Y-m-d H:i:s', strtotime($task['Task']['start_time']));
                                            $t2 = date('Y-m-d H:i:s', strtotime($task['Task']['end_time']));
                                            $d1 = date('Y-m-d', strtotime($task['Task']['start_time']));
                                            $d2 = date('Y-m-d', strtotime($task['Task']['end_time']));
                                            $diff = strtotime($t2)-strtotime($t1);
                                            $dh = floor($diff / 3600);
                                            $dm = floor(($diff / 60) % 60);
                                            $ds = $diff % 60;
                                        
                                            if($diff == 0){
                                                echo date('g:i A', strtotime($t1));
                                            }
                                            // Important seconds
                                            elseif($diff < 60){
                                                echo $this->Time->format('g:i:s', $task['Task']['start_time']).' - '.$this->Time->format('g:i:s A', $task['Task']['end_time']).'<br/>('.$diff.'s)';
                                            }
                                            // < hr
                                            elseif(($diff >= 60) && ($diff < 3600)){
                                                echo $this->Time->format('g:i', $task['Task']['start_time']).' - '.$this->Time->format('g:i A', $task['Task']['end_time']).'<br/>('.$dm.' min)';
                                            }
                                            // > 1h < 24h
                                            elseif($diff >= 3600 && $diff < 86400){
                                                echo $this->Time->format('g:i A', $task['Task']['start_time']).' - '.$this->Time->format('g:i A', $task['Task']['end_time']).'<br/>('.$dh.' hr, '.$dm.' min)'; 
                                            }
                                            // >1 day or spans days
                                            elseif ($diff >= 86400){
                                                echo date('g:i A', strtotime($t1));
                                            }
                                            if($d1 != $d2){
                                                echo '<br/>(Multi-day)';
                                            }
                                        
                                        
                                    ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-2">
                            <?php
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                                echo $this->Ops->makeTeamsSig($task['TasksTeam'], $zoneTeamCodeList, $userControls);
                            ?> 
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-7">
                            <div class="csTaskDetails">
                                <?php
                                    echo $task['Task']['short_description'].'<br/>';
                                    if ($show_details && !empty($task['Task']['details'])){
                                        echo '<hr align="left" style="width: 100%; margin-bottom:3px; margin-top:3px; border-top: 1px solid #aaa;"/>';
                                        echo nl2br($task['Task']['details']);
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-1">
                            <div class="pull-right task-buttons" style="text-align: right; margin-left: 8px;">
                            <?php 
                                if($hasDueDate){
                                    if($hasDueSoon){
                                        echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                    }
                                    else {
                                        echo '<button type="button" class="btn btn-warning btn-xs xxs-bot-marg">';
                                    }
                                    echo '<i class="fa fa-bell-o"></i>&nbsp;';
                                    echo $this->Time->format('M d', $task['Task']['due_date']);
                                    echo '</button><br/>';
                                }
    
                                if($hasActionable){
                                    echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-flag fa-lg"></i>&nbsp;';
                                    echo $task['Task']['actionable_type'];
                                    echo '</button><br/>';
                                }
                                if($hasComment){
                                    echo '<button type="button" class="btn btn-primary btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-comment-o"></i>&nbsp;';
                                    echo $commentCount.' New';
                                    echo '</button><br/>';    
                                }
                                if($hasChange && $hasNewChange){
                                    echo '<button type="button" class="btn btn-success btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-exchange"></i>&nbsp;';
                                    echo $numChange.' New';
                                    echo '</button><br/>';    
                                }
                                if(($isTimeControlled) && ($taskTO != 0)){
                                    echo '<button type="button" class="btn btn-darkgrey btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-clock-o"></i>&nbsp;';
                                    echo $this->Ops->offsetToFriendly($taskTO);
                                    echo '</button><br/>';    
                                }
                                else if(($isTimeControlled) && ($taskTO == 0)){
                                    echo '<button type="button" class="btn btn-primary btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-clock-o"></i>&nbsp;';
                                    echo $this->Ops->offsetToFriendly($taskTO);
                                    echo '</button><br/>';    
                                }
                            ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                    if (!empty($task['Parent']['id']) && ($single_task || in_array($view_type, array(0)))): ?>
                                    
                    <div class="row xs-bot-marg lg-top-marg">
                        <div class="col-xs-2">
                            <div class="text-align-right">
                                <?php 
                                if($task['Parent']['id'] && ($task['Task']['time_control']==1)){
                                    //$off = $this->Ops->offsetToFriendly($task['Task']['time_offset']);
                                    $relType ='<i class="fa fa-clock-o"></i> <b>Time Synced To</b>' ; 
                                }
                                else{
                                    $relType ='<i class="fa fa-external-link-square"></i> <b>Linked To</b>';
                                }
                                ?>
                                <h5><?php echo $relType;?></h5>
                            </div>
                        </div>
                        <div class="col-xs-10">
                            <?php 
                                echo $this->Ops->subtaskRowSingle($task['Parent']);
                            ?>
                        </div>
                    </div>
                    <?php endif; 
                    
                    if (!empty($task['Assist']) && ($single_task || in_array($view_type, array(0)))):
                        $ass = Hash::combine($task['Assist'], '{n}.id', '{n}','{n}.time_control');

                        if(isset($ass[1])):?>
                            <div class="row xs-bot-marg">
                                <div class="col-xs-2">
                                    <div class="text-align-right">
                                        <h5><i class="fa fa-clock-o"></i> <b>Controls Start Of</b></h5>
                                    </div>
                                </div>
                                <div class="col-xs-10">
                                <?php
                                    foreach($ass[1] as $tid => $tsk){
                                        echo $this->Ops->subtaskRowSingleWithOffset($tsk);
                                    }
                                ?>
                                </div>
                            </div>
                        <?php
                        endif;
                        if(isset($ass[0])):?>
                            <div class="row xs-bot-marg">
                                <div class="col-xs-2">
                                    <div class="text-align-right">
                                        <h5><i class="fa fa-sitemap"></i> <b>Incoming Links</b></h5>
                                    </div>
                                </div>
                                <div class="col-xs-10">
                                <?php
                                    foreach($ass[0] as $tid => $tsk){
                                        echo $this->Ops->subtaskRowSingle($tsk);
                                    }
                                ?>
                                </div>
                            </div>
                        <?php
                        endif;
                    endif;
                    ?>
                </div>
      
                <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>" style="display:none;"></div>    
                </div>
            </div>
        </div>
    <?php
        $last_t_day = $curr_t_day;
        $last_t_hr = $curr_t_hr;
        $last_c_day = $curr_c_day;
        endforeach; 

    echo '<br/>';
    if($this->Paginator->param('current')):
    ?>
    <p>
        <small>
        <?php
            echo $this->Paginator->counter(array(
                'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
            ));
        ?>
        </small>
    </p>
    <ul class="pagination">
        <?php
            echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
            echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
            echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
            echo '<span class="csSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
            echo $this->Html->image('ajax-loader_old.gif');
            echo '</span>'; 
        ?>
    </ul><!-- /.pagination -->
        <div class="pageNum" id="pageNum" style="visibility:hidden;"><?php echo $this->Paginator->param('page');?></div>
    </div><!-- /.index -->

<!-- Modal -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleteTaskModalLabel">Task Deletion Warning</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you wish to delete this task:</p>
        <p class="deleteTaskDesc"></p>
        <p>If you choose to continue:</p>
        <ul>
            <li>All teams will be removed</li>
            <li>Any linked tasks will be unlinked (but not deleted)</li>
            <li>Any requests (open or closed) to other teams will be removed</li>
        </ul>
        <p>Deleting tasks is <b>permanent</b> and <u>cannot be undone!</u></p>
        <span style="visibility: hidden;" id="deleteTaskId"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
        <button type="button" class="btn btn-danger btn-doDelete"><i class="fa fa-trash-o"></i> Delete Task</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="dueDateTaskModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="dueDateTaskModalLabel">Set Due Date As End Time?</h4>
            </div>
            <div class="modal-body">
                <p>You're setting a due date for this task. Would you like to set the due date as the task's end date also?</p>
                <p>By default, task's run from when they're created, until their due date. Associated teams will also be notified when due dates and end dates are coming up.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-danger btn-doDelete"><i class="fa fa-trash-o"></i> Delete Task</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="teamAddLinkedModal" tabindex="-1" role="dialog" aria-labelledby="teamAddLinkedModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="teamAddLinkedModalLabel">Choose Team for New Linked Task</h4>
            </div>
            <div class="modal-body">
                <p>Multiple teams can link to this task. Which team do you want to link as?</p>
                
                <div class="form-group">
                    
                <select class="form-control" id="selectLinkedTeam">
                </select>
                </div>
                <span class="hiddenParentId collapse" style="visibility: hidden;"></span>
                <span class="hiddenTid collapse" style="visibility: hidden;"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-success btn-doLink"><i class="fa fa-plus-circle"></i> Add Task</button>
            </div>
        </div>
    </div>
</div>
          
          
            
<?php
endif;
    }
    // No tasks
    else { 
        
        if($single_task == 1){?>
            <div class="alert alert-danger" role="alert">
                <div class="row">
                    <div class="col-md-8">
                        <i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>Task Not Found! </b> The task you requested was not found. It may have been deleted. Please verify the task ID.        
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right">
                            <i class="fa fa-gears"></i> Back to Compiled Tasks                                   
                        </a>        
                    </div>
                </div>
            </div>
        <?php 
        }
        else{
            ?>
            <div class="alert alert-danger" role="alert">
                <i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>No Tasks Found! </b> No tasks matched your search parameters.  Please try refining your search terms.
            </div>
        <?php 
        } 
    }

    echo $this->Js->writeBuffer();    
?>

    <script>
        function cs_f(text){
            return 'hi from compile screen '+text;
        }
        var $cs = 'in cs';
    
        function sayCs(){
            alert('sayCs');
        }
    
    //sayHello();
    
    //sayC();
    
    
    
    </script>


<!-- Comment styling from http://bootsnipp.com/snippets/featured/user-comment-example -->
