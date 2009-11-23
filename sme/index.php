<?
session_start();
$u = $_GET['u'];
$p= $_GET['p'];

if(!file_exists($_GET['path']))
    die('Backup file not found');

include('lib.php');

$a= processRequest('*/gettoken/'.encodeArgs(array($u,$p)));

$token='';

if($a[0]==''){
	$token=$a[1]['token'];

}else
	die('Wrong smestorage login');

$folderid='0';
//create folder
$a= processRequest($token.'/doCreateNewFolder/'.encodeArgs(array(
		'My WordPress backup',
		'',
		'0')));

$folderid=$a[1]['file']['fi_id'];

$argname='fi_pid';
if($_REQUEST['saveornot']=='y') $argname='fi_id';
$rand = rand(1,100);
$month = date("m");
$year = date("Y");
$day = date("d");
$sum = $year.$month.$day;

$data=array(
	$argname=>$folderid,
	//$_REQUEST['fid']		
	'file_name1'=>"wordpress-backup-".$sum,
	'file_desc1'=>"Wordpress Backup",
	'file_tags1'=>"wordpress, backup",
);
	
$files=array('file_1'=>$_GET['path']);
	
$a= processRequest($token.'/doUploadFiles/',0,$data,$files);

//print_r($a);
if(file_exists($_GET['path']))
  unlink($_GET['path']);

?>