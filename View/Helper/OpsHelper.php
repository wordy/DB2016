<?php

App::uses('AppHelper', 'View/Helper');


class OpsHelper extends AppHelper {

    var $helpers = array('Html', 'Text', 'Time');
    
    // Used in Compile_Screen
    public function makeTeamsSig($taskTeams = array(), $zoneTeamList, $userControls = false, $task=false){
        $tt = ($taskTeams)? $taskTeams: array();
        
        $buttons13 = $buttons2 = '';
        $allowOpen = ' ban-edit'; 
        $allowClose = ' ban-edit';
        $allowPush = ' ban-edit';
        
        if($userControls){
            $allowOpen = ' openTeam';
            $allowClose = ' closeTeam';
            $allowPush =  ' pushTeam';
        }

        // Figures out teams in each zone, to combine buttons
        $ztlist = array();
        foreach ($zoneTeamList as $zone => $tids){
            $ztlist[$zone] = array_keys($tids);
        }
        
        $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
        $tt_p = Hash::combine($tt, '{n}[task_role_id=2].team_id', '{n}[task_role_id=2].team_code');                                    
        $tt_oa = Hash::combine($tt, '{n}[task_role_id=3].team_id', '{n}[task_role_id=3].team_code');
        $tt_ca = Hash::combine($tt, '{n}[task_role_id=4].team_id', '{n}[task_role_id=4].team_code');
        $tt_all = Hash::combine($tt, '{n}.team_id', '{n}.team_code');
        
        // Pushed ONLY
        $tt_p_only = $tt_p;

        foreach ($tt_l as $tid => $tcode){
            $buttons13.= '<span data-trid="1" data-teamid = "'.$tid.'" class="btn btn-ttrid1 btn-xxs tglTR ban-edit">'.$tcode.'</span>';    
        }                                    
        foreach ($tt_oa as $tid => $tcode){
            $buttons13.= '<span data-trid="3" data-teamid = "'.$tid.'" class="btn btn-danger btn-xxs tglTR is-oreq'.$allowOpen.'">'.$tcode.'</span>';    
        }
        foreach ($tt_ca as $tid => $tcode){
            $buttons13.= '<span data-trid ="4" data-teamid = "'.$tid.'" class="btn btn-success btn-xxs tglTR is-creq'.$allowClose.'">'.$tcode.'</span>';    
        }

        // If a task involves a whole zone's teams, shorten the list by writing out a "zone" button
        // instead of a long list. Unset full zones' teams from list -- output stragglers later
        $ak_tta = array_keys($tt_all);
        $fullZones = array();
        $fullTeamZones = 0;
        
        foreach ($ztlist as $zone => $tlist){
            $curDiff = array_diff($tlist, $ak_tta);
            if (empty($curDiff)){
                if($zone != 'GMS'){  
                   $fullTeamZones++; 
                }
                $fullZones[]=$zone;    
                
                foreach($tlist as $tid){
                    unset($tt_p_only[$tid]);
                }
            }    
        }
        // @DBALL var (EXEC + 4 zones in usual org chart)
        if($fullTeamZones >= 4){
            $buttons2.= '<span class="btn btn-primary btn-xxs ban-edit">TEAMS</span>';
            if(in_array('GMS', $fullZones)){
                $buttons2.= '<span class="btn btn-primary btn-xxs ban-edit">GMS</span>';
            }
        }
        else{
            foreach ($fullZones as $zone){
                $buttons2.= '<span class="btn btn-primary btn-xxs ban-edit">'.$zone.'</span>';    
            }
        }
        // Stragglers
        foreach ($tt_p_only as $tid=>$team){
            $buttons2.= '<span data-trid ="2" data-teamid = "'.$tid.'" class="btn btn-default btn-xxs tglTR is-pushed'.$allowPush.'">'.$team.'</span>';
        }                                    
        //This is a lazy way to show requests before pushes
        $buttons = $buttons13.$buttons2;
        
        return $buttons;
    }

    public function makeTeamsSigBasic($taskTeams = array(), $zoneTeamList){
        $tt = ($taskTeams)? $taskTeams: array();
        $buttons13 = $buttons2 = '';

        // Figures out teams in each zone, to combine buttons
        $ztlist = array();
        foreach ($zoneTeamList as $zone => $tids){
            $ztlist[$zone] = array_keys($tids);
        }
        $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
        $tt_p = Hash::combine($tt, '{n}[task_role_id=2].team_id', '{n}[task_role_id=2].team_code');                                    
        $tt_oa = Hash::combine($tt, '{n}[task_role_id=3].team_id', '{n}[task_role_id=3].team_code');
        $tt_ca = Hash::combine($tt, '{n}[task_role_id=4].team_id', '{n}[task_role_id=4].team_code');
        $tt_all = Hash::combine($tt, '{n}.team_id', '{n}.team_code');
        
        // Pushed ONLY
        $tt_p_only = $tt_p;

        foreach ($tt_l as $tid => $tcode){
            $buttons13.= '<span class="btn btn-ttrid1 btn-xxs">'.$tcode.'</span>';    
        }                                    
        foreach ($tt_oa as $tid => $tcode){
            $buttons13.= '<span class="btn btn-danger btn-xxs">'.$tcode.'</span>';    
        }
        foreach ($tt_ca as $tid => $tcode){
            $buttons13.= '<span class="btn btn-success btn-xxs">'.$tcode.'</span>';    
        }

        // If a task involves a whole zone's teams, shorten the list by writing out a "zone" button
        // instead of a long list. Unset full zones' teams from list -- output stragglers later
        $ak_tta = array_keys($tt_all);
        $fullZones = array();
        $fullTeamZones = 0;
        
        foreach ($ztlist as $zone => $tlist){
            $curDiff = array_diff($tlist, $ak_tta);
            if (empty($curDiff)){
                if($zone != 'GMS'){  
                   $fullTeamZones++; 
                }
                $fullZones[]=$zone;    
                
                foreach($tlist as $tid){
                    unset($tt_p_only[$tid]);
                }
            }    
        }
        // @DBALL var (EXEC + 4 zones in usual org chart)
        if($fullTeamZones >= 5){
            $buttons2.= '<span class="btn btn-primary btn-xxs">TEAMS</span>';
            if(in_array('GMS', $fullZones)){
                $buttons2.= '<span class="btn btn-primary btn-xxs">GMS</span>';
            }
        }
        else{
            foreach ($fullZones as $zone){
                $buttons2.= '<span class="btn btn-primary btn-xxs">'.$zone.'</span>';    
            }
        }
        // Stragglers
        foreach ($tt_p_only as $tid=>$team){
            $buttons2.= '<span class="btn btn-default btn-xxs">'.$team.'</span>';
        }                                    
        //This is a lazy way to show requests before pushes
        $buttons = $buttons13.$buttons2;
        
        return $buttons;
    }
    
    public function makeTeamsSigNoPush($taskTeams = array(), $zoneTeamList, $userControls = false, $task=false){
        $tt = ($taskTeams)? $taskTeams: array();
        $buttons13 = $buttons2 = '';
        $allowOpen = ' ban-edit'; 
        $allowClose = ' ban-edit';
        $allowPush = ' ban-edit';
        
        if($userControls){
            $allowOpen = ' openTeam';
            $allowClose = ' closeTeam';
            $allowPush =  ' pushTeam';
        }
        
        // Figures out teams in each zone, to combine buttons
        $ztlist = array();
        foreach ($zoneTeamList as $zone => $tids){
            $ztlist[$zone] = array_keys($tids);
        }


        
        $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
        //$tt_p = Hash::combine($tt, '{n}[task_role_id=2].team_id', '{n}[task_role_id=2].team_code');                                    
        $tt_oa = Hash::combine($tt, '{n}[task_role_id=3].team_id', '{n}[task_role_id=3].team_code');
        $tt_ca = Hash::combine($tt, '{n}[task_role_id=4].team_id', '{n}[task_role_id=4].team_code');
        //$tt_all = Hash::combine($tt, '{n}.team_id', '{n}.team_code');
        
        // Pushed ONLY
        //$tt_p_only = $tt_p;

        foreach ($tt_l as $tid => $tcode){
            $buttons13.= '<span data-trid="1" data-teamid = "'.$tid.'" class="btn btn-ttrid1 btn-xxs ban-edit">'.$tcode.'</span>';    
        }                                    
        foreach ($tt_oa as $tid => $tcode){
            $buttons13.= '<span data-trid="3" data-teamid = "'.$tid.'" class="btn btn-danger btn-xxs is-oreq'.$allowOpen.'">'.$tcode.'</span>';    
        }
        foreach ($tt_ca as $tid => $tcode){
            $buttons13.= '<span data-trid ="4" data-teamid = "'.$tid.'" class="btn btn-success btn-xxs is-creq'.$allowClose.'">'.$tcode.'</span>';    
        }

        //$buttons = $buttons13;
        
        return $buttons13;
    }


