<?

include('lib.php');

//controller
//there are all possible actions

if($action=='creategroup'){
	//share file with email
	$a= processRequest($token.'/doCreateGroup/'.encodeArgs(array(
		$_REQUEST['name'],
		$_REQUEST['description'])));
	//print_r($a);
	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='share'){
	//share file with group or user
	//print_r($_REQUEST);
	//return;
	$a= processRequest($token.'/doShareFileWithGroup/'.encodeArgs(array(
		$_REQUEST['gid'],
		$_REQUEST['fid'],
		$_REQUEST['days'],
		$_REQUEST['comment'],
		$_REQUEST['uid'])));
	//print_r($a);
	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='invite'){
	//renamefile or change attributes
	$a= processRequest($token.'/doInviteToGroup/'.encodeArgs(array(
		$_REQUEST['gid'],
		$_REQUEST['email'],
		$_REQUEST['from_name'],
		$_REQUEST['text'])));
	//print_r($a);
	//return;
	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage'].', code is '.$a[1]['invitationcode']);
}elseif($action=='deletegroup'){
	//delete group
	$a= processRequest($token.'/doDeleteGroup/'.encodeArgs(array(
		$_REQUEST['gid'])));

	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='renameg'){
	//rename folder
	$a= processRequest($token.'/doRenameGroup/'.encodeArgs(array(
		$_REQUEST['gid'],
		$_REQUEST['name'],
		$_REQUEST['description'])));
	//print_r($a);
	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='accept'){
	//delete folder
	$a= processRequest($token.'/doAcceptInvitation/'.encodeArgs(array(
		$_REQUEST['code'],$_REQUEST['accept'])));

	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='deleteshared'){
	//create foler
	$a= processRequest($token.'/doDeleteShared/'.encodeArgs(array(
		$_REQUEST['sid'])));

	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage']);
}elseif($action=='unsubscribe'){
	//create foler
	$a= processRequest($token.'/doUnsubscribeGroup/'.encodeArgs(array(
		$_REQUEST['gid'],$_REQUEST['uid'])));

	header('Location: indexgr.php?token='.$token.'&message='.$a[1]['statusmessage']);
}
?>
<html>
<head>
<title>SEMStorage API demo</title>
</head>
<body>
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
$a= processRequest($token.'/getGroupsList/');

