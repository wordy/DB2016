$('div#taskList').on('submit', 'form.form-edit-task', function(){
	alert('submitting from compile');

});


	$('body').on('submit','form.formEditTask', function(e){
            var subBut = $(this).find('.eaFormSubmitButton');
            var valCont = $(this).find('.validationContent');
            

        // Update submit text to indicate something is happening
        subBut.val('Saving...');
        //$('#spinner').fadeIn();

        // Post the form using the form's action and data
        $.post($(this).attr('action'), $(this).serialize())
        // called when post has finished
        .done(function(data) {
            //$('#spinner').fadeOut('fast');
            
                
            
            //console.log(data);
            // inject returned html into page
            valCont.html(data);
            
            
            
            
        })
        // called on failure
        .fail(function(data, textStatus) {
            //alert(data.responseText);
            //console.log(data);
            valCont.html(data.responseText);
        })
        
        .always(function(){
            //$('#spinner').fadeOut('fast');
            subBut.val('Save Changes');
            
            
            
        });

    // return false to stop the page from posting normally
    return false;
    });