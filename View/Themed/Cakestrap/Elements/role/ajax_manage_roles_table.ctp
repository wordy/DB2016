<?php
    $userRole = AuthComponent::user('user_role_id');
?>
<div class="roles index">
    <h2><?php echo __('Existing Roles'); ?></h2>
    <p>Roles names (handles) are <b>unique across all teams</b>.</p>
	<div class="table-responsive">
	    <table cellpadding="0" cellspacing="0" class="table table-striped">
			<thead>
				<tr>
					<th>Team</th>
					<th>Handle</th>
					<?php if($userRole >=5000000000):?><th>Actions</th><?php endif;?>
				</tr>
			</thead>

			<tbody>
				<?php
				$curCode = '';
                $doPrint = FALSE;
				foreach ($roles as $role): 
                    $doPrint = ($curCode <> $role['Team']['code'])? TRUE: FALSE;
				?>
            	<tr>
            		<td><?php echo ($doPrint)? '<b>'.h($role['Team']['code']).'</b>':''; ?>&nbsp;</td>
            		<td><?php echo '@'.h($role['Role']['handle']); ?>
                    <?php if($userRole >=5000000000):?>
                    <td>
                        <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $role['Role']['id']), array('class' => 'btn btn-primary btn-xs')); ?>
                        <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $role['Role']['id']), array('class' => 'btn btn-danger btn-xs'), __('Are you sure you want to delete # %s?', $role['Role']['id'])); ?>
                    </td>
                    <?php endif;?>
                    </td>
                </tr>
                <?php 
                    $curCode = $role['Team']['code'];
                    endforeach; 
                ?>
            </tbody>
        </table>
    </div><!-- /.table-responsive -->
</div><!-- /.index -->
