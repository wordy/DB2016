<?php 

    $this->Js->buffer("
        $('.test_form_class').on('submit', function(){
            var tid = $(this).attr('data-tid');
            
            var val_div = $(this).find('.validation_message');
            var test_input = $(this).find('.testInput');
            var test_input2 = $(this).find('.test_input2');
            
            $(this).find('.taskSubmitButton').val('Saving...');
            
            
            //var ajax_load = '".$this->Html->image('ajax-loader_old.gif')."';
            var loadUrl = '/tasks/details/'+tid;

            test_input2.load(loadUrl);


            
            
            
            
                        
            return false;
        });
        
    
    ");

?>

<form id="testForm1" data-tid="666" class="test_form_class">
       <div class="test_input2"></div>

    <input class="testInput"/>
    
    <?php 
    echo $this->Form->submit('Save Changes', array(
                'id'=>'edit_sub_but_'.$tid, 
                'div'=>false, 
                'class' => 'submit btn btn-large btn-success taskSubmitButton'));?>
    
</form>

<br/><br/>

<form id="testForm2" data-tid="999" class="test_form_class">
    <div class="test_input2"></div>
    <input class="testInput"/>
    <?php 
    echo $this->Form->submit('Save Changes', array(
                'id'=>'edit_sub_but_'.$tid, 
                'div'=>false, 
                'class' => 'submit btn btn-large btn-success taskSubmitButton'));?>

    
</form>

<div class="validation_message">
    
    
</div>


<?php echo $this->Js->writeBuffer(); ?>  