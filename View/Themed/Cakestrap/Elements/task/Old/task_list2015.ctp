<?php        
        $today = date('Y-m-d');
                $today_str = strtotime($today);
                $owa = strtotime($today.'-1 day');
                
                $owfn = strtotime($today.'+1 week');
                
                foreach ($tasks as $task):
                    //Hide/show elements based on permissions.
                    $userControls = false;
                    if(in_array($task['Task']['team_id'], $controlled_teams)){ $userControls = true; }
                    $hasDueDate = false; $hasDueSoon = false; $hasActionable = false; $hasChange = false; $hasNewChange = false;
                    
                    if(!empty($task['Task']['due_date'])){
                        $dueString = strtotime($task['Task']['due_date']);
                        $hasDueDate = true;
                        if(($dueString >= $today_str) && ($dueString < $owfn)){ $hasDueSoon = true; }
                    }
                    if(!empty($task['Task']['actionable_type'])){ $hasActionable = true; }
                    if (!empty($task['Change'])){
                        $hasChange = true;
                        $numChange = 0;
                        foreach ($task['Change'] as $chg){
                            if (strtotime($chg['created'])  > $owa){
                                $hasNewChange = true;
                                $numChange++;
                            }
                        }  
                    }
                    $z0c = $z1c = $z2c = $z3c = $z4c = false;
?>

            <div class="row">
            <div class="col-md-12">
                <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" 
                    class="panel panel-default task-panel" 
                    style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <label class="task">
                                    <input type="checkbox" data-taskid="<?php echo $task['Task']['id']?>" id="hide_<?php echo ($task['Task']['id']); ?>">
                                    <?php
                                        echo $this->Time->format('M j H:i', $task['Task']['start_time']);
                                        echo '-';
                                        echo $this->Time->format('H:i', $task['Task']['end_time']);

                                        $time1 = strtotime($task['Task']['start_time']);
                                        $time2 = strtotime($task['Task']['end_time']);
                                        $diff = $time2 - $time1;
            
                                        // NOTE: LIMITATION Hides for duration less than one min.  May impact PRO
                                        // since their events last seconds [for everyone else though, it makes sense]
                                        if((60 < $diff)  && ($diff <= 3599)){
                                            echo '<br/>('.gmdate("i", $diff).' min)';
                                        }
                                        elseif($diff > 3599){
                                            echo '<br/>('.gmdate('H', $diff).' hr, '.gmdate('i',$diff).' min)';  
                                        }
                                    ?>
                                    </input>
                                </label>     
                            </div>
                            <div class="col-sm-3">
                                <?php
                                    $tt = $task['TasksTeam'];
                                    
                                    $ztlist = array();
                                    $zcount = count($teams)-1;
                                    for ($i=0; $i<$zcount; $i++) {
                                        $ztlist[$i] = array_keys($teams['Zone '.$i]);
                                    }
                                    
                                    //$tbz = Hash::extract($teams, '{s}.{n}');
                                    
                                    
                                    //$tbz = Hash::combine($teams, '{s}','{s}.{n}','{s}.{n}.zone');
                                    //debug($ztlist);
                                    
                                    
                                    
                                    $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
                                    $tt_p = Hash::extract($tt, '{n}[task_role_id=2].team_id');
                                    
                                    /*
                                    for($v = 0; $v < $zcount; $v++){
                                        if(in_array($ztlist[$v], $tt_p)){
                                            $z
                                        }
                                    }
                                     
                                     
                                    
                                    foreach ($ztlist as $z=>$team_ids){
                                        
                                    }
                                    */
                                    
                                    $tt_r = Hash::extract($tt, '{n}[task_role_id=3].team_id');

                                    $buttons13 = '';
                                    $buttons2 = '';
                            
                                    foreach ($task['TasksTeam'] as $k => $tat) {
                                        if($tat['task_role_id'] == 1){
                                            $buttons13.= '<span class="btn btn-leadt">'.$tat['team_code'].'</span>';
                                        }    
                                        elseif ($tat['task_role_id']==2) {
                                            $buttons2.= '<span class="btn btn-default btn-xxs">'.$tat['team_code'].'</span>';
                                        }
                                        elseif ($tat['task_role_id']==3) {
                                            $buttons13.= '<span class="btn btn-danger btn-xxs">'.$tat['team_code'].'</span>';
                                        }
                                    }
                                //This is a retarded way to show requests before pushes
                                $buttons = $buttons13.$buttons2;
                                
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                                echo $buttons;
                                ?> 
                                    
                            </div>
                            <div class="col-sm-7">
                                <div class="pull-right task-buttons">
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
                                                echo '<button type="button" class="btn btn-primary btn-xs xxs-bot-marg">';
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
                                        <div class="btn-group">
                                            <?php echo $this->Html->link(__('View'), array('controller'=>'tasks', 'action' => 'view', $task['Task']['id']), array('class' => 'btn btn-default btn-xs')); ?>
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view', $task['Task']['id'])); ?>
                                                </li>
                                                <?php if($userControls):?>
                                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', $task['Task']['id'])); ?></li>
                                                <?php endif; 
                                                if ($user_role >= 200 || $userControls):?>
                                                    <li class="divider"></li>
                                                    <li><?php echo $this->Form->postLink(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', $task['Task']['id']), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                                <?php endif;?> 
                                            </ul>
                                        </div>
                                </div><!--flags-->
                                <?php
                                    echo $task['Task']['short_description'].'<br/>';
                                    
                                    if ($show_details && !empty($task['Task']['details'])){
                                        echo '<hr align="left" style="width: 80%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                        echo '<u>Details:</u><br/>'; 
                                        echo nl2br($task['Task']['details']);
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--task row-->
        
        <?php endforeach; ?>   
