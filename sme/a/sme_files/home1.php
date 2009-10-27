<?

define('_SERVICE_URL','http://www.smetube.com/smestorage/api/');
define('_UPLOADER_URL','http://www.smetube.com/cgi-bin/uploader/uploader1.cgi');

$token=$_REQUEST['token'];


require("http.php");
require('class.xmltoarray.php');


//check permissions of upload dir
$dir=dirname(__FILE__).'/upl';
if(!is_writable($dir)){
	echo '"'.$dir.'" directory does not exists or is not writable';
	exit;
}

if($token=='' && $_REQUEST['login']==''){
	//show login form
	?>
	
	Use your SMEStorage login info here<br>
	<form>
	hhhh
	Login:<input name="login" type="text"><br>
	Password:<input name="password" type="password"><br>
	<input type="submit">
	</form>
	<?
	exit;
}elseif($token=='' && $_REQUEST['login']!=''){
	//process login and get token
	echo "hello nishant";
	
	$a= processRequest('*/gettoken/'.encodeArgs(array($_REQUEST['login'],$_REQUEST['password'])));//,array(),array(),1
	print_r($a);
	die();
	if($a[0]==''){
		//if 
		if($a[1]['notice']=='1'){
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
			exit;
		}
		//if login success redirect with token

			//include('home.php?page=sme_navigation&token='.$a[1]['token'].'&message=Last+visit:+'.$a[1]['lastlogin']);	
			header('Location: home.php?token='.$a[1]['token'].'&message=Last+visit:+'.$a[1]['lastlogin']);	
		exit;
	}else
	echo '<font color="red">'.$a[0].'</font>';
	return;
}

//controller
//there are all possible actions
$action=$_REQUEST['action'];
if($action=='providerlogin'){
	//move file to another folder
	$a= processRequest($token.'/setProviderData/'.encodeArgs(array(
		$_REQUEST['login'],
		$_REQUEST['password'])));
	//print_r($a);
	//include('home.php?page=sme_navigation&token='.$token.'&message='.$a[1]['statusmessage']);
	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}