    /**
     * pdfSig2016 Method
     * Returns simplified team signature with lead and involved teams. Doesn't distinguish requests. 
     * @param $taskTeams Array team roles for given task
     * @return String
     */
    public function pdfSig2016($taskTeams = array()){
        $tt = ($taskTeams)? $taskTeams: array();
        $buttons13 = '';
        //Lead and involved but not-lead.
        $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
        $tt_nl = Hash::combine($tt, '{n}[task_role_id!=1].team_id', '{n}[task_role_id!=1].team_code');

        foreach ($tt_l as $tid => $tcode){
            $buttons13.= '<span class="btn btn-ttrid1 btn-xxs">&nbsp;'.$tcode.'&nbsp;</span>&nbsp;';    
        }                                    

        if(count($tt_nl) > 10){
            $buttons13.= '<span class="btn btn-default btn-xxs">&nbsp;ALL&nbsp;</span>&nbsp;';
        }
        else{
            foreach ($tt_nl as $tid => $tcode){
                $buttons13.= '<span class="btn btn-default btn-xxs">&nbsp;'.$tcode.'&nbsp;</span>&nbsp;';
            }
        }
        
        return $buttons13;
    }

    //2018
    public function makeAssignmentButtons($ass = array()){
        $buttons = '';

        foreach ($ass as $handle){
            $buttons.= '<span class="btn btn-pale-yellow btn-xxs">@'.$handle.'</span> ';    
        }                                    
        return $buttons;
    }


    public function ttSigLeadOpen($taskTeams = array(), $zoneTeamList, $userControls = false, $task=false){
        $tt = ($taskTeams)? $taskTeams: array();
        $buttons13 = $buttons2 = '';
        $allowOpen = ' ban-edit'; 
        $allowClose = ' ban-edit';
        $allowPush = ' ban-edit';
        
        if($userControls){
            $allowOpen = ' openTeam';
            $allowClose = ' closeTeam';
            $allowPush =  ' pushTeam';
        }
        
        // Figures out teams in each zone, to combine buttons
        $ztlist = array();
        foreach ($zoneTeamList as $zone => $tids){
            $ztlist[$zone] = array_keys($tids);
        }

        $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
        //$tt_p = Hash::combine($tt, '{n}[task_role_id=2].team_id', '{n}[task_role_id=2].team_code');                                    
        $tt_oa = Hash::combine($tt, '{n}[task_role_id=3].team_id', '{n}[task_role_id=3].team_code');
        //$tt_ca = Hash::combine($tt, '{n}[task_role_id=4].team_id', '{n}[task_role_id=4].team_code');
        //$tt_all = Hash::combine($tt, '{n}.team_id', '{n}.team_code');
        
        // Pushed ONLY
        //$tt_p_only = $tt_p;

        foreach ($tt_l as $tid => $tcode){
            $buttons13.= '<span data-trid="1" data-teamid = "'.$tid.'" class="btn btn-ttrid1 btn-xxs ban-edit">'.$tcode.'</span>';    
        }                                    
        foreach ($tt_oa as $tid => $tcode){
            $buttons13.= '<span data-trid="3" data-teamid = "'.$tid.'" class="btn btn-danger btn-xxs is-oreq'.$allowOpen.'">'.$tcode.'</span>';    
        }
        //foreach ($tt_ca as $tid => $tcode){
        //    $buttons13.= '<span data-trid ="4" data-teamid = "'.$tid.'" class="btn btn-success btn-xxs is-creq'.$allowClose.'">'.$tcode.'</span>';    
        //}

        //$buttons = $buttons13;
        
        return $buttons13;
    }
    
    public function makePdfTeamsSig2015($taskTeams = array(), $ztlist){
        $tt = ($taskTeams)? $taskTeams: array();
        $button_lead = array();
        $button_pushed=array();
        $button_assist=array();       
        //$buttons13 = '';
        //$buttons2 = '';
        
        $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
        $tt_p = Hash::combine($tt, '{n}[task_role_id=2].team_id', '{n}[task_role_id=2].team_code');                                    
        $tt_r = Hash::combine($tt, '{n}[task_role_id=3].team_id', '{n}[task_role_id=3].team_code');
        $tt_all = Hash::combine($tt, '{n}.team_id', '{n}.team_code');
        
        // Pushed ONLY
        $tt_p_only = array_diff($tt_p, $tt_r);
        
        foreach($tt_r as $tid =>$tcode){
            $button_assist[]= $tcode;
        }

        // lazy future compatibility... >1 lead?
        foreach ($tt_l as $tid => $tcode){
            $button_lead[]= $tcode;    
        }                                    
        
        //foreach ($tt_r as $tid => $tcode){
        //    $buttons13.= '<span class="btn btn-danger btn-xxs">'.$tcode.'</span>';    
        //}
        // If a task involves a whole zone's teams, shorten the list by writing
        // out a "Z#" button insted of a list of the teams.
        // Finally, unset the full zones' teams from the list, so we can output
        // the stragglers later

        $ak_tta = array_keys($tt_all);
        $fullZones = array();
        $fullTeamZones = 0;
        
        foreach ($ztlist as $zone => $tlist){
            $curDiff = array_diff($tlist, $ak_tta);
            if (empty($curDiff)){
                if($zone != 'GMS'){
                   $fullTeamZones++; 
                }
                $fullZones[]=$zone;    
                //$buttons2.= '<span class="btn btn-default btn-xxs">Z'.$znum.'</span>';
                
                foreach($tlist as $tid){
                    unset($tt_p_only[$tid]);
                }
            }    
        }
        // arbitra
        if($fullTeamZones >= 5){
            $button_pushed[] = 'ALL';
            
            if(in_array('GMS', $fullZones)){
                $button_pushed[] = 'GMS';
            }
        }
        
        else{
            foreach ($fullZones as $zone){
                $button_pushed[] = $zone;    
            }
        }
        // Stragglers
        foreach ($tt_p_only as $team){
            $button_pushed[]= $team;
        }                                    
        //This is a lazy way to show requests before pushes
        //$buttons = $buttons13.$buttons2;
    
        $buttons = array('lead'=>$button_lead, 'assist'=>$button_assist, 'pushed'=>$button_pushed);
        //return array('lead'=>$button_lead, 'pushed'=>$button_pushed);
        return $buttons;

        
        
    }

