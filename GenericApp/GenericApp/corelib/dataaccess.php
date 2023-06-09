<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

class dataConnection
{
    private static $dbconn;
    private static $dblink=null;
    const db = 'MySQL';

    public static function connect()
    {
        global $CFG;
        //# Move this to config soon!
        $host=$CFG['db_host']; // Host name
        $username=$CFG['db_username']; // Mysql username
        $password=$CFG['db_password']; // Mysql password
        $db_name=$CFG['db_name']; // Database name
        self::$dblink = mysqli_connect("$host", "$username", "$password", $db_name)or die("Cannot connect to database");
    }

    public static function runQuery($query)
    {
        if(self::$dblink==null)
            dataConnection::connect();
        $result = mysqli_query(self::$dblink, $query);
        if (!$result)
        {
            $message  = 'Invalid query: ' . mysqli_error(self::$dblink) . "\n";
            $message .= 'Whole query: ' . $query;
            die($message);
        }
        if($result===true)
            $output = true;
        else
        {
            $output = array();
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
               $output[] = $row;
            }
        }
        return $output;
    }

    public static function close()
    {
        if(self::$dblink!=null)
            mysqli_close(self::$dblink);
        self::$dblink = null;
    }

    public static function safe($in)
    {
		if (self::$dblink==NULL)
    	{
	    	dataConnection::connect();
		}
	  	return mysqli_real_escape_string(self::$dblink, $in);
	}

	public static function db2date($in)
	{
	    list($y,$m,$d) = explode("-",$in);
	    return mktime(0,0,0,$m,$d,$y);
	}

	public static function date2db($in)
	{
	    return strftime("%Y-%m-%d", $in);
	}

	public static function db2time($in)
	{
        if(strlen($in)==0)
            return 0;
	    list($dt, $ti) = explode(" ",$in);
	    list($y,$m,$d) = explode("-",$dt);
	    list($hh,$mm,$ss) = explode(":",$ti);
	    return mktime($hh,$mm,$ss,$m,$d,$y);
	}

	public static function time2db($in)
	{
	    return strftime("%Y-%m-%d %H:%M:%S", $in);
	}

};




?>
