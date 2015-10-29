<?php
    $monday = strtotime('last monday', strtotime('tomorrow'));
    $wk_start = date('F dS', $monday);
?>

<h2><?php echo Configure::read('EventShortName'); echo ' '.$team_code;?> Compiler Updates (Wk of <?php echo date('M dS',$monday);?>)</h2>
<?php 

    if(empty($next_meeting) && empty($recent_request) && empty($recent_links)){
        echo 'There are currently no tasks for '.$team_code."'s digest. Please check back soon.";
    }



    if(!empty($next_meeting)|| !empty($recent_requests)||!empty($recent_links)):
      

?>


<h1>Recent Compiler Updates for <?php echo $team_code;?></h1>
Here are the recent changes from the compiler for the week of <?php echo $wk_start;?>. This summary covers only changes from the past 2 weeks, and includes only <b>Open Requests</b> (i.e. items still marked as outstanding).  

<?php if(!empty($next_meeting)):?>
    <h3><span class="text-yh">Next Ops Meeting</span></h3>
    <b>Reminder:</b> The next Ops Meeting is scheduled for <?php echo date('M j\, Y', strtotime($next_meeting['Task']['start_time'])).' at '.date('g:i A', strtotime($next_meeting['Task']['start_time']))?>
<?php endif; ?>

<?php if (!empty($recent_requests)):?>
    <h3><span class="text-yh">New Requests from Other Teams</span></h3>
    <p>These are new requests for help from other teams (Open Requests).</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 20%;">Date</th>
                <th style="width: 70%;">Description</th>
                <th style="width: 10%;">Link</th>
            </tr>
        </thead>
        <tbody>    
            <?php
                foreach($recent_requests as $task){
                    echo '<tr><td>'.$this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true).'</td>';
                    echo '<td><b>'.$task['Task']['team_code'].' ('.$task['Task']['task_type'].'): </b> '.$task['Task']['short_description'].'</td>';
                    echo '<td>'.$this->Html->link('View', array('controller'=>'tasks', 'action'=>'compile', '?'=>array('task'=>$task['Task']['id']), 'full_base'=>true)).'</td>';
                    echo '</tr>';
                }    
            ?>
        </tbody>
    </table>
<?php endif;?>

<?php if (!empty($recent_links)):?>
    <h3><span class="text-yh">New Links from Other Teams</span></h3>
    <p>Other teams have recently linked to your tasks.</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 20%;">Date</th>
                <th style="width: 70%;">Description</th>
                <th style="width: 10%;">Link</th>
            </tr>
        </thead>
        <tbody>    
            <?php
                foreach($recent_links as $task){
                    echo '<tr><td>'.$this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true).'</td>';
                    echo '<td><b>'.$task['Task']['team_code'].' ('.$task['Task']['task_type'].'): </b> '.$task['Task']['short_description'].'</td>';
                    echo '<td>'.$this->Html->link('View', array('controller'=>'tasks', 'action'=>'compile', '?'=>array('task'=>$task['Task']['id']), 'full_base'=>true)).'</td>';
                    echo '</tr>';
                }    
            ?>
        </tbody>
    </table>
    <span class="small">This message was generated automatically. Please do not respond.</span>

<?php endif; 

endif;

?>

