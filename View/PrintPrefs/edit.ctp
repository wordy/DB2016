
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="printPrefs form">
		
			<?php echo $this->Form->create('PrintPref', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Edit User Ignore'); ?></h2>
			<div class="form-group">
		<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('user_id', 'user_id');?>
		<?php echo $this->Form->input('user_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('task_id', 'task_id');?>
		<?php echo $this->Form->input('task_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->
<div class="form-group">
    <?php echo $this->Form->label('hide_detail');?>
        <?php echo $this->Form->input('hide_detail', array('class' => '')); ?>
</div><!-- .form-group -->
<div class="form-group">
    <?php echo $this->Form->label('hide_task');?>
        <?php echo $this->Form->input('hide_task', array('class' => '')); ?>
</div><!-- .form-group -->


				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
