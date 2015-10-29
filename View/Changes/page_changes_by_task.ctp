<?php 
    $this->extend('/Common/Task/view_task');

    $this->assign('page_title', 'Task ');
    $this->assign('page_title_sub', 'Changes'); 
// When set from view
if (!empty($task)){
    $task_id = $task['Task']['id'];

}

// When set by ajax response
else { $task_id = $tid;}


// if we dont have data, go get it with requestAction
if (empty($changes)) {
    $data = $this->requestAction(
        array('controller' => 'changes', 'action' => 'pageChanges'), 
        array('named'=>array('task'=>$task_id)));
    
    $changes = $data['changes'];
    
    // if the 'paging' variable is populated, merge it with the already present paging variable in $this->params. This will make sure the PaginatorHelper works
    if(!isset($this->params['paging'])) $this->params['paging'] = array();
    $this->params['paging'] = array_merge( $this->params['paging'] , $data['paging'] );
    
}



    $this->Paginator->options(array(
        'update' => '#ajax-content-load',
        'evalScripts' => true,
        'before' => $this->Js->get('#spinner')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('#spinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'changes', 'action' => 'pageChanges', $task_id)
    ));

?>

<div class="panel panel-primary" id="chgcontent">
    <div class="panel-heading"><i class="fa fa-sitemap"></i>&nbsp;&nbsp;<b>Changes</b></div>
    <div class="panel-body">
        <?php 
            if (!empty($changes)){ ?> 
                <table id="changes" class="table table-condensed">
                    <thead>
                        <th width="10%">Date</th>
                        <th width="20%">Type</th>
                        <th width="55%">Change</th>
                        <th width="15%">User</th>
                    </thead>
                    <tbody>  
                        <?php foreach ($changes as $change):?>
                            <tr>
                                <td><?php echo $this->Time->format('M d', $change['Change']['created']);?></td>
                                <td><?php echo $change['Change']['change_type']; ?></td>
                                <td><?php echo $change['Change']['text'];?></td>
                                <td><?php echo $change['Change']['user_handle'];?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>    
                </table>    
   
                <ul class="pagination">
                    <?php
                        echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                        echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo '<span id="spinner" style="display: none; float: left;">';
                        echo $this->Html->image('ajax-loader.gif', array('id' => 'spinner', ));
                        echo '</span>';
                    ?>
                </ul><!-- /.pagination -->
        <?php 
            }   else{  //No changes
                    echo '<span class="fa fa-info"></span>&nbsp; &nbsp; There are no changes listed for this task.';
                } ?>
    </div><!-- end panelbody-->
</div><!-- end panelsuccess-->

</div>
<?php  echo $this->Js->writeBuffer(); //Necessary cuz we don't use a layout ?>

      
                                               

