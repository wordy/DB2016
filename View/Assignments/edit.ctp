
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="assignments form">
		
			<?php echo $this->Form->create('Assignment', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Edit Assignment'); ?></h2>
			<div class="form-group">
	<?php echo $this->Form->label('id', 'id');?>
		<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('actor_id', 'actor_id');?>
		<?php echo $this->Form->input('actor_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('task_id', 'task_id');?>
		<?php echo $this->Form->input('task_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('assign_role', 'assign_role');?>
		<?php echo $this->Form->input('assign_role', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
