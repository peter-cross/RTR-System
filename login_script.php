<?php
/**
 * File with JavaScript for login.php
 * @author Peter Cross
 * @version Nov 21, 2017
 */
?>
<script>
    const CALL_URL 	= "controller.php", 
		  INS_DIV 	= "#insert-div",
		  PROT_TYPE = "POST",
		  DATA_TYPE = "html",
		  NEXT_BTN 	= "#next",
		  NEXT_CODE = "#next_code",
		  INIT_DATA = { data: {step: 5} };
		
	var callbackFunc = function( data )
		{
			$(INS_DIV).html( JSON.parse(data) );
		};
		
	var evalFunc = function()
		{
			eval( $(NEXT_CODE).val() );
		};
			
	$.ajaxSetup( {url       : CALL_URL,
				  success   : callbackFunc,
				  type      : PROT_TYPE,
				  dataType  : DATA_TYPE,
				  cache     : false,
				  enctype   : 'multipart/form-data'} );
	
	$.ajax(INIT_DATA);
	
	eval( "$(NEXT_BTN).prop('value', 'Submit')" );
	 
	window.onload = function()
    {   
        $(NEXT_BTN).click( evalFunc );
	};
</script>