    public function miniSubtaskRow($task){
        $html = '<div class="linked_task"
            id="tid'.$task['id'].'"
            data-tid="'.$task['id'].'" 
            style="border-left: 5px solid '. $task['task_color_code'].'">

            <div class="row astHeading" data-tid="'.$task['id'].'">
                <div class="col-xs-3">'. date('M j g:iA', strtotime($task['start_time'])).'</div>
                <div class="col-xs-1"><span class="btn btn-ttrid1 btn-xxs">'.$task['team_code'].'</span></div>
                <div class="col-xs-3"><strong>'.$task['task_type'].':</strong></div>
                <div class="col-xs-5">'.$task['short_description'].'</div>
            </div>                            
        </div>';
                
        return $html;  
    }

/*
    public function subtaskRow($task){
        $html = '<div class="linked_task astRow"
            id="tid'.$task['id'].'"
            data-tid="'.$task['id'].'" 
            style="border-left: 5px solid '. $task['task_color_code'].'">

            <div class="row astHeading" data-tid="'.$task['id'].'">
                <div class="col-xs-3 col-sm-3 col-md-2">'. date('M j g:i A', strtotime($task['start_time'])).'</div>
                <div class="col-xs-3 col-sm-4 col-md-1"><span class="btn btn-ttrid1 btn-xxs">'.$task['team_code'].'</span></div>
                <div class="col-xs-6 col-sm-5 col-md-9"><strong>'.$task['task_type'].':</strong> &nbsp;&nbsp;'.$task['short_description'].'</div>
            </div>                            
        </div>';
                
        return $html;  
    }
  */  

    public function offsetToFriendly($seconds, $type = 0){
        $sign = ($type < 0)? '-':'+';
        $plural = '';            
        
        if(!$seconds || $seconds == 0){
            return 'Sync';
        }
        elseif($seconds > 0){
            $getMins = floor($seconds/60);
            if($getMins>0){
                //$plural = 's';
                return $sign.$getMins.'';
            }
            $getSecs = floor($seconds % 60);
            return $sign.$getSecs.' s';
        }
    }


    // Used in tab_links (viewing/editing a task); 2018 In use; THIS task
    public function subtask($task, $opt = array()){
        $hr = $details = $tdet = $tacts = $offMsg = $hi_class = '';
        $br = $br_esc = ' ';
        $defaults = array(
            'show_offset'=>false,
            'multi_line'=>false,
            'show_view'=>false,
            'show_details'=>false,
            'highlight'=>false,     
        );
        
        $options = array_merge($defaults, $opt);
        
        //$this->log($options);
        $show_offset = $options['show_offset'];
        $show_multi = $options['multi_line'];
        $show_view = $options['show_view'];
        $show_details = $options['show_details'];
        $highlight = $options['highlight'];
        
        $toff = $task['time_offset'];
        $tctrl = $task['time_control'];
        $tctrl_type = isset($task['time_offset_type'])? $task['time_offset_type']: 0;
        $off = $this->offsetToFriendly($toff);
        
        if($highlight && is_string($highlight)){
            $hi_class = ' '.$highlight;
        }
        
        if($show_multi){
            $br_esc = ' \<\b\r\>';
            $br = '<br/>';
        }

        if($show_offset){
            if($tctrl){
                if($tctrl_type == -1 || $tctrl_type == 1){
                    $offMsg = '<button type="button" class="btn btn-info btn-xs">'.$off.' <i class="fa fa-clock-o"></i></button> ';                    
                }
                elseif($tctrl_type == -2 || $tctrl_type == 2){
                    $offMsg = '<button type="button" class="btn btn-info btn-xs"><i class="fa fa-clock-o"></i> '.$off.'</button> ';                    
                }
                else{
                    $offMsg = '<button type="button" class="btn btn-info btn-xs"><i class="fa fa-clock-o"></i> '.$off.'</button> ';
                }
            }
        }
        
        
        if($show_view || $offMsg){
             $tacts = '<div class="pull-right">'.$offMsg.$this->Html->link('<i class="fa fa-eye"></i> View', array(
                            'controller'=>'tasks',
                            'action'=>'compile',
                            '?'=>array('task'=>$task['id'])
                            ), 
                            array(
                                'escape'=>false,
                                'class'=>'btn btn-default btn-xs task_view_button')
                            ).
                        '</div>';
       }
        if($show_details){
            if(!empty($task['details'])){
                $hr = '<hr class="inSubtask">';
                $details = nl2br($task['details']);
            }        
        }        
             
        if($show_multi){
            $html = '<div class="linked_task'.$hi_class.'"
                id="tid'.$task['id'].'"
                data-tid="'.$task['id'].'" 
                style="border-left: 7px solid '. $task['task_color_code'].'">
    
                <div class="astHeading" data-tid="'.$task['id'].'">
                    <div class="row">
                        <div class="col-xs-4 col-sm-3 col-md-3"><span class="btn ban-edit btn-ttrid1 btn-xxs">'.$task['team_code'].'</span> <strong>'. date('g:i A', strtotime($task['start_time'])).'</strong></div>
                        <div class="col-xs-6 col-sm-6 col-md-6"><strong><em>'.$task['task_type'].'&nbsp;&nbsp;</em></strong>'.$task['short_description'].$hr.$details.'</div>
                        <div class="col-xs-2 col-sm-3 col-md-3"><div class="pull-right">'.$tacts.'</div></div>
                    </div>
                </div>                            
            </div>';
        }
        else{
            $html = '<div class="linked_task'.$hi_class.'"
            id="tid'.$task['id'].'"
            data-tid="'.$task['id'].'" 
            style="border-left: 7px solid '. $task['task_color_code'].'">

            <div class="row astHeading" data-tid="'.$task['id'].'">
                <div class="col-xs-4 col-sm-3 col-md-3"><span class="btn btn-ttrid1 ban-edit btn-xxs">'.$task['team_code'].'</span> <strong>'. date('g:i A', strtotime($task['start_time'])).'</strong></div>
                <div class="col-xs-6 col-sm-6 col-md-6"><strong><em>'.$task['task_type'].'</em>&nbsp;&nbsp;</strong>'.$task['short_description'].'</div>
                <div class="col-xs-2 col-sm-3 col-md-3"><div class="pull-right">'.$tacts.'</div></div>
            </div>                            
        </div>';
        }
                
        return $html;  
    }

