<?php
/*
Plugin Name: SME Plugin
Plugin URI: http://www.cnelindia.com/
Description: The Plugin Name Is SME Storage Plugin
Author: CnEL
Version: 2.1
Author URI: http://cnelindia.com/
*/

define('_SERVICE_URL','http://www.smetube.com/smestorage/api/');
define('_UPLOADER_URL','http://www.smetube.com/cgi-bin/uploader/uploader1.cgi');
require("http.php");
require('class.xmltoarray.php');

$token=$_REQUEST['token'];

$action=$_REQUEST['action'];
if($action=='providerlogin'){
	//move file to another folder
	$a= processRequest($token.'/setProviderData/'.encodeArgs(array(
		$_REQUEST['login'],
		$_REQUEST['password'])));
	//print_r($a);
	//include('admin.php?page=sme_navigation&token='.$token.'&message='.$a[1]['statusmessage']);
	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}





$token = $_REQUEST['token'];
	if($token=='' && $_REQUEST['login']!=''){
	//process login and get token
	//echo "hello nishant";
	
	$a= processRequest('*/gettoken/'.encodeArgs(array($_REQUEST['login'],$_REQUEST['password'])));//,array(),array(),1
	//print_r($a);
	//die();
	if($a[0]=='')
	{
		//if 
		if($a[1]['notice']=='1')
		{
			//must provide provider info
			?>
			<title>SEMStorage API demo. Login to provider.</title>
			You must to enter your provider access info (login/password for Gmail or keys for Amazon S3)<br>
			<form>
			Login:<input name="login" type="text"><br>
			Password:<input name="password" type="password"><br>
			<input name="token" type="hidden" value="<?=$a[1]['token']?>">
			<input name="action" type="hidden" value="providerlogin">
			<input type="submit" value="Continue">
			</form>
			<?
			//exit;
		}
		//if login success redirect with token

			//include('admin.php?page=sme_navigation&token='.$a[1]['token'].'&message=Last+visit:+'.$a[1]['lastlogin']);	
			header('Location: admin.php?page=sme_navigation&token='.$a[1]['token'].'&message=Last+visit:+'.$a[1]['lastlogin']);	
			//header('Location: admin.php?page=sme_navigation');	
		//exit;
	}
	else
	echo '<font color="red">'.$a[0].'</font>';
	return;
}





add_action('init','createtable');
function createtable()
{
	global $wpdb;
	$pre = $wpdb->prefix;	
	//$pre = substr($pre,0,2);	
	$table_na=$pre."login";
	if($wpdb->get_var("show tables like '$table_na'")!=$table_na)
	{		
		$sql="CREATE TABLE ".$table_na." (
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		username TEXT NOT NULL,
		password TEXT NOT NULL		
		)ENGINE = MYISAM " ;		
		$comment=$wpdb->get_results($sql);	
		//mysql_query("INSERT INTO ".$table_na."( id,content)VALUES ( '1','')");
	}
}
if($_POST['submit'])
{
	$user = $_POST['user'];
	$pass = $_POST['pass'];
	global $wpdb;
	$pre = $wpdb->prefix;	
	$table_na=$pre."login";
	if(strlen($user)>0)
	{
		$sql = "insert into ".$table_na." set username='$user', password='$pass'";
		$result = mysql_query($sql);
	}
	/*if(strlen($user)>0)
	{
		$sql = "select * from ".$table_na." where username='$user', password='$pass'";
		$result = mysql_query($sql);
		$num_rows = mysql_num_rows($result);
		if($num_rows>0)
		{
			echo "login successfull";
		}
	}*/
}
function login_form()
{
?>
	<form name="form1" action="" method="post">		
	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>User Name</td>
			<td><input type="text" value="" name="user" /></td>
		  </tr>
		  <tr>
			<td>Password</td>
			<td><input type="text" name="pass" value="" /></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Login"></td>
		  </tr>
	</table>
	</form>
<?
}
function sme()
{
	//include("admin.php");
	
	
	if($token=='' && $_REQUEST['login']=='')
	{
	//show login form
	?>
	
	Use your SMEStorage login info here<br>
	<form>
	
	Login:<input name="login" type="text"><br>
	Password:<input name="password" type="password"><br>
	<input type="submit">
	</form>
	<?
	//exit;

	//	include("admin.php");
	}
}
function sme_navigation()
{
	
	
			
		$a= processRequest($token.'/getFilesList/'.encodeArgs(array('',1)));//,array(),array(),1

		if($a[0]=='')
		{
		
		//if all god and list is taken
		
			$files=$a[1]['filelist'];
			//print_r($files);
			$tree=array();
			$fids=array();
			//create tree structure
			if(strlen($files)>0)
			{
				foreach (array_values($files) as $f)
				{
					$fids[$f['fi_id']]=$f;
					if($f['fi_pid']=='')
						$f['fi_pid']='0';
					if(!array_key_exists($f['fi_pid'],$tree))
						$tree[$f['fi_pid']]=array(array(),array());
				
					if($f['fi_type']=='') $f['fi_type']=0;
					//echo 'Push '.$f['fi_name'].' with type '.$f['fi_type'].' and pid '.$f['fi_pid'].' and id '.$f['fi_id'].'<br>';
					$tree[$f['fi_pid']][intval($f['fi_type'])][]=$f['fi_id'];
					//if($f['fi_type']=='1') echo $f['fi_name'].'<br>';
				}
			}				
		}	
	
	if(strlen($files)>0)
	{	?>
		<script language="javascript">
		window.location.href='<? echo 'admin.php?page=sme_navigation&token='.$a[1]['token'].'&message=Last+visit:+'.$a[1]['lastlogin'] ?>';
		</script>
		<?
	}
}
add_action('admin_menu', 'wp_autoblog_add_sme_to_admin');

