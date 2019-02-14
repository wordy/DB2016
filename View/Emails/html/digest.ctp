<?php
    $monday = strtotime('last monday', strtotime('tomorrow'));
    $wk_start = date('F jS', $monday);
?>

<h1>Recent Compiler Updates for <?php echo $team_code;?></h1>
Here are the recent changes from the compiler for the week of <?php echo $wk_start;?>. This summary covers only changes from the past 2 weeks and includes only <b>Open Requests</b> (i.e. items still marked as outstanding).  

<?php if(!empty($next_meeting)):?>
    <h3 style="color: #00816C;">Next Ops Meeting</h3>
    <p><b>Reminder:</b> The next Ops Meeting is scheduled for <?php echo date('M j\, Y', strtotime($next_meeting['Task']['start_time'])).' at '.date('g:i A', strtotime($next_meeting['Task']['start_time']))?></p>
<?php endif; ?>

<?php if (!empty($recent_requests)):?>
    <h3 style="color: #00816C;">New Requests from Other Teams</h3>
    <p>These are new requests for help from other teams (Open Requests).</p>
    <table style="width:'100%';">
        <thead>
            <tr>
                <th style="width: 200px; text-align: left"><b>Date</b></th>
                <th style="width: 400px; text-align: left"><b>Description</b></th>
                <th style="width: 100px; text-align: left"><b>Link</b></th>
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
    <h3 style="color: #00816C;">New Links from Other Teams</h3>
    <p>Other teams have recently linked to your tasks.</p>
    <table style="width:'100%';">
        <thead>
            <tr>
                <th style="width: 200px; text-align: left"><b>Date</b></th>
                <th style="width: 400px; text-align: left"><b>Description</b></th>
                <th style="width: 100px; text-align: left"><b>Link</b></th>
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
<?php endif; ?>
<p>That's it for now. You can <a href="http://ops.yhdragonball.com">log into the compiler here</a> to view all your tasks.</p>
<p>Thanks,<br>
    DBOps Compiler
</p>


<p>&nbsp;</p>
<small>This message was generated automatically. Please do not respond.</small>
