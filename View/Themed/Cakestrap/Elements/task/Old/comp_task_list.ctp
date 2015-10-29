<?php
/*
    if(empty($tasks)){
        $data = $this->requestAction(
            array('controller' => 'tasks', 'action' => 'compileUser'));
        
        $tasks = $data['tasks'];
        $paging = $data['paging']['Task'];
        
        // if the 'paging' variable is populated, merge it with the already present paging variable in $this->params. This will make sure the PaginatorHelper works
        if(!isset($this->params['paging'])) $this->params['paging'] = array();
        $this->params['paging'] = array_merge($data['paging'], $this->params['paging']);
    }
    
    $this->Paginator->options(array(
        'update' => '#taskListWrap',
        'evalScripts' => true,
        //'before' => $this->Js->get('#spinner')->effect('fadeIn', array('buffer' => false)),
        //'complete' => $this->Js->get('#spinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'tasks', 'action' => 'compileUser', $task)
    ));





*/

    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+1 week');
    
    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
    
    // Exclude Zone 0 & GMs
    for ($i=0; $i<5; $i++) {
        $ztlist[$i] = array_keys($teams['Zone '.$i]);
    }
    
    if (!empty($tasks)){ ?>
        <div class="tasks index">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <p><i class="fa fa-clock-o"></i> Due Date, <i class="fa fa-exchange"></i> Changes, <i class="fa fa-flag"></i> Action Item</p>
                </div>
            </div>
        </div>
    <?php
                
    foreach ($tasks as $task):
        
        $tid = $task['Task']['id']; 

        //Hide/show elements based on permissions.
        $userControls = false;
        if(in_array($task['Task']['team_id'], $controlled_teams)){ $userControls = true; }
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
                        <label class="task">
                            <input type="checkbox" data-taskid="<?php echo $task['Task']['id']?>" id="hide_<?php echo $tid;?>">
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
                            foreach ($ztlist as $znum => $tlist){
                                if (empty(array_diff($tlist, array_keys($tt_all)))){
                                    $buttons2.= '<span class="btn btn-default btn-xxs">Z'.$znum.'</span>';
                                    
                                    foreach($tlist as $tid){
                                        unset($tt_p_only[$tid]);
                                    }
                                }    
                            }
                            // Stragglers
                            foreach ($tt_p_only as $k => $team){
                                $buttons2.= '<span class="btn btn-default btn-xxs">'.$team.'</span>';
                            }                                    
                        //This is a lazy way to show requests before pushes
                        $buttons = $buttons13.$buttons2;
                        
                        echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                        echo $buttons;
                        ?> 
                    </div>
                    <div class="col-sm-7">
                        <?php
                            echo $task['Task']['short_description'].'<br/>';
                            
                            if ($show_details && !empty($task['Task']['details'])){
                                echo '<hr align="left" style="width: 100%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                echo '<u>Details:</u><br/>'; 
                                echo nl2br($task['Task']['details']);
                            }
                        ?>
                    </div>
                    <div class="col-sm-1">
                        <div class="row">
                     <!--<div class="pull-right task-buttons"> -->
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
                            <!--
                                <div class="btn-group">
                                    <?php echo $this->Html->link(__('Details'), array(
                                        'controller'=>'tasks', 
                                        'action' => 'details', $task['Task']['id']), 
                                        array(
                                            'data-tid'=>$task['Task']['id'], 
                                            'class' => 'btn btn-default btn-xs tl_2')); 
                                    ?>
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
                                </div>-->
                        <!--</div>--><!--flags-->
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
            ?>
        </ul><!-- /.pagination -->
    </div><!-- /.index -->
            
    <?php  }
        else { //no $tasks
            echo 'No tasks matched your search parameters.  Please try refining your search terms.';
        }
    
    echo $this->Js->writeBuffer();
    ?>