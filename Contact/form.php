<?php

// if the from is loaded from WordPress form loader plugin, 
// the phpfmg_display_form() will be called by the loader 
if( !defined('FormmailMakerFormLoader') ){
    # This block must be placed at the very top of page.
    # --------------------------------------------------
	require_once( dirname(__FILE__).'/form.lib.php' );
    phpfmg_display_form();
    # --------------------------------------------------
};


function phpfmg_form( $sErr = false ){
		$style=" class='form_text' ";

?>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/Javascript">
$(document).ready(function(){

		//Hide div w/id hidden
	   $("#hidden").css("display","none");

		// Add onclick handler to checkbox w/id hideme
	   $("#hideme").click(function(){

		// If checked
		if ($("#hideme").is(":checked"))
		{
			//show the hidden div
			$("#hidden").show("fast");
		}
		else
		{
			//otherwise, hide it
			$("#hidden").hide("fast");
		}
	  });

	});
</script>
</head>
<div id='content'>
<img src='../images/herbanner.jpg' id='logo'/>
<div id='frmFormMailContainer'>

<form name="frmFormMail" id="frmFormMail" target="submitToFrame" action='<?php echo PHPFMG_ADMIN_URL . '' ; ?>' method='post' enctype='multipart/form-data' onsubmit='return fmgHandler.onSubmit(this);'>

<input type='hidden' name='formmail_submit' value='Y'>
<input type='hidden' name='mod' value='ajax'>
<input type='hidden' name='func' value='submit'>
            
            
<ol class='phpfmg_form' >

<li class='field_block' id='field_0_div'><div class='col_label'>
	<label class='form_field'>First Name:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_0"  id="field_0" value="<?php  phpfmg_hsc("field_0", ""); ?>" class='text_box'>
	<div id='field_0_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_1_div'><div class='col_label'>
	<label class='form_field'>Last Name:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_1"  id="field_1" value="<?php  phpfmg_hsc("field_1", ""); ?>" class='text_box'>
	<div id='field_1_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_13_div'><div class='col_label'>
	<label class='form_field'>Email</label> <label class='form_required' >&nbsp;</label> </div>
	<div class='col_field'>
	<input type="text" name="field_13"  id="field_13" value="<?php  phpfmg_hsc("field_13", ""); ?>" class='text_box' style='width:300px;'>
	<div id='field_13_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_2_div'><div class='col_label'>
	<label class='form_field'>Card Type:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<?php phpfmg_dropdown( 'field_2', "Visa|MasterCard|American Express|Discover" );?>
	<div id='field_2_tip' class='instruction'>AMEX, DISCOVER, VISA, MASTER CARD</div>
	</div>
</li>

<li class='field_block' id='field_3_div'><div class='col_label'>
	<label class='form_field'>Card Number:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" maxlength=16 name="field_3"  id="field_3" value="<?php  phpfmg_hsc("field_3"); ?>" class='text_box'>
	<div id='field_3_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_4_div'><div class='col_label'>
	<label class='form_field'>Expiration Date:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
		<?php 
selectList( "field_4_MM", $_POST["field_4_MM"], 1, 12, "MM", $style ) ;
selectList( "field_4_YYYY", $_POST["field_4_YYYY"], date("Y"), date("Y")+10, "YYYY", $style ) ;
?>

	<div id='field_4_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_5_div'><div class='col_label'>
	<label class='form_field'>Billing Address:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_5"  id="field_5" value="<?php  phpfmg_hsc("field_5", ""); ?>" class='text_box' style='width:300px;'>
	<div id='field_5_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_6_div'><div class='col_label'>
	<label class='form_field'>Billing State:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<?php phpfmg_dropdown( 'field_6', "AK|AZ|AR|CA|CO|CT|DE|FL|GA|HI|ID|IL|IN|IA|KS|KY|LA|ME|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VT|VA|WA|WV|WI|WY" );?>
	<div id='field_6_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_7_div'><div class='col_label'>
	<label class='form_field'>Billing City:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_7"  id="field_7" value="<?php  phpfmg_hsc("field_7", ""); ?>" class='text_box'>
	<div id='field_7_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_8_div'><div class='col_label'>
	<label class='form_field'>Billing Zip:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_8"  id="field_8" value="<?php  phpfmg_hsc("field_8", ""); ?>" class='text_box' style='width:50px;'>
	<div id='field_8_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_14_div'><div class='col_label'>
	<label class='form_field'>Total Payment:</label> <label class='form_required' >*</label> </div>
	<div class='col_field'>
	<input type="text" name="field_14"  id="field_14" value="<?php  phpfmg_hsc("field_13", ""); ?>" class='text_box' style='width:50px;'>$
	<div id='field_14_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='phpfmg_captcha_div'>
	<div class='col_label'><label class='form_field'>Security Code:</label> <label class='form_required' >*</label> </div><div class='col_field'>
	<?php phpfmg_show_captcha(); ?>
	</div>
</li>

<legend><input type='checkbox' id='hideme' style='-ms-transform: scale(2); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;'>Shipping Address different than billing</legend>

<span id='hidden' style='display:none;'>
<li class='field_block' id='field_9_div'><div class='col_label'>
	<label class='form_field'>Shipping Address:</label> <label class='form_required' >&nbsp;</label> </div>
	<div class='col_field'>
	<input type="text" name="field_9"  id="field_9" value="<?php  phpfmg_hsc("field_9", ""); ?>" class='text_box' style='width:300px;'>
	<div id='field_9_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_10_div'><div class='col_label'>
	<label class='form_field'>Shipping State:</label> <label class='form_required' >&nbsp;</label> </div>
	<div class='col_field'>
	<?php phpfmg_dropdown( 'field_10', "AK|AZ|AR|CA|CO|CT|DE|FL|GA|HI|ID|IL|IN|IA|KS|KY|LA|ME|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VT|VA|WA|WV|WI|WY" );?>
	<div id='field_10_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_11_div'><div class='col_label'>
	<label class='form_field'>Shipping Zip:</label> <label class='form_required' >&nbsp;</label> </div>
	<div class='col_field'>
	<input type="text" name="field_11"  id="field_11" value="<?php  phpfmg_hsc("field_11", ""); ?>" class='text_box'>
	<div id='field_11_tip' class='instruction'></div>
	</div>
</li>

<li class='field_block' id='field_12_div'><div class='col_label'>
	<label class='form_field'>Shipping City:</label> <label class='form_required' >&nbsp;</label> </div>
	<div class='col_field'>
	<input type="text" name="field_12"  id="field_12" value="<?php  phpfmg_hsc("field_12", ""); ?>" class='text_box'>
	<div id='field_12_tip' class='instruction'></div>
	</div>
</li>

</span>

            <li>
            <div class='col_label'>&nbsp;</div>
            <div class='form_submit_block col_field'>
	
				
                <input type='submit' value='Submit' class='form_button'>

				<div id='err_required' class="form_error" style='display:none;'>
				    <label class='form_error_title'>Please check the required fields</label>
				</div>
				


                <span id='phpfmg_processing' style='display:none;'>
                    <img id='phpfmg_processing_gif' src='<?php echo PHPFMG_ADMIN_URL . '?mod=image&amp;func=processing' ;?>' border=0 alt='Processing...'> <label id='phpfmg_processing_dots'></label>
                </span>
            </div>
            </li>
            
</ol>
</form>

<iframe name="submitToFrame" id="submitToFrame" src="javascript:false" style="position:absolute;top:-10000px;left:-10000px;" /></iframe>

</div> 
</div>
<!-- end of form container -->


<!-- [Your confirmation message goes here] -->
<div id='thank_you_msg' style='display:none;text-align:center;font-weight:bold;font-size:250%;padding:20px;'>
Your payment has been recieved. Thank you!
</div>

            
            






<?php
			
    phpfmg_javascript($sErr);

} 
# end of form




