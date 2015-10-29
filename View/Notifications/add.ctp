
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="notifications form">
		
			<?php echo $this->Form->create('Notification', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Notification'); ?></h2>
			<div class="form-group">
	<?php echo $this->Form->label('type_id', 'type_id');?>
		<?php echo $this->Form->input('type_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('parent_task_id', 'parent_task_id');?>
		<?php echo $this->Form->input('parent_task_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('child_task_id', 'child_task_id');?>
		<?php echo $this->Form->input('child_task_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('rec_team_id', 'rec_team_id');?>
		<?php echo $this->Form->input('rec_team_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('send_team_id', 'send_team_id');?>
		<?php echo $this->Form->input('send_team_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('body', 'body');?>
		<?php echo $this->Form->input('body', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('is_read', 'is_read');?>
		<?php echo $this->Form->input('is_read', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