    //In use - 2018
    // Synced tasks
    public function subtaskRowSingleWithOffset($task, $highlight=false){
        //$this->log($task);
        $toff = $task['time_offset'];
        $tctrl = $task['time_control'];
        $tctrl_type = isset($task['time_offset_type'])? $task['time_offset_type']:0;
        $strMsg = '';
        $off = $this->offsetToFriendly($toff,$tctrl_type);
        $bg = ($highlight)? ' highlight-thistask':'';
        if($tctrl){
            if($tctrl_type == -1 || $tctrl_type == 1){
                $strMsg = ($toff == 0) ? '<button type="button" class="btn btn-xs btn-info">'.$off.' <i class="fa fa-clock-o"></i></button>' : '<button type="button" class="btn btn-xs btn-info">'.$off.' <i class="fa fa-clock-o"></i></button>';
            }
            elseif($tctrl_type == -2 || $tctrl_type == 2){
                $strMsg = ($toff == 0) ? '<button type="button" class="btn btn-xs btn-info"><i class="fa fa-clock-o"></i> '.$off.' </button>':'<button type="button" class="btn btn-xs btn-info" style="margin-bottom:2px;"><i class="fa fa-clock-o"></i> '.$off.'</button>';                                
            }
        }
        
        $html = '<div class="linked_task'.$bg.'" data-tid="'.$task['id'].' "style="border-left: 7px solid '.$task['task_color_code'].'">
             <div class="row">
                <div class="col-xs-10 col-sm-11 col-md-11">
                    <div class="row">
                        <div class="col-xs-2 col-sm-1 col-md-1"><span class="btn btn-ttrid1 ban-edit btn-xxs">'.$task['team_code'].'</span></div>
                        <div class="col-xs-5 col-sm-2 col-md-2"> <span><strong>'.date('g:i A', strtotime($task['start_time'])).'</strong></span></div>
                        <div class="col-xs-5 col-sm-3 col-md-2 sm-bot-marg"><strong><em>'.$task['task_type'].'</em></strong></div>
                        <div class="col-xs-12 col-sm-6 col-md-7"> &nbsp;'.$task['short_description'].'</div>
                    </div>
                </div>
                <div class="col-xs-2 col-sm-1 col-md-1"><span class="pull-right">'.$strMsg.'</span></div>
            </div>                            
       
        </div>';
                
        return $html;  
    }
/*
 *      <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-3"><div class="row"><div class="col-xs-2"><span class="btn btn-ttrid1 ban-edit btn-xxs">'.$task['team_code'].'</span></div><div class="col-xs-4"> <span><strong>'.date('g:i A', strtotime($task['start_time'])).'</strong></span></div></div></div>
                <div class="col-xs-12 col-sm-7 col-md-7"><div class="row"><div class="col-xs-6"><strong><em>'.$task['task_type'].'</em></strong></div><div class="col-xs-12"> &nbsp;'.$task['short_description'].'</div></div></div>
                <div class="col-xs-12 col-sm-2 col-md-2"><span class="pull-right">'.$strMsg.'</span></div>
            </div>                            
       /*
    // Parent task, linked+synced tasks - 2018 - used in team's homepage
    public function subtaskRowSingle($task, $options = array()){
        $date_format = 'M j g:i A';
        $br = $safe_br = '';
        if(!empty($options)){
            if($options['date_format']){
                $date_format = $options['date_format'];    
            }
        }
        $html = '<div class="linked_task col-xs-12" id="tid'.$task['id'].'" data-tid="'.$task['id'].'" style="border-left: 5px solid '.$task['task_color_code'].'"><div class="row astHeading" data-tid="'.$task['id'].'">
                <div class="col-xs-2 col-sm-2 col-md-2">'.date($date_format, strtotime($task['start_time'])).'</div>
                <div class="col-xs-2 col-sm-2 col-md-1"><span class="btn btn-ttrid1 ban-edit btn-xxs">'.$task['team_code'].'</span></div>
                <div class="col-xs-8 col-sm-8 col-md-9"><strong>'.$task['task_type'].':</strong>&nbsp;&nbsp;'.$task['short_description'].'</div>
            </div></div>';
        return $html;  
    }

    // Task-->linkages; 1 & 2 levels up
    /*
    public function subtaskMultiWithView($task, $show_offset = false){
        $toff = $task['time_offset'];
        $tctrl = $task['time_control'];
        $tctrl_type = $task['time_offset_type'];
        
        $off = $this->offsetToFriendly($toff);
        $hr = '';
        $details = '';
        $tdet =''; 

        $strMsg = '';
        
        if($show_offset == true){
            if($tctrl){
                if($tctrl_type == -1 || $tctrl_type == 1){
                    $strMsg = '<button type="button" class="btn btn-orange btn-xs" style="margin-bottom:2px;">'.$off.' <i class="fa fa-clock-o"></i></button>&nbsp;';                    
                }
                elseif($tctrl_type == -2 || $tctrl_type == 2){
                    $strMsg = '<button type="button" class="btn btn-orange btn-xs" style="margin-bottom:2px;"><i class="fa fa-clock-o"></i> '.$off.'</button>&nbsp;';                    
                }
                else{
                    $strMsg = '<button type="button" class="btn btn-orange btn-xs" style="margin-bottom:2px;"><i class="fa fa-clock-o"></i> '.$off.'</button>&nbsp;';
                }
            }
        }
        
        
        if(!empty($task['details'])){
             $hr = '<hr class="inSubtask">';
             $details = nl2br($task['details']);
        }     
        $tdet = '<div class="pull-right">'.$strMsg.' '.$this->Html->link('<i class="fa fa-eye"></i> View', array(
                'controller'=>'tasks',
                'action'=>'compile',
                '?'=>array('task'=>$task['id'])
                ), 
                array(
                    'escape'=>false,
                    'class'=>'btn btn-default btn-xs task_view_button')
                ).
            '</div>';
        
        
        $html = '<div class="linked_task subtask_display" id="tid'.$task['id'].'" data-tid="'.$task['id'].'" style="border-left: 5px solid '. $task['task_color_code'].'">

            <div class="astHeading" data-tid="'.$task['id'].'">
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-3"><span class="btn ban-edit btn-ttrid1 btn-xxs">'.$task['team_code'].'</span> @ <b>'. date('M j g:i A', strtotime($task['start_time'])).'</b></div>
                    <div class="col-xs-7 col-sm-6 col-md-6 highlight"><strong>'.$task['task_type'].':&nbsp;&nbsp;</strong>'.$task['short_description'].$hr.$details.'</div>
                    <div class="col-xs-2 col-sm-3 col-md-3 pull-right"><div class="pull-right"><div class="row"><div class="col-md-12 col-sm-12 pull-right"><div class="pull-right">'.$tdet.'</div></div></div></div></div>
                
                </div>
            </div>                            
        </div>';
                
        return $html;  
    }
    */

/**
 * @param $task taskid
 * @param $details show/hide details if present (Default: false)
 * @param $full_width applies -ve right margin (Default: false)
 * 
 */
    /*
    public function fullSubtaskWithViewLink($task, $full_width=false){
        $row_class = ($full_width)? 'linked_task linked_task-neg-right astRow': 'linked_task astRow';
        
        $hr = '';
        $details = '';
        if(!empty($task['details'])){
             $hr = '<hr class="inSubtask">';
             $details = nl2br($task['details']);
        }
        
        $html = '<div class="'.$row_class.'" id="tid'.$task['id'].'"
                    data-tid="'.$task['id'].'" 
                    style="border-left: 5px solid '. $task['task_color_code'].'">

                    <div class="row astHeading" data-tid="'.$task['id'].'">
                        <div class="col-sm-2">'.date('M j <\\b\\r/> g:i A', strtotime($task['start_time'])).'
                        </div>
                        <div class="col-sm-2"><strong>'.$task['task_type'].'</strong><br/>
                        <span class="btn btn-ttrid1 btn-xxs">'.$task['team_code'].'</span>
                        
                        </div>
                        <div class="col-sm-8">
                            <div class="pull-right">'.$this->Html->link('View', array(
                                'controller'=>'tasks',
                                'action'=>'view', $task['id']),array(
                                'class'=>'btn btn-default btn-xs')).
                            '</div>'.
                            $task['short_description'].$hr.$details.'
                        </div>                            
                    </div>
                </div>';
                
        return $html;  
        
    }
*/

    // $team and $user{role,teams,handle} passed to check if user can delete comment
    public function commentByTaskWithDelete($comment, $team, $user){
        //$this->log($comment);
        $cid = (!empty($comment['Comment']['id']))? $comment['Comment']['id']: null;
        $tid = (!empty($comment['Comment']['task_id']))? $comment['Comment']['task_id']: null;
        $text = (!empty($comment['Comment']['text']))? $comment['Comment']['text']: '';
        $handle = (!empty($comment['Comment']['user_handle']))? $comment['Comment']['user_handle']: '';
        $created = (!empty($comment['Comment']['created']))? $this->Time->timeAgoInWords($comment['Comment']['created']): '';
        $urole = $user['role'];
        $canDel = '';
        
        if($urole >=200 || in_array($team, $user['teams']) || $handle == $user['handle']){
            $canDel = '&nbsp;&nbsp;<button type="button" data-tid="'.$tid.'" data-cid="'.$cid.'" class="close deleteComment">&times;</button>';    
        }

        $html = '
        <div class="row">
            <div class="col-sm-12">
                <div id="commentBody'.$cid.'" data-tid="'.$tid.'" class="panel panel-comment panel-tcom">
                    <div class="panel-heading panel-heading-tcom">
                        <strong>'.$handle.'</strong>
                        <div class="pull-right">
                            <span class="text-muted small">'.$created.'</span>'.$canDel.'
                        </div>
                    </div>
                    <div class="panel-body panel-body-tcom">'.$text.'</div>
                </div>
            </div>
        </div>';
        return $html;        
    }

