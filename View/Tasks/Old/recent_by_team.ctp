<?php 
    //$this->extend('/Common/Task/view_task');
    $this->assign('page_title', 'Task ');
    $this->assign('page_title_sub', 'Changes'); 
    
    
  //debug($task);  
    
    /*
    $this->Paginator->options(array(
        'update' => '#ajax-content-load',
        'evalScripts' => true,
        'before' => $this->Js->get('#spinner')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('#spinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'changes', 'action' => 'pageChanges', $task['Task']['id'])
    ));
*/
//debug($parent_changes);
//debug($parent_changes);
//debug($due_soon);

    if(!empty($due_soon)):

?> 

<div class="panel panel-danger">
  <div class="panel-heading">
    <h3 class="panel-title"><b>Tasks With Due Dates Within a Week</b></h3>
  </div>
  <div class="panel-body">

   
<div class="panel-group" id="accordion1">

    <?php 
    $p = 1;
    foreach ($due_soon as $st): ?>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h5 class="panel-title">
        
          <?php 
          echo date('M d H:i', strtotime($st['Task']['start_time']));
          echo '&nbsp;&nbsp;('.$st['Task']['team_code'].') ';
          echo $st['Task']['task_type']. ': ';
          echo $st['Task']['short_description'];
          ?>
        <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view',$st['Task']['id']), array('class'=>'btn btn-default btn-xs pull-right'));?>
      </h5>
    </div>

  </div>
    <?php $p++;
        endforeach; 
        
    ?>
  </div> <!--end accordion-->
  </div>
</div>

    <?php endif; 









    
  








    if(!empty($parent_changes)):?>
<div class="panel panel-yh">
  <div class="panel-heading">
    <h5 class="panel-title"><b>Changes in Tasks Asking For Your Help</b></h5>
  </div>
  <div class="panel-body">
    
<div class="panel-group" id="accordion2">

    <?php 
    $j = 1;
    foreach ($parent_changes as $st): ?>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion2" href="#parcollapse<?php echo $j;?>">
          <?php 
          echo date('M d H:i', strtotime($st['Task']['start_time']));
          echo '&nbsp;&nbsp;('.$st['Task']['team_code'].') ';
          echo $st['Task']['task_type']. ': ';
          echo $st['Task']['short_description'];
          
          
          ?>
         
        </a><b class="caret"></b> 

          <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view',$st['Task']['id']), array('class'=>'btn btn-default btn-xs pull-right'));?>
      </h4>
    </div>
    <div id="parcollapse<?php echo $j;?>" class="panel-collapse collapse">
      <div class="panel-body">
        <?php
        
        if (!empty($st['Change'])){ ?> 
                <table id="tasks" class="table table-condensed">
                    <thead>
                        <th width="20%">Date</th>
                        <th width="20%">Type</th>
                        <th width="45%">Change</th>
                        <th width="15%">User</th>
                    </thead>
                    <tbody>  
                        
                        
                        <?php 
                        //debug($tasks);
                        foreach ($st['Change'] as $chg):
                            
                            //
                            ?>
                            
                            <tr 
                            <?php if(($chg['change_type_id'] >=300) ||($chg['change_type_id'] <200)){
                                echo 'class="danger"';
                                
                            }
                            ?>
                            
                            >
                                <td><?php echo date('M d', strtotime($chg['created'])); ?></td>
                                <td><?php echo $chg['change_type']; ?></td>
                                <td><?php echo $chg['text'];?></td>
                                <td><?php echo $chg['user_handle'];?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>    
                </table>
                <?php }?>    
        
        
       
        
        
        
        </div>
    </div>
  </div>
    <?php $j++;
        endforeach; 
        
    ?>
</div> <!--end accordion--> 
   </div>
</div>

    <?php endif; 
  

if(!empty($subtask_changes)):

?> 

<div class="panel panel-warning">
  <div class="panel-heading">
    <h3 class="panel-title"><b>Changes in Tasks Other Teams Are Doing For You</b></h3>
  </div>
  <div class="panel-body">

   
<div class="panel-group" id="accordion1">

    <?php 
    $i = 1;
    foreach ($subtask_changes as $st): ?>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion1" href="#collapse<?php echo $i;?>">
          <?php 
          echo date('M d H:i', strtotime($st['Task']['start_time']));
          echo '&nbsp;&nbsp;('.$st['Task']['team_code'].') ';
          echo $st['Task']['task_type']. ': ';
          echo $st['Task']['short_description'];
          ?>
        </a><b class="caret"></b> <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view',$st['Task']['id']), array('class'=>'btn btn-default btn-xs pull-right'));?>
      </h4>
    </div>
    <div id="collapse<?php echo $i;?>" class="panel-collapse collapse">
      <div class="panel-body">
        <?php
        
        if (!empty($st['Change'])){ ?> 
                <table id="tasks" class="table table-condensed">
                    <thead>
                        <th width="10%">Date</th>
                        <th width="20%">Type</th>
                        <th width="55%">Change</th>
                        <th width="15%">User</th>
                    </thead>
                    <tbody>  
                        
                        
                        <?php 
                        //debug($tasks);
                        foreach ($st['Change'] as $chg):
                            
                            //
                            ?>
                            
                            <tr 
                            <?php if(($chg['change_type_id'] >=300) ||($chg['change_type_id'] <200)){
                                echo 'class="danger"';
                                
                            }
                            ?>
                            
                            >
                                <td><?php echo date('M d', strtotime($chg['created'])); ?></td>
                                <td><?php echo $chg['change_type']; ?></td>
                                <td><?php echo $chg['text'];?></td>
                                <td><?php echo $chg['user_handle'];?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>    
                </table>
                <?php }?>    
        

        
        
        
        </div>
    </div>
  </div>
    <?php $i++;
        endforeach; 
        
    ?>
  </div> <!--end accordion-->
  </div>
</div>

    <?php endif; ?>





<!--
<div class="panel panel-primary" id="chgcontent">
    <div class="panel-heading"><i class="fa fa-sitemap"></i>&nbsp;&nbsp;<b>Changes</b></div>
    <div class="panel-body">
        <?php 
            if (!empty($parent_changes)){ ?> 
                <table id="tasks" class="table table-condensed">
                    <thead>
                        <th width="10%">Date</th>
                        <th width="20%">Type</th>
                        <th width="55%">Change</th>
                        <th width="15%">User</th>
                    </thead>
                    <tbody>  
                        
                        
                        <?php 
                        debug($tasks);
                        foreach ($tasks as $task):
                            
                            //
                            ?>
                            
                            <tr>
                                <td><?php echo $task['Change']['change_type']; ?></td>
                                <td><?php echo $task['Change']['text'];?></td>
                                <td><?php echo $task['Change']['user_handle'];?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>    
                </table>    
   
                <ul class="pagination">
                    <?php
                        echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                        echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo '<span id="spinner" style="display: none; float: left;">';
                        echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner', ));
                        echo '</span>';
                    ?>
                </ul><!-- /.pagination -->
        <?php 
            }   else{  //No changes
                    echo '<span class="fa fa-info"></span>&nbsp; &nbsp; There are no changes listed for this task.';
                } ?>
    </div><!-- end panelbody-->
</div><!-- end panelsuccess-->



<?php  //echo $this->Js->writeBuffer(); //Necessary cuz we don't use a layout ?>

      
                                               

