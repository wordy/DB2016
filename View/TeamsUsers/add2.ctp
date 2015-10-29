<?php 

$this->Js->buffer('

$(function() {

    var _loadingDiv = $("#loadingDiv");

    $("#tt_add_form").submit(function(){
        _loadingDiv.show();
        $.post("/cake/teams_users/ajaxAdd",
            $(this).serializeArray(),
            afterValidate,
            "json"
        );
        return false;
    });
 
    function afterValidate(data, status)  {
        $(".message").remove();
        $(".error-message").remove();

        if (data.errors) {
            onError(data.errors);
        } else if (data.success) {
            onSuccess(data.success);
        }
    }
 
    function onSuccess(data) {
        flashMessage(data.message);
        _loadingDiv.hide();
        window.setTimeout(function() {
            window.location.href = "/posts";
        }, 2000);
    };
 
    function onError(data) {
        flashMessage(data.message);
        $.each(data.data, function(model, errors) {
            for (fieldName in this) {
                var element = $("#" + camelize(model + "_" + fieldName));
                var _insert = $(document.createElement("div")).insertAfter(element);
                _insert.addClass("error-message").text(this[fieldName])
            }
            _loadingDiv.hide();
        });
    };
 
    function flashMessage(message) {
        var _insert = $(document.createElement("div")).css("display", "none");
        _insert.attr("id", "flashMessage").addClass("message").text(message);
        _insert.insertBefore($(".posts")).fadeIn();
    }

    function camelize(string) {
        var a = string.split("_"), i;
        s = [];
        for (i=0; i<a.length; i++){
            s.push(a[i].charAt(0).toUpperCase() + a[i].substring(1));
        }
        s = s.join("");
        return s;
    }

});


');




?>


<div id="page-container" class="row">

	
	<div id="page-content" class="col-sm-12">

		<div class="teamsUsers form">
		
			<?php echo $this->Form->create('TeamsUser', array('id'=>'tt_add_form', 'inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Add Teams User'); ?></h2>
			<div class="form-group">
	<?php echo $this->Form->label('team_id', 'team_id');?>
		<?php echo $this->Form->input('team_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('user_id', 'user_id');?>
		<?php echo $this->Form->input('user_id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

				</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->

<?php echo $this->Js->writeBuffer(); ?>