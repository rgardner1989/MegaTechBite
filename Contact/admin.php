<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "jonickka@msu.edu" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "1225" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'4F49' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGOjQ6THVAFgsRaWBodQgIQBJjBIlNdXQQQRJjnQLkBcLFwE6aNm1q2MrMrKgwJPcFANWxAu1A1hsaChQLDWgQQXELkNfo4IBFDMUtUDFUNw9U+FEPYnEfALBYzLzaLcycAAAAAElFTkSuQmCC',
			'308D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYAhhCGUMdkMQCpjCGMDo6OgQgq2xlbWVtCHQQQRabItLoCFQnguS+lVHTVmaFrsyahuw+VHVQ80QaXdHNw2IHNrdgc/NAhR8VIRb3AQC7qMpDns2u2wAAAABJRU5ErkJggg==',
			'2046' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHaY6IImJTGEMYWh1CAhAEgtoZW1lmOroIICsu1Wk0SHQ0QHFfdOmrczMzEzNQnZfgEija6MjinmMDkCx0EAHEWS3NADtaHREERNpALqlEdUtoaGYbh6o8KMixOI+AJgEy8JNHbjWAAAAAElFTkSuQmCC',
			'0834' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGRoCkMRYA1hbWRsdGpHFRKaINDo0BLQiiwW0srYyNDpMCUByX9TSlWGrpq6KikJyH0SdowOqXpB5gaEhmHZgcwuKGDY3D1T4URFicR8A1q7OYdL0JK0AAAAASUVORK5CYII=',
			'9474' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QMQ6AIBAEl4LeAv9DQ3+FNLzmLO4H4A9oeKXR6kBLjd4mW0yyyeTQLsf4U17xswSxkZgUcxkFTKtmJIhH98wErD6T8ttKra22lJSfDU6QjddbyBw9mbgoNgnEeIwuYrlnp/PAvvrfg7nx2wHPhs07DH64TgAAAABJRU5ErkJggg==',
			'D80A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMLQiiwVMYW1lCGWY6oAs1irS6OjoEBCAIsbaytoQ6CCC5L6opSvDlq6KzJqG5D40dXDzXBsCQ0Mw7HBEVQd2CyOKGMTNqGIDFX5UhFjcBwCLWM0fvnDJxQAAAABJRU5ErkJggg==',
			'7599' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGaY6IIu2ijQwOjoEBKCJsTYEOoggi00RCUESg7gpaurSlZlRUWFI7mN0YGh0CAmYiqyXtQEo1hDQgCwm0iDS6NgQgGJHQANrK7pbAhoYQzDcPEDhR0WIxX0Ag+XL2eUt04AAAAAASUVORK5CYII=',
			'8398' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaY6IImJTBFpZXR0CAhAEgtoZWh0bQh0EEFRx9DK2hAAUwd20tKoVWErM6OmZiG5D6SOISQAwzwHNPNAYo4YdmC6BZubByr8qAixuA8AedPMdaCIBGoAAAAASUVORK5CYII=',
			'B365' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUMDkMQCpoi0Mjo6OiCrC2hlaHRtQBObwtDK2sDo6oDkvtCoVWFLp66MikJyH1ido0ODCIZ5AVjEAh1EMNziEIDsPoibGaY6DILwoyLE4j4AyeLM49YWKHcAAAAASUVORK5CYII=',
			'D713' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMIQ6IIkFTGFodAhhdAhAFmtlaHQMYWgQQRVrZZgCpJHcF7V01TQgXJqF5D6gugAkdVAxRgeQGKp5rA0YYlOAvCmobgkNEGlgDHVAcfNAhR8VIRb3AQCWDc4MHl211QAAAABJRU5ErkJggg==',
			'613B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGUMdkMREpjAGsDY6OgQgiQW0AFU2BDqIIIs1MAQwINSBnRQZtSpq1dSVoVlI7guZgqIOoreVAdM8LGIiQL3obmENYA1Fd/NAhR8VIRb3AQDRXcpSWaX28gAAAABJRU5ErkJggg==',
			'7D9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUMdkEVbRVoZHR0dAlDFGl0bAh1EkMWmQMQCkN0XNW1lZmZkaBaS+xgdRBodQgJRzGNtAIqhmScCFHNEEwtowHRLQAMWNw9Q+FERYnEfAMBFy/NTaYZgAAAAAElFTkSuQmCC',
			'2FB4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQ11DGRoCkMREpog0sDY6NCKLBbQCxYAkshhDK1jdlABk902bGrY0dFVUFLL7AkDqHB2Q9TI6gMwLDA1BdksD2A5UtzSA7UARCw0FiqG5eaDCj4oQi/sAiVnOE7IeZFkAAAAASUVORK5CYII=',
			'BE35' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQxmBMABJLGCKSANro6MDsrqAVhEgGYgqBlTH0Ojo6oDkvtCoqWGrpq6MikJyH0SdQ4MIhnkBWMQCHUQa0N3iEIDsPoibGaY6DILwoyLE4j4AoNvNhBnUWMQAAAAASUVORK5CYII=',
			'81BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGUMDkMREpjAGsDY6OiCrC2hlDWBtCEQRE5nCgKwO7KSlUauiloauDM1Cch+aOqh5DBjmYRPDphfoklB0Nw9U+FERYnEfAM7cyLeAEvrYAAAAAElFTkSuQmCC',
			'C248' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WEMYQxgaHaY6IImJtLK2MrQ6BAQgiQU0igBVOTqIIIs1AHUGwtWBnRS1atXSlZlZU7OQ3AeUn8LaiGZeA0MAa2ggqnmNjA4Mjah2AN0CsgVFL2uIaKgDmpsHKvyoCLG4DwAKC82gho7llwAAAABJRU5ErkJggg==',
			'C9B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGaY6IImJtLK2sjY6BAQgiQU0ijS6NgQ6iCCLNQDFGh1hYmAnRa1aujQ1dFVUGJL7AhoYA10bHaai6mUAmgc0AcUOFpAYih3Y3ILNzQMVflSEWNwHANtmzZNe94vWAAAAAElFTkSuQmCC',
			'0A8A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgDGEMYHR2mOiCJiUxhbWVtCAgIQBILaBVpdHR0dBBBcl/U0mkrs0JXZk1Dch+aOqiYaKhrQ2BoCIodIo1AMRR1rAGYehkdRBodQhlRxAYq/KgIsbgPAAasy2xd3NlpAAAAAElFTkSuQmCC',
			'3AFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDA6YGIIkFTGEMYW1gCBBBVtnK2srawOjAgiw2RaTRFSiG7L6VUdNWpoauzEJxH6o6qHmioZhiEHXIdgSA9aK6RTQALIbi5oEKPypCLO4DAGfyywA3UqDAAAAAAElFTkSuQmCC',
			'EB6B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGUMdkMQCGkRaGR0dHQJQxRpdGxwdRNDUsTYwwtSBnRQaNTVs6dSVoVlI7gOrw2peILp52MQw3ILNzQMVflSEWNwHAO0NzNWnA/XrAAAAAElFTkSuQmCC',
			'F6CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHVqRxQIaWFsZHQKmOqCIiTSyNggEBKCKNbA2MDqIILkvNGpa2NJVK7OmIbkvoEG0FUkd3DzXBsbQEAwxQTR1ILcEoomB3OyIIjZQ4UdFiMV9ALLdzIUpN9sGAAAAAElFTkSuQmCC',
			'D14A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHVqRxQKmMAYwtDpMdUAWa2UNYJjqEBCAIgbUG+joIILkvqilq6JWZmZmTUNyH0gdayNcHUIsNDA0BN08dHVTMMVCgTrRxQYq/KgIsbgPAO5ay9CjhZrWAAAAAElFTkSuQmCC',
			'42E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37pjCGsIY6THVAFgthbWVtYAgIQBJjDBFpdG1gdBBBEmOdwgAUg6sDO2natFVLl4aumpqF5L6AKQxT0M0LDWUIYEUzD+gWB0wx1gZ0vQxTRENd0d08UOFHPYjFfQALzMtSVXvu4wAAAABJRU5ErkJggg==',
			'2CA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMLQii4lMYW10CGWYiiwW0CrS4OgIFEXWDRRjBcqguG/atFVLV0UtRXFfAIo6MGR0AIqFooqxNog0uKKpA6pqRBcLDWUMBZoXGjAIwo+KEIv7ANEJzPVm0uUKAAAAAElFTkSuQmCC',
			'FEA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQRNjbQiAqQM7KTRqatjSVVFTs5Dch6YOIRYaiMU8bGLoekVDgWIobh6o8KMixOI+ABx2zf6sB4yCAAAAAElFTkSuQmCC',
			'7B9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUMDkEVbRVoZHR0dGFDFGl0bAlHFpoi0siLEIG6Kmhq2MjMyNAvJfYwOIq0MIah6WRtEGh3QzBMBijmiiQU0YLoloAGLmwco/KgIsbgPAFnqygtGfJUEAAAAAElFTkSuQmCC',
			'C35D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WEOAMNQx1AFJTKRVpJW1gdEhAEksoJGh0RUoJoIs1sDQyjoVLgZ2UtSqVWFLMzOzpiG5D6SOoSEQXW+jA7oY2A5UMZBbGB0dUdwCcjNDKCOKmwcq/KgIsbgPADTcy261qgeUAAAAAElFTkSuQmCC',
			'5BF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA6Y6IIkFNIi0sjYwBASgijW6NjA6iCCJBQaA1TWIILkvbNrUsKWhq1ZFIbuvFayuEdkOoBjQPIZWZLcEQMSmIIuJTIG4BVmMNQDo5gbG0JBBEH5UhFjcBwCU5cxJXUqpwQAAAABJRU5ErkJggg==',
			'2373' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QsQ2AMAwEn8IbeCCzgZEIQzCFKbJBxAZpPCUpKBxBCQJ/9/p/nQy/nOFPeoWPlGZKmiR4XDjDJtHgacYmpsaxnXG6gW/3xavXNfJpyxVY3BukNRXdHhm2UXqPjTO1dOym1JgNHfNX/3tQN3wHn2vMUfSQofgAAAAASUVORK5CYII=',
			'D804' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMDQEIIkFTGFtZQhlaEQRaxVpdHR0aEUVY21lBaoOQHJf1NKVYUtXRUVFIbkPoi7QAd0814bA0BBMO7C5BUUMm5sHKvyoCLG4DwBVYc+MRCWNKQAAAABJRU5ErkJggg==',
			'3AB5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7RAMYAlhDGUMDkMQCpjCGsDY6OqCobGVtZW0IRBWbItLo2ujo6oDkvpVR01amhq6MikJ2H1idQ4MIinmioa4NAWhiQHVAO0RQ3ALWG4DsPtEAoFgow1SHQRB+VIRY3AcAa8DM2jgkrGwAAAAASUVORK5CYII=',
			'2E73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0IdkMREpogAyUCHACSxgFaQWECDCLJukFijQ0MAsvumTQ1btXTV0ixk9wUA1U1haEA2j9EBKBbAgGIeK5DH6IAqJgKErEBRZL2hoUA3NzCguHmgwo+KEIv7ACZUy+uHyxJtAAAAAElFTkSuQmCC',
			'74AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZWhmmMIY6IIu2MkxlCGV0CEAVC2V0dHQQQRabwujK2hAIUwdxU9TSpUtXRYZmIbmP0UGkFUkdGLI2iIa6hgaimAdkg9UhiwVAxQIwxVDdPEDhR0WIxX0A0KDLdgAi00QAAAAASUVORK5CYII=',
			'8431' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYWhlDGVqRxUSmMExlbXSYiiwW0MoQCiRDUdUxujI0OsD0gp20NGrp0lVTVy1Fdp/IFJFWJHVQ80RDHUCmotrRyoAmBnRLKyuaXqibQwMGQfhREWJxHwD8SczzcQUzygAAAABJRU5ErkJggg==',
			'2296' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEDW3coAFkNx37RVS1dmRqZmIbsvgGEKQ0gginmMDkBRoF4RZLcARRnRxERAomhuCQ0VDXVAc/NAhR8VIRb3AQChI8rsfSDHfAAAAABJRU5ErkJggg==',
			'996C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaYGIImJTGFtZXR0CBBBEgtoFWl0bXB0YMEQY3RAdt+0qUuXpk5dmYXsPlZXxkBXR0cHFJtbGYB6A1HEBFpZwGLIdmBzCzY3D1T4URFicR8A0jPLBllrOJcAAAAASUVORK5CYII=',
			'30AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYAhimMIY6IIkFTGEMYQhldAhAVtnK2sro6Ogggiw2RaTRtSEQJgZ20sqoaStTV0VmTUN2H6o6qHlAsVB0MdZWVjR1ILeAxJDdAnIzUAzFzQMVflSEWNwHAMyHy1Dq/xQUAAAAAElFTkSuQmCC',
			'609D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0sLayNgQ6iCCLNYg0uiLEwE6KjJq2MjMzMmsakvtCpog0OoSg6W0FiqGb18rayogmhs0t2Nw8UOFHRYjFfQC0EssJ3h9MsgAAAABJRU5ErkJggg==',
			'7B78' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA6Y6IIu2irQyNAQEBKCKNTo0BDqIIItNAaprdICpg7gpamrYqqWrpmYhuY/RAahuCgOKeawNQPMCGFHMEwGKOTqgigU0iLSyNqDqDWgAurmBAdXNAxR+VIRY3AcAym3MjANtj3kAAAAASUVORK5CYII=',
			'BA9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaYGIIkFTGEMYXR0CBBBFmtlbWVtCHRgQVEn0ugKFEN2X2jUtJWZmZFZyO4DqXMIgauDmica6tCALibS6IjFDkc0t4QGAM1Dc/NAhR8VIRb3AQA6Ps1PNp3HGAAAAABJRU5ErkJggg==',
			'AEFA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA1qRxVgDRBpYGximOiCJiUwBiwUEIIkFtILEGB1EkNwXtXRq2NLQlVnTkNyHpg4MQ0PBYqEhuM3DIwZ0M5rYQIUfFSEW9wEAgNfK6Ipj6VsAAAAASUVORK5CYII=',
			'1845' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUMDkMRYHVhbGVodHZDViTqINDpMRRVjBKkLdHR1QHLfyqyVYSszM6OikNwHUsfa6NAggqJXpNEVaCu6mEOjo4MIuh2NDgHI7hMNAbnZYarDIAg/KkIs7gMAsLvJpe9g9m8AAAAASUVORK5CYII=',
			'82FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6YGIImJTGFtZW1gCBBBEgtoFWl0bWB0YEFRxwAWQ3bf0qhVS5eGrsxCdh9Q3RRWhDqoeQwBmGKMDqwYdrA2oLuFNUA01LWBAcXNAxV+VIRY3AcAR7XKkwGjSEkAAAAASUVORK5CYII=',
			'3569' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7RANEQxlCGaY6IIkFTBFpYHR0CAhAVtkq0sDa4Ogggiw2RSSEtYERJgZ20sqoqUuXTl0VFYbsvikMja6ODlNR9LYCxRoCGlDFREBiKHYETGFtRXeLaABjCLqbByr8qAixuA8AqJrL4sieGDgAAAAASUVORK5CYII=',
			'5D1C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNEQximMEwNQBILaBBpZQhhCBBBFWt0DGF0YEESCwwQaXSYwuiA7L6wadNWZgERivtaUdThFAuAiiHbITIF6JYpqG5hDRANYQx1QHHzQIUfFSEW9wEAr9rLvAMkvQ0AAAAASUVORK5CYII=',
			'0762' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QMQ7AIAhFceAG9j4wdGeoS0+jgzfQ3sDFUxY3TDu2ifyB5AP5L0B/VISV9Aufoy1QgErGQ4HETCLG8wXSHpm88SRDxjEzfGfrV6vaDZ/uCTIlmm4d4ZhMGRjVKzCx+OiUZWbWxODCscD/PtQL3w3IWsu2/QklFQAAAABJRU5ErkJggg==',
			'054B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHUMdkMRYA0QaGFodHQKQxESmAMWmOjqIIIkFtIqEMATC1YGdFLV06tKVmZmhWUjuC2hlaHRtRDUPLBYaiGIe0I5Gh0ZUO1gDWIEqUfUyOjCGoLt5oMKPihCL+wDVF8v3nQkkfQAAAABJRU5ErkJggg==',
			'A25C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ2AMAwEbQlvEPYxRXoj4YYNYIqkyAZhhDRMSUoHKEHg704v/cmwXy7An/KKHzJOpLyJYSSUKIA4w1x20dd2Z5gkiH5Dtn5z2UtZltX61V6GMLLdVQU5M0nIVFm7QQEHblwk9coKjfNX/3swN34H6qzLZ+wvYj0AAAAASUVORK5CYII=',
			'400B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjAEMExhDHVAFgthDGEIZXQIQBJjDGFtZXR0dBBBEmOdItLo2hAIUwd20rRp01amrooMzUJyXwCqOjAMDYWIiaC4BdMOoNsw3ILVzQMVftSDWNwHABKHyp71J2vKAAAAAElFTkSuQmCC',
			'DFB2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGaY6IIkFTBFpYG10CAhAFmsFijUEOoigizU6NIgguS9q6dSwpaFAGsl9UHWNDhjmBbQyYIpNYcDiFlQ3A8VCGUNDBkH4URFicR8Ao9TO32eWWGwAAAAASUVORK5CYII=',
			'AC02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMEx1QBJjDWBtdAhlCAhAEhOZItLg6OjoIIIkFtAq0sDaENAgguS+qKXTVi1dFQWECPdB1TUi2xEaChZrZUAzD2jFFFQxiFtQxUBuZgwNGQThR0WIxX0AcdHNa3IzCCkAAAAASUVORK5CYII=',
			'1E47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHUNDkMRYHUQaGFodGkSQxERBYlNRxRhBYoEODQFI7luZNTVsZWbWyiwk94HUsTY6tDKg6WUNDZiCLsbQ6BCAKebogCwmGgJ2M4rYQIUfFSEW9wEA9srJbS0QiFwAAAAASUVORK5CYII=',
			'46DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpI37pjCGsIYyhoYgi4WwtrI2Ojogq2MMEWlkbQhEEWOdItKAJAZ20rRp08KWrooMzUJyX8AU0VZ0vaGhIo2uaGIMU7CJYboF6mZUsYEKP+pBLO4DAJ0XyjA7LDNHAAAAAElFTkSuQmCC',
			'96F4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDAxoCkMREprC2sjYwNCKLBbSKNALFWtHEGoBiUwKQ3Ddt6rSwpaGroqKQ3MfqKgo0j9EBWS8D0DzXBsbQECQxAbAYAza3oIiB3YwmNlDhR0WIxX0A9STMxMkhcAYAAAAASUVORK5CYII=',
			'4667' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjCGMIQyhoYgi4WwtjI6OjSIIIkxhog0sjagirFOEWlgBdIBSO6bNm1a2NKpq1ZmIbkvYIpoK6ujQyuyvaGhIo2uQBlUt4DFAlDFQG5xdMDiZlSxgQo/6kEs7gMAMEzLWpgps68AAAAASUVORK5CYII=',
			'DFB9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQ11DGaY6IIkFTBFpYG10CAhAFmsFijUEOoigizU6wsTATopaOjVsaeiqqDAk90HUOUzF0NsQ0IBFDNUOLG4JDQCKobl5oMKPihCL+wAHic5+XY1agQAAAABJRU5ErkJggg==',
			'A137' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUNDkMRYAxgDWBsdGkSQxESmsAYwNASgiAW0MgQwANUFILkvaumqqFVTV63MQnIfVF0rsr2hoQwg86YwoJvXEBCALsba6OiAKsYKdDEjithAhR8VIRb3AQCQY8ryPlXrpwAAAABJRU5ErkJggg==',
			'7E23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQxmA0AFZtFWkgdHR0SEATYy1IaBBBFlsCogX0BCA7L6oqWGrVmYtzUJyH6MDUF0rQwOyeawgk6YwoJgnAuIFoIqBbGR0YERxS0CDaChraACqmwco/KgIsbgPAG8py6CKfQ3AAAAAAElFTkSuQmCC',
			'7AE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHaY6IIu2MoawNjAEBKCIsbayNjA6CCCLTRFpdAWKobgvatrK1NCVqVlI7mN0AKtDMY+1QTQUpFcESUykAWIeslgAWAzVLWAxdDcPUPhREWJxHwD32Mui2UdfVQAAAABJRU5ErkJggg==',
			'37C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7RANEQx1CHVqRxQKmMDQ6OgRMdUBW2crQ6NogEBCALDaFoZW1gdFBBMl9K6NWTVu6amXWNGT3TWEIQFIHNY/RAVOMtYEVzY6AKSJAVahuEQ0A6kJz80CFHxUhFvcBAMWXy7bzkuE3AAAAAElFTkSuQmCC',
			'607B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0MdkMREpjCGMDQEOgQgiQW0sLaCxESQxRpEGh0aHWHqwE6KjJq2MmvpytAsJPeFTAGqm8KIal4rUCyAEdW8VtZWRgdUMZBbWBtQ9YLd3MCI4uaBCj8qQizuAwB+dMtqruuJuQAAAABJRU5ErkJggg==',
			'1950' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHVqRxVgdWFtZGximOiCJiTqINLo2MAQEoOgFik0FkkjuW5m1dGlqZmbWNCT3Ae0IdGgIhKmDijE0YoqxAO0IQLODtZXR0QHVLSGMIQyhDChuHqjwoyLE4j4ApN7JZagvWLoAAAAASUVORK5CYII=',
			'3CE7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7RAMYQ1lDHUNDkMQCprA2ugJpEWSVrSINGGJTRBpYQeqR3LcyatqqpaGrVmYhuw+irpUBzTyg2BR0MaAdAchiELcwOmBxM4rYQIUfFSEW9wEAuEXLjgu5wRgAAAAASUVORK5CYII=',
			'5502' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQRILDBAJYYWohrsvbNrUpUtXRQEhkvtaGRpdGwIake2AirUiuyWgVaQRaMUUZDGRKaytILcgi7EGMIYwTGEMDRkE4UdFiMV9AOUMzJtID6k/AAAAAElFTkSuQmCC',
			'20CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCHaYGIImJTGEMYXQICBBBEgtoZW1lbRB0YEHW3SrS6NrA6IDivmnTVqauWpmF4r4AFHVgyOiAKcbagGmHSAOmW0JDMd08UOFHRYjFfQB7TcoCZERLkgAAAABJRU5ErkJggg==',
			'1DE4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHRoCkMRYHURaWRsYGpHFRB1EGl0bGFoDUPSCxaYEILlvZda0lamhq6KikNwHUcfogKmXMTQE07wGNHUgt6CIiYZgunmgwo+KEIv7APcNy0c/o/9mAAAAAElFTkSuQmCC',
			'D690' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGVqRxQKmsLYyOjpMdUAWaxVpZG0ICAhAFWtgbQh0EEFyX9TSaWErMyOzpiG5L6BVtJUhBK4Obp5DA6aYI7odWNyCzc0DFX5UhFjcBwDaW82Xc1OHTgAAAABJRU5ErkJggg==',
			'0DA9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQximMEx1QBJjDRBpZQhlCAhAEhOZItLo6OjoIIIkFtAq0ujaEAgTAzspaum0lamroqLCkNwHURcwFUNvaECDCJodQHUodoDcwtoQgOIWkJuBYihuHqjwoyLE4j4ADOLNLffKhQYAAAAASUVORK5CYII=',
			'EEAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIY6IIkFNIg0MIQyOgSgiTE6OjqIoImxNgTC1IGdFBo1NWzpqsjQLCT3oalDiIUGYjUPjx1wNwPFUNw8UOFHRYjFfQC8wczb2xH29wAAAABJRU5ErkJggg==',
			'C47A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WEMYWllDA1qRxURaGaYyNARMdUASC2hkCAWSAQHIYg2MrgyNjg4iSO6LWrV06aqlK7OmIbkvAGTiFEaYOqiYaKhDAGNoCKodrYwOqOqAOltZG1DFwG5GExuo8KMixOI+AOSey5Ur7a89AAAAAElFTkSuQmCC',
			'DFCC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHaYGIIkFTBFpYHQICBBBFmsVaWBtEHRgwRBjdEB2X9TSqWFLV63MQnYfmjoCYmh2YHFLKIiH5uaBCj8qQizuAwDZssyqWKh6QgAAAABJRU5ErkJggg==',
			'A66D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDSyNjg6iCCJBbSKNLACTRBBcl/U0mlhS6euzJqG5L6AVtFWVkdUvaGhIo2uDYHo5mERw3RLQCummwcq/KgIsbgPAMpEy3S320OZAAAAAElFTkSuQmCC',
			'E0D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUNDkMQCGhhDWBsdGkRQxFhbWYEkqphIoyuQDEByX2jUtJWpq6JWZiG5D6qulQFT7xQGTDsCGDDc4uiAxc0oYgMVflSEWNwHACZ6zYo2gyKcAAAAAElFTkSuQmCC',
			'5454' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7QkMYWllDHRoCkMSA7KmsDQyNaGKhQLFWZLHAAEZX1qkMUwKQ3Bc2benSpZlZUVHI7msVaQWqdkDWy9AqCrQ1MDQE2Y5WoFuANiGrE5nC0MroiOo+1gCGVoZQBhSxgQo/KkIs7gMA9HHNien6WyMAAAAASUVORK5CYII=',
			'368E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUMDkMQCprC2Mjo6OqCobBVpZG0IRBWbItKApA7spJVR08JWha4MzUJ23xRRrOa5opuHRQybW7C5eaDCj4oQi/sAh6zJWcovhk8AAAAASUVORK5CYII=',
			'E20F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMIaGIIkFNLC2MoQyOjCgiIk0Ojo6ookxNLo2BMLEwE4KjVq1dOmqyNAsJPcB1U1hRaiDiQVgijE6MGLYwdqA7pbQENFQhymoYgMVflSEWNwHAEyAyoJV9+kPAAAAAElFTkSuQmCC',
			'9EF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA6Y6IImJTBFpYG1gCAhAEgtoBYkxOohgiAHVI7lv2tSpYUtDV62KQnIfqytYXSOyHQwQva3IbhGAiE1hwOIWDDc3MIaGDILwoyLE4j4A4MXK9UpoHoIAAAAASUVORK5CYII=',
			'A459' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7GB0YWllDHaY6IImxBjBMZW1gCAhAEhOZwhDKClQtgiQW0MroyjoVLgZ2UtRSIMjMigpDcl9Aq0grkJyKrDc0VDTUoSGgAdU8oFsaAhzQxRgdHVDcAhJjCGVAcfNAhR8VIRb3AQB8/cwDK9sgewAAAABJRU5ErkJggg==',
			'CB0B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WENEQximMIY6IImJtIq0MoQyOgQgiQU0ijQ6Ojo6iCCLAVWyNgTC1IGdFLVqatjSVZGhWUjuQ1MHE2t0BYqJELADm1uwuXmgwo+KEIv7AP54zBABcopLAAAAAElFTkSuQmCC',
			'4DB9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37poiGsIYyTHVAFgsRaWVtdAgIQBJjDBFpdG0IdBBBEmOdAhRrdISJgZ00bdq0lamhq6LCkNwXAFbnMBVZb2goyLyABhEUt4DFHNDEMNyC1c0DFX7Ug1jcBwCjCc2H7CRPGgAAAABJRU5ErkJggg==',
			'111F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIaGIImxOjAGMIQAZZDERB1YAxjRxKB6YWJgJ63MWhW1atrK0Cwk96Gpo1hMNIQ1lDHUEUVsoMKPihCL+wDEUsPcOzM4/QAAAABJRU5ErkJggg==',
			'B044' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHRoCkMQCpjCGMLQ6NKKItbK2Mkx1aEVVJ9LoEOgwJQDJfaFR01ZmZmZFRSG5D6TOtdHRAdU8oFhoYGgIuh3Y3IImhs3NAxV+VIRY3AcA6/PQA0NC/6QAAAAASUVORK5CYII=',
			'137B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDA0MdkMRYHURaGRoCHQKQxEQdGBodgGIiKHoZWhkaHWHqwE5ambUqbNXSlaFZSO4Dq5vCiGIeI8i8AEZ084CmoYuJtLI2oOoVDQG6uYERxc0DFX5UhFjcBwDC1ch48i1jdQAAAABJRU5ErkJggg==',
			'F7E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHRoCkMSA7EZXIMYi1oom1srawDAlAMl9oVGrpi0NXRUVheQ+oHwAawOjA6peRgegWGgIihgrEDKguUUEuxiamwcq/KgIsbgPADbrzofZ9B27AAAAAElFTkSuQmCC',
			'C4E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYWllDHaY6IImJtDJMZW1gCAhAEgtoZAhlbWB0EEEWa2B0ZQWpR3Jf1KqlS5eGgmiE+wKAJgLVNTqg6BUNdW1gaGVAtQOkbgoDqltAYgGYbnYMDRkE4UdFiMV9ALKFy8dlagX+AAAAAElFTkSuQmCC',
			'B343' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNYQxgaHUIdkMQCpoi0MrQ6OgQgi7UCVU11aBBBUcfQyhDo0BCA5L7QqFVhKzOzlmYhuQ+kjrURrg5unmtoAKp5IDsa0e0AuqUR1S3Y3DxQ4UdFiMV9ABPKz1aG9jBvAAAAAElFTkSuQmCC',
			'0E29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaY6IImxBog0MDo6BAQgiYlMEWlgbQh0EEESC2gF8eBiYCdFLZ0atmplVlQYkvvA6loZpmLonQI0F80OhgAGFDvAbnFgQHELyM2soQEobh6o8KMixOI+AARVynKKCwTOAAAAAElFTkSuQmCC',
			'EB7A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDA1qRxQIaRID8gKkOqGKNDg0BAQHo6hodHUSQ3BcaNTVs1dKVWdOQ3AdWN4URpg5hXgBjaAiamKMDhrpW1gZUMbCb0cQGKvyoCLG4DwDS580b4goLMgAAAABJRU5ErkJggg==',
			'96E1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHVqRxUSmsLayNjBMRRYLaBVpBIqFook1AMVgesFOmjZ1WtjS0FVLkd3H6iraiqQOAoHmuaKJCWARg7oFRQzq5tCAQRB+VIRY3AcAt3LLDB4lhG4AAAAASUVORK5CYII=',
			'46DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpI37pjCGsIYyhjogi4WwtrI2OjoEIIkxhog0sjYEOoggibFOEWlAEgM7adq0aWFLV0VmTUNyX8AU0VZ0vaGhIo2uaGIMU7CJYboFq5sHKvyoB7G4DwBbn8u2jQrnmgAAAABJRU5ErkJggg==',
			'73A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNZQximMLSiiLaKtDKEMkx1QBFjaHR0dAgIQBYD6mNtCHQQQXZf1Kqwpasis6YhuY/RAUUdGLI2MDS6hqKKiYDEGgJQ7AhoEAHqDUBxS0ADawhQDNXNAxR+VIRY3AcAQnjMmyiJcfsAAAAASUVORK5CYII=',
			'CBFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7WENEQ1hDA0NDkMREWkVaWRsYHZDVBTSKNLqiizWgqAM7KWrV1LCloStDs5Dch6YOJoZpHhY7sLkF7GY0sYEKPypCLO4DAGFfycslwmIIAAAAAElFTkSuQmCC',
			'48D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37pjCGsIYyTHVAFgthbWVtdAgIQBJjDBFpdG0IdBBAEmOdAlQHFEN237RpK8OWropMzUJyXwBEHYp5oaEQ80RQ3IJNDNMtWN08UOFHPYjFfQBscsyO4a9PxwAAAABJRU5ErkJggg==',
			'04D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YWllDGVqRxVgDGKayNjpMRRYTmcIQytoQEIosFtDK6AoUg+kFOylqKRAASWT3BbSKtCKpg4qJhrqiiQHtwFAHdEsr0C0oYlA3hwYMgvCjIsTiPgD46swk4+VVywAAAABJRU5ErkJggg==',
			'996A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaXRtcAgIwBBjdBBBct+0qUuXpk5dmTUNyX2sroyBro6OMHUQ2MoA1BsYGoIkJtDKAhJDUQdxC6peiJsZUc0boPCjIsTiPgB4UctgLuA5bQAAAABJRU5ErkJggg==',
			'3EEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAATElEQVR4nGNYhQEaGAYTpIn7RANEQ1lDHUMDkMQCpog0sDYwOqCobMUihqoO7KSVUVPDloauDM1Cdh+x5mERw+YWbG4eqPCjIsTiPgDR88iunQbBZAAAAABJRU5ErkJggg==',
			'04C6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCHaY6IImxBjBMZXQICAhAEhOZwhDK2iDoIIAkFtDK6MoKMgHJfVFLgWDVytQsJPcFtIq0AtWhmBfQKhrqCtQrgmpHK8gOEVS3tKK7BZubByr8qAixuA8ALfzKqVVAfXwAAAAASUVORK5CYII=',
			'C804' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYQximMDQEIImJtLK2MoQyNCKLBTSKNDo6OrSiiDWwtrI2BEwJQHJf1KqVYUtXRUVFIbkPoi7QAVWvSKNrQ2BoCKYd2NyCIobNzQMVflSEWNwHAMDPzkwBBuZIAAAAAElFTkSuQmCC',
			'C6F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA0NDkMREWllbWUE0klhAo0gjhhiQxwqmEe6LWjUtbGnoqpVZSO4LaBAFmdfKgKq30bWBYQoDmh1AsQBkMYhbGB0w3IwmNlDhR0WIxX0AiTnLchCNhRkAAAAASUVORK5CYII=',
			'1475' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YWllDA0MDkMRYHRimMjQEOiCrE3VgCEUXY3RgdGVodHR1QHLfyqylS1ctXRkVheQ+RgeRVoYpDA0iKHpFQx0C0MUYWoFmOqCLsTYwBCC7TzQELDbVYRCEHxUhFvcBADtuyD7P+RdSAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>