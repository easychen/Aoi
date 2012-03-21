<?php
define( 'AOI_HOME_URL' , 'http://aoihome.sinaapp.com/zip/' );
define( 'AOI_FUN_URL' , 'http://aoihome.sinaapp.com/fun/' );


function list_function( $file )
{
	if(!$content = file_get_contents( $file )) return false;
	$reg = '/function\s+([a-z]{1}.+?)\(([a-z0-9,$_ ="\']*)\)\s*\{(.+?)\}/is';
	if( preg_match_all( $reg , $content , $out ) )
	{
		return $out[1];
		// $out[0] --> match
		// $out[1] --> function name
		// $out[2] --> function args
		// $out[3] --> function code
		
	}
	return false;
}

function file_add_function( $filename , $code , $func )
{
	if( $flist = list_function( $filename ) )
	{
		if( in_array( $func , $flist ) )
		{
			aecho("function exist already");
			return false;
		}
	}
	
	$content = file_get_contents( $filename );
	$reg = '/<\?php\s*(.+?)$/is';
	
	if( preg_match( $reg , $content , $out ) )
	{
		$code_old = $out[1];
		
		$new_code = "<?php \r\n// From aoihome.sinaapp.com/fun via Aoi [" . $func . "]\r\n". $code . "\r\n\r\n" . $code_old;
		
		return file_put_contents( $filename , $new_code );
		
	}
	
}

function class_add_function( $cfile , $afile , $action )
{
	if(!$content = file_get_contents( $cfile )) return false;
	$reg = '/class.+?\{((\s*).+)\}/is';
	if( preg_match( $reg , $content , $out ) )
	{
		$code = $out[1];
		$ts = strlen($out[2]) - 2;
		if( $ts < 1 ) $ts = 1;
		
		$new_code = $code."\r\n";
		
		if($acodes = file( $afile ))
		{
			foreach( $acodes as $line )
			{
				$line = str_replace( "{{aname}}" , $action , $line );
				$new_code .= str_repeat( "\t" , $ts ) . $line ; 
			}
			
			$new_code .= "\r\n";
		}
		
		$new_class = str_replace( $code , $new_code , $content );
		file_put_contents( $cfile , $new_class );
	}
	return true;
}




function is_lp_root( $path = null )
{
	if( $path == null ) $path = getcwd();
	
	
	return  file_exists( $path . DS . 'index.php' ) &&  ( clean_rn(read_part( $path . DS . 'index.php' , 24 )) == "<?php/* lp app root */"  ) ;
}

function clean_rn( $str )
{
	return str_replace( array("\r" , "\n") , '' , $str );
}

function read_part( $file , $len )
{
	$handle = fopen($file, "r");
	$contents = fread($handle, $len );
	fclose($handle);
	return $contents;
}

function v( $str )
{
	return isset( $_REQUEST[$str] ) ? $_REQUEST[$str] : false;
}

function z( $str )
{
	return strip_tags( $str );
}


function g( $str )
{
	return isset( $GLOBALS[$str] ) ? $GLOBALS[$str] : false;	
}

function t( $str )
{
	return trim($str);
}

function u( $str )
{
	return urlencode( $str );
}

function copy_r( $path, $dest )
{
	if( is_dir($path) )
	{
		@mkdir( $dest );
		$objects = scandir($path);
		if( sizeof($objects) > 0 )
		{
			foreach( $objects as $file )
			{
				if( $file == "." || $file == ".." )
					continue;
				// go on
				if( is_dir( $path.DS.$file ) )
				{
					copy_r( $path.DS.$file, $dest.DS.$file );
				}
				else
				{
					copy( $path.DS.$file, $dest.DS.$file );
				}
			}
		}
		return true;
	}
	elseif( is_file($path) )
	{
		return copy($path, $dest);
	}
	else
	{
		return false;
	}
}	