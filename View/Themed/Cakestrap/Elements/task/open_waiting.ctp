<div class="row">
    <div class='col-md-6'></div>
    <div class='col-md-6'>
        <div class="panel panel-danger">
    <div class="panel-heading">Open Requests To Other Teams</div>
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th width="15%">Start</th>
                    <th width="25%">Teams</th>
                    <th width="60%">Description</th>
                    
                    
                    </tr>
            </thead>
            <tbody>    
            <?php
                foreach($tasks as $task){
                    echo '<tr>';
                    echo '<td>'.$this->Ops->durationFriendly($task['Task']['start_time'], $task['Task']['end_time']).'</td>';
                    echo '<td><b>'.$task['Task']['task_type'].'</b><br>';
                    echo $this->Ops->ttSigLeadOpen($task['TasksTeam'], $zoneTeamCodeList);
                    echo '</td>';
                    echo '<td>'.$this->Text->truncate(
    $task['Task']['short_description'],75,
    
    array(
        'ellipsis' => '...',
        'exact' => false
    )
).'<br><div class="pull-right">';
                    if($task['Task']['due_date']){
                        echo '<button type="button" class="btn btn-danger btn-xs" style="margin-right:5px;">';
                        echo '<i class="fa fa-bell-o"></i>&nbsp;';
                        echo $this->Time->format('M d', $task['Task']['due_date']);
                        echo '</button>';
                    }
                    echo $this->Html->link('<i class="fa fa-eye"></i> View', array(
                                'controller'=>'tasks',
                                'action'=>'compile',
                                '?'=>array('task'=>$task['Task']['id'])
                                ), 
                                array(
                                    'escape'=>false,
                                    'class'=>'btn btn-default btn-xs task_view_button')
                                );
                    
                    echo '</div></td>';
                }    
            ?>
            </tbody>
        </table>
</div>
        
    </div>
</div>

