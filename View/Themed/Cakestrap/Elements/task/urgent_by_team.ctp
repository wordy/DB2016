<?php

//debug($urgentByTeam);
$utasks = $urgentByTeam['utasks'];
$nextMeeting = $urgentByTeam['nextMeeting'];

//debug($utasks);
//debug($nextMeeting);
    $due_tasks = array();
    $nodue_tasks = array();


    foreach ($utasks as $k=>$task){
        if($task['Task']['due_date']){
            $due_tasks[] = $task;
        }
        else{
            $nodue_tasks[] = $task;
        }
    }
?>


<?php 
    if(isset($nextMeeting) && !empty($nextMeeting)){
        $dtask = $nextMeeting;
    ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Next Ops Meeting</h2>
                <?php 
                    $dueBut = '';
                    $viewBut = $this->Html->link('<i class="fa fa-eye"></i> View', array(
                        'controller'=>'tasks',
                        'action'=>'compile',
                        '?'=>array('task'=>$dtask['Task']['id'])
                        ), 
                        array(
                            'escape'=>false,
                            'class'=>'btn btn-default btn-xs task_view_button')
                    );
                    
                    if($dtask['Task']['due_date']){
                        $dueBut = '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg"><i class="fa fa-clock-o"></i> '.date('M j', strtotime($dtask['Task']['due_date'])).'</button>';    
                    }
        $html = '
        <div class="urgentTask astRow"
            id="tid'.$dtask['Task']['id'].'"
            data-tid="'.$dtask['Task']['id'].'" 
            style="border-left: 5px solid '. $dtask['Task']['task_color_code'].'">
            <div class="row astHeading" data-tid="'.$dtask['Task']['id'].'">
                <div class="col-xs-2 col-sm-2 col-md-2">'. date('M j\<\b\r\>g:i A', strtotime($dtask['Task']['start_time'])).'</div>
                <div class="col-xs-3 col-sm-4 col-md-3"><strong>'.$dtask['Task']['task_type'].'</strong><br/>
                <strong>'.$this->Ops->makeTeamsSigNoPush($dtask['TasksTeam'], $zoneTeamCodeList).'</strong></div> 
                <div class="col-xs-5 col-sm-4 col-md-5">'.$dtask['Task']['short_description'].'</div>
                <div class="col-xs-2 col-sm-2 col-md-2">
                    <div class="pull-right">
                    '.$dueBut.'<br/>'.$viewBut.'
                    </div>
                
                </div>
            </div>                            
        </div>';
                
        echo $html;  
        ?>
        </div>
    </div>
<?php   } ?>
        
    <div class="row">
        <div class="col-md-12">
        <?php
            if(!empty($due_tasks)){
                echo '<h2>Due Soon</h2>';
            }
            
            foreach($due_tasks as $dtask){
                $dueBut = '';
                $chgBut = '';
                $viewBut = '';
                    
                $viewBut = $this->Html->link('<i class="fa fa-eye"></i> View', array(
                    'controller'=>'tasks',
                    'action'=>'compile',
                    '?'=>array('task'=>$dtask['Task']['id'])
                    ), 
                    array(
                        'escape'=>false,
                        'class'=>'btn btn-default btn-xs task_view_button')
                    );
                    
                    if($dtask['Task']['due_date']){
                        $dueBut = '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg"><i class="fa fa-clock-o"></i> '.date('M j', strtotime($dtask['Task']['due_date'])).'</button>';    
                    }
        $html = '
        <div class="urgentTask astRow"
            id="tid'.$dtask['Task']['id'].'"
            data-tid="'.$dtask['Task']['id'].'" 
            style="border-left: 5px solid '. $dtask['Task']['task_color_code'].'">
            <div class="row astHeading" data-tid="'.$dtask['Task']['id'].'">
                <div class="col-xs-2 col-sm-2 col-md-2">'. date('M j\<\b\r\>g:i A', strtotime($dtask['Task']['start_time'])).'</div>
                <div class="col-xs-3 col-sm-4 col-md-3"><strong>'.$dtask['Task']['task_type'].'</strong><br/>
                <strong>'.$this->Ops->makeTeamsSigNoPush($dtask['TasksTeam'], $zoneTeamCodeList).'</strong></div> 
                <div class="col-xs-5 col-sm-4 col-md-5">'.$dtask['Task']['short_description'].'</div>
                <div class="col-xs-2 col-sm-2 col-md-2">
                    <div class="pull-right">
                    '.$dueBut.'<br/>'.$viewBut.'
                    </div>
                </div>
            </div>                            
        </div>';
                
        echo $html;  
    }

    if(!empty($nodue_tasks)){
        echo '<h2>Ending Soon</h2>';
    }

        foreach($nodue_tasks as $ndtask){
            $viewBut = $this->Html->link('<i class="fa fa-eye"></i> View', array(
                'controller'=>'tasks',
                'action'=>'compile',
                '?'=>array('task'=>$ndtask['Task']['id'])
                ), 
                array(
                    'escape'=>false,
                    'class'=>'btn btn-default btn-xs task_view_button')
                );
    
            $html = '
            <div class="urgentTask astRow"
                id="tid'.$ndtask['Task']['id'].'"
                data-tid="'.$ndtask['Task']['id'].'" 
                style="border-left: 5px solid '. $ndtask['Task']['task_color_code'].'">
                <div class="row astHeading" data-tid="'.$ndtask['Task']['id'].'">
                    <div class="col-xs-2 col-sm-2 col-md-2">'. date('M j\<\b\r\>g:i A', strtotime($ndtask['Task']['start_time'])).'</div>
                    <div class="col-xs-3 col-sm-4 col-md-3"><strong>'.$ndtask['Task']['task_type'].'</strong><br/>
                    <strong>'.$this->Ops->makeTeamsSigNoPush($ndtask['TasksTeam'], $zoneTeamCodeList).'</strong></div> 
                    <div class="col-xs-5 col-sm-4 col-md-5">'.$ndtask['Task']['short_description'].'</div>
                    <div class="col-xs-2 col-sm-2 col-md-2"><div class="pull-right">'.$viewBut.'</div></div>
                </div>
            </div>';
            echo $html;  
        }

        if(empty($nodue_tasks) && empty($due_tasks)){
            echo '<h2>Due or Ending Soon</h2><div class="alert alert-success sm-bot-marg" role="alert">
                <i class="fa fa-lg fa-thumbs-o-up"></i>&nbsp; <b>Nice! </b>There are no upcoming items due for the team(s) selected.
            </div>';
        }
        ?>
        </div>
    </div>

