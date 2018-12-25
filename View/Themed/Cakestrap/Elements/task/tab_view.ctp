<?php
    if(!empty($task)){
        $this->request->data = $task;
        $tid = $task['Task']['id'];
    }
    
    
    
    if (AuthComponent::user('id')){
        //$user_role = AuthComponent::user('user_role_id');
        $userTeamsList = AuthComponent::user('TeamsList');
        $userTeams = AuthComponent::user('Teams');
    }
    
    
    $userControls = (in_array($task['Task']['team_id'], $userTeams))? true : false;

    
    //If user controls >2 teams, force empty input so they can't forget and accidently set an incorrect team
    $team_input_readonly = false;
    
    if(count($controlled_teams) == 1){
        $team_input_empty = false;
        $team_input_readonly = 'readonly';
    }
    elseif(count($controlled_teams)>=2){
        $team_input_empty = true;
    }
    
    $uControlledATeams = array(); 
    
    $ateams = array();
    if (!empty($task['TasksTeam'])) {
        $ateams = Hash::extract($task['TasksTeam'], '{n}.team_id');
    //    $ateams_codes = Hash::extract($task['TasksTeam'], '{n}[task_role_id != 1].team_code');
    }
    
    foreach($userTeamsList as $team_id => $tcode){
        if(in_array($team_id, $ateams)){
            $uControlledATeams[$team_id] = $tcode;
        }
    }
    
    $cntCtrl = (!empty($ateams))? count($uControlledATeams): 0;    

/*    
    $this->Js->buffer("
        var lpt_id = ".$task['Task']['team_id'].";
    ");*/

    //Figure out current team contributions
    /*
    if(!empty($task['TasksTeam'])){
        $tt = $task['TasksTeam'];
        $lead_id = Hash::extract($tt, '{n}[task_role_id=1].team_id');
        $push_id = Hash::extract($tt, '{n}[task_role_id=2].team_id');
        $assist_id = Hash::extract($tt, '{n}[task_role_id=3].team_id');
    }*/
?>

<div id="viewTask<?php echo $tid;?>">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-bdanger">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-wrench"></i> Task Actions 
                                <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="Task Actions" data-content="<p><b>View Single Task:</b> Allows you to view this task alone. Useful for opening single tasks in a new tab/window.</p><p><b>Add Task Here:</b> Sets you up to add a new task at the same time as this one.</p><p><b>Link to Task: </b> Create a new task that links to this task. Only available if this task allows links.</p>">
                                            <i class="fa fa-question-circle text-default"></i>                    
                                        </a></h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <?php
                                        echo $this->Html->link('<i class="fa fa-eye"></i> View Single Task', 
                                            array(
                                                'controller'=>'tasks',
                                                'action'=>'compile',
                                                '?'=>array(
                                                    'task'=>$task['Task']['id'],
                                                )
                                            ), 
                                            array(
                                                //'target' => '_blank',
                                                'escape'=>false,
                                                'class'=>'btn btn-default sm-bot-marg task_view_button')
                                            );
                                    ?>
                                    <button class="btn btn-yh addTask sm-bot-marg"><i class="fa fa-plus-circle"></i> Add Task Here</button>
                                    <?php if($cntCtrl>=1):?>
                                        <button class="btn btn-yh addLink sm-bot-marg"><i class="fa fa-link"></i> Link to Task</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div><!--panel body-->
                    </div><!--panel-->     
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div id="commentsByTask<?php echo $tid;?>">
                        <?php 
                            echo $this->element('comment/by_task', array('tid'=>$tid, 'team'=>$task['Task']['team_id']));
                        ?> 
                    </div>                    
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-bsteel">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-exchange"></i> Changes</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row" id="loaded_chg_<?php echo $tid ?>">
                                        <?php echo $this->element('change/changes_by_task', array('task'=>$tid, 'userControls'=> $userControls)); ?>
                                    </div>                        
                                </div>
                            </div>
                        </div><!--panel body-->
                    </div><!--panel-->   
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-crosshairs"></i> Meta Data</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row sm-bot-marg">
                                <div class="col-xs-2">
                                    <strong>ID</strong><br/>
                                    <?php echo $task['Task']['id']; ?>
                                </div>
                                <div class="col-xs-4">
                                    <strong>Start Time</strong><br/>
                                    <?php echo date('j/m/y g:i:s', strtotime($task['Task']['start_time'])); ?>
                                </div>
                                <div class="col-xs-4">
                                    <strong>End Time</strong><br/>
                                    <?php echo date('j/m/y g:i:s', strtotime($task['Task']['end_time'])); ?>
                                </div>
                                <div class="col-xs-2">
                                    <strong>Due</strong><br/>
                                    <?php echo (isset($task['Task']['due_date']))? date('M j', strtotime($task['Task']['due_date'])):'N/A'; ?>
                                </div>
                            </div>
                            <div class="row sm-bot-marg">
                                <div class="col-xs-2">
                                    <b>Created</b><br/>
                                    <?php echo (!empty($task['Task']['created']))? date('M j', strtotime($task['Task']['created'])): 'N/A'; ?>
                                </div>                    
                                <div class="col-xs-2">
                                    <b>Modified</b><br/>
                                    <?php echo (!empty($task['Task']['created']))? date('M j', strtotime($task['Task']['modified'])): 'N/A'; ?>
                                </div>
                                <div class="col-xs-4">
                                    <strong>Time Link (Offset)</strong><br/>
                                    <?php echo ($task['Task']['time_control']==1)? '<i class="fa fa-check text-success"></i>('.$task['Task']['time_offset'].'s)':'<i class="fa fa-times text-danger"></i>'; ?>
                                </div>
                            </div>
                            <?php
                            /*
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4>Team Roles</h4>
                                    <table class="table table-condensed">
                                        <thead>
                                            <tr>
                                                <th>Team</th>
                                                <th>ID</th>
                                                <th>Role</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            //foreach($task['TasksTeam'] as $k => $tt){
                                            //    echo '<tr><td>'.$tt['team_code'].'</td><td>'.$tt['team_id'].'</td><td>'.$tt['task_role_id'].'</td></tr>';
                                            //}
                                        
                                        ?>
                                        </tbody>                                
                                    </table>
                                </div>
                            </div>
                             */
                             ?>  
                        </div>
                    </div><!--/panel-->                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    echo $this->Js->writeBuffer(); 
?>  