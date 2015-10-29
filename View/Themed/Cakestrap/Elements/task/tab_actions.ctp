<?php
    if(!empty($task)){
        $this->request->data = $task;
        $tid = $task['Task']['id'];
    }
    $singleTeamControl = false;
    $showAdvPid = false;
       
    if(!empty($task['Task']['parent_id'])){
        $showAdvPid = true;
    }
    
    if (AuthComponent::user('id')){
        //$user_role = AuthComponent::user('user_role_id');
        $userTeamList = AuthComponent::user('TeamsList');
        //$user_teams = AuthComponent::user('TeamsByZone');
    }
    
    $uControlledATeams = array(); 
    
    $ateams = array();
    if (!empty($task['TasksTeam'])) {
        $ateams = Hash::extract($task['TasksTeam'], '{n}.team_id');
    }
    
    foreach($userTeamList as $team_id => $tcode){
        if(in_array($team_id, $ateams)){
            $uControlledATeams[$team_id] = $tcode;
        }
    }

    $cntCtrl = (!empty($ateams))? count($uControlledATeams): 0;    
       
    //If user controls >2 teams, force empty input so they can't forget and accidently set an incorrect team
    $team_input_readonly = false;
    
    if(count($controlled_teams) == 1){
        $team_input_empty = false;
        $team_input_readonly = 'readonly';
        $ar_k = array_keys($userTeamList);
        $singleTeamControlled = $ar_k[0];
        $singleTeamControl = true;
    }
    elseif(count($controlled_teams)>=2){
        $team_input_empty = true;
    }
    /*
    $this->Js->buffer("
        $('.helpTTs').popover({
            container: 'body',
            html:true,
        });
   ");*/
?>
    <div class="row" id="taskActions<?php echo $tid;?>">
        <div class="col-md-9">
           <div class="panel panel-bdanger">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-wrench"></i> Actions</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h5>Add New Task At This Time <small>Create the new task that being at the same time as this task.</small></h5>
                        </div>
                    </div>
                    
                <?php 
                    if($cntCtrl > 0):
                        $selected = null;
                        $readOnly = false;
                        
                        if($cntCtrl == 1 ){
                            $readOnly = true;
                            $ak_uct = array_keys($uControlledATeams);
                            $selected = $ak_uct[0];
                        }
                        
                        echo '<h4><b>Link to This Task</b> <small>Create a new task that links to this.</small></h4>';
                        echo $this->Form->input('child_team', array(
                            'class' => 'form-control child_team',
                            'type'=>'select',
                            'multiple'=>false,
                            'selected'=>($selected)? $selected: null,
                            'readonly'=>($readOnly)? 'readonly':false,
                            'label'=>false,
                            'div'=>array(
                                'class'=>'input-group'),
                            'options'=>$uControlledATeams,
                            'after'=>'<span class="input-group-btn">
                                <button class="btn btn-default linkChildTask" data-task_id="'.$tid.'" type="submit"><i class="fa fa-link"></i> Link</button></span>',
                            'placeholder'=>'<Team>', 
                            'escape'=>false)); 
                    endif;?>   
                </div>
            </div> 
        </div><!--col-md-9-->
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger text-center">
                        <button type="button" data-tid="<?php echo $tid;?>" class="btn btn-block btn-danger eaTaskDeleteButton"><i class="fa fa-trash-o"></i> Delete Task</button>
                        <p class="sm-top-marg"><i class="fa fa-warning"></i>&nbsp;<b>Warning:</b> Cannot be undone</p>                               
                    </div>
                </div>
            </div>
        <!-- col wrap-->
        </div><!--col-md-3-->
    </div><!--row-->
<?php
    echo $this->Js->writeBuffer(); 
?> 
<!-- End TAB Actions--> 
