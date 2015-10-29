<?php
    // If there's no lead, there's no signature
    if(!empty($teamsRoles[1])){
        $norole = $teamsRoles[0];
        $lead = $teamsRoles[1];
        $push = $teamsRoles[2];
        $open = $teamsRoles[3];
        $closed = $teamsRoles[4];
        $lbut = '';

        foreach($lead as $tid => $tcode){
            $lbut.= '<span class="btn btn-sm btn-ttrid1 tt-btn ban-edit" data-team_id = "'.$tid.'" data-tr_id = "1">'.$tcode.'</span>';
        }
        foreach($open as $tid => $tcode){
            $lbut.= '<span class="btn btn-sm btn-danger tt-btn" data-team_id = "'.$tid.'" data-tr_id = "3">'.$tcode.'</span>';
        }
        foreach($closed as $tid => $tcode){
            $lbut.= '<span class="btn btn-sm btn-success tt-btn" data-team_id = "'.$tid.'" data-tr_id = "4">'.$tcode.'</span>';
        }
        foreach($push as $tid => $tcode){
            $lbut.= '<span class="btn btn-sm btn-ttrid2 tt-btn" data-team_id = "'.$tid.'" data-tr_id = "2">'.$tcode.'</span>';
        }
        foreach($norole as $tid => $tcode){
            $lbut.= '<span class="btn btn-sm btn-ttrid0 tt-btn" data-team_id = "'.$tid.'" data-tr_id = "0">'.$tcode.'</span>';
        }
        echo $lbut;    
    }
    else{
        echo '<div class="alert slim-alert alert-info" role="alert">Select a lead team first.</div>';
    }
    
?>
    
