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
		aecho("只能在LP项目的Root创建test啦主人");
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
				aecho("不能从网络上下载模板文件，主人你又在下载什么H动画了？T__T ");
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
			aecho("Test Action " . 'test_' . $action . " 已然存在了");
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
			aecho(" 完成。^o^");
			if( $new_file == 1 )
			 system( AOI_EDITOR_PATH . ' ' .  $tfile );
		}
			
		else
			aecho(" 失败了。T____T");
	}

}

function aoi_if()
{
	$args = func_get_args();
	return call_user_func_array('aoi_import_function' , $args );
}

function aoi_import_function()
{
	if(!is_lp_root())
	{
		aecho("只能在LP项目的Root导入函数啦主人");
		return false;
	}
	
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$func = z(t( $args[0] ));
	else
		$func = want("Remote function name: ");
	
	$root = getcwd();
	$afile = $root . DS . 'lib' . DS .'app.function.php';
	
	if(!$code = file_get_contents( AOI_FUN_URL . '?a=raw&func=' . u($func) ))
		return aecho("没有查询到可用的函数");
	elseif( file_exists( $afile ) )
		if(file_add_function( $afile , $code , $func ))
			return aecho("函数追加成功");
		else
			return aecho("函数追加失败");
	else
		return aecho( $afile .' 不存在' );
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
		aecho("只能在LP项目的Root创建view啦主人");
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
		aecho( "要先告诉aoi Layout的布局才可以啦，打开" .AROOT. "/aoi.config.php配置下吧");
		return false;
	}
	else
	{
		foreach( $layout[$layoutid] as $dir )
		{
			if( $dir == '.' )
			{
				$view_path = $root . DS . 'view' . DS . 'layout' . DS . strtolower($layoutid) . DS . strtolower($controller);
				$view_demo = _ROOT . 'demos' . DS . 'view' . DS . strtolower($layoutid) .DS .'empty_sharp.tpl.html'; 
			}
			else
			{
				$view_path = $root . DS . 'view' . DS . 'layout' . DS . strtolower($layoutid) . DS . strtolower( $dir ) . DS . strtolower($controller);
			
				$view_demo = _ROOT . 'demos' . DS . 'view' . strtolower($layoutid) . DS . strtolower( $dir ) . DS .'empty_sharp.tpl.html';
			}
			
			$view_file = $view_path . DS . strtolower($action).'.tpl.html';
			
			@mkdir( $view_path , 0777 , true );
			
			if( file_exists( $view_file ) )
			{
				aecho( '模板文件'.$view_file.'已经存在啦。' );
				system( AOI_EDITOR_PATH . ' ' .  $view_file );
			}
			else
			{
				if( file_exists( $view_demo ) )
					copy( $view_demo , $view_file );
				else
					file_put_contents( $view_file , '' );
				
				aecho( strtolower($controller) . '/' . strtolower($action).'的模板创建完成' );
				system( AOI_EDITOR_PATH . ' ' .  $view_file );
			}
			
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
		aecho("只能在LP项目的Root创建Action啦主人");
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
				aecho("不能从网络上下载模板文件，主人你又在下载什么H动画了？T__T");
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
			aecho( "Action 已然存在了" );
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
			aecho(" 完成。^o^" );
			if( $new_file == 1 )
				system( AOI_EDITOR_PATH . ' ' .  $cfile );
		}
			
		else
			aecho (" 失败了。T____T");
	
	}

}

function aoi_cat()
{
	if(!is_lp_root())
	{
		aecho ("只能在LP项目的Root创建Action啦主人");
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
		aecho("不能在一个项目里边创建另一个项目哦");
		return false;
	}
	
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$app_name = z(t( $args[0] ));
	else
		$app_name = want("项目的名称，不要用中文: ");
	$path = getcwd();
	
	$app_path = $path . DS . $app_name;
	
	if( file_exists( $app_path ) )
	{
		aecho("项目已然存在啦");
		return false;
	}
	
	$from  = _ROOT . 'demos' . DS . 'empty_project';
	if( !file_exists( $from ) )
	{
		echo "从Aoi的家下载代码模板数据...";
		if(!copy( AOI_HOME_URL  .'empty_project.zip' , _ROOT . 'tmp' . DS . 'empty_project.zip' ))
		{
			aecho( "网络不好用，那只花猫又蹲无线路由上了？..." );
			return false;
		}
		else
		{
			aecho( "解压中..." );
			require_once( _ROOT . 'dUnzip2.inc.php');
			$zip = new dUnzip2(_ROOT . 'tmp' . DS . 'empty_project.zip');
			$zip->debug = false; 
			$zip->unzipAll( _ROOT . 'demos' );
			aecho( "本地代码模板更新成功。\r\n复制到项目……" );
			copy_r( $from , $app_path );
			aecho( "完成啦。");
		}
	}
	else
	{
		copy_r( $from , $app_path );
		aecho( "完成啦。" );
	}

	// system( 'explorer.exe ' . $app_path );	
	
	
}

function aoi_install_layout()
{
	$args = func_get_args();
	
	if( !empty( $args[0] ) )
		$layout_name = z(t( $args[0] ));
	else
		$layout_name = want("要安装的LayOut名称[web/ajax/rest]: ");
	
	
	
}


function aoi_help()
{
	echo "Aoi是一只傲娇小萝莉 , 也是LP框架的看板娘。\n你可以这样支使她帮你干活: aoi [action] [args]\n";
	echo "- Create Project: aoi cp project_name \n";
	echo "- Create Action: aoi ca controller_name action_name \n";
	echo "- Create Test: aoi ct controller_name action_name \n";
	echo "- Create View: aoi cv controller_name action_name layout_name \n";
	echo "- Import remote function: aoi if function_name \n";

		

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