    public function urgentTaskRowDouble($task){
        $html = '<div class="linked_task astRow"
            id="tid'.$task['id'].'"
            data-tid="'.$task['id'].'" 
            style="border-left: 5px solid '. $task['task_color_code'].'">

            <div class="row astHeading" data-tid="'.$task['id'].'">
                <div class="col-xs-3 col-sm-3 col-md-2">'. date('M j g:i A', strtotime($task['start_time'])).'</div>
                <div class="col-xs-1 col-sm-1 col-md-2"><span class="btn btn-ttrid1 btn-xxs">'.$task['team_code'].'</span></div>
                <div class="col-xs-1 col-sm-1 col-md-1"><span class="btn btn-ttrid1 btn-xxs">'.$task['team_code'].'</span></div>
                <div class="col-xs-2 col-sm-2 col-md-2"><strong>'.$task['task_type'].'</strong></div> 
                <div class="col-xs-6 col-sm-6 col-md-6">'.$task['short_description'].'</div>
            </div>                            
        </div>';
                
        return $html;  
    }

    // Adapted from W3C accessibility guidelines
    // SOURCE: http://jsfiddle.net/PXJ2C/
    function isColorLight($htmlCode){
        if($htmlCode[0] == '#')
          $htmlCode = substr($htmlCode, 1);
    
        if (strlen($htmlCode) == 3){
            $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
        }
    
        $r = hexdec($htmlCode[0] . $htmlCode[1]);
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
        $b = hexdec($htmlCode[4] . $htmlCode[5]);
    
        $bright = round((($r * 299) + ($g * 587) + ($b * 114)) /1000);

        if ($bright > 145){
            return true;
        }
        return false;
    }

    //2015
    public function chgMessage($change){
        if(isset($change['Change'])){
            $change = $change['Change'];
        }
        $msg = '';
        
        if(!$change){
            return $msg;
        }
        
        $ctype = $change['change_type_id'];
        $team = $change['team_code'];
        $old_val = $change['old_val'];
        $new_val = $change['new_val'];
        
        switch($old_val){
            case 1:
                $old_role = 'Lead';
                $old_class='btn-ttrid1';
                break;
            case 2:
                $old_role = 'Pushed';
                $old_class='btn-default';
                break;
            case 3:
                $old_role = 'Open';
                $old_class='btn-danger';
                break;
            case 4:
                $old_role = 'Closed';
                $old_class='btn-success';
                break;
            default:
                $old_role = null;
                $old_class='btn-default';
                
                break;
        }
        
        switch($new_val){
            case 1:
                $new_role = 'Lead';
                $new_class='btn-ttrid1';
                break;
            case 2:
                $new_role = 'Pushed';
                $new_class='btn-default';
                break;
            case 3:
                $new_role = 'Open';
                $new_class='btn-danger';
                break;
            case 4:
                $new_role = 'Closed';
                $new_class='btn-success';
                break;
            default:
                $new_role = null;
                $new_class='btn-default';
                break;
        }
        
        if(!$old_role && $new_role){
            $msg = '<span class="btn btn-ttrid1 btn-xxs">'.$team.'</span> set to <span class="btn btn-xxs '.$new_class.'">'.$new_role.'</span>';
        }
        else if($old_role && !$new_role){
            $msg = '<span class="btn btn-ttrid1 btn-xxs">'.$team.' removed as <span class="btn btn-xxs '.$old_class.'">'.$old_role.'</span>';
        }
        
        else if($old_role && $new_role){
            $msg = '<span class="btn btn-ttrid1 btn-xxs">'.$team.'</span> changed from <span class="btn btn-xxs '.$old_class.'">'.$old_role.'</span> to <span class="btn btn-xxs '.$new_class.'">'.$new_role.'</span>';
        }
        else if(!$old_role && !$new_role && ($ctype==299)){
            $msg = '<span class="btn btn-ttrid1 btn-xxs">'.$team.'</span> removed from task.';
        }

        return $msg;
    }

    public function durationFriendlyDaysOnly($start, $end, $two_lines = false){
        if(!$start||!$end){ return 'None';}
        
        $str='';
        $is = strtotime($start);
        $ie = strtotime($end);
        
        if($ie < $is){
            return 'Invalid Range';
        }

        $br = ($two_lines)? '<br>': '';
        
        $diff = ($ie-$is);        
        
        if($diff < (60*60*24)){
            return date('M j', strtotime($start));
        }
        // Days
        elseif($diff >= (60*60*24)){
            return date('M j', strtotime($start)).' -'.$br.date('M j', strtotime($end));
        }

        return $str;
    }

    public function durationFull($start, $end, $two_lines = false, $show_date = true){
        if(!$start || !$end){
            return 'None';
        }
        
        $str='';
        $is = strtotime($start);
        $ie = strtotime($end);
        
        if($ie < $is){
            return 'Invalid Range';
        }

        $br = ($two_lines)? '\<b\r\>': '';
        $day = ($show_date)? '\<\b\>M j\<\/\b\> '.$br: '';
        $diff = ($ie-$is);        
        
        if($diff < 60){
            return date($day.'H:i:s', strtotime($start)).' - '.date('H:i:s', strtotime($end));
        }
        // Minutes
        elseif(($diff >=60) && ($diff < 3600)){
            return date($day.'H:i:s', strtotime($start)).' - '.date('H:i:s', strtotime($end));
        }
        // Hours
        elseif(($diff >=3600) && ($diff < 60*60*24)){
            return date($day.'H:i:s', strtotime($start)).' - '.date('H:i:s', strtotime($end));
        }
        // Days
        elseif($diff >= (60*60*24)){
            return date('M j', strtotime($start)).' - '.date('M j', strtotime($end));
        }

        return $str;
    }

    public function durationGeneric($start, $end, $options = array()){
        if(!$start||!$end){ return 'None';}
        
        $two_lines = isset($options['two_lines'])? $options['two_lines']:true;
        $show_date = isset($options['show_date'])? $options['show_date']:true;
        $show_zero = isset($options['show_zero'])? $options['show_zero']:false;
        $seconds_matter = isset($options['seconds_matter'])? $options['seconds_matter']:false;
        
        $str= $sm = '';
        $is = strtotime($start);
        $ie = strtotime($end);
        
        if($ie < $is){
            return 'Invalid Range';
        }

        $sm = ($seconds_matter)? 'H:i:s':'H:i';
        $br = ($two_lines)? '\<b\r\>': '';
        $day = ($show_date)? '\<\b\>M j\<\/\b\> '.$br: '';
        $diff = ($ie-$is);        
        
        if($diff == 0){
            return date($day.'H:i', strtotime($start));
        }
        elseif($diff < 60){
            return date($day.$br.$sm, strtotime($start)).' - '.date($sm, strtotime($end));
        }
        // Minutes
        elseif(($diff >=60) && ($diff < 3600)){
            return date($day.$br.$sm, strtotime($start)).' - '.date($sm, strtotime($end));
        }
        // Hours
        elseif(($diff >=3600) && ($diff < 60*60*24)){
            return date($day.$br.$sm, strtotime($start)).' - '.date($sm, strtotime($end));
        }
        // Days
        elseif($diff >= (60*60*24)){
            return date('M j', strtotime($start)).' - '.date('M j', strtotime($end));
        }

        return $str;
    }
    
