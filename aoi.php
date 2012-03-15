<?php
include_once( '_aoi_boudoir/core.function.php' );
include_once( 'aoi.config.php' );
define( 'AROOT' , dirname( __FILE__ ) . DS  );
define( '_ROOT' , AROOT . '_aoi_boudoir' . DS );

if( !isset( $argv[1] ) )
{
	// show help
	aoi_help();
}

$action  = basename(trim($argv[1]));

if( function_exists( 'aoi_' . $action ) )
{
	array_shift($argv);
	array_shift($argv);
	
	
	call_user_func_array( 'aoi_' . $action , $argv );
}
else
{
	aoi_help();
}

// ===================================
// ct
function aoi_ct()
{
	$args = func_get_args();
	return call_user_func_array('aoi_create_test' , $args );
}

function aoi_create_test()
{
	if(!is_lp_root())
	{
		aecho("ֻ����LP��Ŀ��Root����test������");
		return false;
	}
	
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$controller = z(t( $args[0] ));
	else
		$controller = want("Controller Name: ");
	
	if( !empty( $args[1] ) )
		$action = z(t( $args[1] ));
	else
		$action = t(want("Action name: "));
		
	$root = getcwd();
	
	$new_file = 0;
	
	$controller = basename( strtolower($controller) );
	$tfile = $root . DS . 'test' . DS . 'phptest' . DS . $controller . '.test.php';
	if( !file_exists( $tfile ) )
	{
		// create it
		$from  = _ROOT . 'demos' . DS . 'empty_test.class.phps';
		if( !file_exists( $from ) )
		{
			if(!copy( AOI_HOME_URL  .'empty_test.class.phps' , $from ))
			{
				aecho("���ܴ�����������ģ���ļ�����������������ʲôH�����ˣ�T__T ");
				return false;
			}
		}
		
		$content = file_get_contents( $from );
		$new_content = str_replace( '{{cname}}' , ucfirst($controller) , $content );
		file_put_contents( $tfile , $new_content );
		
		$new_file = 1;
	}
	
	$do = 0;
	
	if($funcs = list_function( $tfile ))
	{
		if( in_array( 'test_' . $action , $funcs ) )
		{
			aecho("Test Action " . 'test_' . $action . " ��Ȼ������");
			return false;
		}
		else
		{
			$do = 1;
		}
	}
	else
	{
		$do = 1;
	}
	
	if( $do == 1 )
	{
		$afile = _ROOT . 'demos' . DS . 'empty_test_action.phps';
		if( class_add_function( $tfile , $afile , $action ) )
		{
			aecho(" ��ɡ�^o^");
			if( $new_file == 1 )
			 system( AOI_EDITOR_PATH . ' ' .  $tfile );
		}
			
		else
			aecho(" ʧ���ˡ�T____T");
	}

}

function aoi_cv()
{
	$args = func_get_args();
	return call_user_func_array('aoi_create_view' , $args );
}

function aoi_create_view()
{
	global $layout;
	
	if(!is_lp_root())
	{
		aecho("ֻ����LP��Ŀ��Root����view������");
		return false;
	}
	
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$controller = z(t( $args[0] ));
	else
		$controller = want("Controller Name: ");
	
	if( !empty( $args[1] ) )
		$action = z(t( $args[1] ));
	else
		$action = t(want("Action name: "));
		
	if( !empty( $args[2] ) )
		$layoutid = z(t( $args[2] ));
	else
		$layoutid = t(want("Layout: "));
	
	$root = getcwd();
	
	if( !isset( $layout[$layoutid] ) || !is_array( $layout[$layoutid] ) )
	{
		aecho( "Ҫ�ȸ���aoi Layout�Ĳ��ֲſ���������" .AROOT. "/aoi.config.php�����°�");
		return false;
	}
	else
	{
		foreach( $layout[$layoutid] as $dir )
		{
			if( $dir == '.' )
			{
				$view_path = $root . DS . 'view' . DS . 'layout' . DS . strtolower($layoutid) . DS . strtolower($controller);
				$view_demo = _ROOT . 'demos' . DS . 'view' . DS . strtolower($layoutid) .DS .'empty_style.tpl.html'; 
			}
			else
			{
				$view_path = $root . DS . 'view' . DS . 'layout' . DS . strtolower($layoutid) . DS . strtolower( $dir ) . DS . strtolower($controller);
			
				$view_demo = _ROOT . 'demos' . DS . 'view' . strtolower($layoutid) . DS . strtolower( $dir ) . DS .'empty_style.tpl.html';
			}
			
			$view_file = $view_path . DS . strtolower($action).'.tpl.html';
			
			@mkdir( $view_path , 0777 , true );
			
			if( file_exists( $view_file ) )
			{
				aecho( 'ģ���ļ�'.$view_file.'�Ѿ���������' );
				system( AOI_EDITOR_PATH . ' ' .  $view_file );
			}
			
			if( file_exists( $view_demo ) )
				copy( $view_demo , $view_file );
			else
				file_put_contents( $view_file , '' );
			
			aecho( strtolower($controller) . '/' . strtolower($action).'��ģ�崴�����' );
			system( AOI_EDITOR_PATH . ' ' .  $view_file );
			
			
		}
	}
	
	
	
	
}