if($a[0]==''){

//if all god and list is taken

$grouplist=$a[1]['grouplist'];

$filelist=array();
$b= processRequest($token.'/getFilesList/');

if($b[0]=='' && is_array($b[1]['filelist'])){
	
	foreach($b[1]['filelist'] as $f){
		if($f['fi_type']=='')
		$filelist[$f['fi_id']]=$f['fi_name'];
	}
}

$sharedfor=array();
$sharedby=array();
$c= processRequest($token.'/getAllShared/');
if($c[0]==''){
	if(is_array($c[1]['foruser']))
	foreach($c[1]['foruser'] as $s){
		$sharedfor[$s['gr_sharedid']]=$s;
	}
	if(is_array($c[1]['byuser']))
	foreach($c[1]['byuser'] as $s){
		$sharedby[$s['gr_sharedid']]=$s;
	}
	
}
//print_r($c);

//there is html path of the demo
?>
<script language="javascript">

var MyId='<?=$a[1]['myid']?>';

function radioclick(id,type,name,gid){
	if(type==1){
	document.getElementById('filediv').style.display='none';
	document.getElementById('folderdiv').style.display='block';
	document.getElementById('foldername').value=name;
	document.fsharefilegr.gid.value=id;
	document.fdeletegroup.gid.value=id;
	document.frenamegroup.gid.value=id;
	document.finviteuser.gid.value=id;
	document.frenamegroup.description.value=document.getElementById('description'+id).value;
	
	if(document.getElementById('creator'+id).value==MyId){
		document.fdeletegroup.deletegroup.disabled=false;
		document.frenamegroup.renamegroup.disabled=false;
		document.finviteuser.invitegroup.disabled=false;
	}else{
		document.fdeletegroup.deletegroup.disabled=true;
		document.frenamegroup.renamegroup.disabled=true;
		document.finviteuser.invitegroup.disabled=true;
	}
	
	/*document.frenamefolder.fid.value=id;
	document.fdeletefolder.fid.value=id;
	document.fuploadfile.fid.value=id;
	document.frenamefolder.name.value=name;
	*/
	}else{
	document.getElementById('filediv').style.display='block';
	document.getElementById('folderdiv').style.display='none';
	document.getElementById('filename').value=name;
	document.fsharefile.uid.value=id;
	document.fsharefile.gid.value=gid;
	/*document.fmovefile.fid.value=id;
	document.frenamefile.fid.value=id;
	document.frenamefile.name.value=name;
	document.frenamefile.description.value=document.getElementById('description'+id).value;
	document.frenamefile.tags.value=document.getElementById('tags'+id).value;
	document.fdeletefile.fid.value=id;*/
	}
}

</script>
<table width="100%" border=0>
<tr><td width="30%" valign="top">
<form name="treeform" id="treeform">
<table border=1>
<?
$groups=array();
$users=array();
if(!is_array($grouplist))
	$grouplist=array();
foreach($grouplist as $gr){
	$groups[$gr['gr_id']]=$gr['gr_title'];
	
	?>
	<tr>
	<td><?=$gr['gr_title']?></td>
	<td></td>
	<td><input type="radio" name="gid" value="<?=$gr['gr_id']?>" onclick="radioclick(<?=$gr['gr_id']?>,1,'<?=$gr['gr_title']?>',0)">
	<input type="hidden" id="description<?=$gr['gr_id']?>" name="description<?=$gr['gr_id']?>"  value="<?=$gr['gr_description']?>">
	<input type="hidden" id="creator<?=$gr['gr_id']?>" name="creator<?=$gr['gr_id']?>"  value="<?=$gr['gr_creator']?>">
	</td>
	</tr>
	<?
	foreach($gr['gr_users'] as $us){
	$users[$us['us_id']]=$us['us_name'];
	?>
	<tr>
	<td></td>
	<td><?=$us['us_name']?></td>
	<td><input type="radio" name="gid" value="<?=$us['us_id']?>" onclick="radioclick(<?=$us['us_id']?>,0,'<?=$us['us_name']?>',<?=$gr['gr_id']?>)">
	</td>
	<td>
	<?
	if($us['us_id']==$a[1]['myid'] || $gr['gr_creator']==$a[1]['myid']){
	?>
	<form name="funsubscribe" id="funsubscribe" >
	<input type="hidden" name="action" value="unsubscribe">
	<input type="hidden" name="uid" value="<?=$us['us_id']?>">
	<input type="hidden" name="gid" value="<?=$gr['gr_id']?>">
	<input type="hidden" name="token" value="<?=$token?>">
	<input type="submit" value="Unsubs" >
	</form>
	<?
	}
	?>
	</td>
	</tr>
	<?
	}
}
?>
</table>
<h3>Shared by me</h3>
<table border=1>
<tr>
<th>
File
</th>
<th>
For
</th>
</tr>
<?

foreach($sharedby as $s){
	?>
	<tr>
	<td><?=$s['fi_name']?></td>
	<?
	$n=$groups[$s['gr_id']];
	if($s['us_id']!='0' && $s['us_id']!='')
		$n.='->'.$users[$s['us_id']];
	?>
	<td><?=$n?></td>
	<td>
	<form name="fdeletesharedbyme" id="fdeletesharedbyme" >
	<input type="hidden" name="action" value="deleteshared">
	<input type="hidden" name="sid" value="<?=$s['gr_sharedid']?>">
	<input type="hidden" name="token" value="<?=$token?>">
	<input type="submit" value="Delete" >
	</form>
	</td>
	</tr>
	<?
}
?>
</table>
<h3>Shared for me</h3>
<table border=1>
<tr>
<th>
File
</th>
<th>
For
</th>
<th>
By
</th>
</tr>
<?

foreach($sharedfor as $s){
	?>
	<tr>
	<td><?=$s['fi_name']?></td>
	<?
	$n=$groups[$s['gr_id']];
	if($s['us_id']!='0'  && $s['us_id']!='')
		$n.='->'.$users[$s['us_id']];
	?>
	<td><?=$n?></td>
	<td><?=$s['us_name']?></td>
	<td>
	<form name="fdeletesharedforme" id="fdeletesharedforme" >
	<input type="hidden" name="action" value="deleteshared">
	<input type="hidden" name="sid" value="<?=$s['gr_sharedid']?>">
	<input type="hidden" name="token" value="<?=$token?>">
	<input type="submit" value="Delete" >
	</form>
	</td>
	</tr>
	<?
}
?>
</table>
</form>
</td><td valign="top" align="left">
<div id="filediv" name="filediv" style="display:none">
<table>
<caption>User operations</caption>
<tr>
<th>Selected user <input type="text" disabled value="" name="filename" id="filename"></th>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Share file with selected user</th>
</tr>
<tr>
<td>
<form name="fsharefile" id="fsharefile" >
<input type="hidden" name="action" value="share">
<input type="hidden" name="uid" value="">
<input type="hidden" name="gid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>Days</td>
<td><input type="text" name="days"></td>
</tr>
<tr>
<td>Comment</td>
<td><input type="text" name="comment"></td>
</tr>
<tr>
<td>File to share</td>
<td>
<select name="fid">
<?
foreach(array_keys($filelist) as $f)
echo "<option value=\"$f\">$filelist[$f]\n";
?>
</select></td>
</tr>

</table>
<input type="submit" value="Share">
</form>
</td>
</tr>



</table>
</div>

<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

<div id="folderdiv" name="folderdiv" style="display:none">
<table>
<caption>Group operations</caption>
<tr>
<th>Selected group <input type="text" disabled value="" name="foldername" id="foldername"></th>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Share file with selected group</th>
</tr>
<tr>
<td>
<form name="fsharefilegr" id="fsharefilegr" >
<input type="hidden" name="action" value="share">
<input type="hidden" name="uid" value="0">
<input type="hidden" name="gid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>Days</td>
<td><input type="text" name="days"></td>
</tr>
<tr>
<td>Comment</td>
<td><input type="text" name="comment"></td>
</tr>
<tr>
<td>File to share</td>
<td>
<select name="fid">
<?
foreach(array_keys($filelist) as $f)
echo "<option value=\"$f\">$filelist[$f]\n";
?>
</select></td>
</tr>

</table>
<input type="submit" value="Share">
</form>
</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<th>Rename group</th>
</tr>
<tr>
<td>

<form name="frenamegroup" id="frenamegroup" >
<input type="hidden" name="action" value="renameg">
<input type="hidden" name="gid" value="">
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
<input type="submit" value="Rename" disabled name="renamegroup" id="renamegroup">
</form>
</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Invite to group </th>
</tr>
<tr>
<td>
<form name="finviteuser" id="finviteuser" >
<input type="hidden" name="action" value="invite">
<input type="hidden" name="gid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>Email</td>
<td>
<input type="text" name="email">
</td>
</tr>
<tr>
<td>From name</td>
<td>
<input type="text" name="from_name">
</td>
</tr>
<tr>
<td>Text</td>
<td>
<input type="text" name="text">
</td>
</tr>
</table>
<input type="submit" value="Invite" disabled name="invitegroup" id="invitegroup">
</form>
</td>
</tr>
<!-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<tr>
<td><hr></td>
</tr>
<tr>
<th>Delete group </th>
</tr>
<tr>
<td>
<form name="fdeletegroup" id="fdeletegroup" >
<input type="hidden" name="action" value="deletegroup">
<input type="hidden" name="gid" value="">
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="Delete" disabled name="deletegroup" id="deletegroup">
</form>
</td>
</tr>
</table>
</div>
<hr>

<h3>Create group</h3>
<form name="fcreategroup" id="fcreategroup" >
<input type="hidden" name="action" value="creategroup">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>Group name</td>
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
<h3>Accept invitation</h3>
<form name="faccept" id="faccept" >
<input type="hidden" name="action" value="accept">
<input type="hidden" name="token" value="<?=$token?>">
<table>
<tr>
<td>Invitation code</td>
<td>
<input type="text" name="code">
</td>
</tr>
<tr>
<td>Accept or refuse</td>
<td>
<select name="accept">
<option value="y" selected>yes
<option value="y">no
</select>
</td>
</tr>
</table>
<input type="submit" value="Create">
</form>
</td></tr>
</table>


<?
}else
echo '<font color="red">'.$a[0].'</font>';
//print_r($files);
?>
</body>
</html>
<?




?>