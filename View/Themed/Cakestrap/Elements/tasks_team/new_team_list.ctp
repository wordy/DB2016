<?php 
    $lbut = '';
    $abut ='';
    if(!empty($new_teams[1]) && !empty($new_teams[0])){
        foreach($new_teams[1] as $k=>$lteam){
            $lbut.= '<span class="btn btn-sm btn-ttrid1 ttl-btn ban-edit " data-team_id = "'.$k.'" data-tr_id = "1">'.$lteam.'</span>';        }
    
        foreach($new_teams[0] as $k =>$ateam){
            $abut.='<span class="btn btn-sm btn-ttrid0 tt-btn" data-team_id = "'.$k.'" data-tr_id = "0">'.$ateam.'</span>';
        }
    
        echo $lbut.$abut;    
    }
    //else{
      //  echo '<div class="alert slim-alert alert-info" role="alert"> 
        //Select a lead team first</div>';
    //}















    
?>
    
