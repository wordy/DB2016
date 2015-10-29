
<?php if(!empty($tasks)){ ?>

    <table class="table table-condensed" id="timeShiftTable">
        <tr>
            <th>Remove</th>
            <th>Current</th>
            <th class="success">Shifted</th>
            <th>Description</th>
        </tr>
        <?php foreach($tasks as $task): ?>
        <tr class="userTimeShift">
            <td><i data-tid="<?php echo $task['Task']['id']; ?>" class="fa fa-ban highlight-duesoon remShift"></i></td>
            <td>
                <span class="stimebef" 
                    data-sstr = "<?php echo strtotime($task['Task']['start_time']);?>">
                        <?php echo date('g:i:s A', strtotime($task['Task']['start_time'])); ?>
                </span>-
                <span
                    class="etimebef" 
                    data-estr = "<?php echo strtotime($task['Task']['end_time']);?>">
                        <?php echo date('g:i:s A', strtotime($task['Task']['end_time'])); ?>
                </span>
            </td>
            <td class="success">
                <span class="stimeaft">
                        <?php echo date('g:i:s A', strtotime($task['Task']['start_time'])); ?>
                    </span> - 
                <span class="etimeaft">
                    <?php echo date('g:i:s A', strtotime($task['Task']['end_time'])); ?>
                </span>
            </td>
            <td><?php echo $task['Task']['short_description']; ?></td>
        </tr>
        <?php endforeach; ?>
        
    </table>
<?php } 
    else{ ?>
        <div class="alert alert-info" role="alert">
            <b>No Tasks Selected</b><br/> 
            You haven't picked up any tasks to time shift yet.  Select tasks from your compiled plan by clicking on their checkboxes.
        </div>
<?php   }

    echo $this->Js->writeBuffer();

?>