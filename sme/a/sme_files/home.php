<?


//check permissions of upload dir
/*$dir=dirname(__FILE__).'/upl';
if(!is_writable($dir)){
	echo '"'.$dir.'" directory does not exists or is not writable';
	exit;
}*/

if($token=='' && $_REQUEST['login']==''){
	//show login form
	?>
	
	Use your SMEStorage login info here<br>
	<form>
	
	Login:<input name="login" type="text"><br>
	Password:<input name="password" type="password"><br>
	<input type="submit">
	</form>
	<?
	exit;
}else

//controller
//there are all possible actions



/**
* Takes REST command, process request and return array created from returned xml document
* 
**/










//lib.php code endes here

//********************************************

//include('lib.php');

?>