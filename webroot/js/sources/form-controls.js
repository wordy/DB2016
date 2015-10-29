$(document).ready(function(){  

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
    
    $('.input-conteam-select').select2({
    	'width':'100%',
    	'allowClear':true,
    	'placeholder':'test',
    	 
    	'minimumResultsForSearch':-1,
    	
    	/*
    	formatResultCssClass: function(object){
    		return "highlight";},
    	
    	 formatSelectionCssClass: function (data, container) { 
    	 	return "highlight2"; },
    	
    	formatSelection: function (referencia) {
        	return referencia.text;
		}*/
    	
    	});
    	
    	    
    $('select.input-ateam-select').select2({
        'width':'100%',
        'allowClear':true,
        'placeholder':'test',
        'minimumResultsForSearch':-1,
        formatSelectionCssClass: function (data, container) { 
            return 'team-assist'; },
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
        formatSelectionCssClass: function (data, container) { 
            return 'team-push'; },
        /*
        formatResultCssClass: function(object){
            return 'highlight';},
        
         
        
        formatSelection: function (referencia) {
            return referencia.text;
        }*/
        
        });
        

        	

    $('.boot-popover').hover(function () {
        $(this).popover({
            html: true
        }).popover('show');
            }, function () {
                $(this).popover('hide');
            });

    
    
 
    
});
