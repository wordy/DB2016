$(document).ready(function(){  

	//$('#input-conteam-select').select2({'width':'100%', 'minimumResultsForSearch':-1});



	$('.datetimepicker-stime').datetimepicker({
        format: 'yyyy-mm-dd hh:ii:ss',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        showMeridian: true,
        startDate:'2013-11-01',
        //forceParse:false,
        endDate:'2014-03-31',
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
        startDate:'2013-11-01',
        forceParse:false,
        endDate:'2014-03-31',
    });
       
    // Compile Options    
    $('.input-date-notime').datetimepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayBtn: 'linked',
        todayHighlight: true,
        minuteStep: 1,
        minView: 2,
        showMeridian: true,
        startDate:'2013-11-01',
        forceParse:true,
        endDate:'2014-03-31',
    });
    
    /*
    $('#add_input-leadteam-select').change(function () {
        var selected = $(this).val();
        $('#add_spinner').show();
    
        $.ajax({
            type: 'POST',
            url: '/tasks/parents/' + selected,
            success: function (msg) {
                $('#add_linkableparents').html(msg);
                $('#add_spinner').hide();
            }
        });
    });

	$('#edit_input-leadteam-select').change(function () {
        var selected = $(this).val();
        $('#edit_spinner').show();
    
        $.ajax({
            type: 'POST',
            url: '/tasks/parents/' + selected,
            success: function (msg) {
                $('#edit_linkableparents').html(msg);
                $('#edit_spinner').hide();
            }
        });
    });    
    
    $('#qa_input-leadteam-select').change(function () {
        var selected = $(this).val();
        $('#qa_spinner').show();
        
        $.ajax({
            type: 'POST',
            url: '/tasks/parents/' + selected,
            success: function (msg) {
                $('#qa_linkableparents').html(msg);
                $('#qa_spinner').hide();
            }
        });
    });
    
    $('.input-conteam-select').select2({'width':'100%', 'minimumResultsForSearch':-1}).change(function(){
        if($('#input-task-public').prop('checked')==false){
        	$('#addLinkageWhilePrivateModal').modal({backdrop:'static'});
        }
    });
    */
     
    


