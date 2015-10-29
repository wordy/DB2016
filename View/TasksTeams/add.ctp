
<div id="page-container" class="row">

	
	<div id="page-content" class="col-md-8 col-md-offset-2">

		<div class="tasksTeams form well">
		
			<?php echo $this->Form->create('TasksTeam', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Team to Task'); ?></h2>
	                <p>Avoid doing this manually.</p>

			<div class="form-group">
	<?php echo $this->Form->label('task_id', 'Task');?>
		<?php echo $this->Form->input('task_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('team_id', 'Team');?>
		<?php echo $this->Form->input('team_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('task_role_id', 'Task Role');?>
		<?php echo $this->Form->input('task_role_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->


				</fieldset>
			<?php echo $this->Form->submit('Add New Task Role', array('class' => 'btn btn-large btn-yh')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
