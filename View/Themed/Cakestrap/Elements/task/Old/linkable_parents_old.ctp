<?php
    if(isset($curParent)){
       
    }


?>
    <div class="form-group">
        <?php echo $this->Form->input('parent_id', array(
            'readonly'=> (empty($linkable))? true:false,
            'empty'=> '< Not Linked >', 
            'default'=>(isset($curParent))? $curParent: 'empty',
            'div'=>false,
            'selected'=>(isset($curParent))? $curParent: null, 
            'multiple'=>false, 
            'options'=>$linkable,
            'label'=>false, 
            'id'=>'eaLinkedParent'.$tid, 
            'class' => 'form-control',
            )); 
            
            
        ?>
    </div><!-- .form-group -->



