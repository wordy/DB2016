$(document).ready(function(){  


$('#tc-add').on('click', function(event){
        var this_button = $(this);        
        alert('clicked');
        
        
        //var eb = $(this);
        this_button.val('Saving...');
        
        var spinner = $('<img src="/img/ajax-loader-small.gif" id="spinner" />');
        
        return false;
    });
           
    
    
    
    
    
    
    
 
    
});
