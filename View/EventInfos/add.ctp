<?php

    $uid = AuthComponent::user('id');

?>
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="eventInfos form">
		
			<?php echo $this->Form->create('EventInfo', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Event Info'); ?></h2>
			<div class="form-group">
	<?php echo $this->Form->label('entertainment1', 'entertainment1');?>
		<?php echo $this->Form->input('entertainment1', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('entertainment2', 'entertainment2');?>
		<?php echo $this->Form->input('entertainment2', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('prizes1', 'prizes1');?>
		<?php echo $this->Form->input('prizes1', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('prizes2', 'prizes2');?>
		<?php echo $this->Form->input('prizes2', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('food1', 'food1');?>
		<?php echo $this->Form->input('food1', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('food2', 'food2');?>
		<?php echo $this->Form->input('food2', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('auction1', 'auction1');?>
		<?php echo $this->Form->input('auction1', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('auction2', 'auction2');?>
		<?php echo $this->Form->input('auction2', array('class' => 'form-control')); ?>
</div><!-- .form-group -->


				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
