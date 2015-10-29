<?php 

    $type_ids = array(100=>100,200=>200,300=>300,400=>400);

?>
<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="notifications form">
		
			<?php echo $this->Form->create('Notification', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Edit Notification'); ?></h2>
			<div class="form-group">
		<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
		<?php echo $this->Form->input('type_id', array(
		//'type'=>'hidden',
		'options'=>$type_ids,
		//'selected'=>$this->request->data('Notification.type_id'),
		
        )); ?>
</div><!-- .form-group -->

<div class="form-group">
	       <?php echo $this->Form->input('parent_task_id', array(
        'type'=>'hidden',
        'value'=>$this->request->data('Notification.parent_task_id'),
        
        )); ?>
</div><!-- .form-group -->

<div class="form-group">
           <?php echo $this->Form->input('child_task_id', array(
        'type'=>'hidden',
        'value'=>$this->request->data('Notification.child_task_id'),
        
        )); ?>
</div><!-- .form-group -->

<div class="form-group">
		<?php echo $this->Form->input('rec_team_id', array('type'=>'hidden',
		'value'=>$this->request->data('Notification.rec_team_id'),
		'class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
        <?php echo $this->Form->input('send_team_id', array('type'=>'hidden',
        'value'=>$this->request->data('Notification.send_team_id'),
        'class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('body', 'Message Body');?>
		<?php echo $this->Form->input('body', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('is_read', 'is_read');?>
		<?php echo $this->Form->input('is_read', array('type'=>'checkbox', 'class' => 'checkbox')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
