<?php

    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    
    $controlled_teams = AuthComponent::user('TeamsList');
    
    //$this->log("params from compile");
    //$this->log($this->Paginator->params());
    //$this->log($this->params->paging);

    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    
    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
    
    // Exclude Zone 0 & GMs
    $tlist=array();
    foreach ($teams as $zone){
        foreach($zone as $tid=>$code){
            $tlist[$tid] = $code;
        } 
    }
    
    
    //debug($ztlist);
    if (!empty($task)){ ?>
        <div class="tasks index">

    <?php
                
        $tid = $task['Task']['id']; 

        //Hide/show elements based on permissions.
        $userControls = false;
        if(in_array($task['Task']['team_id'], $user_controls)){ $userControls = true; }
        
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

<div class="row">
    <div class="col-md-12">
        <!--<h4 class="great">Nov 11</h4>-->
        <div 
            data-taskid="<?php echo ($task['Task']['id']); ?>" 
            id="tid<?php echo ($task['Task']['id']); ?>" 
            class="panel panel-default task-panel" 
            style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
         >
            <div class="panel-heading task-panel-heading" data-tid="<?php echo ($task['Task']['id']); ?>">
                <div class="row">
                    <div class="col-sm-2">
                        <label class="taskTimeshift task">
                            <input class="tsCheck" type="checkbox"
                                <?php if(!$userControls){echo 'disabled="disabled"';} ?> 
                                data-stime="<?php echo strtotime($task['Task']['start_time']); ?>" 
                                data-etime="<?php echo strtotime($task['Task']['end_time']); ?>" 
                                data-tid="<?php echo $task['Task']['id']?>" 
                                id="hide<?php echo $tid;?>">
                        <?php 
                                
                            echo $this->Time->format('M j H:i', $task['Task']['start_time']);
                            echo '-';
                            echo $this->Time->format('H:i', $task['Task']['end_time']);

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
                        if($userControls){
                            echo '</input>';
                        }
                        ?>
                </label>     
                    </div>
                    <div class="col-sm-2">
                        <?php
                            $tt = $task['TasksTeam'];
                            $buttons13 = '';
                            $buttons2 = '';
                            
                            $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
                            $tt_p = Hash::combine($tt, '{n}[task_role_id=2].team_id', '{n}[task_role_id=2].team_code');                                    
                            $tt_r = Hash::combine($tt, '{n}[task_role_id=3].team_id', '{n}[task_role_id=3].team_code');
                            $tt_all = Hash::combine($tt, '{n}.team_id', '{n}.team_code');
                            
                            // Pushed ONLY
                            $tt_p_only = array_diff($tt_p, $tt_r);

                            foreach ($tt_l as $tid => $tcode){
                                $buttons13.= '<span class="btn btn-leadt">'.$tcode.'</span>';    
                            }                                    
                            
                            foreach ($tt_r as $tid => $tcode){
                                $buttons13.= '<span class="btn btn-danger btn-xxs">'.$tcode.'</span>';    
                            }
                            // If a task involves a whole zone's teams, shorten the list by writing
                            // out a "Z#" button insted of a list of the teams.
                            // Finally, unset the full zones' teams from the list, so we can output
                            // the stragglers later

                            $ak_tta = array_keys($tt_all);
                            $fullZones = array();
                            
                            foreach ($ztlist as $znum => $tlist){
                                $curDiff = array_diff($tlist, $ak_tta);
                                if (empty($curDiff)){
                                    $fullZones[]=$znum;
                                    //$buttons2.= '<span class="btn btn-default btn-xxs">Z'.$znum.'</span>';
                                    
                                    foreach($tlist as $tid){
                                        unset($tt_p_only[$tid]);
                                    }
                                }    
                            }
                            
                            //debug($fullZones);

                            if(count($fullZones) >= 5){
                                $buttons2.= '<span class="btn btn-default btn-xxs">ALL</span>';
                                
                                if(in_array('GMS', $fullZones)){
                                    $buttons2.= '<span class="btn btn-default btn-xxs">GMS</span>';
                                }
                            }
                            
                            else{
                                foreach ($fullZones as $zone){
                                    $buttons2.= '<span class="btn btn-default btn-xxs">'.$zone.'</span>';    
                                }
                            }
                            // Stragglers
                            foreach ($tt_p_only as $team){
                                $buttons2.= '<span class="btn btn-default btn-xxs">'.$team.'</span>';
                            }                                    
                        //This is a lazy way to show requests before pushes
                        $buttons = $buttons13.$buttons2;
                        
                        echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                        echo $buttons;
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
  
            <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>">
            <?php echo $this->element('task/task_details', array('task_det'=>$task, 'tid'=>$task['Task']['id'])); ?>
            </div>    
        </div>
    
      </div>

</div>
    </div><!-- /.index -->
            
    <?php  }
    echo $this->Js->writeBuffer();
    ?>