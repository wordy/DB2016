<?php 
    $cntTasks = count($tasks);
    $t_ids = Hash::extract($tasks, '{n}.Task.id');
    $cntShown = $cntTasks;
?>

<h2><?php echo Configure::read('EventShortName');?> Compiled Plan</h2>
<span>Compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/><br/>
<?php echo '<p>Showing '.$cntShown.' tasks for search term <b>'.$term.'</b> from ' .date('M j\/Y', strtotime($start_date)). ' to '.date('M j\/Y', strtotime($end_date)).'</p>'; ?> 

<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class='col-md-12'>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7px;"></th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 150px;">Teams</th>
                            <th style="width: 750px;">Description</th>
                        </tr>
                    </thead>

                    <tbody>    
                    <?php
                        $pST = null;
                        foreach($tasks as $task){
                            $cST = date('Y-m-d', strtotime($task['Task']['start_time']));
                            $sameDay = ($cST == $pST)? true : false;
                            
                            echo '<tr>';
                            echo '<td style="background: '.$task['Task']['task_color_code'].'"></td>';
                            echo '<td>';

                            if($sameDay){
                                echo $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true, false);    
                            }
                            else{
                                echo $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true);
                                
                            }
                            
                            echo '</td><td><b>'.$task['Task']['task_type'].'</b><br>';
                            echo $this->Ops->pdfSig2016($task['TasksTeam'], $zoneTeamCodeList).'</td>';
                            echo '<td>'.$task['Task']['short_description'];
                            
                            if($task['Task']['details']){
                                echo '<hr class="hr-slim">';
                                echo nl2br($task['Task']['details']);    
                            }
                            
                            echo '</td>';
                            echo '</tr>';
                            $pST = $cST;
                        }    
                    ?>
                </tbody>
                </table>
                <span>Showing <?php echo $cntShown.' tasks ';?></span>
            </div>
        </div>
    </div>
</div>
