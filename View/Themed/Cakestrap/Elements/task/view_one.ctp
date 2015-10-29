<?php

$this->Js->buffer('

        $(".task-panel-heading").trigger("click");


');
    

    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    $filter = $this->Session->read('Auth.User.Compile.filter');
    //$filter_act = $this->Session->read('Auth.User.Compile.filter_act');
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
    
    
    if($filter == 3){
        $this->Js->buffer("

        
        
        
            $('div.acomp').hide();

            var shtml = '<i class=\"fa fa-eye\"></i> Show Completed';
            var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Completed';                                   
            
        
            $('#aiHideComp').on('click', function(){
                if($(this).hasClass('ai_hidden')){
                    $('div.acomp').show();
                    $(this).removeClass('ai_hidden').addClass('ai_shown');
                    $(this).html(hhtml);    
                }
                else{
                    $('div.acomp').hide();
                    $(this).removeClass('ai_shown').addClass('ai_hidden');
                    $(this).html(shtml); 
                }
                
            });
                    
        
        ");
    }






    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    
    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
        
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }
    
    if (!empty($task)){ ?>

        <div class="tasks index">
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
                    <div class="col-xs-3 col-sm-2">
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
                                    elseif(($diff >= 60) && ($diff < 3600)){
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
                    <div class="col-xs-4 col-sm-2">
                        <?php
                            echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                            echo $this->Ops->makeTeamsSig($task['TasksTeam'], $ztlist);
                        ?> 
                    </div>
                    <div class="col-xs-4 col-sm-7">
                        <?php
                            echo $task['Task']['short_description'].'<br/>';
                            
                            if ($show_details && !empty($task['Task']['details'])){
                               echo '<hr align="left" style="width: 100%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #aaa;"/>';
                                //echo '<h5><b>Details</b></h5>';
                                echo nl2br($task['Task']['details']);
                            }
                        ?>
                    </div>
                    <div class="col-xs-1">
                        <div class="row sm-bot-marg">
                            <div class="pull-right task-buttons" style="margin-right: 10px;">
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

            <?php 
                if (!empty($task['Parent']['id'])): ?>
                                
                <div class="row xs-bot-marg">
                    <div class="col-xs-2">
                        <div class="text-align-right">
                            <h5><i class="fa fa-external-link"></i> <b>Linked To</b></h5>

                        </div>
                    </div>
                    <div class="col-xs-10">
                        <?php 
                            if (!empty($task['Parent']['id'])){
                                $as = $task['Parent'];
                                //echo '<h5><i class="fa fa-external-link"></i> <b>Linked To</b></h5>';
                                echo $this->Ops->subtaskRowSingle($as);
                            } 
                        ?>
                    </div>
                </div>
            <?php 
                endif; 
            
                if (!empty($task['Assist'])): ?>
                <div class="row xs-bot-marg">
                    <div class="col-xs-2">
                        <div class="text-align-right">
                            <h5><i class="fa fa-link"></i> <b>Linked Teams</b></h5>
                        </div>
                    </div>
                    <div class="col-xs-10">
                        <?php 
                            if((!empty($task['Parent']['id']) || !empty($task['Assist']))){
                                //echo '<hr align="left" style="width: 100%; margin-bottom:5px; margin-top:8px; border-top: 1px solid #555;"/>';    
                            }
                            
                            if (!empty($task['Assist'])){
                                //echo '<h5><i class="fa fa-link"></i> <b>Incoming Links</b></h5>';

                                foreach($task['Assist'] as $as){
                                    echo $this->Ops->subtaskRowSingle($as);
                                }
                            }
                            if((empty($task['Parent']['id']) && empty($task['Assist'])) && (!empty($task['Comment']) && $view_type == 0)){
                                //echo '<hr align="left" style="width: 100%; margin-bottom:5px; margin-top:8px; border-top: 1px solid #555;"/>';    
                            }  
                        ?>
                    </div>
                </div>
                
                <?php endif; ?>

            </div>
  
            <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>"" style="display:none;"></div>    
        </div>
    </div>
    <?php 
    /*
        foreach($task['Assist'] as $as):?>
            <div class="wellops" style="border-left: 5px solid <?php echo ($as['task_color_code'])? $as['task_color_code'] : '#555'; ?>">
                <div class="row">
                    <div class="col-sm-2"><?php echo date('M d g:i A', strtotime($as['start_time']));?></div>
                    <div class="col-sm-1"><?php echo ' <b>'.$as['team_code'].'</b>';?></div>
                    <div class="col-sm-9"><?php echo ' ('.$as['task_type'].'): '.$as['short_description'];?></div>
                </div>
            </div>            
            
    <?php 
        endforeach;*/
    ?>
</div>
    
</div><!-- /.index -->
            
<?php  }
    else { //no $tasks
        echo 'No tasks matched your search parameters.  Please try refining your search terms.';
    }

    echo $this->Js->writeBuffer();
?>