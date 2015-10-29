<?php

    
    $user_controls = $this->Session->read('Auth.User.Teams');
    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    $filter = $this->Session->read('Auth.User.Compile.filter');
    $user_shift = $this->Session->read('Auth.User.Timeshift');


    $this->set('controlled_teams', $this->Session->read('Auth.User.Teams'));
    

    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    
    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
        
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }
    
    //debug($ztlist);
   
    
    //debug($ztlist);
    if (!empty($task)){ ?>

        <div class="tasks index">
        <?php if($filter == 1):?>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="alert alert-info" role="alert">
                        <b>Note: </b> You're filtering tasks with due dates.  Tasks are ordered by <b>ascending due date</b>.
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if($filter == 2):?>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="alert alert-info" role="alert">
                        <b>Note: </b> You're filtering tasks where the selected team(s) are assisting.  Tasks are sorted by <b>start date using your compile preference</b>.
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if($filter == 3):?>
            <div class="alert alert-info" role="alert">
                <div class="row">
                    <div class="col-md-10">
                        <b>Note: </b> You're filtering action items.  Action items are ordered by <b>ascending due date</b> to highlight things that are overdue or coming up.
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="aiHideComp" class="btn btn-default ai_hidden">
                            <i class="fa fa-eye"></i> Show Completed                                   
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
            
    <?php
     // Hold days of tasks
    $cur_t_day = '';
    $prev_t_day = '';
    $last_t_day = '';
    $last_t_hr = '';
    $curr_t_day = '';            
    $curr_t_hr = '';
    
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  
    
        $daysAreSame = false;
        $onEday = false;
        $hoursAreSame = false;
        $curr_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        $curr_t_hr = date('H', strtotime($task['Task']['start_time']));

        if($last_t_day == $curr_t_day){
            $daysAreSame = true;
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
        
        $hasDueDate = false; $hasDueSoon = false; $hasActionable = false; $hasChange = false; $hasNewChange = false;
            
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
            }
        }
    
        if(!empty($task['Task']['actionable_type'])){
             $hasActionable = true; 
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
<div class="row <?php echo ($task['Task']['actionable_type_id'] > 300)? 'acomp':'';?>">
    <?php
    if(!$daysAreSame){
            echo '<h4 class="great">'.date('M j', strtotime($curr_t_day)).'</h4>';
        }
        
        elseif($onEday && !$hoursAreSame){
            echo '<h4 class="eday">'.date('g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
        
        elseif($onEday && !$hoursAreSame && !$daysAreSame){
            echo '<h4 class="great">'.date('M j g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
    ?>
    <div class="col-md-12">
        <div 
            data-taskid="<?php echo ($task['Task']['id']); ?>" 
            id="tid<?php echo ($task['Task']['id']); ?>" 
            class="panel panel-default task-panel" 
            style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
         >
            <div class="panel-heading task-panel-heading" 
                data-tid="<?php echo ($task['Task']['id']); ?>">
                <div class="row">
                    <div class="col-sm-1">
                        <div class="taskTs">
                            <label class="taskTimeshift" for="hide<?php echo $tid;?>">
                                <?php
                                    $t1 = date('Y-m-d H:i:s', strtotime($task['Task']['start_time']));
                                    $t2 = date('Y-m-d H:i:s', strtotime($task['Task']['end_time']));
                                 
//                                    $m1 = date('Y-m-d H:i', strtotime($task['Task']['start_time']));
//                                    $m2 = date('Y-m-d H:i', strtotime($task['Task']['end_time']));                                    
                                    
//                                    $hr1 = date('g:i', strtotime($task['Task']['start_time']));
//                                    $hr2 = date('g:i', strtotime($task['Task']['end_time']));
                                    
                                    $d1 = date('Y-m-d', strtotime($task['Task']['start_time']));
                                    $d2 = date('Y-m-d', strtotime($task['Task']['end_time']));
                                    
                                    $diff = (strtotime($task['Task']['end_time']) - strtotime($task['Task']['start_time']));
                                    $dh = floor($diff / 3600);
                                    $dm = floor(($diff / 60) % 60);
                                    $ds = $diff % 60;
                                    
                                    if($diff == 0){
                                        echo date('g:i A', strtotime($t1));
                                    }
                                    // Important seconds
                                    elseif($diff < 60){
                                        echo $this->Time->format('g:i:s', $task['Task']['start_time']);
                                        echo ' - ';
                                        echo $this->Time->format('g:i:s A', $task['Task']['end_time']);
                                        echo '<br/>('.$diff.'s)';
                                    }
                                    // < hr
                                    elseif(($diff > 60) && ($diff < 3600)){
                                        echo $this->Time->format('g:i', $task['Task']['start_time']);
                                         echo ' - ';
                                         echo $this->Time->format('g:i A', $task['Task']['end_time']);
                                         echo '<br/>('.$dm.' min)';
                                    }
                                    // > 1h < 24h
                                    elseif($diff >= 3600 && $diff < 86400){
                                        echo $this->Time->format('g:i A', $task['Task']['start_time']);
                                        echo ' - ';
                                        echo $this->Time->format('g:i A', $task['Task']['end_time']);
                                        echo '<br/>('.$dh.' hr, '.$dm.' min)'; 
                                    }
                                    // >1 day or spans days
                                    elseif ($diff >= 86400){
                                        echo date('g:i A', strtotime($t1));
                                        //echo '<br/>(Multi-day)';
                                        
                                    }
                                    if($d1 != $d2){
                                        echo '<br/>(Multi-day)';
                                    }
                            ?>
                                
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <?php
                            echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                            echo $this->Ops->makeTeamsSig2015($task['TasksTeam'], $ztlist);
                        ?> 
                    </div>
                    <div class="col-sm-5">
                        <?php
                            echo $task['Task']['short_description'].'<br/>';
                            
                            if ($show_details && !empty($task['Task']['details'])){
                                echo '<hr align="left" style="width: 100%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                echo nl2br($task['Task']['details']);
                            }
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <div class="row">
                     <div class="pull-right task-buttons" style="margin-right: 5px;">
                            <?php 
                                if($hasActionable){
                                    echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-flag fa-lg"></i>&nbsp';
                                    echo $task['Task']['actionable_type'];
                                    echo '</button><br/>';
                                }
                                if($hasDueDate){
                                    if($hasDueSoon){
                                        echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                    }
                                    else {
                                        echo '<button type="button" class="btn btn-warning btn-xs xxs-bot-marg">';
                                    }
                                echo '<i class="fa fa-clock-o"></i>&nbsp;';
                                echo $this->Time->format('M d', $task['Task']['due_date']);
                                echo '</button><br/>';
                            }
                                if($hasChange && $hasNewChange){
                                    echo '<button type="button" class="btn btn-success btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-exchange"></i>&nbsp;';
                                    echo $numChange.' New';
                                    echo '</button><br/>';    
                                }
                            ?>
                            </div>
                       </div>    
                    </div>
                </div>
            </div>
        
            <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>">
                <?php 
                
                echo $this->element('task/task_details', array(
                    'controlled_teams'=>$user_controls, 
                    'task_det'=>$task, 
                    )); 
                ?>
            </div>    
        </div>
    </div>
</div>

</div><!-- /.index -->
            
<?php  }
    else { //no $tasks
        echo 'No tasks matched your search parameters.  Please try refining your search terms.';
    }

    echo $this->Js->writeBuffer();
?>