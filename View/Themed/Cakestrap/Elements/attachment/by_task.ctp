<?php

    if (AuthComponent::user('id')){
        $controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }

?>
<div id="page-container" class="row">

	
	
	<div id="page-content" class="col-sm-12">

		<div class="attachments index">
		
			<h2><?php echo __('Attachments'); ?></h2>
			
			<div class="table-responsive">
				<table cellpadding="0" cellspacing="0" class="table table-striped table-bordered">
					<thead>
						<tr>
                                                            <th width="10%"><?php echo __('Date'); ?></th>															
															<th width="5%"><?php echo __('Team'); ?></th>
															<th width="65%"><?php echo __('File'); ?></th>
															<th width="10%"><?php echo __('File Size'); ?></th>
															

															
															<th width="10%"class="actions"><?php echo __('Actions'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($attachments as $attachment): ?>
	<tr>
        <td><?php echo date('M d', strtotime($attachment['Attachment']['created'])); ?>&nbsp;</td>
		
		<td>
			<?php echo $this->Html->link($attachment['Team']['code'], array('controller' => 'teams', 'action' => 'view', $attachment['Team']['id'])); ?>
		</td>

		<td><?php echo h($attachment['Attachment']['display_name']); ?>&nbsp;</td>
		
		<td>
            <?php 
              if($attachment['Attachment']['file_size'] > 0){
                  $filesize_kb = (int) $attachment['Attachment']['file_size']/1024;
                  echo round($filesize_kb,0) . ' kB';
              }
              else { echo '-';} 
            ?>
        </td>             	      
		


		<td class="actions">
			<?php //echo $this->Html->link(__('Download'), array('action' => 'download', $attachment['Attachment']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php //echo $this->Html->link(__('View'), array('action' => 'view', $attachment['Attachment']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php //echo $this->Html->link(__('Edit'), array('action' => 'edit', $attachment['Attachment']['id']), array('class' => 'btn btn-default btn-xs')); ?>
			<?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $attachment['Attachment']['id']), array('class' => 'btn btn-default btn-xs'), __('Are you sure you want to delete # %s?', $attachment['Attachment']['id'])); ?>
		
		<!-- Split button -->
<div class="btn-group">
  <?php echo $this->Html->link(__('Download'), array('action' => 'download', $attachment['Attachment']['id']), array('class' => 'btn btn-default btn-xs')); ?>
  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
    <span class="caret"></span>
    <span class="sr-only">Toggle Dropdown</span>
  </button>
  <ul class="dropdown-menu" role="menu">
      <li>
          <?php echo $this->Html->link(__('Download'), array('action' => 'download', $attachment['Attachment']['id'])); ?>
          
      </li>
     
            <!--<li><?php echo $this->Html->link(__('View'), array('action' => 'view', $attachment['Attachment']['id'])); ?></li>-->
            <?php 
                if ($user_role >= 500):?>
                    <li><?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $attachment['Attachment']['id'])); ?></li>
                <?php endif; ?>
                
            
            <?php 
                if(in_array($attachment['Attachment']['team_id'], $controlled_teams)){
                    echo '<li class="divider"></li><li>';
                    echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $attachment['Attachment']['id']), null, __('Are you sure you want to delete # %s?', $attachment['Attachment']['id'])); 
                    echo '</li>';
                    }
                 
            ?>
  </ul>
</div>
		</td>
	</tr>
<?php endforeach; ?>
					</tbody>
				</table>
			</div><!-- /.table-responsive -->
			
			
			
		</div><!-- /.index -->
	
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
