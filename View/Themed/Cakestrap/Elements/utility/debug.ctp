<?php

    if(isset($data)){
        debug($data);
    }
    
    echo '<br/><br/>';
    
    if(isset($data1)){
        debug($data1);
    }
    
    echo '<br/><br/>';
    
    if(isset($data2)){
        debug($data2);
    }
    
    echo '<br/><br/>';
    
    if(isset($data3)){
        debug($data3);
    }
    
    echo '<br/><br/>';
    
    if(isset($form_data)){
        echo $this->Form->input('test', array('type'=>'select', 'options'=>$form_data));
        
        
    }

?>