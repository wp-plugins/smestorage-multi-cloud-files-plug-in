<?

include('lib.php');

$task=$_REQUEST['task'];

if($action=='login'){
	$data=array();
	foreach(split(',',$_REQUEST['fields']) as $f)
		$data[]=$f.'='.$_REQUEST[$f];
	$data=join("\n",$data);

	$a= processRequest($token.'/doImpExpLogin/'.
		encodeArgs(array($_REQUEST['pr_id'],$data)));



	if($a[1]['status']=='ok'){

		header('Location: indexie.php?token='.$token.'&step=2&pr_id='.$_REQUEST['pr_id'].'&task='.$task);
		exit;
	}

	header('Location: indexie.php?token='.$token.'&step=1&pr_id='.$_REQUEST['pr_id'].'&message='.$a[1]['statusmessage'].'&task='.$task);
	exit;
}elseif($action=='setexpdest'){

	$a= processRequest($token.'/doExpChoseDest/'.encodeArgs(array($_REQUEST['pr_id'],'1',$_REQUEST['folder'])));

	header('Location: indexie.php?token='.$token.'&step=3&task='.$task.'&pr_id='.$_REQUEST['pr_id']);
	exit;
}elseif($action=='setexpsource'){
	$folders=array();
	if(is_array($_REQUEST['folders']))
		$folders=$_REQUEST['folders'];
	if(count($folders)==0){
		header('Location: indexie.php?token='.$token.'&step=3&task='.$task.'&pr_id='.$_REQUEST['pr_id'].'&message=Select+folder(s)');
		exit;
	}
		
	$a= processRequest($token.'/doExpChoseSource/'.encodeArgs(array($_REQUEST['pr_id'],'1',join(',',$folders))));

	header('Location: indexie.php?token='.$token.'&action=export&task='.$task.'&pr_id='.$_REQUEST['pr_id']);
	exit;
}elseif($action=='export'){

	$a= processRequest($token.'/doExport/'.encodeArgs(array(
		$_REQUEST['pr_id'],
		$_REQUEST['statusdata'])));//
//print_r($a);
	if($a[1]['exported']=='') $a[1]['exported']='0';
	if($a[1]['totalfiles']=='') $a[1]['totalfiles']='0';
	if($a[1]['procent']=='') $a[1]['procent']='0';
	$a[1]['iscompleted']=strval(intval($a[1]['iscompleted']));

	if($a[1]['iscompleted']=='0' && $a[1]['statusdata']!=''){
		?>
		<title>Export</title>
		<br>Exported <?=$a[1]['exported']?> files from (<?=$a[1]['totalfiles']?>) (<?=$a[1]['procent']?>%) <br>
		<?=$a[1]['expmessage']?><br>
		<form name='myform'>
			<input name="token" type="hidden" value="<?=$token?>">
			<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
			<input name="statusdata" type="hidden" value="<?=$a[1]['statusdata']?>">
			<input name="action" type="hidden" value="export">
			<input type="submit" value="Continue">
			</form>
			<script  language="JavaScript" type="text/javascript" >

			setTimeout("document.myform.submit();", 3000);

			</script>
		<?
		exit;
	}else{
		header('Location: indexie.php?token='.$token.'&message='.urlencode('Exported '.$a[1]['exported'].' files'));
		exit;
	}
	
}elseif($action=='setimpsource'){

	$folders=array();
	if(is_array($_REQUEST['folders']))
		$folders=$_REQUEST['folders'];
	if(count($folders)==0){
		header('Location: indexie.php?token='.$token.'&step=3&task='.$task.'&pr_id='.$_REQUEST['pr_id'].'&message=Select+folder(s)');
		exit;
	}

	$a= processRequest($token.'/doImpChoseSource/'.encodeArgs(array($_REQUEST['pr_id'],'1',join(',',$folders))));
	//,array(),array(),1

	header('Location: indexie.php?token='.$token.'&step=3&task='.$task.'&pr_id='.$_REQUEST['pr_id']);
	exit;
}elseif($action=='setimpdest'){

	$a= processRequest($token.'/doImpChoseDest/'.encodeArgs(array($_REQUEST['pr_id'],$_REQUEST['folder'])));//,array(),array(),1

	header('Location: indexie.php?token='.$token.'&action=import&task='.$task.'&pr_id='.$_REQUEST['pr_id']);
	exit;
}elseif($action=='import'){

	$a= processRequest($token.'/doImport/'.encodeArgs(array(
		$_REQUEST['pr_id'],
		$_REQUEST['statusdata'])));//,array(),array(),1
//print_r($a);
	if($a[1]['imported']=='') $a[1]['imported']='0';
	if($a[1]['totalfiles']=='') $a[1]['totalfiles']='0';
	if($a[1]['procent']=='') $a[1]['procent']='0';
	$a[1]['iscompleted']=strval(intval($a[1]['iscompleted']));

	if($a[1]['iscompleted']=='0' && $a[1]['statusdata']!=''){
		?>
		<title>Import</title>
		<br>Imported <?=$a[1]['imported']?> files from (<?=$a[1]['totalfiles']?>) (<?=$a[1]['procent']?>%) <br>
		<?=$a[1]['impmessage']?><br>
		<form name='myform'>
			<input name="token" type="hidden" value="<?=$token?>">
			<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
			<input name="statusdata" type="hidden" value="<?=$a[1]['statusdata']?>">
			<input name="action" type="hidden" value="import">
			<input type="submit" value="Continue">
			</form>
			<script  language="JavaScript" type="text/javascript" >

			setTimeout("document.myform.submit();", 3000);

			</script>
		<?
		exit;
	}else
	header('Location: indexie.php?token='.$token.'&message='.urlencode('Imported '.$a[1]['imported'].' files'));
	exit;
}

