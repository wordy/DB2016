<?php
//$today = date('Y-m-d');
$owa = date('Y-m-d', strtotime('-1 week'));
?>

<h1>Email Digest Admin</h1>

<p>This interface allows you to send the Digest to users or whole teams.</p>
<div class="col-md-10 col-md-offset-1">
<?php



foreach ($data as $tid =>$tcounts){
    $allow_sendall = true;
    
    if(isset($digestUsers[$tid])){
        $last_digests = Hash::extract($digestUsers[$tid], '{n}.last_digest');
        
        // Disallow send to all team members if someone has gotten digest already recently.
        foreach($last_digests as $k => $ld){
            if(strtotime($ld) > strtotime($owa)){
                $allow_sendall = false;
                break;
            }
        }
    }
    else{ $allow_sendall = false;}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-2">
                <b><?php echo $teamsList[$tid];?></b>        
            </div>

            <div class="col-xs-8">
                <div class="pull-right">
                    <span class="badge <?php echo ($tcounts['next_meeting'] > 0)?'badge-success':'badge-danger';?>">
                        <?php echo $tcounts['next_meeting'];?>
                    </span> Ops Meeting &nbsp;&nbsp;
                    <span class="badge <?php echo ($tcounts['recent_requests'] > 0)?'badge-success':'badge-danger';?>">
                        <?php echo $tcounts['recent_requests'];?>
                    </span> Recent Requests &nbsp;&nbsp;
        
                    <span class="badge <?php echo ($tcounts['recent_links'] > 0)?'badge-success':'badge-danger';?>">
                        <?php echo $tcounts['recent_links'];?>
                    </span> Recent Links &nbsp;&nbsp;
                </div>                
            </div>

            <div class="col-xs-2">
                <div class="pull-right">
                    <?php
                        $sent_cl = false;
                        if(($tcounts['recent_links'] == 0 && $tcounts['recent_requests'] == 0 && $tcounts['next_meeting'] == 0) || $allow_sendall == false ){
                            $sent_cl = 'btn-danger';  
                        }
                        elseif($tcounts['recent_links'] >= 1 || $tcounts['recent_requests'] >= 1 || $tcounts['next_meeting'] >=1 || $allow_sendall == false ){
                            $sent_cl = 'btn-success';
                        }
                    ?>
                    <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'sendDigestToTeam', $tid));?>" class="btn btn-xs btn-default <?php echo ($sent_cl)? $sent_cl:'';?>"><i class="fa fa-send-o"></i> Send to Team</a>    
                </div>
            </div>
        </div>
    </div>
        <?php
            if(isset($digestUsers[$tid])){?>
                <table class="table">
                    <tr>
                        <th>User</th>
                        <th>Last Sent (Days Ago)</th>
                        <th><span class="pull-right">Actions</span></th>
                    </tr>
                <?php
                $i = 1;
                foreach($digestUsers[$tid] as $uid => $usr){
                    
                    echo '<tr><td>'.$usr['user_handle'].'</td>';
                    echo '<td>';
                    if($usr['last_digest']){
                        echo floor((strtotime(date('Y-m-d')) - strtotime($usr['last_digest']))/(60*60*24));
                        /*
                        echo $this->Time->timeAgoInWords($usr['last_digest'],
                         array(
                           'accuracy' => array('day' => 'day'),
)
                        
                        );*/
                    }
                    echo '</td>';
                    echo '<td><div class="pull-right"><span class="btn btn-xs btn-default '.$sent_cl.'"><i class="fa fa-send-o"></i> Send to User</span></div></td></tr>';
                    $i++;
                }
                echo '</table>';
            } 
        ?>


                
                
</div>



<?php
} // End foreach
//debug($digestUsers);




?>

</div>