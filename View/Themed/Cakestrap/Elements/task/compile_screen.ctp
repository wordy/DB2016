<?php
    if (AuthComponent::user('id')){
        $userTeams = AuthComponent::user('Teams');
    }
    //$user_controls = $this->Session->read('Auth.User.Teams');
    $single_task = (isset($single_task))? $single_task:0;
    $search_term = (isset($search_term))? $search_term:null;
    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $sort = $this->Session->read('Auth.User.Compile.sort');
    $view_type = $this->Session->read('Auth.User.Compile.view_type');
    $view_links = ($this->Session->read('Auth.User.Compile.view_links'))? 1:0;
    $view_details = ($this->Session->read('Auth.User.Compile.view_details'))? 1:0;
    $view_threaded = ($this->Session->read('Auth.User.Compile.view_threaded'))? 1:0;
    $user_shift = $this->Session->read('Auth.User.Timeshift');
    
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  

    $this->Paginator->options(array(
        'update' => '#taskListWrap',
        'evalScripts' => true,
        'before' => $this->Js->get('.csSpinner')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('.csSpinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'tasks', 'action' => 'compile','?'=>array('src'=>'compile'))
    ));

    //$cURL = $this->params->here;

    if($single_task != 0){
        $this->Js->buffer("
            // Show details right away if viewing single task
            $('#taskListWrap').find('.task-panel-heading').trigger('click').delay(2000);
        ");
    }
    else{
        $this->Js->buffer("
            var ses_details = ".$view_details.";
            var ses_links = ".$view_links.";

            if(ses_details == 0){
                $('div.divTaskDetails').hide();  
            }
            if(ses_links == 0){
                $('div.taskLinkages').hide();   
            }
        ");
        
    }
    
    
    if($view_type == 1){
        $this->Js->buffer("
            var ses_threaded = ".$view_threaded.";
            if(ses_threaded == 1 ){
                $('div.isChild').hide();
            }
            $('#coViewThreadedBut').removeClass('disabled');
            $('#coDateRange').attr('disabled', false);
            $('#coSort').attr('readonly', false);
        ");
    }
    elseif($view_type == 10){
        $this->Js->buffer("
            $('#coViewThreadedBut').addClass('disabled');
            $('#coDateRange').attr('disabled', false);
            $('#coSort').attr('readonly', false);
        ");
    }
    elseif($view_type == 500){
        $this->Js->buffer("
            //$('div.aiCompleted').hide();
            var shtml = '<i class=\"fa fa-eye\"></i> Show Completed';
            var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Completed';                                   
            $('#aiHideComp').on('click', function(){
                if($(this).hasClass('ai_hidden')){
                    $('div.aiCompleted').show();
                    $(this).removeClass('ai_hidden').addClass('ai_shown');
                    $(this).html(hhtml);    
                }
                else{
                    $('div.aiCompleted').hide();
                    $(this).removeClass('ai_shown').addClass('ai_hidden');
                    $(this).html(shtml); 
                }
            });
            
            $('#coViewThreadedBut').addClass('disabled');
            $('#coDateRange').attr('disabled', true);
            $('#coSort').attr('readonly', true);
        ");
    }    
    else{
        $this->Js->buffer("
            $('#coViewThreadedBut').addClass('disabled');
            $('#coDateRange').attr('disabled', true);
            $('#coSort').attr('readonly', true);
        ");
    }
    





    // Variables for what's currently being shown
    $viewTeams = array();
    $viewMessage = $viewRange = $viewTeamsStr = '';
    $viewStartDate = date('M j', strtotime($start_date));
    $viewEndDate = date('M j', strtotime($end_date));
    $viewSort = (int)$sort;
    $viewSort = ($viewSort == 0) ? 'ascending start time':'descending start time';    
            
    if(!empty($comp_teams)){
        foreach($comp_teams as $k=>$tid){
            $viewTeams[] = $teamIdCodeList[$tid];
        }
    
    // Oxford comma on teams list
    $viewTeamsCount = count($viewTeams);
    $last_t = array_pop($viewTeams);
    $noLastTeam = implode(', ', $viewTeams);
    
    if ($noLastTeam){
        if($viewTeamsCount > 2){
            $noLastTeam .= ', and ';    
        }
        elseif($viewTeamsCount == 2) {
           $noLastTeam .= ' and '; 
        }
    }

    $viewTeamStr = $noLastTeam.$last_t;

    // Single Task
    if(!empty($single_task)){ 
        $viewMessage = 'Viewing a single task.';
    }
    elseif(isset($search_term)){
        $viewMessage = 'Viewing search results for <b>'.strtoupper($search_term).' </b>';
    }
    // Threaded
    elseif($view_type == 888){
        $viewMessage = 'Viewing tasks for <b>'.$viewTeamStr.'</b> in a <b>threaded</b> view from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by <b>'.$viewSort.'.</b>';  
    }
    // Rundown
    elseif($view_type == 1){
        $viewMessage = 'Viewing rundown for <b>'.$viewTeamStr.'</b> from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by <b>'.$viewSort.'.</b>';    
    }
    // Lead Only
    elseif($view_type == 10){
        $viewMessage = 'Viewing tasks lead by <b>'.$viewTeamStr.'</b> from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by <b>'.$viewSort.'.</b>';
    }
    // Requests
    elseif($view_type == 30){
        $viewMessage = 'Viewing tasks where <b>'.$viewTeamStr.'</b> have Open Requests <b>from</b> other teams on <b>ANY</b> date, ordered by <b>'.$viewSort.'.</b>';
    }
    // Requests
    elseif($view_type == 31){
        $viewMessage = 'Viewing tasks where <b>'.$viewTeamStr.'</b> have Open Requests <b>to</b> other teams on <b>ANY</b> date, ordered by <b>'.$viewSort.'.</b>';
    }
    // Action Items
    elseif($view_type ==  500){
        $viewMessage = 'Showing Action Iteams from <b>ALL</b> teams from <b>ANY</b> date, ordered by <b>ascending start date.</b>';
    }
    // Recently Modified
    elseif($view_type ==  100){
        $viewMessage = 'Viewing <b>recently modified</b> tasks involving <b>'.$viewTeamStr.'</b> from <b>ANY</b> date, ordered by <b>descending modified date.</b> (i.e most recent first)';
    }
}
?>
<h1><?php 
    $view = $this->Session->read('Auth.User.Compile.view_type');
    if(isset($single_task) && !empty($single_task)){
        echo 'Viewing Single Task';
    }
    elseif(isset($search_term) && !empty($search_term)){
        echo 'Search Results For "'.$search_term.'"';
    }
    elseif($view == 1){
        echo 'Compiled Tasks (Rundown)'; 
    }
    elseif($view == 10){
        echo 'Compiled Tasks (Lead Only)'; 
    }
    elseif($view == 30){
        echo 'Open Requests (Owing)'; 
    }
    elseif($view == 31){
        echo 'Open Requests (Waiting)'; 
    }
    elseif($view == 100){
        echo 'Recently Modified Tasks'; 
    }
    elseif($view == 500){
        echo 'Action Items';
    }
    else{
        echo 'Compiled Tasks'; 
    } 
?></h1>
<?php if (!empty($tasks)){ ?>
<div class="tasks index">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
            <?php if (!empty($single_task) || !empty($search_term)){ ?>
                <div class="row">
                    <div class="col-md-9"><?php echo $viewMessage; ?></div>
                    <div class="col-md-3 hidden-print"><a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right"> <i class="fa fa-gears"></i> Back to Compiled Tasks</a></div>
                </div>
            <?php                        
                }elseif (isset($view_type) && $view_type != 500){ ?>
                    <div class="row">
                        <div class="col-md-12"><?php echo $viewMessage; ?></div>
                    </div>
            <?php                        
                }elseif ($view_type == 500){ ?>
                    <div class="row">
                        <div class="col-md-9"><?php echo $viewMessage; ?></div>
                        <div class="col-md-3 hidden-print"><button type="button" id="aiHideComp" class="btn btn-primary pull-right"><i class="fa fa-eye-slash"></i> Hide Completed</button></div>
                    </div>
            <?php                        
                }else { echo $viewMessage; }
            ?></div>
        </div>
    </div>
        
<?php if(!$single_task && $this->Paginator->param('current')):?>
    <div class="row hidden-print">
        <div class=" col-xs-12" style="margin-bottom: -10px;">
            <div style="text-align:right; margin-bottom:-40px;">
                <div>
                    <ul class="pagination" style="margin-top:0px; margin-left: auto; margin-right:auto">
                    <?php
                        if($view_type == 100){
                            $prev_lab = 'Newer';
                            $next_lab = 'Older';
                        }
                        else{
                            $prev_lab = 'Earlier';
                            $next_lab = 'Later';    
                        }
                        echo '<span class="csSpinner" style="display: none; margin-left: 5px;">'.$this->Html->image('ajax-loader_old.gif').'</span>'; 
                        echo $this->Paginator->prev('< ' . __($prev_lab), array('tag' => 'li'), null, array('class' => 'pagPrev disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo $this->Paginator->numbers(array('separator' => '', 'class'=>'pagNum', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                        echo $this->Paginator->next(__($next_lab) . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                    ?>
                    </ul><!-- /.pagination -->
                </div>
                <p><?php echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?></p>
            </div>
        </div><!-- /.index -->
    </div>
<?php endif; 

    // Hold days of tasks
    $cur_t_day = $prev_t_day = $last_t_day = $last_t_hr = $cur_t_day = $cur_t_hr = $last_mod_day = '';

    /******************************
     *  START of FOREACH $tasks   *          
     ******************************/
    foreach ($tasks as $k => $task):
    $inUsrShift = $userControls = $uControlsInvolved = $hasComment = $commentCount = $hasDueDate = $hasDueSoon = $hasActionable = $hasChange = $hasNewChange = false;
    $hoursAreSame = $daysAreSame = $modDaysAreSame = $onEday = $isPastDue = $isTimeControlled = false;
        
    // FULL THREADING
    /*
    if(($view_type == 0) && !$single_task && !empty($task['Task']['parent_id']) && empty($task['Assist'])){
        continue;
    }*/

    $tid = $task['Task']['id']; 
    $taskTO = 0; 
    $teamsInvolved = Hash::extract($task['TasksTeam'],'{n}.team_id');
    $controlledInInvolved = array_intersect($teamsInvolved, $userTeams);
    $jsCIN = array();

    foreach($controlledInInvolved as $k => $team_id){
        $jsCIN[$team_id] = $teamIdCodeList[$team_id];
    }
    $jsonCIN = json_encode($jsCIN);
    
    if(!empty($controlledInInvolved)){ $uControlsInvolved = true;}

    // Figure out task start date & hr & created date.  Used to group tasks by relevant headers
    $cur_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
    $cur_t_hr = date('H', strtotime($task['Task']['start_time']));
    $cur_mod_day = date('Y-m-d', strtotime($task['Task']['modified']));
    
    //debug($curr_mod_day);
    //debug($last_mod_day);    
    if($last_t_day == $cur_t_day){ $daysAreSame = true;}
    if($last_mod_day == $cur_mod_day){ $modDaysAreSame = true;}
    if($cur_t_day == $eday){ $onEday = true;}
    if($cur_t_hr == $last_t_hr){ $hoursAreSame = true; }
    
    //Hide/show elements based on permissions.
    if(in_array($task['Task']['team_id'], $userTeams)){ $userControls = true; }
    if(in_array($task['Task']['id'], $user_shift)){ $inUsrShift = true; }
    
    if(!empty($task['Task']['due_date'])){
        $dueString = strtotime($task['Task']['due_date']);
        $hasDueDate = true;
    
        // Highlights due 1 week from now
        if(($dueString >= $today_str) && ($dueString < $owfn)){ $hasDueSoon = true; }
        // Highlights past due tasks
        if($dueString < $today_str) {
            $hasDueSoon = true;
            $isPastDue = true; 
        }
    }
    if(!empty($task['Task']['actionable_type'])){ $hasActionable = true; }
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
<div <?php
        if((empty($task['Assist']) && !empty($task['Task']['parent_id'])) || ($task['Task']['actionable_type_id'] > 300)){
            echo 'class = "';
        if (!empty($task['Assist']) || !empty($task['Task']['parent_id'])){
            echo "isChild ";    
        }
        if($task['Task']['actionable_type_id'] > 300){
            echo "aiCompleted";
        }
        echo '"';
    }?>>

<?php //if(empty($task['Assist']) && (!empty($task['Task']['parent_id']))){ echo 'isChild';}?> 

<div class="row <?php echo ($isPastDue)? 'past_due':'';?>">
    <div class="col-xs-12">
    <?php
        // For recently created, use "created date" as basis for grouping tasks for display
        if($view_type == 100 && !$single_task && !$modDaysAreSame){
            echo '<h4 class="great">';
            
            if($cur_mod_day != $today){
                //echo 'mod not today';
                echo $this->Time->timeAgoInWords($cur_mod_day, array(
                    'accuracy' => array('day' => 'day'),
                    'end' => '1 week',
                    'format' => 'M d'
                ));    
            }
            else{ echo 'Today'; }
            echo '</h4>';
        }
        // Otherwise, use task start date/time
        elseif(!$onEday && !$daysAreSame && ($view_type != 100 || $single_task)){
            echo '<h4 class="great">'.date('M j', strtotime($curr_t_day)).'</h4>';
        }
        elseif($onEday && !$daysAreSame && ($view_type != 100 || $single_task)){
            echo '<h4 class="eday"><i class="fa fa-diamond"></i> '.date('M j \- g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
        elseif($onEday && !$hoursAreSame && ($view_type != 100 || $single_task)){
            echo '<h4 class="eday"><i class="fa fa-diamond"></i> '.date('g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
    ?>
    </div>
</div> 
<div class="row">
    <div class="col-md-12">
        <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" class="panel panel-default task-panel" style="border-left: 7px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
            <div class="panel-heading task-panel-heading" data-team_id = "<?php echo $task['Task']['team_id'];?>" data-stime="<?php echo $task['Task']['start_time'];?>" data-uconinv="<?php echo ($uControlsInvolved)?'true':'false';?>" data-tid="<?php echo ($task['Task']['id']); ?>">
                <span class="collapse jsonCIN" style="visibility: hidden;"><?php echo $jsonCIN; ?></span>
                <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="taskTs checkbox facheckbox xs-bot-marg facheckbox-circle facheckbox-success">
                            <input type="checkbox" class="tsCheck <?php if($inUsrShift){echo 'checked';} ?>" id="hide<?php echo $tid;?>" <?php if(!$userControls || $isTimeControlled){echo 'disabled="disabled"';} ?> <?php if($inUsrShift){echo 'checked="checked"';} ?> 
                            data-tid="<?php echo $task['Task']['id']?>"/>
                            <label class="taskTimeshift" for="hide<?php echo $tid;?>"><?php 
                                    if($view_type != 100){
                                        //echo $this->Ops->durationFriendlyNoDate($task['Task']['start_time'], $task['Task']['end_time']);
                                        echo $this->Ops->startTimeFriendly($task['Task']['start_time'], $task['Task']['end_time'], array('duration'=>true));
                                    }
                                    else { // When showing by created date, must show task date since overall tasks are sorted by created date (not task date)
                                        echo date('M j g:i A', strtotime($task['Task']['start_time']));
                                    }
                                ?></label>
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-2">
                        <?php echo '<b>'.$task['Task']['task_type'].'</b><br/>'.$this->Ops->makeTeamsSig($task['TasksTeam'], $zoneTeamCodeList, $userControls);?> 
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-6">
                        <div class="csTaskDetails"><?php
                                echo $task['Task']['short_description'].'<br/>';
                                if (!empty($task['Task']['details'])){
                                    echo '<div class="divTaskDetails">';
                                    echo '<hr align="left" style="width: 100%; margin-bottom:3px; margin-top:3px; border-top: 1px solid #aaa;"/>';
                                    echo nl2br($task['Task']['details']);
                                    echo '</div>';
                                }else{echo '';} ?></div>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="pull-right task-buttons" style="text-align: right; margin-left: 8px;">
                        <?php 
                            if($hasDueDate){
                                echo ($hasDueSoon)? '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">':'<button type="button" class="btn btn-warning btn-xs xxs-bot-marg">';
                                echo '<i class="fa fa-bell-o"></i>&nbsp;'.$this->Time->format('M d', $task['Task']['due_date']).'</button><br/>';
                            }
                            if($hasActionable){
                                echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                echo '<i class="fa fa-flag fa-lg"></i>&nbsp;'.$task['Task']['actionable_type'].'</button><br/>';
                            }
                            if($hasComment){
                                echo '<button type="button" class="btn btn-primary btn-xs xxs-bot-marg">';
                                echo '<i class="fa fa-comment-o"></i>&nbsp;'.$commentCount.' New</button><br/>';    
                            }
                            if($hasChange && $hasNewChange){
                                echo '<button type="button" class="btn btn-success btn-xs xxs-bot-marg">';
                                echo '<i class="fa fa-exchange"></i>&nbsp;'.$numChange.' New</button><br/>';    
                            }
                            if(($isTimeControlled) && ($taskTO != 0)){
                                echo '<button type="button" class="btn btn-darkgrey btn-xs xxs-bot-marg">';
                                echo '<i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO).'</button><br/>';    
                            }
                            else if(($isTimeControlled) && ($taskTO == 0)){
                                echo '<button type="button" class="btn btn-primary btn-xs xxs-bot-marg">';
                                echo '<i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO).'</button><br/>';    
                            }
                            if(count($task['Assist']) > 0 || $task['Task']['parent_id'] > 0){
                                $lnkCnt = count($task['Assist']);
                                if(!empty($task['Task']['parent_id'])){ $lnkCnt++; }
                                $lstr = ($lnkCnt > 1 )? 'Links': 'Link';

                                echo '<button type="button" class="btn btn-default btn-xs xxs-bot-marg">';
                                echo '<i class="fa fa-link"></i>&nbsp;'.$lnkCnt.' '.$lstr.'</button><br/>';    
                            }
                        ?>
                        </div>
                    </div>
                </div>
                <div class="taskLinkages"><?php 
                    if (!empty($task['Parent']['id'])): ?>
                        <div class="row xs-bot-marg sm-top-marg">
                            <div class="col-xs-2">
                                <div class="text-align-right">
                                    <?php 
                                        $relType = ($task['Parent']['id'] && ($task['Task']['time_control']==1))? '<i class="fa fa-clock-o"></i> <b>Time Linked To</b>':'<i class="fa fa-external-link-square"></i> <b>Linked To</b>';
                                        echo '<h5>'.$relType.'</h5>';
                                    ?>  
                                </div>
                            </div>
                            <div class="col-xs-10"><?php echo $this->Ops->subtaskRowSingle($task['Parent']); ?></div>
                        </div>
                    <?php endif; 
                    
                    if (!empty($task['Assist'])):
                        $ass = Hash::combine($task['Assist'], '{n}.id', '{n}','{n}.time_control');

                        if(isset($ass[1])){
                            // Re-sort Time Controlled tasks by offset to highlight timing (before, synced, after)
                            $tc_tasks = Hash::sort($ass[1], '{n}.time_offset', 'asc');
                    ?>
                            <div class="row xs-bot-marg sm-top-marg">
                                <div class="col-xs-2"><div class="text-align-right"><h5><i class="fa fa-clock-o"></i> <b>Controls Start Of</b></h5></div></div>
                                <div class="col-xs-10">
                                    <?php
                                        foreach($tc_tasks as $tid => $tsk){
                                            echo $this->Ops->subtaskRowSingleWithOffset($tsk);
                                        }
                                    ?>
                                </div>
                            </div>
                        <?php
                        }
                        if(isset($ass[0])):?>
                            <div class="row xs-bot-marg sm-top-marg">
                                <div class="col-xs-2"><div class="text-align-right"><h5><i class="fa fa-sitemap"></i> <b>Incoming Links</b></h5></div></div>
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
                    endif;?></div>
            </div>
            <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>" style="display:none;"></div>    
            </div>
        </div>
    </div>
</div>
<?php
    $last_t_day = $cur_t_day;
    $last_t_hr = $cur_t_hr;
    $last_mod_day = $cur_mod_day;
    endforeach; 

echo '<br/>';
    

if(!$single_task && $this->Paginator->param('current')):
?>
<p>
    <small>
        <?php  echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')));    ?>
    </small>
</p>
<ul class="pagination hidden-print">
    <?php
        echo $this->Paginator->prev('< ' . __($prev_lab), array('tag' => 'li'), null, array('class' => 'pagPrev disabled', 'tag' => 'li', 'disabledTag' => 'a'));
        echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'class'=>'pagNum', 'tag' => 'li', 'currentClass' => 'disabled'));
        echo $this->Paginator->next(__($next_lab) . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
        echo '<span class="csSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
        echo $this->Html->image('ajax-loader_old.gif');
        echo '</span>'; 
    ?>
</ul><!-- /.pagination -->
<div class="pageNum" id="pageNum" style="visibility:hidden;"><?php echo $this->Paginator->param('page');?></div>
<?php
endif;
}
// No tasks
else { 
    if($single_task > 0){?>
        <div class="alert alert-danger" role="alert" style="margin-top:20px;margin-bottom:250px;">
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
        <div style = "margin-top: 20px; margin-bottom: 80px;" class="alert alert-danger" role="alert">
            <p><i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>No Tasks Found! </b> No tasks matched your search parameters.  Please try modifying your Compile Options or search term.</p>
        </div>
    <?php 
    } 
}
?>

      
<span style="visibility: hidden" id ="coViewThreaded"><?php echo $view_threaded;?></span>
<span style="visibility: hidden" id ="coViewLinks"><?php echo $view_links;?></span>
<span style="visibility: hidden" id ="coViewDetails"><?php echo $view_details;?></span>
<div class="singleTask" id="singleTask" style="visibility:hidden;"><?php echo ($single_task)?:0;?></div>

</div><!-- /.index -->

<?php
    echo $this->Js->writeBuffer();    
?>


