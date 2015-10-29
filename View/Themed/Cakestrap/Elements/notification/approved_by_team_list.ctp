<?php

    //$viewBox = 'inbox';
    
   $this->Js->buffer("

   
   
   
   
   
   ");




    
?>
<div class="row">
<?php 
    if(!empty($notifications)){ ?>
    <div class="table-responsive">
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Notification</th>
                    <th>Actions</th>
                </tr>
                
            </thead>
            <tbody>
            <?php
            foreach($notifications as $note):
                $nid = $note['Notification']['id'];
                
                $is_approved = false;
                
                
                
                $note_type = $note['Notification']['type_id'];
                
                if($note_type <= 100){
                    continue;
                }
                
                if($note['Notification']['is_approved'] == 1){
                    $is_approved = true;
                    //continue;
                    
                }
                else{continue;}
                
                // bg-secondary --> red, sys
                // bg-info
                // bg-warning
                // bg-success
                
                $type = $note['Notification']['type_id'];
                
                if($type < 100){
                    $color = 'bg-danger';
                    $title = 'SYSTEM MESSAGE';
                }
                elseif($type >= 100 && $type < 200){
                    $color = 'bg-danger';
                    $title = 'SYSTEM MESSAGE';
                }
                elseif($type >= 200 && $type < 300){
                    $color = 'bg-info';
                }
                else{
                    $color = 'bg-primary';
                }
                
            ?>
                <tr>
                    <td><?php echo date('n\/d', strtotime($note['Notification']['created'])); ?></td>
                    <td>
                        <h4>
                            <?php
                                if($type >= 200 && $type <300){
                                    echo '<p><b>'.$note['Child']['team_code'] .'</b> responded to your request in ';
                                    echo $this->Html->link(date('M d g:i A', strtotime($note['Parent']['start_time'])).' ('.$note['Parent']['task_type'] .') '.$note['Parent']['short_description'], array(
                                        'controller' => 'tasks',
                                        'action' => 'view', $note['Parent']['id'],
                                        )
                                    );
                                    echo '</p>';
                                }
                            ?>
                        </h4>
                        <?php 
                            if($type >= 200 && $type <300){
                                echo '<p>';
                                echo $this->Html->link(date('M d g:i A', strtotime($note['Child']['start_time'])).' ('. $note['Child']['task_type'].') - '.$note['Child']['short_description'], array(
                                    'controller' => 'tasks',
                                    'action' => 'view', $note['Child']['id'],
                                ));
                                echo '</p>';
//                                if (!empty($note['Child']['details'])){
//                                    echo nl2br($note['Child']['details']);
//                                }
                                //echo '</blockquote>';
                            } 
                        ?> 
                    </td>
                    <td>
                        <?php if($type >= 100 && $is_approved): ?>
                            <!--<button class="btn btn-xs btn-default btn-block mark_read" data-nid="<?php echo $nid; ?>" id="mr<?php echo $nid; ?>"><i class="fa fa-check"></i>&nbsp;Mark Read</button>                            -->
                            <button class="btn btn-xs btn-danger btn-block del_note" data-nid="<?php echo $nid; ?>" id="del<?php echo $nid; ?>"><i class="fa fa-close"></i>&nbsp;Delete</button>
                        <?php endif; ?>
                    </td>
                    
                </tr>
                <?php   
                    endforeach;
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php   
    }
    
    ?>
<?php //echo $this->Js->writeBuffer(); ?>

<!-- CREDIT FOR PAGE LAYOUT: http://www.bootsnipp.com/snippets/featured/single-column-timeline-dotted -->