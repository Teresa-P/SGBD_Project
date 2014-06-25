<script type="text/javascript" src="<?php echo get_bloginfo('wpurl');?>/custom/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl');?>/custom/js/jquery-1.9.0.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl');?>/custom/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl');?>/custom/js/anytime.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('wpurl');?>/custom/css/cdc.css">
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl');?>/custom/js/validatious-custom-0.9.1.min.js"></script> 
<script type="text/javascript" src="<?php echo get_bloginfo('wpurl');?>/custom/js/properties.js"></script> 

<?php 
//require_once("custom/php/common.php");

/*
function resize()
{
	IF($tables.width > '800px')
	{
		$tables.css.('font-size') --;
	}
}
*/

/*function isLoggedIn()
{
	get_currentuserinfo();
	global $user_level;
	if($user_level > 0)
		return true;
	else
		return false;
}*/

function insertError()
{
	echo mysql_error();	echo "<br>";
	?>
	<div>
		Erro na inserção!
	</div>
	<?php 
}	

function getEnumValues($table, $field) {
    $enum_array = array();
    $query = 'SHOW COLUMNS FROM `' . $table . '` LIKE "' . $field . '"';
    $result = mysql_query($query);
    $row = mysql_fetch_row($result);
    //preg_match_all('/\'(.*?)\'/', $row[1], $enum_array);
    preg_match_all('/\((.*?)\\)/', $row[1], $enum_array);
 
    if(!empty($enum_array[1])) {
 
    $str1=$enum_array[1][0];
 
    $str2=array();
    
    $tok = strtok($str1, ",");
 
 
while ($tok !== false) {
    $str2[]=$tok;
    $tok = strtok(",");
}
 
    $enumarray2=array();
    foreach ($str2 as $mkey=>$mval){
        preg_match_all('/\'(.*?)\'/', $mval, $istr);
        $enumarray2[$mkey+1]="";
        foreach($istr[1] as $mkey2=>$mval2){
            if ($mkey2>0) $enumarray2[$mkey+1].="'";
            $enumarray2[$mkey+1].=$mval2;
        }
    }
 
    return $enumarray2; 
 
    }
    
    else return array();
    
}
?>