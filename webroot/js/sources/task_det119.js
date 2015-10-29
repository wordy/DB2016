$('body').on('submit','form .form_task_edit', function(e){
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
    
    
    
    
    
    
    
    $('select.input-ateam-select').select2({
        'width':'100%',
        'allowClear':true,
        'placeholder':'test',
        'minimumResultsForSearch':-1,
        
        /*
        formatResultCssClass: function(object){
            return 'highlight';},
        
         formatSelectionCssClass: function (data, container) { 
            return 'highlight2'; },
        
        formatSelection: function (referencia) {
            return referencia.text;
        }*/
        
        });
    
    $('select.input-pteam-select').select2({
        'width':'100%',
        'allowClear':true,
        'placeholder':'test',
        'minimumResultsForSearch':-1,
        
        /*
        formatResultCssClass: function(object){
            return 'highlight';},
        
         formatSelectionCssClass: function (data, container) { 
            return 'highlight2'; },
        
        formatSelection: function (referencia) {
            return referencia.text;
        }*/
        
        });
        
    $('.datetimepicker-stime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        showMeridian: true,
        startDate:'2014-11-01',
        //forceParse:false,
        endDate:'2015-03-31',
        linkField: 'TaskEndTime',
        linkFormat: 'yyyy-mm-dd hh:ii:ss',
    });

    $('.datetimepicker-etime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        showMeridian: true,
        startDate:'2014-11-01',
        forceParse:false,
        endDate:'2015-03-31',
    });
       
    $('.input-date-notime').datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        minView: 2,
        showMeridian: true,
        startDate:'2014-11-01',
        forceParse:true,
        endDate:'2015-03-31',
    });
    
    

    ");