    public function durationFriendlyNoDate($start, $end, $options=array()){
        $show_d = '';

        if(isset($options['show_date']) && $options['show_date']==true){
            $show_d = 'M j ';
        }
        
        $t1 = date('Y-m-d H:i:s', strtotime($start));
        $s1 = date('s', strtotime($start));
        $s2 = date('s', strtotime($end));
        $t2 = date('Y-m-d H:i:s', strtotime($end));
        $d1 = date('Y-m-d', strtotime($start));
        $d2 = date('Y-m-d', strtotime($end));
        $m1 = date('A', strtotime($start));
        $m2 = date('A', strtotime($end));
        $diff = strtotime($t2)-strtotime($t1);
        $dh = floor($diff / 3600);
        $dm = floor(($diff / 60) % 60);
        $ds = $diff % 60;
        $str = '';

        $impSecs = false;
        
        // Secs are important when: (S1 != S2) && ((S1 != 0) && S2 !=0)) 
        if(($s1 != $s2) && (($s1 != 0) && ($s2 !=0) || (($diff <600) && ($diff >0)))){
            $impSecs = true;
        }

        if($diff == 0 && $impSecs == false){
            $str.= date($show_d.'g:i A', strtotime($t1));
        }
        elseif($diff == 0 && $impSecs == true){
            $str.= date($show_d.'g:i:s A', strtotime($t1));
        }
        // Important seconds
        elseif($diff < 60){
            $str.= $this->Time->format($show_d.'g:i:s', $start).' - '.$this->Time->format('g:i:s A', $end).'<br/>('.$diff.'s)';
        }
        // < hr
        elseif(($diff >= 60) && ($diff < 3600) && $impSecs == false){
            $str.= $this->Time->format($show_d.'g:i', $start).' - '.$this->Time->format('g:i A', $end).'<br/>('.$dm.' min)';
        }
        elseif(($diff >= 60) && ($diff < 3600) && $impSecs == true){
            $str.= $this->Time->format($show_d.'g:i:s', $start).' - '.$this->Time->format('g:i:s A', $end).'<br/>('.$dm.' min, '.$ds.'s)';
        }        
        // > 1h < 24h
        elseif($diff >= 3600 && $diff < 86400){
            $s = ($dh > 1)? 's':'';
            $a = ($m1 != $m2)? 'A':'';
             
            if($dm == 0){
                $str.= $this->Time->format($show_d.'g:i '.$a, $start).' - '.$this->Time->format('g:i A', $end).'<br/>('.$dh.' hr'.$s.')';
            }
            else{
                $str.= $this->Time->format($show_d.'g:i '.$a, $start).' - '.$this->Time->format('g:i A', $end).'<br/>('.$dh.' hr'.$s.', '.$dm.' min)';
            }
        }
        // >1 day or spans days
        elseif ($diff >= 86400){
            $str.= date($show_d.'g:i A', strtotime($t1));
        }
        if($d1 != $d2){
            $str.= '<br/>(Multi-day)';
        }

        return $str;
    }

    // $options: show date, show duration -- 2018 used in every task of `compile`
    public function startTimeFriendly($start, $end, $options=array()){
        //$show_d = '';
        //$show_dur = false;

        $show_dur = (isset($options['duration']) && ($options['duration'] == true)) ? true: false;
        $show_d = (isset($options['date']) && $options['date']==true)? '\<\b\>M j\<\/\b\> ':''; 
        
        $br1 = (isset($options['line_break_duration']) && ($options['line_break_duration']))? '<br/>':'';
        $br2 = (isset($options['line_break_multiday']) && ($options['line_break_multiday']))? '<br/>':'';

        $t1 = date('Y-m-d H:i:s', strtotime($start));
        $t2 = date('Y-m-d H:i:s', strtotime($end));
        $s1 = date('s', strtotime($start));
        $s2 = date('s', strtotime($end));
        $d1 = date('Y-m-d', strtotime($start));
        $d2 = date('Y-m-d', strtotime($end));
        $m1 = date('A', strtotime($start));
        $m2 = date('A', strtotime($end));
        $diff = strtotime($t2)-strtotime($t1);
        $dh = floor($diff / 3600);
        $dm = floor(($diff / 60) % 60);
        $ds = $diff % 60;
        $str = '';
        $space = ' ';
        $impSecs = false;
        
        // Try to guess when seconds are actually important. 10 mins seems reasonable.
        if(($s1 != $s2) && (($s1 != 0) && ($s2 !=0) || (($diff <600) && ($diff >0)))){
            $impSecs = true;
        }
        if($diff == 0 && $impSecs == false){
            $str.= date($show_d.'g:i A', strtotime($t1));
        }
        elseif($diff == 0 && $impSecs == true){
            $str.= date($show_d.'g:i:s A', strtotime($t1));
        }
        // Important seconds
        elseif($diff < 60){
            $dur = ($show_dur)? ' ('.$diff.'s)':'';
            $str.= $this->Time->format($show_d.'g:i:s', $start).' - '.$br.$this->Time->format('g:i:s A', $end).$dur;
        }
        // < hr
        elseif(($diff >= 60) && ($diff < 3600) && $impSecs == false){
            $dur = ($show_dur)? $br1.$space.'('.$dm.' min)':'';
            $str.= $this->Time->format($show_d.'g:i', $start).' - '.$this->Time->format('g:i A', $end).$dur;
        }
        elseif(($diff >= 60) && ($diff < 3600) && $impSecs == true){
            $dur = ($show_dur)? $br1.$space.'('.$dm.' min, '.$ds.'s)':'';
            $str.= $this->Time->format($show_d.'g:i:s', $start).' - '.$this->Time->format('g:i:s A', $end).$dur;
        }        
        // > 1h < 24h
        elseif($diff >= 3600 && $diff < 86400){
            $s = ($dh > 1)? 's':'';
            $a = ($m1 != $m2)? 'A':'';  // Show only if meridian changed (e.g. 10am-12pm but 10-12pm)
            $br = !empty($a)? ' - ':' - ';
             
            if($dm == 0){
                $dur = ($show_dur)? $br1.$space.'('.$dh.' hr'.$s.')':'';
                $str.= $this->Time->format($show_d.'g:i '.$a, $start).' - '.$this->Time->format('g:i A', $end).$dur;
            }
            else{
                $dur = ($show_dur)? $br1.$space.'('.$dh.' hr'.$s.' '.$dm.' min)':'';
                $str.= $this->Time->format($show_d.'g:i '.$a, $start).' - '.$this->Time->format('g:i A', $end).$dur;
            }
        }
        // >1 day or spans days
        elseif ($diff >= 86400){
            $str.= date($show_d.'g:i A', strtotime($t1));
        }
        if(($d1 != $d2) && $show_dur){
            $str.= $br2.$space.'(Multi-day)';
        }
        return $str;
    }




    public function oxfordComma($arr){
        //$this->log($arr);
        if(empty($arr) || !is_array($arr)){
            return;
        }
        
        $count = count($arr);
        $last = array_pop($arr);
        $all_but_last = implode(', ', $arr);
        
        if ($all_but_last){
            if($count > 2){
                $all_but_last .= ', and ';    
            }
            elseif($count == 2) {
               $all_but_last .= ' and '; 
            }
        }
        
        return $all_but_last.$last;
    }


