<?php

function checkOIDCLogin()
{
    global $CFG;
    if(isset($_REQUEST['state']))
    {
        list($tm, $myState, $sig) = explode(" ", $_REQUEST['state']);
        if(base64_encode(md5("$tm $myState ".$CFG['salt']))==$sig)
        {
            $state = decodeState($myState);
            $id_token = $_REQUEST['id_token'];
            $segs = explode(".",$id_token);
            $hdr = json_decode(base64_decode($segs[0]));
            $id = json_decode(base64_decode($segs[1]));

            $loginInfo = array('id' => sha1($id->oid), 'time'=>$tm, 'state'=>$state, 'extras'=>array());

            foreach($id as $key=>$val)
            {
                switch($key)
                {
                    case 'name':
                        $loginInfo['name'] = $id->name;
                        break;
                    case 'email':
                        $loginInfo['email'] = $id->email;
                        break;
                    default:
                        $loginInfo['extras'][$key] = $val;
                        break;
                }
            }

            //echo 'ID<pre>'.print_r($id,1).'</pre>';
            //echo 'State<pre>'.print_r($state,1).'</pre>';
            //echo 'hdr<pre>'.print_r($hdr,1).'</pre>';
            return $loginInfo;
        }
        else
            return false;
    }
    else
    {
        if(isset($_SERVER['HTTP_REFERER']))
            $stateStr = 'referer='.urlencode($_SERVER['HTTP_REFERER']);
        else
            $stateStr = 'referer=false';
        foreach($_REQUEST as $k=>$v)
        {
            $stateStr .= "&$k=".urlencode($v);
        }
        $loginurl = buildMSOAuth2Request($stateStr);
        header("Location: $loginurl");
        echo "<a href='$loginurl'>Login with Microsoft</a>";
        exit();
    }
}


function buildMSOAuth2Request($state = false)
{
    global $CFG;
    $request = $CFG['oidc_host'];

    $request .= "?client_id=" . $CFG['oidc_id'];
    $request .= "&tenant=organizations";
    $request .= "&response_type=id_token";
    $request .= "&scope=" . urlencode("openid email profile");
    $request .= "&redirect_uri=" . urlencode($CFG['oidc_redirect']);  // This has to be one registered with Microsoft
    $request .= "&response_mode=form_post";
    $request .= "&domain_hint=glasgow.ac.uk";
    $tm = time();
    if($state == false)
        $state = "$tm {$_SERVER['REQUEST_URI']} ";
    else
        $state = "$tm $state ";
    $request .= "&state=" . urlencode($state . base64_encode(md5($state.$CFG['salt'])));
    $request .= "&nonce=".time();
    return $request;
}


function decodeState($stateStr)
{
    $stateParts = explode('&', $stateStr);
    $state = array();
    foreach($stateParts as $part)
    {
        list($name, $value) = explode ('=', $part, 2);
        $state[$name] = urldecode($value);
    }
    return $state;
}