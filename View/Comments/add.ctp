
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-8 col-sm-offset-2 well">

		<div class="comments form">
		
			<?php echo $this->Form->create('Comment', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Comment'); ?></h2>
					<p>Allows you to manually add a comment to a task.</p>
			<div class="form-group">
	<?php echo $this->Form->label('task_id', 'Task');?>
		<?php echo $this->Form->input('task_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('user_id', 'User');?>
		<?php echo $this->Form->input('user_id', array(
		'readonly'=>'readonly',
		'class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('text', 'text');?>
		<?php echo $this->Form->input('text', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
