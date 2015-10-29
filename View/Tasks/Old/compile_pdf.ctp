<?php
    if (AuthComponent::user('id')){
        //$controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }
        
    $show_details = $this->request->data('Task.show_details');
    $single_task = (isset($single_task))? $single_task : 0; 
    
    /*
    $tlist=array();
    foreach ($teams as $zone){
        foreach($zone as $tid=>$code){
            $tlist[$tid] = $code;
        } 
    }
    */
    
    //$teamIdCodeList = Hash::combine($zoneTeamList, '{n}.Team.id', '{n}.Team.code');
        
       
    $cs_teams = array();
    if(!empty($cSettings['Teams'])){
        foreach ($cSettings['Teams'] as $tid){
            $cs_teams[] = $teamIdCodeList[$tid];
        }
    }
    
?>
    <div class="row">
        <h2>Compile Tasks</h2>
    </div>
    <div id="page-content" class="row">
        <div id="taskListWrap">
            <?php 
                echo $this->element('task/compile_basic',
                    array(
                        'tasks'=>$tasks,
                        'show_details'=>$show_details
                    )
                );
            ?>
        </div>
    </div>

