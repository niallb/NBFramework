<?php

function ajaxLink($text, $action, $params=false, $function='ajaxLinkClick')
{
    global $SITEROOT;
    if(substr($action, 0, 1)=='/')
    {
        $action = $SITEROOT.substr($action,1);
    }
    if($params)
    {
        $pr = '?';
        if(is_array($params))
        {
            foreach($params as $k=>$v)
            {
                $pr.= $k.'='.urlencode($v).'&';
            }
            $pr = rtrim($pr, '&');
        }
        else
        {
            $pr .= $params;
        }
    }
    else
    {
        $pr = '';
    }
    return "<span class='ajaxLink' onClick='{$function}(\"{$action}{$pr}\")'>{$text}</span>";
}