    //**2018** Help Tools - result in popover on mousover/click with helpful info
    public function helpPopover($type, $options = array()){
        $out = $title = $msg = "";
        
        switch ($type){
            case 'synchronize':
                $title = 'Synchronize Tasks';
                $msg = "Allow the <b>linked task</b> to control the start time of <b>your</b> task.  Your task moves automatically whenever the linked task moves.<br><br><b>Note:</b> When this is active, the start time is set automatically from the linked task. You will be unable to edit the start time, but may set an end time (duration) and offset.";
                break;
            case 'offset':
                $title = 'Offset (For Synchronized Tasks)';
                $msg = "The amount of time (in mins) to maintain between the start of <u>this task</u> and the <u>linked task</u>. <b>Max = 12 hours (720 mins)</b>.<br><br>You can choose to synchronyize the start of your task to before/after the start/end of the linked task. <br><br>When the linked task moves, this task moves automatically, such that the offset is preseved. <br><br>e.g. If you set an offset of 10 Minutes <i>before the linked task starts</i>, your task will always start 10 minutes before the linked task, even if the linked task's start time changes. <br><br><b>Note:</b> You can only set an offset if the task is <b>synchronized</b> with the linked task.</b>";
                break;
            case 'offset_type':
                $title = 'Offset Type (For Synchronized Tasks)';
                $msg = "Control how your task is offset from the linked task.  You can have <b>your</b> task start before/after the start/end of the linked task. If the linked task moves or its duration changes, your task will move automatically to <b>maintain the offset.</b><br><b>Note:</b> You can only set an offset type if the task is <b>synchronized</b> with the linked task.</b>";
                break;                     
            case 'linked_task':
                $title = 'Linked Tasks';
                $msg = "Link tasks when they are related.<br><br><b>Examples</b><ul><li>Link to production elements (start of grand reception, dinner, etc.) when your tasks are related</li><li>Link tasks with meeting notes to 'Meeting' tasks</li><li>Link to requests from other teams to send them people/materials/info</li></ul>";
                break;                     
            case 'teams':
                $title = "Teams";
                $msg = "Many teams can be linked to a task. There are 5 states that a team can be in for each task:<br/><br/><p><span class='btn btn-xs btn-ttrid0'>None</span><br/>Team is not associated with the task.</p><p><span class='btn btn-xs btn-ttrid1'>Lead Team</span><br>Team is the owner of this task and it appears in their plan.</p><p><span class='btn btn-xs btn-ttrid2'>Pushed Team</span><br/>Team is CC'd on this task and it will appear in their plan.  They do <b>not</b> owe the lead team anything.</p><p><span class='btn btn-xs btn-danger'>Open Request</span><br/>This team <b>owes</b> the lead team materials/info/resources/etc. This task/request will appear in their plan.</p><p><span class='btn btn-xs btn-success'>Closed Request</span><br/>Team <b>had</b> a request for this task and but it is now considered <b>complete / fulfilled / acknowledged / etc.</b>. This task/closed request will appear in their plan.</p>";
                break;                     
            case 'assign_task':
                $title = "Assign Task";
                $msg = "Assign a task to specific roles if those roles are integral to the task or responsible for it.<br><br><b>NOTE:</b> Teams will need to create roles first before assigning tasks to them.";
                break;
            case 'view_type':
                $title = "View Type";
                $msg = "Views organize tasks in different ways, which can give specific insights. <br><br><b>Examples:</b><br><br>A <b>Rundown</b> emphasizes the order of tasks and how they're connected to other tasks.<br>A <b>Timeline</b> emphasizes what different teams/people are doing at the SAME time.<br><b>Owing/Waiting</b> views help teams track deliverables owing/owed to/from other teams.";                     
                break;
            case 'view_options':
                $title = "View Options";
                $msg = "Change the way tasks are displayed.<br><br><b>Show Linked Task Line Items - Keyboard Shortcut:</b> <kbd>Shift+z</kbd><br><br>Tasks that are <u>linked</u> appear in a rundown TWICE -- once under the task they're linked to and again as their own task line item.<br><br>Unselecting this option <u>hides the individual task line items</u>.  This is useful when reviewing the total plan, but if you need to see details of the individual linked tasks, it should be ON (checked).";                     
                break;
            
            case 'task_actions':
	           $title = "Task Actions";
               $msg = "<p><b>View Single Task:</b> Allows you to view this task alone. Useful for opening single tasks in a new tab/window.</p><p><b>Link to Task: </b> Create a new task that links to this task. Only available if this task allows links.</p><p><b>Delete Task:</b> Deletes task and associations (i.e. links to other teams).</p>";
	           break;
        }
        
        if(!empty($type) && !empty($msg)){
            $out = '<a class="helpTTs" tabindex="0" role="button" data-toggle="popover" title="'.$title.'" data-content="'.$msg.'"> <i class="fa fa-lg fa-question-circle text-info"></i></a>';    
        }
        
        return $out;
    }





