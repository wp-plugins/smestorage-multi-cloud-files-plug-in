<?
// SMEStorage Multi Cloud API Upload example
// www.smestorage.com
include('lib.php');

if($action=='prepare' || $action==''){
	?>
	Init uploader...
	<form name="workform" id="workform" action="index.php" method="POST">
	<input type="hidden" name="action" value="initupload">
	<input type="hidden" name="fid" value="">
	<input type="hidden" name="saveornot" value="<?=$_REQUEST['saveornot']?>">
	<input type="hidden" name="token" value="<?=$token?>">
	<input type="hidden" name="name">
	<input type="hidden" name="description">
	<input type="hidden" name="tags">
	<input type="hidden" name="filename">
	<input type="hidden" name="encryptphrase">
	</form>
	<script language="javascript">
	document.workform.fid.value=window.opener.document.getElementById('fidcommon<? if($_REQUEST['saveornot']=='y') echo '2';?>').value;
	<?
	if($_REQUEST['saveornot']!='y'){
	?>
	document.workform.name.value=window.opener.document.getElementById('filenamecommon').value;
	document.workform.description.value=window.opener.document.getElementById('filedescriptioncommon').value;
	document.workform.tags.value=window.opener.document.getElementById('filetagscommon').value;
	<?
	}
	?>
	document.workform.encryptphrase.value=window.opener.document.getElementById('fileencphrase').value;

	var filename=window.opener.document.getElementById('filefilecommon<? if($_REQUEST['saveornot']=='y') echo '2';?>').value;
	if(filename.indexOf('\\')>-1 || filename.indexOf('/')>-1){
		var bs='\\';
		if(filename.indexOf('\\')<filename.indexOf('/'))
			bs='/';
		filename=filename.substring(filename.lastIndexOf(bs)+1);
	}
	document.workform.filename.value=filename;
	document.workform.submit();
	</script>
	<?
	exit;
}elseif($action=='start'){
	?>
	Processing. Please wait.<br>
	<iframe name="uplframe" width="300" height="300" ></iframe><!-- style="display:none;" -->
	<table><tr><td><div name="proc" id="proc">0</div></td><td>%</td></tr></table>
	 <br>
	<form name="workform" id="workform" action="index.php" method="POST">
	<input type="hidden" name="action" value="finishupload">
	<input type="hidden" name="saveornot" value="<?=$_REQUEST['saveornot']?>">
	<input type="hidden" name="token" value="<?=$token?>">
	<input type="hidden" name="code" value="<?=$_REQUEST['code']?>">
	</form>
	<!-- <input type="button" onclick="window.opener.document.fnewuploadfile<? if($_REQUEST['saveornot']=='y') echo '2';?>.submit();timeInterval=setInterval('traceUpload()', 10000);" value="Start">--><input type="button" onclick="finished()" value="Finished">
	<script language="javascript">
	
	function finished(){
		clearInterval(timeInterval);
		redrawbar('100');
		alert('finished');
		document.workform.submit();
	}
	function redrawbar(proc){
		document.getElementById('proc').innerHTML=proc;
	}

//Create AJAX object. 
//I used separate AJAX object becouse of paralel requests and AIM is used in this time.
var xmlHttp = false;
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
try {
  xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
  try {
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (e2) {
    xmlHttp = false;
  }
}
@end @*/
if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
  xmlHttp = new XMLHttpRequest();
}
	var uploader="";
	var uploadDir="";
	var timeInterval="";
	var inprogres=0;
	var idname="";


	function traceUpload() {
		if(inprogres==1) return;
		var url = 'index.php?token=<?=$token?>&action=getstatus&code=<?=$_REQUEST['code']?>&rand='+ Math.random();
		
		xmlHttp.open("GET", url, true);
		xmlHttp.onreadystatechange = updateUploadingInfo;
		inprogres=1;
		xmlHttp.send(null);

	}
	function updateUploadingInfo() {
	if(xmlHttp.readyState == 4){
		var response=xmlHttp.responseText; 
		if(response== '100'){
			clearInterval(timeInterval);
		}
		redrawbar(response);
		inprogres=0;
	}
	}

	window.opener.document.fnewuploadfile<? if($_REQUEST['saveornot']=='y') echo '2';?>.action='<?=_UPLOADER_URL?>?<?=$_REQUEST['code']?>';
	window.opener.document.fnewuploadfile<? if($_REQUEST['saveornot']=='y') echo '2';?>.target='uplframe';
	window.opener.document.fnewuploadfile.submit();
	timeInterval=setInterval("traceUpload()", 10000);

	</script>
	<?
	
}elseif($action=='end'){
	?>
	Completed.<br>
	<input type="button" onclick="window.close();window.opener.document.location.reload();" value="Close">
	<?
}

?>