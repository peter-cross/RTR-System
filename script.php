<?php
/**
 * File with JavaScript for index.php
 * @author Peter Cross
 * @version Nov 19, 2017
 */
?>
<script>
    const CALL_URL 	= "controller.php", 
		  INS_DIV 	= "#insert-div",
		  PROT_TYPE = "POST",
		  DATA_TYPE = "html",
		  NEXT_BTN 	= "#next",
		  NEXT_CODE = "#next_code",
		  INIT_DATA = { data: {step: 0} };
		
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
        
	window.onload = function()
    {   
        $(NEXT_BTN).click( evalFunc );
	};
</script>