   // Color lightness detection
    // SOURCE: http://stackoverflow.com/questions/12228644/how-to-detect-light-colors-with-php
/*
    function HTMLToRGB($htmlCode){
        if($htmlCode[0] == '#')
          $htmlCode = substr($htmlCode, 1);
    
        if (strlen($htmlCode) == 3){
            $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
        }
    
        $r = hexdec($htmlCode[0] . $htmlCode[1]);
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
        $b = hexdec($htmlCode[4] . $htmlCode[5]);
    
        return $b + ($g << 0x8) + ($r << 0x10);
      }


    function RGBToHSL($RGB) {
        $r = 0xFF & ($RGB >> 0x10);
        $g = 0xFF & ($RGB >> 0x8);
        $b = 0xFF & $RGB;
    
        $r = ((float)$r) / 255.0;
        $g = ((float)$g) / 255.0;
        $b = ((float)$b) / 255.0;
    
        $maxC = max($r, $g, $b);
        $minC = min($r, $g, $b);
    
        $l = ($maxC + $minC) / 2.0;
    
        if($maxC == $minC)
        {
          $s = 0;
          $h = 0;
        }
        else
        {
          if($l < .5)
          {
            $s = ($maxC - $minC) / ($maxC + $minC);
          }
          else
          {
            $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
          }
          if($r == $maxC)
            $h = ($g - $b) / ($maxC - $minC);
          if($g == $maxC)
            $h = 2.0 + ($b - $r) / ($maxC - $minC);
          if($b == $maxC)
            $h = 4.0 + ($r - $g) / ($maxC - $minC);
    
          $h = $h / 6.0; 
        }
    
        $h = (int)round(255.0 * $h);
        $s = (int)round(255.0 * $s);
        $l = (int)round(255.0 * $l);

    return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
  }

    public function subtaskWithView($task){
        $html = '<div class="linked_task astRow"
                    id="tid'.$task['id'].'"
                    data-tid="'.$task['id'].'" 
                    style="border-left: 5px solid '. $task['task_color_code'].'">

                    <div class="row astHeading" data-tid="'.$task['id'].'">
                        <div class="col-sm-2">'. date('M j g:i A', strtotime($task['start_time'])).'</div>
                        <div class="col-sm-3"><strong>'.$task['task_type'].'</strong><br/>
                        <span class="btn btn-ttrid1">'.$task['team_code'].'</span></div>
                        <div class="col-sm-7">
                        <div class="pull-right">'.$this->Html->link('View', array(
                            'controller'=>'tasks',
                            'action'=>'view',$task['id']),array(
                            'class'=>'btn btn-default btn-xs')).
                        '</div>
                        
                        '.$task['short_description'].'</div>
                    </div>                            
                </div>';
                
        return $html;  
        
    }

*/

/*
    public function makeTeamsSig2015($taskTeams = array(), $ztlist){
        $tt = ($taskTeams)? $taskTeams: array();
        $buttons13 = '';
        $buttons2 = '';
        
        $tt_l = Hash::combine($tt, '{n}[task_role_id=1].team_id', '{n}[task_role_id=1].team_code');
        $tt_p = Hash::combine($tt, '{n}[task_role_id=2].team_id', '{n}[task_role_id=2].team_code');                                    
        $tt_r = Hash::combine($tt, '{n}[task_role_id=3].team_id', '{n}[task_role_id=3].team_code');
        $tt_all = Hash::combine($tt, '{n}.team_id', '{n}.team_code');
        
        // Pushed ONLY
        $tt_p_only = array_diff($tt_p, $tt_r);

        foreach ($tt_l as $tid => $tcode){
            $buttons13.= '<span class="btn btn-ttrid1">'.$tcode.'</span>';    
        }                                    
        
        foreach ($tt_r as $tid => $tcode){
            $buttons13.= '<span class="btn btn-danger btn-xxs">'.$tcode.'</span>';    
        }
        // If a task involves a whole zone's teams, shorten the list by writing
        // out a "Z#" button insted of a list of the teams.
        // Finally, unset the full zones' teams from the list, so we can output
        // the stragglers later

        $ak_tta = array_keys($tt_all);
        $fullZones = array();
        $fullTeamZones = 0;
        
        foreach ($ztlist as $zone => $tlist){
            //debug($znum);
            $curDiff = array_diff($tlist, $ak_tta);
            if (empty($curDiff)){
                if($zone != 'GMS'){
                   $fullTeamZones++; 
                }
                $fullZones[]=$zone;    
                //$buttons2.= '<span class="btn btn-default btn-xxs">Z'.$znum.'</span>';
                
                foreach($tlist as $tid){
                    unset($tt_p_only[$tid]);
                }
            }    
        }
        
        if($fullTeamZones >= 5){
            $buttons2.= '<span class="btn btn-success btn-xxs">ALL</span>';
            if(in_array('GMS', $fullZones)){
                $buttons2.= '<span class="btn btn-success btn-xxs">GMS</span>';
            }
        }
        else{
            foreach ($fullZones as $zone){
                $buttons2.= '<span class="btn btn-success btn-xxs">'.$zone.'</span>';    
            }
        }
        // Stragglers
        foreach ($tt_p_only as $team){
            $buttons2.= '<span class="btn btn-default btn-xxs">'.$team.'</span>';
        }                                    
        //This is a lazy way to show requests before pushes
        $buttons = $buttons13.$buttons2;
        
        return $buttons;
    }


    public function durationFriendly($start, $end){
        if(!$start||!$end){ return 'None';}
        
        $str = '';
        $is = strtotime($start);
        $ie = strtotime($end);
        
        if($ie < $is){
            return 'Invalid Range';
        }
        
        $diff = ($ie-$is);        
        
        if($diff == 0){
            return date('M j g:i A', strtotime($start));
        }
        // Assume important seconds 
        elseif($diff < 60){
            return date('M j H:i:s', strtotime($start)).' - '.date('H:i:s', strtotime($end));
        }
        // Minutes
        elseif(($diff >=60) && ($diff < 3600)){
            return date('M j H:i:s', strtotime($start)).' - '.date('H:i:s', strtotime($end));
        }
        // Hours
        elseif(($diff >=3600) && ($diff < 60*60*24)){
            return date('M j g:i A', strtotime($start)).' - '.date('g:i A', strtotime($end));
        }
        // Days
        elseif($diff >= (60*60*24)){
            return date('M j', strtotime($start)).' - '.date('M j', strtotime($end));
        }

        return $str;
    }

    public function commentsByTaskSecondary($comment){
        $text = (!empty($comment['text']))? $comment['text']: '';
        $handle = (!empty($comment['user_handle']))? $comment['user_handle']: '';
        $created = (!empty($comment['created']))? $this->Time->timeAgoInWords($comment['created']): '';
        
        $html = '
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-comment panel-tcom">
                    <div class="panel-heading panel-heading-tcom">
                        <small><strong>'.$handle.' </strong></small>
                        <span class="text-muted">commented '.$created.'</span>
                    </div>
                    <div class="panel-body panel-body-tcom">'.$text.'</div>
                </div>
            </div>
        </div>';
        return $html;        
    }

    public function subtaskSingleWithView($task){
        $hr = '';
        $details = '';
        if(!empty($task['details'])){
             $hr = '<hr class="inSubtask">';
             $details = nl2br($task['details']);
             
             $tdet = '
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2">'.$this->Html->link('<i class="fa fa-eye"></i> View', array(
                            'controller'=>'tasks',
                            'action'=>'view', $task['id']),array(
                            'escape'=>false,
                            'class'=>'btn btn-default btn-sm')).
                    '</div>
                    <div class="col-xs-9 col-sm-10 col-md-10">'.$hr.$details.'</div>
                </div>
             ';
        }
        
        $html = '<div class="linked_task astRow"
            id="tid'.$task['id'].'"
            data-tid="'.$task['id'].'" 
            style="border-left: 5px solid '. $task['task_color_code'].'">

            <div class="astHeading" data-tid="'.$task['id'].'">
                <div class="row">
                    <div class="col-xs-3 col-sm-2 col-md-2">'. date('M j g:i A', strtotime($task['start_time'])).'<br></div>
                    <div class="col-xs-1 col-sm-1 col-md-1"><span class="btn btn-ttrid1 btn-xxs">'.$task['team_code'].'</span></div>
                    <div class="col-xs-2 col-sm-3 col-md-2"><strong>'.$task['task_type'].'</strong></div> 
                    <div class="col-xs-6 col-sm-6 col-md-7">'.$task['short_description'].'</div>
                </div>'.$tdet.'
            </div>                            
        </div>';
                
        return $html;  
    }

    public function subtaskRowSinglePdf($task){
        $html = '<div class="linked_task"
            id="tid'.$task['id'].'"
            data-tid="'.$task['id'].'" 
            style="border-left: 5px solid '. $task['task_color_code'].'">

            <div class="row astHeading" data-tid="'.$task['id'].'">
                <div class="col-xs-2 col-sm-2 col-md-2">'. date('M j g:i A', strtotime($task['start_time'])).'</div>
                <div class="col-xs-3 col-sm-2 col-md-3">
                    <strong>'.$task['task_type'].'</strong>
                    <span class="btn btn-ttrid1 ban-edit btn-xxs">'.$task['team_code'].' </span>

                </div>
                <div class="col-xs-5 col-sm-8 col-md-7">'.$task['short_description'].'</div>
            </div>                            
        </div>';
                
        return $html;  
    }

    public function offsetToMinSecParts($seconds){
        if(!$seconds){
            return false;
        }
        elseif($seconds == 0){
            $sign = '+';
            $m = 0;
            $s = 0;    
        }
        elseif($seconds>0){
            $getMins = floor($seconds/60);
            $getSecs = floor($seconds % 60);
            //
            $sign = '+';
            $m = $getMins;
            $s = $getSecs;
        }
        elseif($seconds<0){
            $getMins = floor(abs($seconds)/60);
            $getSecs = floor(abs($seconds) % 60);
            $sign = '-';
            $m = $getMins;
            $s = $getSecs;
            }
        return array('sign'=>$sign, 'min'=>$m, 'sec'=>$s);
    }

    public function offsetToMmss($seconds){
        if(!$seconds || $seconds == 0){
            return '00:00';
        }

        $getMins = floor(abs($seconds)/60);
        $getSecs = floor(abs($seconds) % 60);

        if($getMins < 10){
            $getMins = '0'.$getMins;
        }
        if($getSecs < 10){
            $getSecs = '0'.$getSecs;
        }
        if($seconds>0){
            return '+'.$getMins.':'.$getSecs;
        }
        elseif($seconds<0){
            return '-'.$getMins.':'.$getSecs;
        }

    }
*/
/*
    public function commentInTask($comment){
        $text = (!empty($comment['text']))? $comment['text']: '';
        $handle = (!empty($comment['user_handle']))? $comment['user_handle']: '';
        $created = (!empty($comment['created']))? $this->Time->timeAgoInWords($comment['created']): '';
        
        $html = '
            <blockquote class="tComment"><i class="fa fa-clock-o"></i> '.$created.
            '<p>'.
            $text.'</p><footer>'.
            $handle.'</footer></blockquote>';
        return $html;        
    }

    public function commentInTask2($comment){
        $text = (!empty($comment['text']))? $comment['text']: '';
        $handle = (!empty($comment['user_handle']))? $comment['user_handle']: '';
        $created = (!empty($comment['created']))? $this->Time->timeAgoInWords($comment['created']): '';
        
        $html = '
            <div class="well well-sm">
                <div class="row">
                    <div class="col-sm-12">'.$text.'</div>                
                </div>
                <div class="row">
                    <div class="col-sm-8">'.$handle.'</div>
                    <div class="col-sm-4 pull-right"><i class="fa fa-clock-o"></i> '.$created.'</div>                
                </div>
            </div>';
        return $html;        
    }

    public function commentInTask3($comment){
        $text = (!empty($comment['text']))? $comment['text']: '';
        $handle = (!empty($comment['user_handle']))? $comment['user_handle']: '';
        $created = (!empty($comment['created']))? $this->Time->timeAgoInWords($comment['created']): '';
        
        $html = '
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-comment panel-tcom">
                    <div class="panel-heading panel-heading-tcom">
                        <strong>'.$handle.' </strong>
                        <span class="text-muted">commented '.$created.'</span>
                    </div>
                    <div class="panel-body panel-body-tcom">'.$text.'</div>
                </div>
            </div>
        </div>';
        return $html;        
    }
*/







//EOF
}
//EOF
?>