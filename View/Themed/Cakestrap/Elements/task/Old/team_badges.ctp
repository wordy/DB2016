<?php
    $leadTeam = null;
    $linkedParentTeam = null;
    $activeContributingTeam = array();
    $inactiveContributingTeam = array();
    
    if(!empty($task['TasksTeam'])){

        foreach ($task['TasksTeam'] as $k=>$team){
            if ($team['task_role_id'] == 1){
                $leadTeam = $team['team_code'];
            }
            elseif ($team['task_role_id'] == 2 && $team['link_count'] < 1) {
               $inactiveContributingTeam[] = $team['team_code'];
            }
            elseif ($team['task_role_id'] == 2 && $team['link_count'] >= 1) {
               $activeContributingTeam[] = $team['team_code'];
            }
            elseif ($team['task_role_id'] == 3) {
               $linkedParentTeam = $team['team_code'];
            }
        }
    }
?>
<?php
    //NOTE: DBALL Hard coded CSS 
    if (!empty($leadTeam)){
        echo '<span class="btn btn-medgrey btn-xxs">'.$leadTeam.'</span>';
    }
                        
    if (!empty($linkedParentTeam)){
        echo '&nbsp;<i class="fa fa-long-arrow-right"></i><span class="btn btn-medgrey btn-xxs">'.$linkedParentTeam.'</span><br/>';
    }

    foreach ($activeContributingTeam as $act){
        echo '<span class="btn btn-success btn-xxs">'.$act.'</span>';     
    }

    foreach ($inactiveContributingTeam as $ict){
        echo '<span class="btn btn-default btn-xxs">'.$ict.'</span>';
    }

?>
