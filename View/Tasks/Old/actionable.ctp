<?php




    $this->assign('page_title', 'Action Items ');
    //$this->assign('page_title_sub', 'Edit'); 
    

// Set User's Team ID:

    $user_tid = $this->Session->read('Auth.User.team_id');
    
        if (AuthComponent::user('id')){
            $controlled_teams = AuthComponent::user('Teams');
            $user_role = AuthComponent::user('user_role_id');
        }
        
    $show_threaded = false;
    $show_details = false;
    $color_act_pri = false;
        

?>
    <div class="row">
        <h2>
            <?php echo $this->fetch('page_title');?>

            <?php echo $this->Html->image('ajax-team-menu-spinner.gif', array('class'=>'', 'id' =>'ajax-menu-spinner')); ?>
        </h2>
    </div>
 
    <?php //echo $this->element('task/compile_options'); ?>

	<div id="page-content" class="row">
    
    <?php 
        if (!empty($tasks)){ ?>
            <div class="tasks index">
            
            <div class="row">
                <div class="pull-right">
                <div class="col-md-12">
                    <p>
                        <?php 
                            if(!$hide_completed){            
                                echo $this->Html->link('Hide Completed Action Items', array(
                                    'controller'=>'tasks', 
                                    'action'=>'actionable/hideCompleted'));
                            }
                            else{            
                                echo $this->Html->link('Show Completed Action Items', array(
                                    'controller'=>'tasks', 
                                    'action'=>'actionable'));
                                }
                        ?>
                    </p>
                </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="tasks-index" class="table table-hover table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th width="1%"> </th>
                            <th width="9%">
                                <?php echo $this->Paginator->sort('start_time','Date'); ?>
                           </th>
                            <th width="14%"><?php echo __('Teams'); ?></th>
                            <th width="9%"><?php echo __('Type'); ?></th>
                            <th width="34%"><?php echo __('Description'); ?></th>
                            <th width="8%">
                                <?php echo $this->Paginator->sort('due_date','Due Date'); ?>
                            </th>
                            <th width="8%">
                                <?php echo $this->Paginator->sort('actionable_type','Status'); ?>
                            </th>
                            <th width="8%">Icons</th>
                            <th width="9%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i=1;
                        $today = date('Y-m-d');
                        $today_str = strtotime($today);
                        $owa = strtotime($today.'-1 week');
                        
                        $owfn = strtotime($today.'+1 week');
                        
                        foreach ($tasks as $task):
                            $hasDueDate = $hasDueSoon = false;
                            $hasChange = false;
                            $hasNewChange = false;
                            
                            if(!empty($task['Task']['due_date'])){
                                $dueString = strtotime($task['Task']['due_date']);
                                $hasDueDate = true;
                                
                                if(($dueString >= $today_str) && ($dueString < $owfn)){
                                    $hasDueSoon = true;
                                }
                                
                            }
                            if (!empty($task['Change'])){
                                $hasChange = true;
                                foreach ($task['Change'] as $chg){
                                    if (strtotime($chg['created'])> $owa){
                                            $hasNewChange = true;
                                            break;
                                    }
                                }  
                            }
                        ?>
                        <tr <?php 
                            if($color_act_pri){
                                if($task['Task']['actionable_type_id']){
                                    echo 'class= "danger"';
                                }
                                elseif(!empty($task['Task']['due_date'])) {
                                    //$now_date = date('Y-m-d');
                                    $now = strtotime($today);
                                    $owfn = strtotime($today.'+1 week');
                                    
                                    $duedate = strtotime($task['Task']['due_date']);
                                    
                                    if (($duedate > $now) && ($duedate < $owfn)){
                                        echo 'class="warning"';    
                                    }
                                        
                                        
                                    
                                }
                            }
                            
                        ?>>
                        <td 
                        <?php 
                            if ($this->request->data('Task.color_team')){
                                echo 'style="background:'.$task['Task']['task_color_code'].'"';
                            }
                            else { echo 'style="background:#ccc"';}

                                    
                         ?>
                         >
                            </td> 
                            <td>
                                <?php 
                                    echo $this->Time->format('M d H:i', $task['Task']['start_time']);
                                    $time1 = strtotime($task['Task']['start_time']);
                                    $time2 = strtotime($task['Task']['end_time']);
                                    $diff = $time2 - $time1;

                                    if((60 < $diff)  && ($diff <= 3600)){
                                        echo '<br/>('.gmdate("i", $diff).' min)';
                                    }
                                    elseif($diff > 3600){
                                        echo '<br/>('.gmdate('H', $diff).' hr, '.gmdate('i',$diff).' min)';  
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
//                                $tt = $task['TasksTeam'];
//                                $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
//                                $tt_p = Hash::extract($tt, '{n}[task_role_id=2].team_code');
//                                $tt_r = Hash::extract($tt, '{n}[task_role_id=3].team_code');

                            $buttons = '';
                            
                            
                                foreach ($task['TasksTeam'] as $k => $tat) {
                                    if($tat['task_role_id'] == 1){
                                        $buttons.= '<span class="btn btn-leadt">'.$tat['team_code'].'</span>';
                                    }    
                                    
                                    elseif ($tat['task_role_id']==3) {
                                        $buttons.= '<span class="btn btn-danger btn-xxs">'.$tat['team_code'].'</span>';
                                    }
                                    
                                    elseif ($tat['task_role_id']==2) {
                                        $buttons.= '<span class="btn btn-default btn-xxs">'.$tat['team_code'].'</span>';
                                    }
                                }
                                 
                                 echo $buttons;
                        
                                ?>
                                

                            </td>
                            <td style="background:#e9e9e9">
                              
                                <?php echo $task['Task']['task_type']; ?>
                            </td> 
                            <td>
                                <?php echo $task['Task']['short_description']; 
                                
                                
                                if ($show_details && !empty($task['Task']['details'])){
                                    echo '<hr/>';
                                    echo nl2br($task['Task']['details']);
                                }
                                ?>
                            </td>
                            <td class="danger">
                                <b>
                                    <?php
                                        if($user_role >= 200){
                                        echo $this->Eip->input('Task.due_date', $task, array(
                                            'url'=>array('controller'=>'tasks', 'action' => 'eipDueDate'),
                                            'mode'=>'popup',
                                            'type'=>'text',
                                            'title'=>'Due Date'));
                                        }
                                        else {
                                            echo $task['Task']['due_date'];
                                        }                                             
                                    ?>
                                </b>
                            </td>
                            <td class="danger">
                                <b>
                                    <?php 
                                        if($user_role >= 200){
                                        echo $this->Eip->input('Task.actionable_type_id', $task, array(
                                            'url'=>array('controller'=>'tasks', 'action' => 'eipActionableType'),
                                            'type'=>'select',
                                            'display'=>$task['Task']['actionable_type'],
                                            'mode'=>'popup',
                                            'title'=>'Status',
                                            'source'=>$je_at,
                                            'showbuttons'=>false
                                            )); 
                                        }
                                        else{
                                            echo $task['Task']['actionable_type'];
                                        }
                                    ?>
                                </b>
                            </td>
                          
                            
                            <td>
                                <?php 
                                    if($hasChange && $hasNewChange){
                                        echo '<b><i class="fa fa-exchange fa-lg highlight-new"></i></b>&nbsp;';                                    
                                    }
                                    
                                    elseif($hasChange && !$hasNewChange){
                                        echo '<b><i class="fa fa-exchange fa-lg text-muted"></i></b>&nbsp;';
                                    }
                                    
                                    if($hasDueDate && $hasDueSoon){
                                        echo '<b><i class="fa fa-clock-o fa-lg highlight-duesoon"></i></b>&nbsp;';
                                    }

                                    elseif($hasDueDate && !$hasDueSoon){
                                        echo '<b><i class="fa fa-clock-o fa-lg text-muted"></i></b>&nbsp;';
                                    }
                                ?>
                            </td>
                            <td>
                                        <!-- Split button -->
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
                                                <?php if(in_array($task['Task']['team_id'], $controlled_teams)):?>
                                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', $task['Task']['id'])); ?></li>
                                                    
                                                    <?php endif; 
                                                    
                                                    if ($user_role >= 500):?>
                                                        <li class="divider"></li>
                                                        <li><?php echo $this->Html->link(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', $task['Task']['id'])); ?></li>        
                                                        <li><?php echo $this->Form->postLink(__('Delete2'), array('action' => 'delete2', $task['Task']['id']), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                                <?php endif;?> 
                                                
                                                
                                            </ul>
                                        </div>
                                
                            </td>
                        </tr>
                    <?php $i++; endforeach; ?>
                </tbody>
            </table>
            </div><!-- /.table-responsive -->
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
        else {
            echo 'No tasks matched your search parameters.  Please try refining your search terms.';
        }
    ?>
    </div>	    
	    




<?php 
    echo $this->Js->writeBuffer();
?>
