<?

include('lib.php');

if($action=='sync'){

	$a= processRequest($token.'/doProviderSync/'.encodeArgs(array(
		$_REQUEST['pi_id'],
		$_REQUEST['statusdata'])));//,array(),array(),1
	
	if($a[1]['processedfiles']=='') $a[1]['processedfiles']='0';
	$a[1]['iscompleted']=strval(intval($a[1]['iscompleted']));


	if($a[1]['iscompleted']=='0'){
		?>
		<title>Sync</title>
		Processed <?=$a[1]['processedfiles']?> files (in this iteration)<br>
		<form>
			<input name="token" type="hidden" value="<?=$token?>">
			<input name="pi_id" type="hidden" value="<?=$a[1]['pi_id']?>">
			<input name="statusdata" type="hidden" value="<?=$a[1]['statusdata']?>">
			<input name="action" type="hidden" value="sync">
			<input type="submit" value="Continue">
			</form>
		<?
		exit;
	}else
	header('Location: indexpr.php?token='.$token.'&message='.urlencode('Processed '.$a[1]['processedfiles'].' files'));
}elseif($action=='remove'){

	$a= processRequest($token.'/doRemoveProvider/'.encodeArgs(array($_REQUEST['pi_id'])));//,array(),array(),1
	
	header('Location: indexpr.php?token='.$token.'&message='.urlencode('Removed '.$a[1]['provider']['pr_name'].' provider'));
}elseif($action=='default'){

	$a= processRequest($token.'/doSetDefaultProvider/'.encodeArgs(array($_REQUEST['pi_id'])));//,array(),array(),1
	
	header('Location: indexpr.php?token='.$token.'&message=success');
}elseif($action=='master'){

	$a= processRequest($token.'/doSetMasterCopy/'.encodeArgs(array($_REQUEST['pi_id'],$_REQUEST['v'])));//,array(),array(),1
	
	header('Location: indexpr.php?token='.$token.'&message=success');
}elseif($action=='dopassword'){

	$a= processRequest($token.'/doChangeProviderPassword/'.
		encodeArgs(array($_REQUEST['pi_id'],$_REQUEST['password'])));
	
	header('Location: indexpr.php?token='.$token.'&message='.$a[0]);
}elseif($action=='updatebuckets'){

	if(!is_array($_REQUEST['buckets']))
		$_REQUEST['buckets']=array();

	$a= processRequest($token.'/doUpdateBucketsList/'.
		encodeArgs(array($_REQUEST['pi_id'],join(',',$_REQUEST['buckets']))));//,array(),array(),1
	
	header('Location: indexpr.php?token='.$token.'&action=buckets&pi_id='.$_REQUEST['pi_id'].'&message='.$a[0]);
}elseif($action=='defaultbucket'){

	$a= processRequest($token.'/doSetDefaultBucket/'.
		encodeArgs(array($_REQUEST['pi_id'],$_REQUEST['defaultbucket'])));//,array(),array(),1
	
	header('Location: indexpr.php?token='.$token.'&action=buckets&pi_id='.$_REQUEST['pi_id'].'&message='.$a[0]);
}elseif($action=='addbucket'){

	$a= processRequest($token.'/doAddBucket/'.
		encodeArgs(array($_REQUEST['pi_id'],$_REQUEST['bucket'])));//,array(),array(),1
	
	header('Location: indexpr.php?token='.$token.'&action=buckets&pi_id='.$_REQUEST['pi_id'].'&message='.$a[0]);
}elseif($action=='bucketschosen'){
	
	
	$createnew=$_REQUEST['newbucket'];
	if($_REQUEST['defaultbucket']=='' && $createnew!='')
		$_REQUEST['defaultbucket']=$createnew;

	$buckets='';
	if(is_array($_REQUEST['buckets']))
		$buckets=join(',',$_REQUEST['buckets']);

	$a= processRequest($token.'/doAddProvider/'.
		encodeArgs(array($_REQUEST['pr_id'],'1',$_REQUEST['defaultbucket'],$buckets,$createnew)));
		//,array(),array(),1
	if($a[1]['whatnextcode']=='2'){
		header('Location: indexpr.php?token='.$token.'&action=addprovider2&pr_id='.$_REQUEST['pr_id']);
		exit;
	}
	
	header('Location: indexpr.php?token='.$token.'&action=doaddprovider2&pr_id='.$_REQUEST['pr_id']);
}elseif($action=='doaddprovider'){

	$data=array();
	foreach(split(',',$_REQUEST['fields']) as $f)
		$data[]=$f.'='.$_REQUEST[$f];
	$data=join("\n",$data);

	$a= processRequest($token.'/doAddProvider/'.
		encodeArgs(array($_REQUEST['pr_id'],'0',$data)));//,array(),array(),1

	$response=$a[1];

	if($a[1]['whatnextcode']=='1'){
		?>
		<title>Chose bucket(s)</title>
		Chose buckets/containers that will be used. Chose default bucket<br>
		<form>
		<input name="token" type="hidden" value="<?=$token?>">
		<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
		<input name="action" type="hidden" value="bucketschosen">
		New bucket name <input type="text" name="newbucket"><br>
		Default bucket <select name="defaultbucket">
		<option value="">--create new--
		<? foreach($response['buckets'] as $f){ 
		?>
		<option value="<?=$f?>"><?=$f?>
		<? } ?>
		</select><br>
		Used buckets <select name="buckets[]" MULTIPLE size=4>
		<? foreach($response['buckets'] as $f){ 
		?>
		<option value="<?=$f?>"><?=$f?>
		<? } ?>
		</select><br>
		<input type="submit" value="Continue">
		</form>
		<?
		exit;
	}
	if($a[1]['whatnextcode']=='2'){
		header('Location: indexpr.php?token='.$token.'&action=addprovider2&pr_id='.$_REQUEST['pr_id']);
		exit;
	}
	
	header('Location: indexpr.php?token='.$token.'&action=doaddprovider2&pr_id='.$_REQUEST['pr_id']);
}elseif($action=='doaddprovider2'){
	if($_REQUEST['savemyinfo']!='n') $_REQUEST['savemyinfo']='y';
	$a= processRequest($token.'/doAddProvider/'.
		encodeArgs(array($_REQUEST['pr_id'],'3',$_REQUEST['savemyinfo'])));//,array(),array(),1

	header('Location: indexpr.php?token='.$token);
}elseif($action=='addprovider'){

	$a= processRequest($token.'/getAddProviderMetaFields/'.encodeArgs(array($_REQUEST['pr_id'])));//
	$response=$a[1];
	
	?>
	<title>Add provider</title>
	Please provide access info for provider <?=$response['provider']?>.<br>
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
	<input name="action" type="hidden" value="doaddprovider">
	
	<? 
	$fields=array();
	foreach($response['fields'] as $f){ 
	$fields[]=$f['n0'];
	?>
		<?=$f['n1']?> (<?=$f['n3']?>)<input type="text" name="<?=$f['n0']?>" value="<?=$f['n2']?>"><br>
	<? } ?>
	<input name="fields" type="hidden" value="<?=join(',',$fields)?>">
	<input type="submit" value="Continue">
	</form>
	<?
	
	exit;
}elseif($action=='addprovider2'){

	?>
	<title>Add provider</title>
	Do you want to save your info?<br>
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="action" type="hidden" value="doaddprovider2">
	<input type="radio" name="savemyinfo" value="y" checked>Yes or 
	<input type="radio" name="savemyinfo" value="n">No
	<input type="submit" value="Continue">
	</form>
	<?
	
	exit;
}elseif($action=='buckets'){

	$a= processRequest($token.'/getProviderBuckets/'.encodeArgs(array($_REQUEST['pi_id'])));//,array(),array(),1
	$response=$a[1];

	?>
	<title>Manage buckets</title>
	Manage buckets. (<a href="?token=<?=$token?>">Back to providers</a>)<br>
	The list of buckets used in the SMEStorage (only checked)<br>
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="pi_id" type="hidden" value="<?=$_REQUEST['pi_id']?>">
	<input name="action" type="hidden" value="updatebuckets">
	<? foreach($response['buckets'] as $f){ ?>
		<input type="checkbox" name="buckets[]" value="<?=$f['n0']?>" <?=($f['n1']=='y'?'checked':'')?>><?=$f['n0']?><br>
	<? } ?>
	<input type="submit" value="Update">
	</form>
	<br><br>Set new default bucket<br>
	<form>
	<input name="pi_id" type="hidden" value="<?=$_REQUEST['pi_id']?>">
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="action" type="hidden" value="defaultbucket">
	<select name="defaultbucket">
		<option value="<?=$response['defaultbucket']?>"><?=$response['defaultbucket']?>
		<? foreach($response['buckets'] as $f){ 
		if($f['n1']=='y'){
		?>
		<option value="<?=$f['n0']?>"><?=$f['n0']?>
		<? } } ?>
	</select>
	<input type="submit" value="Set">
	</form>
	<br><br>Add bucket<br>
	<form>
	<input name="pi_id" type="hidden" value="<?=$_REQUEST['pi_id']?>">
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="action" type="hidden" value="addbucket">
	<input type="text" name="bucket">
	<input type="submit" value="Add">
	</form>
	<?
	
	exit;
}elseif($action=='password'){
	
	
	?>
	<title>Set new password</title>
	Set new password.<br>
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="action" type="hidden" value="dopassword">
	<input name="pi_id" type="hidden" value="<?=$_REQUEST['pi_id']?>">
	<input type="text" name="password" value=""><br>
	<input type="submit" value="Continue">
	</form>
	<?
	
	exit;
}

