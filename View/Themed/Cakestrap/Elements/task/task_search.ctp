<?php

    if (AuthComponent::user('id')){
        $userTeams = AuthComponent::user('Teams');
    }
    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    //$filter_due_date = $this->Session->read('Auth.User.Compile.filter_due_date');
    $user_shift = $this->Session->read('Auth.User.Timeshift');
    $user_controls = $this->Session->read('Auth.User.Teams');
    
    
    $this->Js->buffer("


    ");

    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    
    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
        
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }

    if (!empty($tasks)){ 

 // Hold days of tasks
    $cur_t_day = $prev_t_day = $last_t_day = $last_t_hr = $curr_t_day = $curr_t_hr = $last_c_day = '';
       
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  
    
    foreach ($tasks as $k => $task):
        $inUsrShift = $userControls = $uControlsInvolved = $hasComment = $commentCount = $hasDueDate = false; $hasDueSoon = false; $hasActionable = false; $hasChange = false; $hasNewChange = false;
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
        $daysAreSame = $cDaysAreSame = $onEday = $hoursAreSame = $isPastDue = $isTimeControlled = false;
        $curr_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        $curr_t_hr = date('H', strtotime($task['Task']['start_time']));
        $curr_c_day = date('Y-m-d', strtotime($task['Task']['created']));
        
        if($last_t_day == $curr_t_day){ $daysAreSame = true;}
        if($last_c_day == $curr_c_day){ $cDaysAreSame = true;}
        if($curr_t_day == $eday){ $onEday = true;}
        if($curr_t_hr == $last_t_hr){ $hoursAreSame = true; }
        
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
        
        if(!$daysAreSame && !$onEday){
            echo '<h4 class="great">'.date('M j', strtotime($curr_t_day)).'</h4>';
        }
        elseif($onEday && !$hoursAreSame && !$daysAreSame){
            echo '<h4 class="eday"><i class="fa fa-diamond"></i> '.date('M j \- g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
        elseif($onEday && !$hoursAreSame){
            echo '<h4 class="eday"><i class="fa fa-diamond"></i> '.date('g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
        
        
        ?>
<div class="row">
    <div class="col-md-12">
     
        
        <div 
            data-taskid="<?php echo ($task['Task']['id']); ?>" 
            id="tid<?php echo ($task['Task']['id']); ?>" 
            class="panel panel-default task-panel" 
            style="border-left: 7px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
         >
            <div class="panel-heading task-panel-heading" data-tid="<?php echo ($task['Task']['id']); ?>">
                <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="taskTs">
                                <?php 
                                
                                $hr1 = date('g:i', strtotime($task['Task']['start_time']));
                                $hr2 = date('g:i', strtotime($task['Task']['end_time']));
                                
                            echo $this->Time->format('g:i A', $task['Task']['start_time']);
                            
                            if($hr1 != $hr2){
                                echo ' - ';
                                echo $this->Time->format('g:i A', $task['Task']['end_time']);
                                
                            }

                            $time1 = strtotime(date('M j H:i:s', strtotime($task['Task']['start_time'])));
                            $time2 = strtotime(date('M j H:i:s', strtotime($task['Task']['end_time'])));
                                
                            $diff = $time2 - $time1;
                                
                            // NOTE: LIMITATION Hides for duration less than one min.  May impact PRO
                            // since their events last seconds [for everyone else though, it makes sense]
                            if((15<$diff) && ($diff <=59)){
                                echo '<br/>('.gmdate("s", $diff).'s)';
                            }
                            elseif((60 <= $diff)  && ($diff <= 3599)){
                                echo '<br/>('.gmdate("i", $diff).' min)';
                            }
                            elseif($diff > 3599){
                                echo '<br/>('.gmdate('g', $diff).' hr, '.gmdate('i',$diff).' min)';  
                            }
                            ?>
                                
                            
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-2">
                        <?php
                            echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                            echo $this->Ops->makeTeamsSig($task['TasksTeam'], $zoneTeamCodeList, false);
                        ?> 
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-6">
                        <?php
                            echo $task['Task']['short_description'].'<br/>';
                            
                            if (!empty($task['Task']['details'])){
                                echo '<hr align="left" style="width: 100%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                echo nl2br($task['Task']['details']);
                            }
                        ?>
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

                                echo $this->Html->link('<i class="fa fa-eye"></i> View', array(
                                'controller'=>'tasks',
                                'action'=>'compile',
                                '?'=>array(
                                    'task'=>$task['Task']['id']
                                ),
                            ),array(
                                'escape'=>false,
                                'class'=>'btn btn-default btn-xs'));
                                
                            ?>
                            </div>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                            
                    </div>
                </div>
            </div>

                <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>"" style="display:none;">
            </div>    
        </div>
    
      </div>

</div>
<?php

    $last_t_day = $curr_t_day;
    $last_t_hr = $curr_t_hr; 
 endforeach;
?>

    </div><!-- /.index -->
            
    <?php  }
        else { //no $tasks?>
            <div style = "margin-top: 20px; margin-bottom: 80px;" class="alert alert-danger" role="alert">
                <p><i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>No Tasks Found! </b> No tasks matched your search.  Please try modifying your search term.</p>
            </div>
    <?php
        }
    
    echo $this->Js->writeBuffer();
    ?>