/*
    $('#add_input-conteam-select').select2({'width':'100%', 'minimumResultsForSearch':-1}).change(function(){
        if($('#add_input-task-public').prop('checked')==false){
            $('#add_addLinkageWhilePrivateModal').modal({backdrop:'static'});
        }
    });    
    
    $('#edit_input-conteam-select').select2({'width':'100%', 'minimumResultsForSearch':-1}).change(function(){
        if($('#edit_input-task-public').prop('checked')==false){
            $('#edit_addLinkageWhilePrivateModal').modal({backdrop:'static'});
        }
    });
    
    $('#qa_input-conteam-select').select2({'width':'100%', 'minimumResultsForSearch':-1}).change(function(){
        if($('#qa_input-task-public').prop('checked')==false){
            $('#qa_addLinkageWhilePrivateModal').modal({backdrop:'static'});
        }
    }); 
    
      // Select for contributing teams
	    
    //$('#input-conteam-select').select2({'width':'100%', 'minimumResultsForSearch':-1});
    /*
    $('#input-conteam-select').select2({'width':'100%', 'minimumResultsForSearch':-1}).change(function(){
        if($('#input-task-public').prop('checked')==false){
            $('#addLinkageWhilePrivateModal').modal({backdrop:'static'});
        }
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
       
    // Compile Options    
    $(".input-date-notime").datetimepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
        minuteStep: 1,
        minView: 2,
        showMeridian: true,
        startDate:"2014-11-01",
        forceParse:true,
        endDate:"2015-03-31",
    });
    
    $('.boot-popover').hover(function () {
    	$(this).popover({
        	html: true
    	}).popover('show');
			}, function () {
    			$(this).popover('hide');
			});
    
	$('.color_picker_inline').simplecolorpicker({
    	theme: 'fontawesome',
    	picker: false
        }).on('change', function() {
            var tcol = $(this).val();
            $('.panel-taskcolored').css('border-color',tcol);
            $('.panel-taskcolored > .panel-heading').css({'color': tcol, 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading').css({'color': '#000', 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading + .panel-collapse .panel-body').css('border-top-color', tcol);
            $('.panel-taskcolored > .panel-footer + .panel-collapse .panel-body').css('border-bottom-color', tcol);
    });
    
    $('.color_picker_menu').simplecolorpicker({
    	theme: 'fontawesome',
    	picker: true
        }).on('change', function() {
            var tcol = $(this).val();
            $('.panel-taskcolored').css('border-color',tcol);
            $('.panel-taskcolored > .panel-heading').css({'color': tcol, 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading').css({'color': '#000', 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading + .panel-collapse .panel-body').css('border-top-color', tcol);
            $('.panel-taskcolored > .panel-footer + .panel-collapse .panel-body').css('border-bottom-color', tcol);
    });
    
	$('#ajax-menu-spinner').css({'display':'none'});

	//For Inputs
    //$('.input-select-teams').select2({'width':'100%', 'minimumResultsForSearch':-1});
    
    // Used in Compile Options 
    $('#input-multiselect-teams').multiselect({
        includeSelectAllOption: false, 
        buttonClass: 'btn btn-info', 
        buttonWidth: '200px',
        //buttonWidth: false,
        numberDisplayed: 2,
        //maxHeight: 250,
        
        onChange: function(element, checked) {
            if(checked == false) {
                $('#input-multiselect-private').multiselect('deselect', element.val());
                return false;
            }
        }
	});
    
    $('#input-multiselect-private').multiselect({
    	includeSelectAllOption: false, 
    	buttonClass: 'btn btn-default',
    	numberDisplayed: 4, 
    	buttonWidth: '200px',
    	maxHeight: '300px',
   
	    onChange: function(element, checked) {
	        if(checked == true) {
	            $('#input-multiselect-teams').multiselect('select', element.val());
	            return false;
	        }
	    }
	});
    
    $('#input-teams-toggle').click(function(e) {
    	e.preventDefault();
      	multiselect_toggle($('#input-multiselect-teams'), $(this));
    });
    
	$('#input-private-toggle').click(function(e) {
      	e.preventDefault();
      	multiselect_toggle($('#input-multiselect-private'), $(this));
    });
    
     $('.modal-makepublic').click(function(){
        $('#input-task-public').prop('checked', true);
    });        

    $('.modal-makeprivate').click(function(){
        $('#input-conteam-select').select2('val', '');
        $('#input-linkable-parents option').prop('selected', false);
    });
            
    $('#input-task-public').change(function () {
    	var teamcount = $('#input-conteam-select :selected').length;
        var parent = $('#input-linkable-parents :selected').val();

            if($(this).prop('checked')==false){
                if(teamcount!=0 || (parent) ){
                    //alert(teamcount + ' ' + parent);
                    $('#goingPrivateModal').modal({'backdrop':'static'});
                }
            }
    });
    
    
    $('#input-details').wysihtml5();

    $('#input-leadteam-select').change(function () {
        var selected = $(this).val();
        $('#spinner').show();
        
        $.ajax({
            type: 'POST',
            url: '/tasks/parents/' + selected,
            success: function (msg) {
                $('#linkableparents').html(msg);
                $('#spinner').hide();
            }
        });
    });
       
    
    */
    


    
        
    
    $('.boot-popover').hover(function () {
        $(this).popover({
            html: true
        }).popover('show');
            }, function () {
                $(this).popover('hide');
            });
    
    $('.color_picker_inline').simplecolorpicker({
        theme: 'fontawesome',
        picker: false
        }).on('change', function() {
            var tcol = $(this).val();
            $('.panel-taskcolored').css('border-color',tcol);
            $('.panel-taskcolored > .panel-heading').css({'color': tcol, 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading').css({'color': '#000', 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading + .panel-collapse .panel-body').css('border-top-color', tcol);
            $('.panel-taskcolored > .panel-footer + .panel-collapse .panel-body').css('border-bottom-color', tcol);
    });
    
    $('.color_picker_menu').simplecolorpicker({
        theme: 'fontawesome',
        picker: true
        }).on('change', function() {
            var tcol = $(this).val();
            $('.panel-taskcolored').css('border-color',tcol);
            $('.panel-taskcolored > .panel-heading').css({'color': tcol, 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading').css({'color': '#000', 'background-color': tcol, 'border-color': tcol});
            $('.panel-taskcolored > .panel-heading + .panel-collapse .panel-body').css('border-top-color', tcol);
            $('.panel-taskcolored > .panel-footer + .panel-collapse .panel-body').css('border-bottom-color', tcol);
    });
    
    //$('#ajax-menu-spinner').css({'display':'none'});
    
    $('.input-details').wysihtml5();

    /*
    $('#add_input-details').wysihtml5();
    $('#edit_input-details').wysihtml5();
    $('#qa_input-details').wysihtml5();    
    
*/

  /*  
    $('#add_input-task-public').change(function () {
    	var teamcount = $('#add_input-conteam-select :selected').length;
        var parent = $('#add_input-linkableparents :selected').val();

            if($(this).prop('checked')==false){
                if(teamcount!=0 || (parent) ){
                    //alert(teamcount + ' ' + parent);
                    $('#add_goingPrivateModal').modal({'backdrop':'static'});
                }
            }
    });
    
    $('#add1_modal-makepublic').click(function(){
        $('#add_input-task-public').prop('checked', true);
    });        
    
    $('#add1_modal-makeprivate').click(function(){
        $('#add_input-conteam-select').select2('val', '');
        $('#add_input-linkableparents option').prop('selected', false);
    });
    
    $('#add2_modal-makepublic').click(function(){
        $('#add_input-task-public').prop('checked', true);
    });        
    
    $('#add2_modal-makeprivate').click(function(){
        $('#add_input-conteam-select').select2('val', '');
        $('#add_input-linkableparents option').prop('selected', false);
    });
    
    $('#add3_modal-makepublic').click(function(){
        $('#add_input-task-public').prop('checked', true);
    });        
    
    $('#add3_modal-makeprivate').click(function(){
        $('#add_input-conteam-select').select2('val', '');
        $('#add_input-linkableparents option').prop('selected', false);
    });

    

    */
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
 
    
});