?>
<html>
<head>
<title>SMEStorage providers API demo</title>
</head>
<body>
<?

//show message if it is passed
if($_REQUEST['message']!='')
	echo "<h4 style=\"color:red\">".$_REQUEST['message']."</h4>";

$thisscript=basename(__FILE__);
include('menu.php');

?>
<h3>Providers</h3>
<?

//get list of file and folders
$a= processRequest($token.'/getProviders/');//,array(),array(),1
echo "You use ".$a[1]['usedcount'].' providers<br>';

echo '<table border=1>';
echo '<tr>';
echo '<th>Provider</th><th>Login</th><th>Files</th><th>Is default</th><th>Is master</th><th>Action</th>';
echo '</tr>';
foreach($a[1]['used'] as $pr){
	echo '<tr>';
	echo '<td>'.$pr['pr_name'].'</td>';
	echo '<td>'.$pr['pi_login'].'</td>';
	echo '<td>'.$pr['cf'].'</td>';
	echo '<td>'.($pr['pi_id']==$a[1]['defaultprovider']?'Yes':'').'</td>';
	echo '<td>'.($pr['pi_master']=='y'?'Yes':'No').'</td>';
	echo '<td>';
	echo '<a href="?token='.$token.'&action=sync&pi_id='.$pr['pi_id'].'&statusdata=">[synchronize]</a><br>';
	if($a[1]['usedcount']>1){
		echo '<a href="?token='.$token.'&action=remove&pi_id='.$pr['pi_id'].'">[remove]</a><br>';
	}
	if($pr['pi_id']!=$a[1]['defaultprovider']){
		echo '<a href="?token='.$token.'&action=default&pi_id='.$pr['pi_id'].'">[set default]</a><br>';
	}
	if($pr['pi_master']!='y'){
		echo '<a href="?token='.$token.'&action=master&v=y&pi_id='.$pr['pi_id'].'">[master copy]</a><br>';
	}
	if($pr['pi_master']=='y'){
		echo '<a href="?token='.$token.'&action=master&v=n&pi_id='.$pr['pi_id'].'">[slave copy]</a><br>';
	}
	if($pr['pr_code']=='s3' || $pr['pr_code']=='mosso'){
		echo '<a href="?token='.$token.'&action=buckets&pi_id='.$pr['pi_id'].'">[manage buckets]</a><br>';
	}
	if($pr['pr_code']!='s3' && $pr['pr_code']!='mosso'){
		echo '<a href="?token='.$token.'&action=password&pi_id='.$pr['pi_id'].'">[change password]</a><br>';
	}
	echo '</td>';
	echo '</tr>';
}
echo '</table>';

if($a[1]['usedcount']<$a[1]['allowedcount']){
?>
<form>
<input name="token" type="hidden" value="<?=$token?>">
<select name="pr_id">
<?
foreach($a[1]['allowed'] as $pr){
echo '<option value="'.$pr['pr_id'].'">'.$pr['pr_name']."\n";
}
?>
</select>
<input name="action" type="hidden" value="addprovider">
<input type="submit" value="Go">
</form>
<?
}



?>
</body>
</html>
