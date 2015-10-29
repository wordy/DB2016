<?php

    //$viewBox = 'inbox';
    
    
    //echo $this->Html->css('notification_list.css', array('inline'=>false));
    
    //$num_teams = count($this->Session->read('Auth.User.Teams'));
    
    


   $this->Js->buffer("

   
   
   
   
   
   ");




    
?>
   <div class="row">
    
        <div class="timeline-centered">
        
                <?php 

            if(!empty($notifications)){ 
                
                
            foreach($notifications as $note):
                $nid = $note['Notification']['id'];
                $type = $note['Notification']['type_id'];
                
                $is_approved = false;
                
                if($note['Notification']['is_approved'] == 1 && $type >=100){
                    $is_approved = true;
                    continue;
                    
                }
                
                // bg-secondary --> red, sys
                // bg-info
                // bg-warning
                // bg-success
                
                
                if($type < 100){
                    $color = 'bg-danger';
                    $title = 'SYSTEM MESSAGE';
                }
                elseif($type >= 100 && $type < 200){
                    $color = 'bg-danger';
                    $title = 'SYSTEM MESSAGE';
                }
                elseif($type >= 200 && $type < 300){
                    $color = 'bg-success';
                }
                else{
                    $color = 'bg-primary';
                }
                
                
                
            ?>
            
            <article class="timeline-entry" data-nid="<?php echo $nid; ?>">
                <div class="timeline-entry-inner">
                    <div class="timeline-icon <?php echo $color; ?>">
                        <i class="entypo-suitcase"></i><?php 
                        echo date('M d', strtotime($note['Notification']['created']));
                        
                        ?>
                    </div>

                    <div class="timeline-label">
                        <div class="row">
                            <div class="col-md-10">
                                <h2>
                                    <?php
                                        if($type<=100){
                                            echo '<h2><b>SYSTEM MESSAGE</b></h2>'; 
                                        }
                                        
                                        elseif($type >= 200 && $type <300){
                                            echo '<b>'.$note['Child']['team_code'] .'</b> responded to your request in ';
                                            echo $this->Html->link(date('M d g:i A', strtotime($note['Parent']['start_time'])).' ('.$note['Parent']['task_type'] .') '.$note['Parent']['short_description'], array(
                                                'controller' => 'tasks',
                                                'action' => 'view',
                                                $note['Parent']['id'],
                                                )
                                            );
                                        }
                                        elseif($type >= 300 && $type <400){
                                            echo '<b>'.$note['Parent']['team_code'] .'</b> requested your assistance in ';
                                            echo $this->Html->link(date('M d g:i A', strtotime($note['Parent']['start_time'])).' ('.$note['Parent']['task_type'] .') '.$note['Parent']['short_description'], array(
                                                'controller' => 'tasks',
                                                'action' => 'view',
                                                $note['Parent']['id'],
                                                )
                                            );
                                        }                                    
                                        elseif($type >=  400 && $type < 500){
                                            echo '<b>'.$note['Parent']['team_code'] .'</b> closed request in ';
                                            echo $this->Html->link(date('M d g:i A', strtotime($note['Parent']['start_time'])).' ('.$note['Parent']['task_type'] .') '.$note['Parent']['short_description'], array(
                                                'controller' => 'tasks',
                                                'action' => 'view',
                                                $note['Parent']['id'],
                                                )
                                            );
                                        }                                    
                                    
                                    ?>
                                </h2>
                                <?php 
                                
                                
                                if($type <=100){
                                
                                echo nl2br($note['Notification']['body']);
                                    
                                }
                                
                                elseif($type >= 200 && $type <300){
                                    echo '<blockquote>';
                                    echo $this->Html->link(date('M d g:i A', strtotime($note['Child']['start_time'])).' ('. $note['Child']['task_type'].') - '.$note['Child']['short_description'], array(
                                        'controller' => 'tasks',
                                        'action' => 'view', $note['Child']['id'],
                                    ));
                                    
                                    if (!empty($note['Child']['details'])){
                                        echo nl2br($note['Child']['details']);
                                    }
                                    echo '</blockquote>';
                                } 
                                
                                
                                
                                
                                
                                
                                
                                ?>
                            </div>
                            <div class="col-md-2">
                                <?php if($type >= 200 && $type < 300 && !$is_approved): ?>
                                    <button class="btn btn-sm btn-success btn-block accept_a" data-nid="<?php echo $nid; ?>" id="accept<?php echo $nid; ?>"><i class="fa fa-thumbs-o-up fa-lg"></i>&nbsp;Approve</button>
                                    <br>
                                <?php elseif ($type >= 300 && $type < 400):?>
                                    <button class="btn btn-sm btn-danger btn-block del_note" data-nid="<?php echo $nid; ?>" id="del<?php echo $nid; ?>"><i class="fa fa-close"></i>&nbsp;Delete</button>

                                    <br>
                                <?php endif; ?>
                                <?php //echo ($note['Notification']['is_read'] == 0)? 'not read': 'read'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
               
        <?php   
              
            endforeach;
                
            ?>
    

    </div>

    
    </div> 
              <?php   
              
            
                
            }
            
            else { ?>

                        <div class="alert alert-info" role="alert">
                            <b>No Notifications: </b> There are currently no notifications for the team selected.
                </div>
            <?php    
            }
        
        ?>

            
            
  
        
        
    
    
<?php //echo $this->Js->writeBuffer(); ?>

<!-- CREDIT FOR PAGE LAYOUT: http://www.bootsnipp.com/snippets/featured/single-column-timeline-dotted -->