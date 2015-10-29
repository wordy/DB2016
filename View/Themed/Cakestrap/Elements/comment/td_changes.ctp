<?php 
    //$this->extend('/Common/Task/view_task');
    $this->assign('page_title', 'Task ');
    $this->assign('page_title_sub', 'Changes'); 
    
    // if we dont have data, go get it with requestAction
    if(empty($changes)){
        $data = $this->requestAction(
            array('controller' => 'changes', 'action' => 'pageChanges'), 
            //array('named'=>array('task'=>$task)));
            array('pass'=>array($task)));
        
        $changes = $data['changes'];
        $paging = $data['paging']['Change'];
        
        // if the 'paging' variable is populated, merge it with the already present paging variable in $this->params. This will make sure the PaginatorHelper works
        if(!isset($this->params['paging'])) $this->params['paging'] = array();
        $this->params['paging'] = array_merge($data['paging'], $this->params['paging']);
    }
    
    $this->Paginator->options(array(
        'update' => '#loaded_chg_'.$task,
        'evalScripts' => true,
        'before' => $this->Js->get('#spinner')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('#spinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'changes', 'action' => 'pageChanges', $task)
    ));
    
    $today = date('Y-m-d');
    //$today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    //$owfn = strtotime($today.'+1 week');

?>

<div id="chgcontent">
<div class="col-sm-12">    
        <?php 
            if (!empty($changes)){ 
        ?> 
                <table class="table table-condensed xxs-bot-marg">
                    <thead>
                        <th width="20%">Date</th>
                        <th width="25%">Type</th>
                        <th width="55%">Change</th>
                    </thead>
                    <tbody>  
                        <?php 
                            foreach ($changes as $change):
                                $chg_is_new = false;
                                $ccreate = strtotime(date('Y-m-d', strtotime($change['Change']['created'])));
                                if($ccreate > $owa){
                                    $chg_is_new = true;
                                }
                        ?>
                            <tr
                                <?php 
                                    if($chg_is_new){ echo ' class="success"';}
                                ?>
                            >
                                <td><?php echo $this->Time->format('M d', $change['Change']['created']);?></td>
                                <td><?php echo $change['Change']['change_type']; ?></td>
                                <td><?php echo $change['Change']['text'];
                                    if($this->Session->read('Auth.User.user_role_id') >= 5000000){
                                        echo ' - <small>'.$change['Change']['user_handle'].'</small>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>    
                </table>    
   
                <ul class="pagination pagination-sm xxs-bot-marg">
                    <?php  
                        echo $this->Paginator->prev('< ' . __('Prev'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li','disabledTag' => 'a'));
                        echo $this->Paginator->numbers(array('modulus'=>3, 'separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                        echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo '<span id="spinner" style="display: none; margin-left: 5px; vertical-align: middle; float: left;">';
                        echo $this->Html->image('ajax-loader_old.gif', array('id' => 'spinner_img', ));
                        echo '</span>';
                    ?>
                </ul><!-- /.pagination -->
        <?php 
            }   else{  //No changes
                    echo '<span class="fa fa-info"></span>&nbsp; &nbsp; There are no changes listed for this task.';
                } ?>
    </div>
</div><!-- end panelsuccess-->

<?php  echo $this->Js->writeBuffer(); //Necessary cuz we don't use a layout ?>