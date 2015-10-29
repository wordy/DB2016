<div id="page-container" class="row">
    <div id="page-content" class="col-md-12">
    	<div class="users form well">
    		<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
               <h2><?php echo __('Reset User Password'); ?></h2>
                    <p>This resets the user's password to <code>changepassword</code> or whatever you choose.  Remind users to change their password once they log in.</p>
    
				<fieldset>
			<div class="form-group">
	<?php echo $this->Form->label('id', 'User');?>
		<?php echo $this->Form->input('id', array('empty'=>true, 'options'=>$users, 'class' => 'form-control')); ?>
</div><!-- .form-group -->


<div class="form-group">
	<?php echo $this->Form->label('password', 'New Password');?>
		<?php echo $this->Form->input('password', array('type'=>'text', 'value'=>'changepassword', 'class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
</div>
