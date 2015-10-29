<?php

    echo $this->Html->css('timeline');
    
    $show_user = false;
    if(isset($userControls) && !empty($userControls)){
        $show_user = true;
    }
    //$show_user = true;

    if(!$this->request->param('isAjax')){
        $this->extend('/Common/Change/changes_by_task');
        $tdat = $this->requestAction(array('controller'=>'tasks', 'action'=>'getTaskById',$task));
        $this->assign('page_title', 'Changes for Task #'.$task.' ');
        $this->assign('page_title_sub', $tdat['Task']['short_description']);
    } 
 
      if(empty($changes)){
        $data = $this->requestAction(
            array('controller' => 'changes', 'action' => 'pageChanges', $task) 
        );
        
        $changes = $data['changes'];
        
        // if the 'paging' variable is populated, merge it with the already present paging variable in $this->params. This will make sure the PaginatorHelper works
        if(!isset($this->params['paging'])) $this->params['paging'] = array();
        $this->params['paging'] = array_merge($data['paging'], $this->params['paging']);
        //$this->params['paging'] = array_merge($data['paging'], array());
        //$this->params['paging'] = $data['paging'];
        //$this->params['paging'] = $data['paging']['Change'];
    }
    
    $this->Paginator->options(array(
        //'model'=>'change',
        'update' => '#chgcontent'.$task,
        //'evalScripts' => true,
        'before' => $this->Js->get('#chgSpinner'.$task)->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('#chgSpinner'.$task)->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'changes', 'action' => 'pageChanges', $task)
    ));
    
    $today = date('Y-m-d');
    //$today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
?>

