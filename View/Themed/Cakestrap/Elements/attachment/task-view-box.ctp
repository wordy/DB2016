
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="icon-asterisk"></i><b>&nbsp;Changes</b>
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12"><!-- Content-->
                    
                    
                            <!-- Table -->
        <table class="table table-condensed">
                              <thead>
                        <tr>
                                                            <th>Date</th>
                                                            <th>Type</th>
                                                            <th>Change</th>
                                                            
                        </tr>
                    </thead>
          <tbody>
                        <?php
                        foreach ($task['Change'] as $change): ?>
    <tr>
        
        <td><?php echo $this->Time->format('M d', $change['created']); ?></td>
        <td>
            <?php echo $change['change_type'];
        
        
            if (!empty($change['User']['handle'])){
                echo '<br />by ';
                echo $change['User']['handle'];
            }
                ?>
        </td>
        <td><?php echo h($change['text']); ?>&nbsp;</td>
        
        
        
    </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
                    
                    
                    
                    
                    
                    
                </div>
            </div>
        </div>

    </div>
  
