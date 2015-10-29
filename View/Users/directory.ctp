
<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-md-12">

		<div class="users index">
		
			<h2><?php echo __('Users'); ?></h2>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
							<th width="15%"><?php echo $this->Paginator->sort('handle'); ?></th>
							<th width="10%"><?php echo $this->Paginator->sort('username'); ?></th>
                            <th width="20%"><?php echo $this->Paginator->sort('user_role_id'); ?></th>
							<th width="40%"><?php echo $this->Paginator->sort('team_id'); ?></th>
							<!--<th><?php echo $this->Paginator->sort('password'); ?></th>-->

							
							<th width="15%" class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($users as $user): ?>
	<tr>
		
		
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
                 echo '<span class="btn btn-medgrey btn-xxs">'.$team_code.'</span>';     
               }
             
             if (count($user['TeamsUser'])>5){
             echo ' ('.count($user['TeamsUser']).' teams) ';
             }
              }
           ?>&nbsp;</td>
		

		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $user['User']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $user['User']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
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
	
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
