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
	Login:<input name="login" type="text"><br>
	Password:<input name="password" type="password"><br>
	<input type="submit">
	</form>
	<?
	//exit;
}elseif($token=='' && $_REQUEST['login']!=''){
	//process login and get token
	$a= processRequest('*/gettoken/'.encodeArgs(array($_REQUEST['login'],$_REQUEST['password'])));//,array(),array(),1
	
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
			//exit;
		}
		//if login success redirect with token

			//include('home.php?page=sme_navigation&token='.$a[1]['token'].'&message=Last+visit:+'.$a[1]['lastlogin']);	
			header('Location: index.php?token='.$a[1]['token'].'&message=Last+visit:+'.$a[1]['lastlogin']);	
		//exit;
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
	header('Location: index.php?token='.$token.'&message='.$a[1]['statusmessage']);
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

?>