function wp_autoblog_add_sme_to_admin()
{
	add_menu_page('', 'SME', 8, __FILE__, 'sme');				
	add_submenu_page(__FILE__, 'SME Storage', 'SME Storage', 10, 'sme_navigation', 'sme_navigation');
}
function processRequest($request,$debug=0,$data=array(),$files=array())
{
	$result=array('',array());
	$http=new http_class;
	//$http->timeout=0;
	//$http->data_timeout=0;
	$http->debug=($debug==2)?1:0;
	$http->html_debug=1;

	$url=_SERVICE_URL.$request;
//	echo $url.'<br>';
	if($debug>0)
	{
		echo $url.'<br>';
		if($debug>1)
		{
			//exit;
		}
	}
	//exit;
	//return;
	$error=$http->GetRequestArguments($url,$arguments);
	$arguments["RequestMethod"]="POST";
	$arguments["PostValues"]=array(
		
	);
	foreach (array_keys($data) as $d)
		$arguments["PostValues"][$d]=$data[$d];

	if(count($files)>0 ){
		$arguments["PostFiles"]=array();
		$arguments["PostValues"]["MAX_FILE_SIZE"]="10000000";
		foreach(array_keys($files) as $f)
			$arguments["PostFiles"][$f]=array('FileName'=>$files[$f],"Content-Type"=>"automatic/name");
	}
		//print_r($arguments["PostFiles"]);
	//$arguments["Referer"]="http://www.alltheweb.com/";
	$result[0]=$http->Open($arguments);

	if($result[0]=="")
	{
		$result[0]=$http->SendRequest($arguments);

		if($result[0]=="")
		{
			$result[0]=$http->ReadReplyHeaders($headers);

			if($result[0]=="")
			{

				$result[0]=$http->ReadReplyBody($body,1000000);
				if($debug>0)
					echo $body;
				$x=new XmlToArray($body);
				$a=$x->createArray();
				$a=$a['response'];
				if($a['status']!='ok')
					$result[0]=$a['statusmessage'];
				if($debug>0){
					print_r($a);
					//exit;
				}
				$result[1]=$a;
				
			}
		}
		$http->Close();
	}
	return $result;
		
		
}
/**
* Encode arguments with base64 encoding and join with comma delimiter
**/
function encodeArgs($args){
	$a=array();
	foreach ($args as $ar){
		$a[]=base64_encode($ar);
	}
	return join(',',$a);
}

















