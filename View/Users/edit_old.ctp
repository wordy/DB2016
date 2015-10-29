<?php 
    $this->Js->buffer("
    
        //bindToSelect2($('#userControls'));
       $('#userControls').select2({
           'width': '100%',
           'theme':'bootstrap',
           'placeholder': 'Select teams user will control',
       });
    ");
?>
<div id="page-container" class="row">
    <div id="page-content" class="col-md-12">
		<div class="users form">
            <h2><?php echo __('Edit User'); ?></h2>
            <?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
        			<div class="form-group">
                        <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                    </div><!-- .form-group -->
        
                    <div class="form-group">
                        <?php echo $this->Form->label('Teams', 'Teams User Controls');?>
                            <?php 
                                echo $this->Form->input('ControlledTeams', 
                                array(
                                    'type'=>'select', 
                                    'id'=>'userControls',
                                    'selected'=>$userTeamCodes, 
                                    'options'=>$teams, 
                                    'multiple'=>true)
                                ); 
                            ?>
                    </div><!-- .form-group -->
        
                    <div class="form-group">
                    	<?php echo $this->Form->label('handle', 'Handle (Displayed Name)');?>
                    		<?php echo $this->Form->input('handle', array('class' => 'form-control')); ?>
                    </div><!-- .form-group -->
                    
                    <div class="form-group">
                    	<?php echo $this->Form->label('username', 'Username');?>
                    		<?php echo $this->Form->input('username', array('class' => 'form-control')); ?>
                    </div><!-- .form-group -->
        
                    <div class="form-group">
                    	<?php echo $this->Form->label('user_role_id', 'User Role');?>
                    		<?php echo $this->Form->input('user_role_id', array('class' => 'form-control')); ?>
                    </div><!-- .form-group -->
        
                    <div class="form-group">
                        <?php echo $this->Form->label('email', 'Email');?>
                            <?php echo $this->Form->input('email', array('class' => 'form-control')); ?>
                    </div><!-- .form-group -->

                    <div class="form-group">
                        <?php echo $this->Form->label('status', 'Status');?>
                            <?php echo $this->Form->input('status', array('class' => 'form-control')); ?>
                    </div><!-- .form-group -->

                </fieldset>
                <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
            <?php echo $this->Form->end(); ?>
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