?>
<html>
<head>
<title>SMEStorage Import/Export API demo</title>
</head>
<body>
<?

//show message if it is passed
if($_REQUEST['message']!='')
	echo "<h4 style=\"color:red\">".$_REQUEST['message']."</h4>";

$step=strval(intval($_REQUEST['step']));


if($step=='0'){
	$a= processRequest($token.'/getImpExpProviders/');
	//show the list of providers

$thisscript=basename(__FILE__);
include('menu.php');
	
?>



Export to <br>
<form >
<input type="hidden" name="token" value="<?=$token?>">
<input type="hidden" name="step" value="1">
<input type="hidden" name="task" value="export">
<select name="pr_id">
<?
foreach($a[1]['export'] as $provider){
	echo "<option value=\"".$provider['pr_id']."\">".$provider['pr_name'];
}
?>
</select> <input type="submit" value="Go">
</form><br>
Import from <br>
<form >
<input type="hidden" name="token" value="<?=$token?>">
<input type="hidden" name="step" value="1">
<input type="hidden" name="task" value="import">
<select name="pr_id">
<?
foreach($a[1]['import'] as $provider){
	echo "<option value=\"".$provider['pr_id']."\">".$provider['pr_name'];
}
?>
</select> <input type="submit" value="Go">
</form>
<?
}elseif($step=='1'){
	$a= processRequest($token.'/getAddProviderMetaFields/'.encodeArgs(array($_REQUEST['pr_id'])));
	$response=$a[1];

	?>
	Please provide access info for provider <?=$response['provider']?>.<br>
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
	<input name="action" type="hidden" value="login">
	<input type="hidden" name="task" value="<?=$task?>">

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
}elseif($step=='2' && $task=='import'){
	//get list of folders
	$a= processRequest($token.'/doImpChoseSource/'.encodeArgs(array($_REQUEST['pr_id'],'0')));
	$response=$a[1];
	if($response['skip']=='1'){
		?>
		<form name='myform'>
		<input name="token" type="hidden" value="<?=$token?>">
		<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
		<input name="step" type="hidden" value="3">
		<input name="task" type="hidden" value="<?=$task?>">
		</form>
		<script  language="JavaScript" type="text/javascript" >
		setTimeout("document.myform.submit();", 1000);
		</script>
		<?
		exit;
	}
	?>
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
	<input name="action" type="hidden" value="setimpsource">
	<input type="hidden" name="task" value="<?=$task?>">
	<?
	foreach($response['folders'] as $f){
		if($f['level']=='') $f['level']='0';
		
		if($f['level']>0) for ($i=1;$i<=$f['level'];$i++) echo '&nbsp;';
		if($f['id']=='') $f['id']='0';
			echo '<input type="checkbox" name="folders[]" value="'.$f['id'].'">'.$f['name'].'<br>';
	}
	?>
	<input type="submit" value="Continue">
	</form>
	<?
}elseif($step=='2' && $task=='export'){

	//get list of folders
	$a= processRequest($token.'/doExpChoseDest/'.encodeArgs(array($_REQUEST['pr_id'],'0')));
	$response=$a[1];
	if($response['skip']=='1'){
		?>
		<form name='myform'>
		<input name="token" type="hidden" value="<?=$token?>">
		<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
		<input name="step" type="hidden" value="3">
		<input name="task" type="hidden" value="<?=$task?>">
		</form>
		<script  language="JavaScript" type="text/javascript" >
		setTimeout("document.myform.submit();", 1000);
		</script>
		<?
		exit;
	}
	?>
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
	<input name="action" type="hidden" value="setexpdest">
	<input type="hidden" name="task" value="<?=$task?>">
	<?
	foreach($response['folders'] as $f){
		if($f['level']=='') $f['level']='0';
		
		if($f['level']>0) for ($i=1;$i<=$f['level'];$i++) echo '&nbsp;';
		if($f['id']=='') $f['id']='0';
			echo '<input type="radio" name="folder" value="'.$f['id'].'">'.$f['name'].'<br>';
	}
	?>
	<input type="submit" value="Continue">
	</form>
	<?
	
}elseif($step=='3' && $task=='import'){
	//show list of folders
	$a= processRequest($token.'/getFoldersTree/');//,array(),array(),1
	$response=$a[1];
	?>
	Chose folders to import in
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
	<input name="action" type="hidden" value="setimpdest">
	<?
	foreach($response['folders'] as $f){
		if($f['level']=='') $f['level']='0';

		if($f['level']>0) {
			for ($i=1;$i<=$f['level'];$i++) echo '&nbsp;';
			echo '|_';
		}
			echo '<input type="radio" name="folder" value="'.$f['fi_id'].'">'.$f['fi_name'].'<br>';
	}
	?>
	<input type="submit" value="Continue">
	</form>
	<?

}elseif($step=='3' && $task=='export'){
	//show list of folders
	$a= processRequest($token.'/getFoldersTree/');
	$response=$a[1];
	?>
	Chose folders to export
	<form>
	<input name="token" type="hidden" value="<?=$token?>">
	<input name="pr_id" type="hidden" value="<?=$_REQUEST['pr_id']?>">
	<input name="action" type="hidden" value="setexpsource">
	<?
	foreach($response['folders'] as $f){
		if($f['level']=='') $f['level']='0';
		
		if($f['level']>0) {
			for ($i=1;$i<=$f['level'];$i++) echo '&nbsp;';
			echo '|_';
		}
			echo '<input type="checkbox" name="folders[]" value="'.$f['fi_id'].'">'.$f['fi_name'].'<br>';
	}
	?>
	<input type="submit" value="Continue">
	</form>
	<?
	
}

?>
</body>
</html>