<div id="chgcontent<?php echo $task?>">
    <div class="col-sm-12">    
    <?php 
        if (!empty($changes)){
            //$this->log($this->params['paging']); 
    ?> 
        <div class="row">
            <div class="timeline-centered">
                <?php 
                    foreach ($changes as $change_arr):
                        $change = $change_arr['Change'];
                        // Task Detail Chanages
                        if($change['change_type_id'] < 200 && ($change['change_type_id']>100)){
                            if($change['change_type_id'] == 101){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-info"><i class="fa fa-lg fa-clock-o"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p><b>Task Start Time</b><br/>
                                                <?php 
                                                    
                                                    if (isset($change['old_val']) && isset($change['new_val'])){
                                                        echo 'Changed from <b>'.date('M j H:i:s', strtotime($change['old_val'])).'</b> to <b>'.date('M j H:i:s', strtotime($change['new_val'])).'</b>.';
                                                    }
                                                    elseif(!isset($change['old_val']) && isset($change['new_val'])){
                                                        echo 'Set to <b>'.date('M j H:i:s', strtotime($change['new_val'])).'</b>.';
                                                    }
                                                    elseif(!isset($change['new_val'])){
                                                        echo 'Removed.';
                                                    }
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>        
                <?php       }
                            // Short Desc
                            elseif($change['change_type_id'] == 102){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-info"><i class="fa fa-lg fa-list-ul"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p>
                                                <b>Task Description Changed</b>
                                                <?php 
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>        
                    <?php   } 

                            // Linked to Parent
                            elseif($change['change_type_id'] == 141){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-warning"><i class="fa fa-lg fa-link"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p><b>Added Linked Task</b><br/>
                                                <?php 
                                                    if ((!empty($change['var1'])) && (!empty($change['var2']))){
                                                        echo 'Linked to <b>('.$change['var1'].')</b> '.$change['var2'].'</b>.';
                                                    }
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>        
                    <?php   }                    
                            // UNlinked to Parent
                            elseif($change['change_type_id'] == 142){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-warning"><i class="fa fa-lg fa-link"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p><b>Removed Linked Task</b><br/>
                                                <?php 
                                                    if ((!empty($change['var1'])) && (!empty($change['var2']))){
                                                        echo 'Removed link to <b>('.$change['var1'].')</b> '.$change['var2'].'</b>.';
                                                    }
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>        
                    <?php   }                    
                            
                            // Actionable Type
                            elseif($change['change_type_id'] == 150){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-warning"><i class="fa fa-lg fa-flag"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p><b>Action Item Status: </b>
                                                <?php 
                                                    
                                                    if ((!empty($change['old_val'])) && (!empty($change['new_val']))){
                                                        echo 'Changed from <b>'.$change['old_val'].'</b> to <b>'.$change['new_val'].'</b>.';
                                                    }
                                                    elseif(empty($change['old_val']) && !empty($change['new_val'])){
                                                        echo 'Set to <b>'.$change['new_val'].'</b>.';
                                                    }
                                                    elseif(empty($change['new_val'])){
                                                        echo 'Removed from Action Item list.';
                                                    }
                                                    
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>        
                    <?php   }
                            // Due Date
                            elseif($change['change_type_id'] == 160){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner ">
                                        <div class="timeline-icon bg-warning"><i class="fa fa-lg fa-bell-o"></i>
                                        </div>
                                        <div class="timeline-label success">
                                            <p><b>Due Date: </b>
                                                <?php 
                                                    if (isset($change['old_val']) && isset($change['new_val'])){
                                                        echo 'Changed from <b>'.date('M j', strtotime($change['old_val'])).'</b> to <b>'.date('M j', strtotime($change['new_val'])).'</b>.';
                                                    }
                                                    elseif(!isset($change['old_val']) && isset($change['new_val'])){
                                                        echo 'Set to <b>'.date('M j', strtotime($change['new_val'])).'</b>.';
                                                    }
                                                    elseif(!isset($change['new_val'])){
                                                        echo 'Removed.';
                                                    }
        
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>                                
                                        </div>
                                    </div>
                                </article> 
                                 
                    <?php   }
                            // Moved due to time linked parent move
                            elseif($change['change_type_id'] == 170){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-danger"><i class="fa fa-lg fa-clock-o"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p><b>Start Time Changed By Time Linked Task</b><br/>
                                                <?php 
                                                    if (isset($change['old_val']) && isset($change['new_val'])){
                                                        echo 'Moved from <b>'.date('M j H:i:s', strtotime($change['old_val'])).'</b> to <b>'.date('M j H:i:s', strtotime($change['new_val'])).
                                                        '</b> due to change in time linked task <b>('.$change['var1'].')</b> '.
                                                        $change['var2'];
                                                    }
                                                    elseif(!isset($change['old_val']) && isset($change['new_val'])){
                                                        echo 'Set to <b>'.date('M j', strtotime($change['new_val'])).'</b>.';
                                                    }
                                                    elseif(!isset($change['new_val'])){
                                                        echo 'Removed.';
                                                    }
        
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>        
                    <?php   }
                        }
                    
                        if($change['change_type_id'] < 300 && ($change['change_type_id']>200)){
                            if($change['change_type_id'] == 210){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-lead"><i class="fa fa-lg fa-group"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p>Team Role: 
                                                <?php 
                                                    echo $this->Ops->chgMessage($change); 
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>                                
                                        </div>
                                    </div>
                                </article>   
                    <?php   }
                            elseif($change['change_type_id'] == 220){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-default"><i class="fa fa-lg fa-group"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p><b>Team Role:</b> 
                                                <?php 
                                                    echo $this->Ops->chgMessage($change);
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>   
                    <?php   }
                            elseif($change['change_type_id'] == 230){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-secondary"><i class="fa fa-lg fa-group"></i>
                                        </div>
                                        <div class="timeline-label">
                                            <p><b>Team Role:</b> 
                                                <?php 
                                                    echo $this->Ops->chgMessage($change);
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
        
                                        </div>
                                    </div>
                                </article>   
                    <?php   }
                            elseif($change['change_type_id'] == 240){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-success"><i class="fa fa-lg fa-group"></i>
                                        </div>
                                        <div class="timeline-label">
                                           <p><b>Team Role:</b> 
                                                <?php 
                                                    echo $this->Ops->chgMessage($change);
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>   
                    <?php   }
                            elseif($change['change_type_id'] == 299){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-norole"><i class="fa fa-lg fa-group"></i>
                                        </div>
                                        <div class="timeline-label">
                                           <p><b>Team Role:</b> 
                                                <?php 
                                                    echo $this->Ops->chgMessage($change);
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>   
                    <?php   }
                        } // End team role section
        
                        if($change['change_type_id'] < 400 && ($change['change_type_id']>300)){
                             
                            if($change['change_type_id'] == 301){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-info"><i class="fa fa-lg fa-plus"></i>
                                        </div>
                                        <div class="timeline-label">
                                           <p><b>New Task Linkage</b><br/> 
                                                <?php 
                                                    if ((!empty($change['var1'])) && (!empty($change['var2']))){
                                                        echo '<b>'.$change['var1'].'</b> linked a new task "'.$change['var2'].'".';
                                                    }
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>   
                    <?php   } 
         
                            elseif($change['change_type_id'] == 302){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-info"><i class="fa fa-lg fa-minus"></i>
                                        </div>
                                        <div class="timeline-label">
                                           <p><b>Linkage Removed</b><br/> 
                                                <?php 
                                                    if ((!empty($change['var1'])) && (!empty($change['var2']))){
                                                        echo '<b>'.$change['var1'].'</b> unlinked task "'.$change['var2'].'".';
                                                    }
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>   
                    <?php   }                    
                                        
                            elseif($change['change_type_id'] == 310){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-info"><i class="fa fa-lg fa-minus"></i>
                                        </div>
                                        <div class="timeline-label">
                                           <p><b>Task Role Cancelled</b><br/> 
                                                <?php 
                                                    if ((!empty($change['var1'])) && (!empty($change['var2']))){
                                                        echo 'Linked Task removed because <b>'.$change['var1'].'</b> cancelled team\'s role in "'.$change['var2'].'".';
                                                    }
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>   
                    <?php   } 
                        } // End Linkage section
                        
                        if(($change['change_type_id'] < 500) && ($change['change_type_id']>400)){   
                            if($change['change_type_id'] == 401){ ?>
                                <article class="timeline-entry">
                                    <div class="timeline-entry-inner">
                                        <div class="timeline-icon bg-info"><i class="fa fa-lg fa-comment-o"></i>
                                        </div>
                                        <div class="timeline-label">
                                           <p><b>New Comment: </b> 
                                                <?php 
                                                    if (!empty($change['var1'])){
                                                        echo '<b>'.$change['var1'].'</b> posted a comment.';
                                                    }
                                                    echo '<br><small>'.$this->Time->timeAgoInWords(strtotime($change['created']), array('format'=>'M j'));
                                                    if($show_user){
                                                        echo ' by '.$change['user_handle'];
                                                    }
                                                    echo '</small>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>   
                <?php   }        
                    }   // Comments
                    endforeach;
                ?>
            </div>
        </div>
<?php
/*
         
                <table class="table table-condensed xxs-bot-marg">
                    <thead>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Team</th>
                        <th>Old Val</th>
                        <th>New Val</th>
                        <th>Var1</th>
                        <th>Var2</th>
                        <th>Text</th>
                        <?php if($this->Session->read('Auth.User.user_role_id') >= 500){
                            //echo '<th>User</th>';
                        }
                        ?>
                    </thead>
                    <tbody>  
                        <?php 
                            foreach ($changes as $change):
                                $chg_is_new = false;
                                $ccreate = strtotime(date('Y-m-d', strtotime($change['Change']['created'])));
                                if($ccreate > $owa){
                                    $chg_is_new = true;
                                }
                        ?>
                            <tr <?php if($chg_is_new){ echo ' class="success"';} ?>>
                                <td><?php echo $this->Time->format('M j H:i:s', $change['Change']['created']);?></td>
                                <td><?php echo $change['Change']['change_type_id']; ?></td>
                                <td><?php echo $change['Change']['team_id'];?></td>
                                <td><?php echo $change['Change']['old_val'];?></td>
                                <td><?php echo $change['Change']['new_val'];?></td>
                                <td><?php echo $change['Change']['var1'];?></td>
                                <td><?php echo $change['Change']['var2'];?></td>
                                <td><?php echo $change['Change']['text'];?></td>
                                
                             
                                <?php
                                    if($this->Session->read('Auth.User.user_role_id') >= 500){
                                        //echo '<td>'.$change['Change']['user_handle'].'</td>';
                                    }
                                ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>    
                </table>    
    
                 
   */
   
   
        if(($this->params['paging']['Change']['pageCount']) >=0):
   
   ?>
                         
               
                
                
            <ul class="pagination xxs-bot-marg">
                    <?php  
                        echo $this->Paginator->prev('< ' . __('Newer'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li','disabledTag' => 'a'));
                        //echo $this->Paginator->numbers(array('modulus'=>1, 'separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                        echo $this->Paginator->next(__('Older') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo '<span id="chgSpinner'.$task.'" class="chgSpinner" style="display: none; margin-left: 5px; vertical-align: middle; float: left;">';
                        echo $this->Html->image('ajax-loader_old.gif', array('id' => 'spinner_img'.$task, ));
                        echo '</span>';
                    ?>
                </ul><!-- /.pagination -->
        <?php
        endif;
         
            }   else{  //No changes
                    echo '<div class="alert alert-info slim-alert"><i class="fa fa-lg fa-info"></i>&nbsp; There are no changes listed for this task.</div>';
                } 
    
                
                ?>
    </div>
</div><!-- end panelsuccess-->
<!-- Timeline: http://www.bootsnipp.com/snippets/featured/single-column-timeline-dotted -->



<?php  echo $this->Js->writeBuffer(); //Necessary cuz we don't use a layout ?>