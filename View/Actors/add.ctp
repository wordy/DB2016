
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="actors form">
		
			<?php echo $this->Form->create('Actor', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Actor'); ?></h2>
			<div class="form-group">
	<?php echo $this->Form->label('handle', 'handle');?>
		<?php echo $this->Form->input('handle', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('team_id', 'team_id');?>
		<?php echo $this->Form->input('team_id', array('empty'=>true, 'class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('user_id', 'user_id');?>
		<?php echo $this->Form->input('user_id', array('empty'=>true, 'class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->