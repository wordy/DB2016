<div class="row">
    <div class="col-sm-12">
		<div class="teams view">
    		<h2><?php  echo __('Team'); ?></h2>
			
			<div class="table-responsive">
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>
						    <td><strong><?php echo __('Id'); ?></strong></td>
                    		<td><?php echo h($team['Team']['id']); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo __('Name'); ?></strong></td>
                    		<td><?php echo h($team['Team']['name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo __('Code'); ?></strong></td>
                    		<td><?php echo h($team['Team']['code']); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo __('Zone'); ?></strong></td>
                    		<td><?php echo h($team['Team']['zone']); ?></td>
                        </tr>					
                    </tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
		</div><!-- /.view -->

		<div class="related">
		<?php if (!empty($team['TeamsUser'])): ?>
            <h3><?php echo __('Related Users'); ?></h3>
            <div class="table-responsive">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
                    		<th><?php echo __('Handle'); ?></th>
                            <th><?php echo __('User Role'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						$i = 0;
						foreach ($team['TeamsUser'] as $user): ?>
                    		<tr>
                    			<td><?php echo $this->Html->link($user['user_handle'], array('controller' => 'users', 'action' => 'view', $user['user_id'])); ?>
                    			<td><?php echo $user['User']['user_role']; ?></td>
                    		</tr>
                    <?php endforeach; ?>
					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div><!-- /.table-responsive -->
		<?php endif; ?>
        </div>
	</div><!-- /#page-content .span9 -->
</div><!-- /#page-container .row-fluid -->


