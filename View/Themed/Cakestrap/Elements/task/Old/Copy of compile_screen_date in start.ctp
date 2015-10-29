<?php

    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    $filter_due_date = $this->Session->read('Auth.User.Compile.filter_due_date');
    $user_shift = $this->Session->read('Auth.User.Timeshift');
    
    //$this->log("params from compile");
    //$this->log($this->Paginator->params());
    //$this->log($this->params->paging);

    $this->Paginator->options(array(
        'update' => '#taskListWrap',
        'evalScripts' => true,
        'before' => $this->Js->get('#csSpinner')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('#csSpinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'tasks', 'action' => 'compileUser')
    ));
    
    
    
    $this->Js->buffer("


    ");






    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    
    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
    
    // Exclude Zone 0 & GMs
    for ($i=0; $i<5; $i++) {
        $ztlist['Z'.$i] = array_keys($teams['Zone '.$i]);
    }
    $ztlist['GMS'] = array_keys($teams['GMs']);
    
    // Hold days of tasks
    $cur_t_day = '';
    $prev_t_day = '';
    
    
    //debug($ztlist);
    if (!empty($tasks)){ ?>
        <div class="tasks index">
        <?php if($filter_due_date == 1):?>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="alert alert-info" role="alert">
                        <b>Note: </b> You're filtering tasks with due dates.  In this mode, tasks are ordered by ascending due date to highlight things needing attention.
                    </div>
                </div>
            </div>
        <?php endif; ?>
            <div class="row">
                <div class="col-md-12">
                    <div class = "pull-right">
                      <i class = "fa fa-clock-o"></i> Due Date, <i class="fa fa-exchange"></i> Changes, <i class="fa fa-flag"></i> Action Item</p>
                    </div>
                </div>
            </div>
    <?php
    
    $last_t_day = date('Y-m-d', strtotime($tasks[0]['Task']['start_time']));
    $curr_t_day = '';            
    foreach ($tasks as $task):

        $daysAreSame = false;
        
        $curr_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        //debug($last_t_day);
        //debug($curr_t_day);        
        if($last_t_day == $curr_t_day){
            $daysAreSame = true;
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
        
        // Did date change?
        //$

?>
<?php 
            if(!$daysAreSame){
                echo '<h4 class="great">'.date('M j', strtotime($curr_t_day)).'</h4>';
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
                        <div class="taskTs checkbox checkbox-circle checkbox-success">
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
                            ?>
                                
                            </label>
                        </div>
                        
                        
                        <!--
                        <label class="taskTimeshift">
                            <input class="tsCheck <?php if($inUsrShift){echo 'checked';} ?>" type="checkbox"
                                <?php if(!$userControls){echo 'disabled="disabled"';} ?>
                                <?php if($inUsrShift){echo 'checked="checked"';} ?> 
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
                </label> -->    
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
  
                <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>"" style="display:none;">
            </div>    
        </div>
    
      </div>

</div>
<?php

    $last_t_day = $curr_t_day; 
 endforeach;
?>

<p><small>
            <?php
                echo $this->Paginator->counter(array(
                    'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                ));
            ?>
        </small></p>
        <ul class="pagination">
            <?php
                echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                
                echo '<span id="csSpinner" class="csSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
                //echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner_img', ));
                echo $this->Html->image('ajax-loader_old.gif');
                echo '</span>'; 
            
            ?>
        </ul><!-- /.pagination -->
        <div id="pageNum" style="visibility:hidden"><?php echo $this->Paginator->param('page');?></div>
    </div><!-- /.index -->
            
    <?php  }
        else { //no $tasks
            echo 'No tasks matched your search parameters.  Please try refining your search terms.';
        }
    
    echo $this->Js->writeBuffer();
    ?>