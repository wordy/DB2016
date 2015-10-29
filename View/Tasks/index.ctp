<?php


?>


<div class="container">
          
          <div class="row">
              <div class="col-md-12"><!-- Begin table data div-->
                  
<div class="tasks index">
            <h2><?php echo __('Tasks'); ?></h2>
            
            <div class="table-responsive">
                <table id="tasks-index" class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <!--<th><?php echo $this->Paginator->sort('id','ID'); ?></th>-->
                            <th width="1%"> </th>
                            <th width="5%"><?php echo $this->Paginator->sort('id','ID'); ?></th>
                            <th width="7%" colspan"2"><?php echo $this->Paginator->sort('start_time','Date'); ?></th>
                            <th width="8%" colspan"2"><?php echo $this->Paginator->sort('start_time','Time'); ?></th>
                            <th width="10%"><?php echo __('Teams'); ?></th>
                            <th width="5%"><?php echo $this->Paginator->sort('task_type_id','Type'); ?></th>
                            
                            <th width="40%"><?php echo $this->Paginator->sort('short_description','Description'); ?></th>
                            <th width="10%"><?php echo $this->Paginator->sort('created','Created'); ?></th>
                            <th width="14%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        
                        $i=1;
                        $k=1;
                        
                        $first_date = date('Y-m-d', strtotime($tasks[0]['Task']['start_time']));
                        $last_date =null;
                        $this_date ='';
                        $rsc = array();
                        $rsc[1]=1;
                        foreach ($tasks as $task):
                            
                            $this_date = date('Y-m-d', strtotime($task['Task']['start_time']));
                            
                            
                            
                        ?>
    
    
    
    
    
    <tr class="table-striped">

            
            <?php 
            
                if ($showTeamColors){
                    echo '<td style="border:1px solid #bbb; background:'.$task['Task']['task_color_code'].'">';
                }
                
                else
                    echo '<td style="background:#888">';            
            
            
            ?>
            
            
            
            
            
            </td>
            <td>
                <?php echo $task['Task']['id'];?> 
            </td>

            <td>
                <?php echo $this->Time->format('M j', $task['Task']['start_time']);?> 
            </td>
            <td>
                <?php 
                //echo $this->Time->format('M j<\b\\r>g:iA', $task['Task']['start_time']);
                echo $this->Time->format('g:iA', $task['Task']['start_time']);
                ?>
                <?php 
                

                
                $time1 = strtotime($task['Task']['start_time']);
                $time2 = strtotime($task['Task']['end_time']);
                $diff = $time2 - $time1;

                if($diff>=60){
                    echo '<br/>('.date('i', $diff).' min)';
                }
                
                
                
                ?>
                
                
                
                 
            <?php //echo $this->Time->format('g:i A', $task['Task']['end_time']);?></td>

            <td>
                <?php
                
                
                
                            
                        
                        echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                        //echo $this->Ops->makeTeamsSig2015($task['TasksTeam'], $ztlist);
                        echo $this->Ops->makeTeamsSig($task['TasksTeam'], $zoneTeamCodeList);
                        
                    
                        
                        ?> 
            </td>



    <td style="border-bottom: gray; background:#e9e9e9">
    <!--<td style="background:<?php echo $task['Task']['task_color_code'];?>">-->
            <?php echo $task['Task']['task_type']; ?></td> 
            
            
            <td><?php echo h($task['Task']['short_description']); ?></td>
            <td><?php echo (date('M j', strtotime($task['Task']['created']))); ?></td>
        <td>
            <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view',$task['Task']['id']), array('class'=>'btn btn-default btn-xs'));?>
            <?php echo $this->Html->link('Edit', array('controller'=>'tasks', 'action'=>'edit',$task['Task']['id']), array('class'=>'btn btn-default btn-xs'));?>         
            <?php echo $this->Html->link('Delete', array('controller'=>'tasks', 'action'=>'delete',$task['Task']['id']), array('class'=>'btn btn-default btn-xs'));?>         

        </td>
        </tr>


<?php $i++; $last_date = $this_date;endforeach; ?>
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
      
            <p><small>
                <?php
                echo $this->Paginator->counter(array(
                'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                ));
                ?>          </small></p>

            <ul class="pagination">
                <?php
        echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
        echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
        echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
    ?>
            </ul><!-- /.pagination -->
            
        </div><!-- /.index -->
                  
                  
              </div><!-- END table data div-->
          </div>
        </div>

      </div>
    
    
    
    
    
    
        
    </div><!-- /.container -->