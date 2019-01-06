<?php
    //debug($zoneTeamUserList);
    $this->set('title_for_layout', 'Org Chart');
    $cu_rid = $this->Session->read('Auth.User.user_role_id');
    $orgChart = array();

    $orgChart[111] = array(
        'Zone'=>array(
            'name'=>'Compiler Editors',
            'code'=>'COM'
        )
    );    
?>
<h1><i class="fa fa-sitemap"></i> <?php echo Configure::read('EventShortName')?> Ops Team</h1>


<?php 
    foreach($zoneTeamUserList as $k => $zone){
        foreach($zone['Team'] as $m => $team){
            foreach($team['TeamsUser'] as $k =>$usr){
                if(substr($usr['User']['handle'], 0 ,4) == 'TEST'){continue;}
                if(($usr['User']['id']) == ($zone['Zone']['gm_user_id'])){
                    //echo $usr['User']['handle'];
                    $orgChart[$zone['Zone']['id']]['GM'][$usr['User']['id']] = $usr['User']['handle'];
                }
                if($usr['User']['user_role_id'] == 200 && $usr['team_code'] !='CHR'){continue;}
                if($usr['User']['user_role_id'] >=200){
                    $orgChart[111]['Teams']['Compiler Editors'][$usr['User']['id']] = $usr['User']['handle'];    
                    if($usr['User']['user_role_id'] >=400){
                        continue;
                    }
                }
                else if(($zone['Zone']['org_level'] >= 4) && $usr['User']['user_role_id'] >= 100){continue;}    
                else if(($zone['Zone']['code'] == 'GMS') && ($usr['User']['user_role_id'] != 100)){continue;}
         
            $orgChart[$team['zone_id']]['Zone']['name'] = $zone['Zone']['description']; 
            $orgChart[$team['zone_id']]['Zone']['code'] = $zone['Zone']['code'];
            $orgChart[$team['zone_id']]['Teams'][$team['name']][$usr['User']['id']] = $usr['user_handle']; 
            }
        }
    }
    
    ksort($orgChart);
    //debug($orgChart);
?>

<div class="row">
    <div class="col-xs-6">
        <?php 
            foreach($orgChart[1]['Teams'] as $name=>$users){
                if ($name != 'Chair'){continue;}
                echo '<h3 class="text-yh"><i class="fa fa-gavel"></i> <b>'.$name.'</b></h3>';
                foreach($users as $uid=>$uhan){
                    echo $this->Html->link($uhan, array('controller'=>'users', 'action'=>'profile', $uid)).'<br>';
                }
            }
        ?>
    </div>
    <div class="col-xs-3">
        <?php 
            foreach($orgChart[1]['Teams'] as $name=>$users){
                if($name == 'Chair'){continue;}
                echo '<h4><i class="fa fa-building-o"></i> <b>'.$name.'</b></h4>';
                foreach($users as $uid=>$uhan){
                    echo $this->Html->link($uhan, array('controller'=>'users', 'action'=>'profile', $uid)).'<br>';                }
            }
        ?>
    </div>
    <div class="col-xs-3">
        <?php 
            foreach($orgChart[111]['Teams'] as $name=>$users){
                echo '<h4><i class="fa fa-gears"></i><b> '.$name.'</b></h4>';
                foreach($users as $uid=>$uhan){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->Html->link($uhan, array('controller'=>'users', 'action'=>'profile', $uid)).'<br>';                }
            }
        ?>
    </div>
</div>

<div class="row lg-top-marg">
    <div class="col-xs-3">
        <?php 
            echo '<h3 class="text-yh"><i class="fa fa-glass"></i> <b>'.$orgChart[10]['Zone']['name'].'</b></h3>';
            if($orgChart[10]['GM']){
                $gm_id = array_keys($orgChart[10]['GM']);
                $gm_nom = array_values($orgChart[10]['GM']);
                echo '<h5 class="lg-bot-marg"><b>GM </b>';
                echo $this->Html->link($gm_nom[0], array('controller'=>'users', 'action'=>'profile', $gm_id[0]));
                echo '</h5>';              }
            foreach($orgChart[10]['Teams'] as $name=>$users){
                echo '<h4><b>'.$name.'</b></h4>';
                foreach($users as $uid=>$uhan){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->Html->link($uhan, array('controller'=>'users', 'action'=>'profile', $uid)).'<br>';                }
            }
        ?>
    </div>
    <div class="col-xs-3">
        <?php 
            echo '<h3 class="text-yh"><i class="fa fa-diamond"></i> <b>'.$orgChart[20]['Zone']['name'].'</b></h3>';
            if($orgChart[20]['GM']){
                $gm_id = array_keys($orgChart[20]['GM']);
                $gm_nom = array_values($orgChart[20]['GM']);
                echo '<h5 class="lg-bot-marg"><b>GM </b>';
                echo $this->Html->link($gm_nom[0], array('controller'=>'users', 'action'=>'profile', $gm_id[0]));
                echo '</h5>';
            }
            else{ echo "<p>&nbsp;</p>";}

            foreach($orgChart[20]['Teams'] as $name=>$users){
                echo '<h4><b>'.$name.'</b></h4>';
                foreach($users as $uid=>$uhan){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->Html->link($uhan, array('controller'=>'users', 'action'=>'profile', $uid)).'<br>';                }
            }
        ?>
    </div>
    <div class="col-xs-3">
        <?php 
            echo '<h3 class="text-yh"><i class="fa fa-user-circle-o"></i> <b>'.$orgChart[30]['Zone']['name'].'</b></h3>';
            if($orgChart[30]['GM']){
                $gm_id = array_keys($orgChart[30]['GM']);
                $gm_nom = array_values($orgChart[30]['GM']);
                echo '<h5 class="lg-bot-marg"><b>GM </b>';
                echo $this->Html->link($gm_nom[0], array('controller'=>'users', 'action'=>'profile', $gm_id[0]));
                echo '</h5>';              }
            
            foreach($orgChart[30]['Teams'] as $name=>$users){
                echo '<h4><b>'.$name.'</b></h4>';
                foreach($users as $uid=>$uhan){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->Html->link($uhan, array('controller'=>'users', 'action'=>'profile', $uid)).'<br>';                }
            }
        ?>
    </div>
    <div class="col-xs-3">
        <?php 
            echo '<h3 class="text-yh"><i class="fa fa-tachometer"></i> <b>'.$orgChart[40]['Zone']['name'].'</b></h3>';
            if($orgChart[40]['GM']){
                $gm_id = array_keys($orgChart[40]['GM']);
                $gm_nom = array_values($orgChart[40]['GM']);
                echo '<h5 class="lg-bot-marg"><b>GM </b>';
                echo $this->Html->link($gm_nom[0], array('controller'=>'users', 'action'=>'profile', $gm_id[0]));
                echo '</h5>';
		}
            
            foreach($orgChart[40]['Teams'] as $name=>$users){
                echo '<h4><b>'.$name.'</b></h4>';
                foreach($users as $uid=>$uhan){
                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->Html->link($uhan, array('controller'=>'users', 'action'=>'profile', $uid)).'<br>';                }
            }
        ?>
    </div>

</div>