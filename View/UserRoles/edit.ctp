
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="userRoles form">
		
			<?php echo $this->Form->create('UserRole', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Edit User Role'); ?></h2>
			<div class="form-group">
		<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('role', 'Role');?>
		<?php echo $this->Form->input('role', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