/**
* Takes REST command, process request and return array created from returned xml document
* 
**/
function processRequest($request,$debug=0,$data=array(),$files=array()){
	$result=array('',array());
	$http=new http_class;
	//$http->timeout=0;
	//$http->data_timeout=0;
	$http->debug=($debug==2)?1:0;
	$http->html_debug=1;

	$url=_SERVICE_URL.$request;
//	echo $url.'<br>';
	if($debug>0){
		echo $url.'<br>';
		if($debug>1)
			exit;
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
					exit;
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










//lib.php code endes here

//********************************************

//include('lib.php');

if($action=='share'){
	//share file with email
	$a= processRequest($token.'/doSendEmail/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['email'],
		$_REQUEST['name'],
		$_REQUEST['text'],
		$_REQUEST['days'])));
	
	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='move'){
	//move file to another folder
	$a= processRequest($token.'/doMoveFiles/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['newfolder'])));
	
	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='rename'){
	//renamefile or change attributes
	$a= processRequest($token.'/doRenameFile/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['tags'])));
	
	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='delete'){
	//delete file
	$a= processRequest($token.'/doDeleteFile/'.encodeArgs(array(
		$_REQUEST['fid'])));

	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='renamef'){
	//rename folder
	$a= processRequest($token.'/doRenameFolder/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['tags'])));

	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='deletef'){
	//delete folder
	$a= processRequest($token.'/doDeleteFolder/'.encodeArgs(array(
		$_REQUEST['fid'])));

	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='createf'){
	//create foler
	$a= processRequest($token.'/doCreateNewFolder/'.encodeArgs(array(
		$_REQUEST['name'],
		$_REQUEST['description'],
		$_REQUEST['folder'])));

	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}
elseif($action=='fax'){
	//fax file
	
	$a= processRequest($token.'/doFaxDocuments/'.encodeArgs(array(
		$_REQUEST['fid'],
		$_REQUEST['recipients'],
		'test')));
	
	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
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
	exit;
	
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

	header('Location: home.php?token='.$token.'&message='.$a[1]['statusmessage']);
}

?>
<?

//show message if it is passed
if($_REQUEST['message']!='')
	echo "<h4 style=\"color:red\">".$_REQUEST['message']."</h4>";

$thisscript=basename(__FILE__);
include('menu.php');

?>

<h3>Operations</h3>

<?

//get list of file and folders
$a= processRequest($token.'/getFilesList/'.encodeArgs(array(
		'',1)));//,array(),array(),1
//print_r($a);
if($a[0]==''){

//if all god and list is taken

$files=$a[1]['filelist'];
//print_r($files);
$tree=array();
$fids=array();
//create tree structure
foreach (array_values($files) as $f){
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
<caption>File operations</caption>
<tr>
<th>Selected file <input type="text" disabled value="" name="filename" id="filename"></th>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Share file </th>
</tr>
<tr>
<td>
<form name="fsharefile" id="fsharefile" >
<input type="hidden" name="action" value="share">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>Days</td>
<td><input type="text" name="days"></td>
</tr>
<tr>
<td>Friend's email</td>
<td><input type="text" name="email"></td>
</tr>
<tr>
<td>Your name</td>
<td><input type="text" name="name"></td></tr>
<tr>
<td>Text</td>
<td><input type="text" name="text"></td></tr>
</table>
<input type="submit" value="Send">
</form>
</td>
</tr>

<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Move file </th>
</tr>
<tr>
<td>
<form name="fmovefile" id="fmovefile" >
<input type="hidden" name="action" value="move">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>To folder</td>
<td>
<select name="newfolder">
<?
$l=getFolders(0);

foreach(array_keys($l) as $a)
echo "<option value=\"$a\">$l[$a]\n";
?>
</select></td>
</tr>

</table>
<input type="submit" value="Move">
</form>
</td>
</tr>


<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>

<tr>
<th>Replace file with new method</th>
</tr>
<tr>
<td>
<input type="hidden" name="fid" value="" id="fidcommon2">
<table>
<tr>
<td>Encrypt phrase</td>
<td>
<input type="text" name="encphrase" id="fileencphrase">
</td>
</tr>
<tr>
<td>File</td>
<td>
<form name="fnewuploadfile2" id="fnewuploadfile2" enctype="multipart/form-data" method="post">
<input type="file" name="uplfile" id="filefilecommon2">
</form>
</td>
</tr>
</table>
<input type="button" value="Upload" onClick="startuploader('y')">

</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Replace file with old method</th>
</tr>
<tr>
<td>
<form name="fuploadfile2" id="fuploadfile2" enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
<input type="hidden" name="saveornot" value="y">
<input type="hidden" name="action" value="upload">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>File</td>
<td>
<input type="file" name="uplfile">
</td>
</tr>
</table>
<input type="submit" value="Upload" name="uploadold">
</form>
</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Rename file </th>
</tr>
<tr>
<td>
<form name="frenamefile" id="frenamefile" >
<input type="hidden" name="action" value="rename">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>New name</td>
<td>
<input type="text" name="name">
</td>
</tr>
<tr>
<td>Description</td>
<td>
<input type="text" name="description">
</td>
</tr>
<tr>
<td>Tags</td>
<td>
<input type="text" name="tags">
</td>
</tr>
</table>
<input type="submit" value="Rename">
</form>
</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Delete file </th>
</tr>
<tr>
<td>
<form name="fdeletefile" id="fdeletefile" >
<input type="hidden" name="action" value="delete">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="Delete">
</form>
</td>
</tr>

<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Fax file </th>
</tr>
<tr>
<td>
<form name="ffaxfile" id="ffaxfile" >
<input type="hidden" name="action" value="fax">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
Recipients
<textarea name="recipients" rows=3 cols=30></textarea><br>
<input type="submit" value="Fax">
</form>
</td>
</tr>
</table>
</div>

<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

<div id="folderdiv" name="folderdiv" style="display:none">
<table>
<caption>Folder operations</caption>
<tr>
<th>Selected folder <input type="text" disabled value="" name="foldername" id="foldername"></th>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>

<tr>
<th>Upload file with new method</th>
</tr>
<tr>
<td>
<input type="hidden" name="fid" value="" id="fidcommon">
<table>
<tr>
<td>File name</td>
<td>
<input type="text" name="name" id="filenamecommon">
</td>
</tr>
<tr>
<td>Description</td>
<td>
<input type="text" name="description"  id="filedescriptioncommon">
</td>
</tr>
<tr>
<td>Tags</td>
<td>
<input type="text" name="tags" id="filetagscommon">
</td>
</tr>
<tr>
<td>Encrypt phrase</td>
<td>
<input type="text" name="encphrase" id="fileencphrase">
</td>
</tr>
<tr>
<td>File</td>
<td>
<form name="fnewuploadfile" id="fnewuploadfile" enctype="multipart/form-data" method="post">
<input type="file" name="uplfile" id="filefilecommon">
</form>
</td>
</tr>
</table>
<input type="button" value="Upload" onClick="startuploader('n')">

</td>
</tr>

<tr>
<th>Upload file with old method</th>
</tr>
<tr>
<td>
<form name="fuploadfile" id="fuploadfile" enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
<input type="hidden" name="action" value="upload">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>File name</td>
<td>
<input type="text" name="name">
</td>
</tr>
<tr>
<td>Description</td>
<td>
<input type="text" name="description">
</td>
</tr>
<tr>
<td>Tags</td>
<td>
<input type="text" name="tags">
</td>
</tr>
<tr>
<td>File</td>
<td>
<input type="file" name="uplfile">
</td>
</tr>
</table>
<input type="submit" value="Upload" name="uploadold">
</form>
</td>
</tr>


<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<th>Rename folder</th>
</tr>
<tr>
<td>

<form name="frenamefolder" id="frenamefolder" >
<input type="hidden" name="action" value="renamef">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>New name</td>
<td>
<input type="text" name="name">
</td>
</tr>
<tr>
<td>Description</td>
<td>
<input type="text" name="description">
</td>
</tr>
</table>
<input type="submit" value="Rename">
</form>
</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Delete folder </th>
</tr>
<tr>
<td>
<form name="fdeletefolder" id="fdeletefolder" >
<input type="hidden" name="action" value="deletef">
<input type="hidden" name="fid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="Delete">
</form>
</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Create folder </th>
</tr>
<tr>
<td>
<form name="fcreatefolder" id="fcreatefolder" >
<input type="hidden" name="action" value="createf">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>In folder</td>
<td>
<select name="folder">
<option value="0">/
<?
$l=getFolders(0);

foreach(array_keys($l) as $a)
echo "<option value=\"$a\">$l[$a]\n";
?>
</select></td>
</tr>
<tr>
<td>New name</td>
<td>
<input type="text" name="name">
</td>
</tr>
<tr>
<td>Description</td>
<td>
<input type="text" name="description">
</td>
</tr>
</table>
<input type="submit" value="Create">
</form>
</td>
</tr>
</table>
</div>
</td></tr>
</table>
<?
}else
echo '<font color="red">'.$a[0].'</font>';
//print_r($files);
?>
<?

/**
* Print contents of the tree (folder and files)
**/
function printLevel($fid,$l=0){
	global $tree,$fids,$token;
	unset($Tab);
	$Tab='';
	for($i=1;$i<=$l;$i++)$Tab.='&nbsp;&nbsp;&nbsp;&nbsp;';
	if($Tab!='')$Tab.='|__';
	if(array_key_exists($fid,$tree)){
	foreach($tree[$fid][1] as $fi){
		
		echo '<tr><td>'.$Tab;
		$f=$fids[$fi];
		echo '<font color=red>'.$f['fi_name'].'</font></td>';
		echo '<td><input type="radio" name="fid" value="'.$fi.'|1" onclick="radioclick('.$fi.',1,\''.$f['fi_name'].'\')">';
		echo '<input type="hidden" id=name="description'.$fi.'" name="description'.$fi.'" value="'.$f['fi_description'].'">';
		echo '</td></tr>';
		printLevel($fi,$l+1);
	}
	foreach($tree[$fid][0] as $fi){
		
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
/**
* Return array of the folders with level mark
**/
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