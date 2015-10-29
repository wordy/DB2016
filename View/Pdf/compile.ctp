<?php
    $show_details = true;
    //$color_team = $this->request->data('Task.color_team');
    $ztlist = array();
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }
    
    $task_count = (count($tasks)>0)? count($tasks): 0;
    
?>
<h2>DB2015 Compiled Plan</h2>
<span>Plan compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/>
    <?php 
        $show = array();
        foreach ($cteams as $tid){
            $show[] = $teamsList[$tid];    
        }
        echo '<span><b>Showing Tasks For:</b> '. implode(', ', $show) . '</span>';
    ?>
<?php 
    if (!empty($tasks)){ ?>
        <table style="width:100%" class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th width="1%"> </th>
                    <th width="6%">Start Time</th>
                    <th width="6%">End Time</th>
                    <th width="5%">Type</th>
                    <th width="4%">Lead</th>
                    <th width="10%">Assisting</th>
                    <th width="68%">Description</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td style="background-color:<?php echo $task['Task']['task_color_code'];?>">
                    </td> 
                    <td><?php echo $this->Time->format('M d H:i:s', $task['Task']['start_time']);?></td>
                    <td><?php echo $this->Time->format('H:i:s', $task['Task']['end_time']); ?></td>
                    <td><?php echo '<b>'.$task['Task']['task_type']; ?></td>
                    <td><?php 
                        $teams = $this->Ops->makePdfTeamsSig2015($task['TasksTeam'], $ztlist);
                        echo $teams['lead'];?>
                    </td>
                    <td><?php echo $teams['pushed'];?></td>
                    <td>
                    <?php 
                        echo $task['Task']['short_description'];
                            if($show_details && !empty($task['Task']['details'])){
                                echo '<hr style="margin-bottom:5px; width: 100%" />';
                                //echo nl2br($task['Task']['details']);
                            }
                    ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>



<?php
    echo 'Displaying '.$task_count. ' tasks.<br/>' ;
    }
    else {
        echo 'No tasks.';
    }
?>
