<?php 

    $this->extend('/Common/Task/view_task');
    $this->assign('page_title', 'Task');
    $this->assign('page_title_sub', 'Subtasks'); 

//Teams that are listed as participating, but have no subtasks
if (is_array($all_ateams) && is_array($subtasks)){
    $diff = array_diff($all_ateams, array_keys($subtasks));
    
    
}

//echo $this->Html->script('core', array('inline'=>false));
// Credit: http://codepen.io/chriscoyier/pen/wLGDz
$this->Js->buffer('

$("#subtasksbyteam td").hover(function() {

  $el = $(this);
  
  $el.parent().addClass("hoverOnRowspan");

  if ($el.parent().has(\'td[rowspan]\').length == 0)
    
    $el
      .parent()
      .prevAll(\'tr:has(td[rowspan]):first\')
      .find(\'td[rowspan]\')
      .addClass("hoverOnRowspan");

}, function() { 
      
  $el
    .parent()
    .removeClass("hoverOnRowspan")
    .prevAll(\'tr:has(td[rowspan]):first\')
    .find(\'td[rowspan]\')
    .removeClass("hoverOnRowspan");

});

');
?>
<div class="panel panel-yh">
    <div class="panel-heading"><i class="fa fa-sitemap fa-lg"></i>&nbsp;&nbsp;<b>Subtasks By Team</b></b></div>
    <div class="panel-body">
    <?php 
        if (!empty($subtasks)){ ?> 
            <div class="panel panel-success">
            <div class="panel-heading"><b>Teams With Existing Subtasks</b></div>
                    
            <div class="panel-body">
                <table id="subtasksbyteam" class="table table-condensed">
                    <thead>
                        <th width="5%">Team</th>
                        <th width="15%">Time</th>
                        <th width="10%">Type</th>
                        <th width="15%">Teams Involved</th>
                        <th width="55%">Short Description</th>
                    </thead>
                    <tbody>  
                        <?php 
                        
                        foreach ($subtasks as $team=>$tasks): 
                        $num_links_from_team = count($tasks);

                            foreach ($tasks as $k=>$task): ;?>
                                <tr>    
                                    <?php
                                    // exclude <td> in non row-span rows 
                                    if ($k==0){ 
                                        echo '<td rowspan='.$num_links_from_team.'">'; echo '<b>'.$team.'</b></td>'; } ?>
                                        <td><?php echo $this->Time->format('M d - h:m A', $task['Task']['start_time']).'&nbsp;&nbsp;';?></td>
                                        <td><?php echo $task['Task']['task_type'];?></td>
                                        <td><?php echo $this->element('/task/team_badges', array('task'=>$task));?></td>
                                        <td><?php echo $this->Html->link($task['Task']['short_description'], array('controller'=>'tasks', 'action'=>'view',$task['Task']['id']));?>
                                </tr>    
                        <?php endforeach; endforeach;?>
                    </tbody>    
                </table>    
            </div><!-- end panelbody-->
            </div><!--end panel success-->

            
                <?php 
                    if (!empty($diff)){ ?>
                        <div class="panel panel-danger">
                        <div class="panel-heading"><b>Teams Without Subtasks</b></div>
                        <div class="panel-body">
                      
                        <?php echo '<i class="fa fa-info-circle"></i>&nbsp; &nbsp;The following teams are participating in this task, but have no subtasks linked to it: ';
                            $team_diff = array();

                            foreach ($diff as $k=>$team){
                                    $team_diff[] = $team;
                                }
                           echo '<b>'.implode(', ', $team_diff) .'</b>.</div></div>';
                        }  
            
            }
            
            elseif (empty($subtasks) && !empty($all_ateams)) { 
                
                
                    //subtasksByTeam is empty, but has teams listed
                    
                    ?>
                    
                    <div class="panel panel-danger">
                    <div class="panel-heading"><b>Participating, Unlinked</b></div>
                    <div class="panel-body">
                
                        <?php
            
                        echo '<i class="fa fa-info-circle"></i></span>&nbsp; &nbsp;The following teams are assisting in this task, but have no subtasks linked to it: ';

                        $team_diff = array();
                        foreach ($all_ateams as $k=>$team){
                        $team_diff[] = $team;
                        }
                                
                        echo '<b>'.implode(', ', $team_diff) .'</b>';
                        echo '</div></div>';  
                }

            else{  //No subtasks or secteams
            ?>
                <div class="panel panel-default">
                <div class="panel-heading"><b>No Teams Assisting</b></div>
                <div class="panel-body">
                <?php
                        echo '<i class="fa fa-info-circle"></i></span>&nbsp; &nbsp; There are currently no subtasks for this task.  You\'ll need to add teams to the task if you need their assistance.';
                        echo '</div></div>';
            }
			
            ?>
  
</div><!-- container -->
</div>

    
    

	
	

      
                                               

