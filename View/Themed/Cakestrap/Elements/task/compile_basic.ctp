<?php
    if (AuthComponent::user('id')){
        $userTeams = AuthComponent::user('Teams');
    }

    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    $show_comments = true; //$this->Session->read('Auth.User.Compile.show_comments');
    $single_task = (isset($single_task))? $single_task:0;
    $search_term = (isset($search_term))? $search_term:null;
    $view_type = $this->Session->read('Auth.User.Compile.view_type');
    $user_shift = $this->Session->read('Auth.User.Timeshift');
    $user_controls = $this->Session->read('Auth.User.Teams');
    $sort = $this->Session->read('Auth.User.Compile.sort');
    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  

    // Variables for what's currently being shown
    $viewTeams = array();
    $viewMessage = '';
    $viewRange = '';
    $viewTeamsStr = '';
    $viewStartDate = date('M j', strtotime($start_date));
    $viewEndDate = date('M j', strtotime($end_date));
    $viewSort = (int)$sort;
    $viewSort = ($viewSort == 0) ? '<b>ascending start time</b>':'<b>descending start time</b>';    
            
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
    if($single_task == 1){
        $viewMessage = '<b>Note:</b> You\'re currently viewing a single task.';
    }
    elseif(isset($search_term)){
        $viewMessage = '<b>Note:</b> You\'re currently viewing search results for '.$search_term ;
    }
    // Threaded
    elseif($view_type == 0){
        $viewMessage = 'Viewing tasks for <b>'.$viewTeamStr.'</b> in a <b>threaded</b> view from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by '.$viewSort;    
    }
    // Rundown
    elseif($view_type == 1){
        $viewMessage = 'Viewing tasks for <b>'.$viewTeamStr.'</b> in a <b>rundown</b> view from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by '.$viewSort;    
    }
    // Lead Only
    elseif($view_type == 2){
        $viewMessage = 'Viewing tasks where <b>'.$viewTeamStr.'</b> are task lead from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by '.$viewSort;
    }
    // Requests
    elseif($view_type == 3){
        $viewMessage = 'Viewing tasks where <b>'.$viewTeamStr.'</b> have <b>open requests</b> from <b>ANY</b> date, ordered by '.$viewSort.'</b>';
    }
    // Action Items
    elseif($view_type ==  4){
        $viewMessage = '<b>Viewing Action Items.</b> Showing tasks from <b>ALL</b> teams from <b>ANY</b> date, ordered by <b>ascending start date</b>';
    }
    // Recently Created
    elseif($view_type ==  5){
        $viewMessage = 'Viewing <b>recently created</b> tasks involving <b>'.$viewTeamStr.'</b> from <b>ANY</b> date, ordered by <b>descending created date</b> (i.e newest first)';
    }
}
     ?>
     <div class="tasks index">

     
     <?php 
    if (!empty($tasks)){ 
    ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info" role="alert">
                        <?php 
                        if ($single_task == 1){ ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <?php echo $viewMessage; ?>    
                                </div>
                            </div>
                    <?php                        
                        }
                        elseif ($view_type == 2){ ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <?php echo $viewMessage; ?>    
                                </div>
                            </div>
                    <?php                        
                        }
                        elseif ($view_type == 4){ ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <?php echo $viewMessage; ?>    
                                </div>
                            </div>
                    <?php                        
                        }
                        else {
                            echo $viewMessage;                
                        }
                    ?>
                    </div>
                </div>
            </div>

            
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
        // Figure out task start date & hr & created date.  Used to group tasks by relevant headers
        $daysAreSame = false;
        $cDaysAreSame = false;
        $onEday = false;
        $hoursAreSame = false;
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
                if (strtotime($chg['created']) > $owa){
                    $hasNewChange = true;
                    $numChange++;
                }
            }  
        }
    ?>
    
    <div class="row <?php echo ($task['Task']['actionable_type_id'] > 300)? 'acomp':'';?> <?php echo ($isPastDue)? 'past_due':'';?>">
        <div class="col-xs-2">
        <?php
            // For recently created, use "created date" as basis for grouping tasks for display
            if($view_type == 5 && !$cDaysAreSame){
                echo '<h4 class="great">';
                
                if($curr_c_day != $today){
                    echo $this->Time->timeAgoInWords($curr_c_day, array(
                        'accuracy' => array('day' => 'day'),
                        'end' => '1 week',
                        'format' => 'M d'
                    ));    
                }
                else{
                    echo 'Today';
                }
                    
                echo '</h4>';
                
            }
            // Otherwise, use task start date/time
            elseif(!$daysAreSame && $view_type != 5){
                echo '<h4 class="great">'.date('M j', strtotime($curr_t_day)).'</h4>';
            }
            elseif($onEday && !$hoursAreSame && $view_type != 5){
                echo '<h4 class="eday">'.date('g A', strtotime($task['Task']['start_time'])).'</h4>';
            }
            elseif($onEday && !$hoursAreSame && !$daysAreSame && $view_type != 5){
                echo '<h4 class="great">'.date('M j g A', strtotime($task['Task']['start_time'])).'</h4>';
            }
        ?>
        </div>
    </div>
    <div class="row">
            <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" class="panel panel-default task-panel" style="border-left: 7px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
                <div class="panel-heading task-panel-heading" 
                    data-tid="<?php echo ($task['Task']['id']); ?>">
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2">
                                    <?php
                                        if($view_type != 5){
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
                                        }
                                        else {
                                            echo date('M j g:i A', strtotime($task['Task']['start_time']));
                                        }
                                    ?>
                            
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-2">
                            <?php
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                                echo $this->Ops->makeTeamsSigBasic($task['TasksTeam'], $zoneTeamCodeList);
                            ?> 
                        </div>
                        <div class="col-xs-5 col-sm-7 col-md-8">
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
                        <div class="col-xs-9">
                            <?php 
                                echo $this->Ops->subtaskRowSinglePdf($task['Parent']);
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
            </div>
        </div>
    <?php
        $last_t_day = $curr_t_day;
        $last_t_hr = $curr_t_hr;
        $last_c_day = $curr_c_day;
        endforeach; 
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

?>

    </div>
