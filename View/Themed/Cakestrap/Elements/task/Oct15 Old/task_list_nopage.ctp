<?php
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

    if (!empty($tasks)){ ?>

        <div class="tasks index">
    <?php
     // Hold days of tasks
    $cur_t_day = null;
    $prev_t_day = null;
    $last_t_day = null;
    $last_t_hr = null;
    $curr_t_day = null;            
    $curr_t_hr = null;
    
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  
    
    foreach ($tasks as $k => $task):
        $tid = $task['Task']['id']; 

        $daysAreSame = false; 
        $onEday = false; 
        $hoursAreSame = false;
        $hasDueDate = false; 
        $hasDueSoon = false; 
        $hasActionable = false; 
        $hasChange = false; 
        $hasNewChange = false;

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
        
        //Hide/show elements based on permissions.
        $userControls = false;
        if(in_array($task['Task']['team_id'], $user_controls)){ $userControls = true; }
        
        $inUsrShift = false;
        if(in_array($task['Task']['id'], $user_shift)){ $inUsrShift = true; }
        
            
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
            $numChange = count($task['Change']);
        }
        
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
<div class="row">
    <div class="col-md-12">
     
        
        <div 
            data-taskid="<?php echo ($task['Task']['id']); ?>" 
            id="tid<?php echo ($task['Task']['id']); ?>" 
            class="panel panel-default task-panel" 
            style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
         >
            <div class="panel-heading task-panel-heading" data-tid="<?php echo ($task['Task']['id']); ?>">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
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
                                
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <?php
                            echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                            echo $this->Ops->makeTeamsSig($task['TasksTeam'], $ztlist);
                        ?> 
                    </div>
                    <div class="col-sm-6">
                        <?php
                            echo $task['Task']['short_description'].'<br/>';
                            
                            if ($show_details && !empty($task['Task']['details'])){
                                echo '<hr align="left" style="width: 100%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                //echo '<u>Details:</u><br/>'; 
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
        else { //no $tasks
            echo 'No tasks matched your search parameters.  Please try refining your search terms.';
        }
    
    echo $this->Js->writeBuffer();
    ?>