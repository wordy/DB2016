<?php
    $today = date('Y-m-d');
    


?>


<div id="page-container" class="row">

    <div class="row">
        <div class="col-md-6">
            <h3>Admin User Functions</h3>
            <div class="list-group">
                <a href="/users/add" class="list-group-item">
                    
                    <h4 class="list-group-item-heading"><i class="fa fa-user-plus"></i> Add User</h4>
                    <p class="list-group-item-text">Creates a new Ops Team member account.</p>
                </a>

                <a href="/tasks/sendDigestAll" class="list-group-item">
                    <h4 class="list-group-item-heading"><i class="fa fa-newspaper-o"></i> Send Digest to All</h4>
                    <p class="list-group-item-text">Sends digest to all team leads who are subscribed.</p>
                </a>

                <a href="/users/resetPassword" class="list-group-item">
                    <h4 class="list-group-item-heading"><i class="fa fa-refresh"></i> Password Reset</h4>
                    <p class="list-group-item-text">Help users that have forgotten their passwords by resetting.</p>
                </a>


            </div>
        </div>
    </div>
	
	<div id="page-content" class="row">
    <div class="col-md-12">
		<div class="users index">
		
			<h2><?php echo __('Users'); ?></h2>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
						    <th>&nbsp;</th>
							<th width="12%"><?php echo $this->Paginator->sort('handle'); ?></th>
							<th width="8%"><?php echo $this->Paginator->sort('username'); ?></th>
                            <th width="10%"><?php echo $this->Paginator->sort('user_role_id'); ?></th>
							<th width="35%"><?php echo $this->Paginator->sort('team_id'); ?></th>
							<th width="10%"><?php echo $this->Paginator->sort('last_login'); ?></th>
							<th width="5%"><?php echo $this->Paginator->sort('status', 'Status'); ?></th>
							<!--<th><?php echo $this->Paginator->sort('password'); ?></th>-->

							
							<th width="20%" class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						foreach ($users as $user): 
						
						
						?>
	<tr>
		
		<td><?php echo $i; ?></td>
		<td><?php echo h($user['User']['handle']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['username']); ?>&nbsp;</td>
        <td>
            <?php echo $this->Html->link($user['UserRole']['role'], array('controller' => 'user_roles', 'action' => 'view', $user['UserRole']['id'])); ?>
        </td>
		<!--<td><?php echo h($user['User']['password']); ?>&nbsp;</td>-->
		<td><?php 
              if (!empty($user['TeamsUser'])){
              $t_arr = array();
                 
             
                  
                  
              foreach($user['TeamsUser'] as $key=>$tcode){
                   $t_arr[] = $tcode['team_code']; } 
              
              
              //echo implode(', ', $t_arr);
              
              foreach ($t_arr as $team_code){
                 echo '<span class="btn btn-darkgrey btn-xxs">'.$team_code.'</span>';     
               }
             
             if (count($user['TeamsUser'])>5){
             echo ' ('.count($user['TeamsUser']).' teams) ';
             }
              }
           ?>&nbsp;</td>
                <td><?php 
                
                    $ll = strtotime($user['User']['last_login']);
                    $lld = date('Y-m-d', $ll);
                    
                
                    if($lld == $today){
                        echo 'Today';
                    }
                    
                    elseif(!empty($user['User']['last_login']) && $lld != $today){
                        echo $this->Time->timeAgoInWords($user['User']['last_login'], array(
                            'format'=> 'M j',
                            'accuracy' => array('day' => 'day'),
                            'end'=>'+2 weeks'
                            
                        ));    
                    }
                    
                    else {
                        echo 'Never';
                    }
                     
                    
                    
                ?>
                 
                
                
                
                </td>
                <td>
                    <?php 
                        echo ($user['User']['status']); 
                    ?>
                </td>

		

		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $user['User']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Profile'), array('action' => 'profile', $user['User']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $user['User']['id'])); ?>
		</td>
	</tr>
<?php 

$i++;
endforeach; ?>
					</tbody>
				</table>
			</div><!-- /.table-responsive -->
			
			<p><small>
				<?php
				echo $this->Paginator->counter(array(
				'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
				));
				?>			</small></p>

			<ul class="pagination">
				<?php
		echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
		echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
		echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
	?>
			</ul><!-- /.pagination -->
			
		</div><!-- /.index -->
</div>


	
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->

<?php 
//echo "<pre>";
//print_r($users);
//echo "</pre>";
?>
