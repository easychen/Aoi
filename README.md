Aoi是一只傲娇小萝莉 , 也是LP框架的看板娘。
你可以这样支使她帮你干活: aoi [action] [args]

<pre>
- Create Project: aoi cp project_name 
- Create Action: aoi ca controller_name action_name 
- Create Test: aoi ct controller_name action_name 
- Create View: aoi cv controller_name action_name layout_name
</pre>

# 安装
Aoi使用命令行和主人交谈。为了保证能在任何目录召唤出Aoi，你需要进行简单的配置。

## Windows环境
为Aoi找好一个非临时的目录，以防你不小心把她推倒到回收站。假设该目录为E:\aoi

打开【我的电脑】--右键-->【属性】---【高级】Tab--->【环境变量】-->【系统变量】，选择【PATH】项，然后点【编辑】。在原有内容最后加上【;E:\aoi】，保存。
然后你就可以任意目录使用aoi命令召唤Aoi了。

## Mac环境
为Aoi找好一个非临时的目录，以防你不小心把她推倒到回收站。假设该目录为\Users\easychen\Aoi

在Terminal下运行 
<pre>
echo "export PATH=/Users/easychen/Aoi:$PATH" >> ~/.bash_profile
</pre>

然后你就可以任意目录使用aoi命令召唤Aoi了。

# 配置编辑器
打开Aoi目录下的aoi.config.php
将第一行换成你喜欢的编辑器
<pre>
// Mac
define('AOI_EDITOR_PATH' , 'open -a 已经在Mac下安装的编辑器名称');
// Win
define('AOI_EDITOR_PATH' , 'E:\\notepad++.exe');
</pre>

# 配置代码模板
Aoi在创建项目、Action和测试的时候，都会使用到代码模板。这些代码模板放在Aoi目录下的【_aoi_boudoir/demos】下。

默认情况下，Aoi在创建项目时不会创建【_lp】目录下的LP3核心代码。而是通过PHP的include_path去加载它。这样可以多个项目共用一份【_lp】目录，更新和管理更方便。

如果你希望每个项目使用自己的【_lp】目录，只需要将LP3的【_lp】目录copy到【Aoi/_aoi_boudoir/demos/empty_project】下就OK了。