if($action=='share'){
	//share file with email
	$a= processRequest($token.'/doSendEmail/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['email'],
		$_REQUEST['name'],
		$_REQUEST['text'],
		$_REQUEST['days'])));
	
	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='move'){
	//move file to another folder
	$a= processRequest($token.'/doMoveFiles/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['newfolder'])));
	
	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='rename'){
	//renamefile or change attributes
	$a= processRequest($token.'/doRenameFile/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['tags'])));
	
	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='delete'){
	//delete file
	$a= processRequest($token.'/doDeleteFile/'.encodeArgs(array(
		$_REQUEST['fid'])));

	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='renamef'){
	//rename folder
	$a= processRequest($token.'/doRenameFolder/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['tags'])));

	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='deletef'){
	//delete folder
	$a= processRequest($token.'/doDeleteFolder/'.encodeArgs(array(
		$_REQUEST['fid'])));

	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='createf'){
	//create foler
	$a= processRequest($token.'/doCreateNewFolder/'.encodeArgs(array(
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['folder'])));

	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}
elseif($action=='fax'){
	//fax file
	
	$a= processRequest($token.'/doFaxDocuments/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['recipients'],
		'test')));
	
	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='initupload'){
	//init upload
	$fid=($_REQUEST['saveornot']=='y')?$_REQUEST['fid']:'0';

	$a= processRequest($token.'/doInitUpload/'.encodeArgs(array(
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['tags'],
		$_REQUEST['fid'],
		$_REQUEST['filename'],
		'javascript',
		'alert(\'Click button Finished\');',
		$_REQUEST['encryptphrase'],
		'',
		'',
		$fid
		)));
	header('Location: uploader.php?token='.$token.'&action=start&code='.$a[1]['uploadcode'].'&saveornot='.$_REQUEST['saveornot']);
}elseif($action=='getstatus'){
	//create foler
	$a= processRequest($token.'/getUploadStatus/'.encodeArgs(array(
		$_REQUEST['code']
		)));
	if($a[0]=='')
		print $a[1]['percent'];
	else
		print '0';
	//exit;
	
}elseif($action=='finishupload'){
	//create foler
	$a= processRequest($token.'/doCompleteUpload/'.encodeArgs(array(
		$_REQUEST['code']
		)));

	header('Location: uploader.php?token='.$token.'&action=end');
}elseif($action=='upload'){

	//upload file
	if($_FILES['uplfile']['size']<=0)
		return;

	$file=dirname(__FILE__).'/upl/'.basename($_FILES['uplfile']['name']);
	move_uploaded_file($_FILES['uplfile']['tmp_name'], $file);

	$argname='fi_pid';
	if($_REQUEST['saveornot']=='y') $argname='fi_id';

	$data=array(
		$argname=>$_REQUEST['fid'],
		'file_name1'=>$_REQUEST['name'],
		'file_desc1'=>$_REQUEST['description'],
		'file_tags1'=>$_REQUEST['tags'],
	);
	$files=array('file_1'=>$file);

	$a= processRequest($token.'/doUploadFiles/',0,$data,$files);

	header('Location: admin.php?token='.$token.'&message='.$a[1]['statusmessage']);
}
if($_REQUEST['message']!='')
	//echo "<h4 style=\"color:red\">".$_REQUEST['message']."</h4>";

//$thisscript=basename(__FILE__);
//include('menu.php');
$a= processRequest($token.'/getFilesList/'.encodeArgs(array(
		'',1)));//,array(),array(),1

if($a[0]!='')
{

//if all god and list is taken

$files=$a[1]['filelist'];
//print_r($files);
$tree=array();
$fids=array();
//create tree structure
if(strlen($files)>0)
{
	foreach (array_values($files) as $f)
	{
		$fids[$f['fi_id']]=$f;
		if($f['fi_pid']=='')
			$f['fi_pid']='0';
		if(!array_key_exists($f['fi_pid'],$tree))
			$tree[$f['fi_pid']]=array(array(),array());
	
		if($f['fi_type']=='') $f['fi_type']=0;
		//echo 'Push '.$f['fi_name'].' with type '.$f['fi_type'].' and pid '.$f['fi_pid'].' and id '.$f['fi_id'].'<br>';
		$tree[$f['fi_pid']][intval($f['fi_type'])][]=$f['fi_id'];
		//if($f['fi_type']=='1') echo $f['fi_name'].'<br>';
	}
}
//there is html path of the demo
?>
<script language="javascript">

var token='<?=$token?>';

function radioclick(id,type,name){
	if(type==1){
	document.getElementById('filediv').style.display='none';
	document.getElementById('folderdiv').style.display='block';
	document.getElementById('foldername').value=name;
	document.frenamefolder.fid.value=id;
	document.fdeletefolder.fid.value=id;
	document.fuploadfile.fid.value=id;
	document.frenamefolder.name.value=name;
	document.getElementById('fidcommon').value=id;


	document.frenamefolder.description.value=document.getElementById('description'+id).value;
	
	}else{
	document.getElementById('filediv').style.display='block';
	document.getElementById('folderdiv').style.display='none';
	document.getElementById('filename').value=name;
	document.fsharefile.fid.value=id;
	document.fuploadfile2.fid.value=id;
	document.fmovefile.fid.value=id;
	document.frenamefile.fid.value=id;
	document.ffaxfile.fid.value=id;
	document.fdeletefile.fid.value=id;
	document.frenamefile.name.value=name;
	//document.frenamefile.description.value=document.getElementById('description'+id).value;
	document.getElementById('fidcommon2').value=id;
	document.frenamefile.tags.value=document.getElementById('tags'+id).value;
	
	}
}

function startuploader(s){
	wopen('uploader.php?token='+token+'&action=prepare&saveornot='+s,'uploaderwin',400,500);
	document.fnewuploadfile.action='<?=_UPLOADER_URL?>';	
}

function wopen(url, winname, w, h)
{
  w += 32;
  h += 96;
  var wleft = (screen.width - w) / 2;
  var wtop = (screen.height - h) / 2;
  var option='width=' + w + ',height=' + h + ',' +
    'left=' + wleft + ',top=' + wtop + ',' +
    'location=no,menubar=no,' +
    'status=no,toolbar=no,scrollbars=no,resizable=no';

  option=new String(option);
  var win = window.open(url,winname,option);
  win.resizeTo(w, h);
  win.moveTo(wleft, wtop);
  win.focus();
	return win;
}

</script>
<table width="100%" border=0>
<tr><td>
<form name="treeform" id="treeform">
<table border=1>
<?
printLevel(0);
?>
</table>
</form>
</td><td valign="top" align="left">
<div id="filediv" name="filediv" style="display:none">
<table>
<caption></caption>
<tr>
<th></th>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<?
$l=getFolders(0);

foreach(array_keys($l) as $a)
//echo "<option value=\"$a\">$l[$a]\n";
?>


</table>
</div>

<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

<div id="folderdiv" name="folderdiv" style="display:none">

<table>
<tr>
<td>

<?
$l=getFolders(0);

foreach(array_keys($l) as $a)
//echo "<option value=\"$a\">$l[$a]\n";
?>
</td>
</tr>
</table>


</div>
</td></tr>
</table>
<?
}
else
{
//echo '<font color="red">'.$a[0].'</font>';
}
//echo '<font color="red">'.$a[0].'</font>';
function printLevel($fid,$l=0)
{
	global $tree,$fids,$token;
	unset($Tab);
	$Tab='';
	for($i=1;$i<=$l;$i++)$Tab.='&nbsp;&nbsp;&nbsp;&nbsp;';
	if($Tab!='')$Tab.='|__';
	if(array_key_exists($fid,$tree))
	{
		foreach($tree[$fid][1] as $fi)
		{
			
			echo '<tr><td>'.$Tab;
			$f=$fids[$fi];
			echo '<font color=red>'.$f['fi_name'].'</font></td>';
			echo '<td><input type="radio" name="fid" value="'.$fi.'|1" onclick="radioclick('.$fi.',1,\''.$f['fi_name'].'\')">';
			echo '<input type="hidden" id=name="description'.$fi.'" name="description'.$fi.'" value="'.$f['fi_description'].'">';
			echo '</td></tr>';
			printLevel($fi,$l+1);
		}
		foreach($tree[$fid][0] as $fi)
		{
			
			echo '<tr><td>'.$Tab;
			$f=$fids[$fi];
	//there is example of the file downloading
			echo '<i><a href="'._SERVICE_URL.$token.'/getFile/'.encodeArgs(array($fi)).'">'.$f['fi_name'].'</a></i></td>';
			echo '<td><input type="radio" name="fid" value="'.$fi.'|0" onclick="radioclick('.$fi.',0,\''.$f['fi_name'].'\')">';
			echo '<input type="hidden" id=name="description'.$fi.'" name="description'.$fi.'" value="'.$f['fi_description'].'">';
			echo '<input type="hidden" id="tags'.$fi.'" name="tags'.$fi.'" value="'.$f['fi_tags'].'">';
			echo '</td></tr>';
		}
	}
	return;
}
function getFolders($fid,$l=0){

	global $tree,$fids;
	$list=array();
	unset($Tab);
	$Tab='';
	for($i=1;$i<=$l;$i++)$Tab.='--';
	if($Tab!='')$Tab.='>';
	if(array_key_exists($fid,$tree)){

	foreach($tree[$fid][1] as $fi){

		$f=$fids[$fi];
		$list[$fi]=$Tab.$f['fi_name'];
		$ll=getFolders($fi,$l+1);
		if(count($ll)>0){
			//echo 'merge';
			//print_r($list);
			//print_r($ll);
			//$list=array_merge($list,$ll);
			foreach ($ll as $a=>$b)
				$list[$a]=$b;
			//print_r($list);
		}
	}
	
	}
	return $list;
}
?>