// ===================================
// ca 

function aoi_ca()
{
	$args = func_get_args();
	return call_user_func_array('aoi_create_action' , $args );
}

function aoi_create_action()
{
	if(!is_lp_root())
	{
		aecho("ֻ����LP��Ŀ��Root����Action������");
		return false;
	}
	
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$controller = z(t( $args[0] ));
	else
		$controller = want("Controller Name: ");
	
	if( !empty( $args[1] ) )
		$action = z(t( $args[1] ));
	else
		$action = t(want("Action name: "));
	

	$root = getcwd();
	$controller = basename( strtolower($controller) );
	
	$new_file = 0;
	
	$cfile = $root . DS . 'controller' . DS . $controller . '.class.php';
	if( !file_exists( $cfile ) )
	{
		// create it
		$from  = _ROOT . 'demos' . DS . 'empty_controller.class.phps';
		if( !file_exists( $from ) )
		{
			if(!copy( AOI_HOME_URL  .'empty_controller.class.phps' , $from ))
			{
				aecho("���ܴ�����������ģ���ļ�����������������ʲôH�����ˣ�T__T");
				return false;
			}
		}
		
		$content = file_get_contents( $from );
		$new_content = str_replace( '{{cname}}' , $controller , $content );
		file_put_contents( $cfile , $new_content );
		
		$new_file = 1;
	}
	
	$do = 0;
	
	if($funcs = list_function( $cfile ))
	{
		if( in_array( $action , $funcs ) )
		{
			aecho( "Action ��Ȼ������" );
			return false;
		}
		else
		{
			$do = 1;
		}
	}
	else
		$do = 1;
	
	if( $do == 1 )
	{
		$afile = _ROOT . 'demos' . DS . 'empty_action.phps';
		if( class_add_function( $cfile , $afile , $action ) )
		{
			aecho(" ��ɡ�^o^" );
			if( $new_file == 1 )
				system( AOI_EDITOR_PATH . ' ' .  $cfile );
		}
			
		else
			aecho (" ʧ���ˡ�T____T");
	
	}

}

function aoi_cat()
{
	if(!is_lp_root())
	{
		aecho ("ֻ����LP��Ŀ��Root����Action������");
		return false;
	}
	
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$controller = z(t( $args[0] ));
	else
		$controller = want("Controller Name: ");
	
	if( !empty( $args[1] ) )
		$action = z(t( $args[1] ));
	else
		$action = t(want("Action name: "));
		
	call_user_func('aoi_create_action' , $args[0] , $args[1] );	
	call_user_func('aoi_create_test' , $args[0] , $args[1] );	
}


// ==========================================
// cp 

function aoi_cp()
{
	return call_user_func_array('aoi_create_project' , func_get_args() );
}

function aoi_create_project()
{
	if(is_lp_root())
	{
		aecho("������һ����Ŀ��ߴ�����һ����ĿŶ");
		return false;
	}
	
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$app_name = z(t( $args[0] ));
	else
		$app_name = want("��Ŀ�����ƣ���Ҫ������: ");
	$path = getcwd();
	
	$app_path = $path . DS . $app_name;
	
	if( file_exists( $app_path ) )
	{
		aecho("��Ŀ��Ȼ������");
		return false;
	}
	
	$from  = _ROOT . 'demos' . DS . 'empty_project';
	if( !file_exists( $from ) )
	{
		echo "��Aoi�ļ����ش���ģ������...";
		if(!copy( AOI_HOME_URL  .'empty_project.zip' , _ROOT . 'tmp' . DS . 'empty_project.zip' ))
		{
			aecho( "���粻���ã���ֻ��è�ֶ�����·�����ˣ�..." );
			return false;
		}
		else
		{
			aecho( "��ѹ��..." );
			require_once( _ROOT . 'dUnzip2.inc.php');
			$zip = new dUnzip2(_ROOT . 'tmp' . DS . 'empty_project.zip');
			$zip->debug = false; 
			$zip->unzipAll( _ROOT . 'demos' );
			aecho( "���ش���ģ����³ɹ���\r\n���Ƶ���Ŀ����" );
			copy_r( $from , $app_path );
			aecho( "�������");
		}
	}
	else
	{
		copy_r( $from , $app_path );
		aecho( "�������" );
	}

	// system( 'explorer.exe ' . $app_path );	
	
	
}

function aoi_install_layout()
{
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$layout_name = z(t( $args[0] ));
	else
		$layout_name = want("Ҫ��װ��LayOut����[web/ajax/rest]: ");
	
	
	
}


function aoi_help()
{
	echo "Aoi��һֻ����С���� , Ҳ��LP��ܵĿ����\n���������֧ʹ������ɻ�: aoi [action] [args]\n";
	echo "- Create Project: aoi cp project_name \n";
	echo "- Create Action: aoi ca controller_name action_name \n";
	echo "- Create Test: aoi ct controller_name action_name \n";
	echo "- Create View: aoi cv controller_name action_name layout_name \n";

		

	exit;
}

function aoi_hello()
{
	echo 'hello , ' . print_r( func_get_args() , 1 );
}

function aecho( $str )
{
	echo "Aio: " . $str ."\r\n";
}

function want( $str  )
{
	echo $str ;
	return trim(fgets(STDIN));
}
