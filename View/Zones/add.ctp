
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="zones form">
		
			<?php echo $this->Form->create('Zone', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Zone'); ?></h2>
			<div class="form-group">
	<?php echo $this->Form->label('code', 'code');?>
		<?php echo $this->Form->input('code', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('org_level', 'org_level');?>
		<?php echo $this->Form->input('org_level', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('description', 'description');?>
		<?php echo $this->Form->input('description', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('gm_user_id', 'gm_user_id');?>
		<?php echo $this->Form->input('gm_user_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
