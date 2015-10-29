
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="notifications form">
		
			<?php echo $this->Form->create('Notification', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Notification'); ?></h2>
			<div class="form-group">
	<?php echo $this->Form->label('type_id', 'Notification Type');?>
		<?php echo $this->Form->input('type_id', array(
		'class' => 'form-control',
        'selected'=>100,
        'readonly'=>'readonly')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('body', 'Notification');?>
		<?php echo $this->Form->input('body', array(
		  'class' => 'form-control')); ?>
</div><!-- .form-group -->

</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