function phpfmg_form_css(){
    $formOnly = isset($GLOBALS['formOnly']) && true === $GLOBALS['formOnly'];
?>
<style type='text/css'>
<?php 
if( !$formOnly ){
    echo"
body{
    margin-left: 18px;
    margin-top: 18px;
}

#field_9_div {
	padding-top: 10px;
}

#frmFormMailContainer {
	padding: 10px;
}

#content {
	width: auto;
	max-width: 1140px;
	margin: 0 auto;
	box-shadow: 0 0 40px rgba(0, 0, 0, 0.05);
	position: relative;
	box-sizing: border-box;
	background-color: #AC7372;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	-webkit-border-radius: 7px;
	-moz-border-radius: 7px;
	border-radius: 7px;
	color: white;
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99222222, endColorstr=#99222222);
    -ms-filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99222222, endColorstr=#99222222);
    z-index:10;
	-moz-box-shadow:0px 0px 7px #000000;
	-webkit-box-shadow:0px 0px 7px #000000;
	box-shadow:0px 0px 7px #000000;
}

#logo {
	width: 100%;
	-webkit-border-radius: 7px;
	-moz-border-radius: 7px;
	border-radius: 7px;
}

body{
    font-family : Verdana, Arial, Helvetica, sans-serif;
    font-size : 13px;
    color : #474747;
    background-color: #F6A5A4;
}

select, option{
    font-size:13px;
}
";
}; // if
?>

ol.phpfmg_form{
    list-style-type:none;
    padding:0px;
    margin:0px;
}

ol.phpfmg_form input, ol.phpfmg_form textarea, ol.phpfmg_form select{
    border: 1px solid #ccc;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}

ol.phpfmg_form li{
    margin-bottom:5px;
    clear:both;
    display:block;
    overflow:hidden;
	width: 300px;
}


.form_field, .form_required{
    font-weight : bold;
}

.form_required{
    color:red;
    margin-right:8px;
}

.field_block_over{
}

.form_submit_block{
    padding-top: 3px;
}

.text_box, .text_area, .text_select {
}

.text_area{
    height:80px;
}

.form_error_title{
    font-weight: bold;
    color: red;
}

.form_error{
    background-color: #F4F6E5;
    border: 1px dashed #ff0000;
    padding: 10px;
    margin-bottom: 10px;
}

.form_error_highlight{
    background-color: #F4F6E5;
    border-bottom: 1px dashed #ff0000;
	color: black;
	width: 100%
}

div.instruction_error{
    color: red;
    font-weight:bold;
}

hr.sectionbreak{
    height:1px;
    color: #ccc;
}

#one_entry_msg{
    background-color: #F4F6E5;
    border: 1px dashed #ff0000;
    padding: 10px;
    margin-bottom: 10px;
}


#frmFormMailContainer input[type="submit"]{
    padding: 10px 25px; 
    font-weight: bold;
    margin-bottom: 10px;
    background-color: #FAFBFC;
}

#frmFormMailContainer input[type="submit"]:hover{
    background-color: #E4F0F8;
}

<?php phpfmg_text_align();?>    



</style>

<?php
}
# end of css
 
# By: formmail-maker.com
?>