$(document).ready(function(){  
	
	
	$('body').on('submit','form.form_task_edit', function(e){
        alert('submitting!');
        var tid = $(this).attr('data-tid');
        
        // Update submit text to indicate something is happening
        $('#ea_form-submit-button_'+tid).val('Saving...');
        //$('.spinner').fadeIn();

        // Post the form using the form's action and data
        $.post($(this).attr('action'), $(this).serialize())
        // called when post has finished
        .done(function(data) {
            $('#validation_content_'+tid).html(data);
        })
        // called on failure
        .fail(function(data, textStatus) {
            $('#validation_content_'+tid).html(data.responseText);
        })
        
        .always(function(){
            //$('.ea_spinner').fadeOut('fast');
            $('#ea_form-submit-button_'+tid).val('Save Changes');
        });
    // return false to stop the page from posting normally
    //return false;
        e.preventDefault(); //STOP default action
        e.unbind(); //unbind. to stop multiple form submit.
        $('#ea_form-edit-task_'+tid).submit(); //Submit  the FORM
    });
    
 });

   