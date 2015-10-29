<?php

    echo $this->Html->script('libs/jquery.passstrength.min');
    
    $this->Js->buffer("
        $('#newPass').passStrengthify({ 
            security: 1,
            minimum:  7,
        });
    ");

?>

<div class="row">
    <div id="page-content" class="col-md-12">
<h1><?php echo __('Change Password'); ?></h1>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <b>Note: </b>For security purposes, passwords can't be recovered. If you forget your password, you'll need to reset it.
                        </div>
                    </div>
                </div>
        <div class="users form well">
    		<?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>


                <fieldset>
                    <div class="form-group">
                    	<?php echo $this->Form->label('User.old_pass', 'Old Password*');?>
                    		<?php echo $this->Form->input('User.old_pass', array(
                                'type'=>'password', 
                                'class' => 'form-control',
                                'error' => array(
                                    'attributes' => array(
                                        'wrap' => 'span', 
                                        'class' => 'help-inline text-danger bolder'))
                              )); ?>
                    </div><!-- .form-group -->
                    <p class="help-block">Enter your current password.</p>

    
                    <div class="form-group">
                    	<?php echo $this->Form->label('User.new_pass1', 'New Password*');?>
                		<?php echo $this->Form->input('User.new_pass1', array('type'=>'password', 'id'=>'newPass', 'class' => 'form-control')); ?>
                       <p class="help-block">Choose a strong password (7+ characters with a mix of letters/numbers/symbols)</p>

                    </div><!-- .form-group -->
                    
                    <div class="form-group">
                    	<?php echo $this->Form->label('User.new_pass2', 'New Password (Re-Enter)*');?>
                    		<?php echo $this->Form->input('User.new_pass2', array('type'=>'password', 'class' => 'form-control')); ?>
                    </div><!-- .form-group -->
    			</fieldset>
			<?php echo $this->Form->submit('Change Password', array('class' => 'btn btn-large btn-yh')); ?>
            <?php echo $this->Form->end(); ?>
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->

<?php 
    echo $this->Js->writeBuffer(); 
?>