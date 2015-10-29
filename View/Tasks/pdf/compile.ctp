<?php
    $sd = $this->Session->read('Auth.User.Compile.view_details');
    
    $cSettings = $this->Session->read('Auth.User.Compile');
    
    $show_details = ($sd == 0)? false:true; 
    
    $printPrefs['hide_task'] = (!empty($printPrefs['hide_task']))? $printPrefs['hide_task'] : array();
    $printPrefs['hide_detail'] = (!empty($printPrefs['hide_task']))? $printPrefs['hide_task'] : array();
    
    //debug($printPrefs);
    
    $cntTasks = count($tasks);
    $cntHidden = 0;
    
    $t_ids = Hash::extract($tasks, '{n}.Task.id');
    
    if(!empty($printPrefs['hide_task'])){
        $cntHidden = count(array_intersect($t_ids, $printPrefs['hide_task']));    
    }
    
    $cntShown = $cntTasks - $cntHidden;
     
    $cs_teams=array();
    foreach ($cSettings['Teams'] as $tid){
        $cs_teams[] = $teamIdCodeList[$tid];
    }
      
?>

<h2><?php echo Configure::read('EventShortName');?> Compiled Plan</h2>
<span>Plan compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/>
         <?php
            echo '<p>Showing '.$cntShown.' tasks for '; 
            echo implode(', ', $cs_teams);
            echo ' from ' .date('M d Y', strtotime($cSettings['start_date'])). ' to '.date('M d Y', strtotime($cSettings['end_date']));
            echo '</p>';
         ?>   
<?php 
    if (!empty($tasks)){ 
        ?>
        <table>
            <thead>
                <tr>
                    <th width="9%">Start</th>
                    <th width="9%">End</th>
                    <th width="5%">Type</th>
                    <th width="4%">Lead</th>
                    <th width="8%">Assisting</th>
                    <th width="20%">Description</th>
                    <th width = "45%">Details</th>
                </tr>
            </thead>
            <tbody>
            
            <?php 
            //$hiddenTasks = 0;
        foreach ($tasks as $task): 
            $hide_det = false;
            $tid = $task['Task']['id'];

            if(in_array($tid, $printPrefs['hide_task'])){
                //$hiddenTasks++;
               continue;
            }
        
            if(in_array($tid, $printPrefs['hide_detail']) || !$show_details){
                $hide_det = true;
            }
            
            ?>

        <tr>
            <td>
                <?php echo $this->Time->format('m/d/Y H:i:s', $task['Task']['start_time']);?>    
            </td>
            <td>
                <?php echo $this->Time->format('m/d/Y H:i:s', $task['Task']['end_time']); ?>
            </td>
            <td>
                <b><?php echo $task['Task']['task_type']; ?></b>
            </td>
            <td>
                <?php 
                $teams = $this->Ops->makePdfTeamsSig2015($task['TasksTeam'], $zoneTeamCodeList);
                echo '<b>'.$teams['lead'][0].'</b>';?>
            </td>
            <td>
                <?php 
                    $hidePush = false;
                    if(!empty($teams['assist'])){
                        if(count($teams['assist'])>=10){
                            echo 'ALL';
                            $hidePush = true;
                        }
                        else{
                            echo implode(', ', $teams['assist']);
                        }    
                    }
                        
                    if(!empty($teams['pushed']) && !$hidePush){
                        if(!empty($teams['assist'])){
                            echo ', ';
                        }
                        
                        echo implode(', ', $teams['pushed']);    
                    }
                    
                    if(empty($teams['pushed'])){
                        echo '&nbsp;';
                    }
                ?>
            </td>
            <td>
            <?php 
                echo $task['Task']['short_description'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                    
            ?>
            </td>
            <td>
                <?php
                    if(!$hide_det && !empty($task['Task']['details'])){
                        echo '<br/>';
                        echo nl2br($task['Task']['details']);
                    }
                ?>
            </td>
        </tr>
        <?php 
        endforeach; ?>
        </tbody>
    </table>



<?php
//debug($task_count);

//debug($hiddenTasks);


    echo 'Displaying '.$cntShown. ' tasks ';

    if($cntHidden > 0){
        echo '('.$cntHidden.' hidden due to user preferences).';
    }
}
    else {
        echo 'No tasks.';
    }
    

?>
