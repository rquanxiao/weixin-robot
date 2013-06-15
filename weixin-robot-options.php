<?php 
function weixin_robot_about_page() {
	?>
	<div class="wrap" style="width:600px;">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2>微信机器人高级版</h2>

		<p>如果你需要一些更强大的功能，你可以尝试下：<a href="http://wpjam.net/item/weixin-robot-advanced/">微信机器人高级版</a>，相对基础版，高级版本让你你设置更加容易，可以在后台设置 微信 Token 等，还有一些更加强大的功能，比如输入 n 返回最新文章，输入 r 返回随机文章等等，并且这些关键字都可以自定义。高级版还支持自定义文本或者图文回复。以及详细的数据统计等功能，并且高级版。</p>

		<p><img src="http://wpjamnet.qiniudn.com/wp-content/uploads/2013/04/wx-custom-replies.png" alt="微信高级版-自定义回复" /></p>

		<p>高级版是收费的，猛击这里查看和购买<a href="http://wpjam.net/item/weixin-robot-advanced/">微信机器人高级版</a>。</p>

		<h2>定制功能更强大的微信机器人</h2>

		<p>如果高级版还不能满足你你的需求，或者你的需求比较奇怪或者希望有更强大微信机器人，寻找快速的主机和优化你的 WordPress 来运行你的微信机器人，</p>
		<p><strong style="color:red;">请联系 Denis，QQ：11497107。</strong></p>
	</div>
	<?php
}


function weixin_robot_basic_setting_page() {
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2>基本设置</h2>
		<form action="options.php" method="POST">
			<?php settings_fields( 'weixin-robot-basic-group' ); ?>
			<?php do_settings_sections( 'weixin-robot-basic' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function weixin_robot_advanced_setting_page() {
	?>
	<div class="wrap">
		<div id="icon-weixin-robot" class="icon32"><br></div>
		<h2>高级设置</h2>
		<form action="options.php" method="POST">
			<?php settings_fields( 'weixin-robot-advanced-group' ); ?>
			<?php do_settings_sections( 'weixin-robot-advanced' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function weixin_robot_get_default_basic_option(){
	
 return array(
		'weixin_token'					=> 'weixin',
		'weixin_default'				=> '',
		'weixin_keyword_allow_length'	=> '16',
		'weixin_count'					=> '5',
		'weixin_welcome'				=> "请输入关键字开始搜索！",
		'weixin_keyword_too_long'		=> '你输入的关键字太长了，系统没法处理了，请等待公众账号管理员到微信后台回复你吧。',
		'weixin_not_found'				=> '抱歉，没有找到与[keyword]相关的文章，要不你更换一下关键字，可能就有结果了哦 :-)',
		'weixin_voice'					=> '系统暂时还不支持语音回复，直接发送文本来搜索吧。\n获取更多帮助信息请输入：h。',
	);
}

function weixin_robot_get_basic_option(){
	$weixin_robot_basic = get_option( 'weixin-robot-basic' );

	if(!$weixin_robot_basic){
		$defaults = weixin_robot_get_default_basic_option();
		return wp_parse_args($weixin_robot_basic, $defaults);
	}else{
		return $weixin_robot_basic;
	}
}

add_action( 'admin_init', 'weixin_robot_admin_init' );
function weixin_robot_admin_init() {
	
	/* start 基本设置 */
	register_setting( 'weixin-robot-basic-group', 'weixin-robot-basic', 'weixin_robot_basic_validate' );
	add_settings_section( 'weixin-robot-basic-section', '', '', 'weixin-robot-basic' );

	$weixin_robot_basic_settings_fields = array(
		array('name'=>'weixin_token',					'title'=>'微信 Token',		'type'=>'text'),
		array('name'=>'weixin_default',					'title'=>'默认缩略图',		'type'=>'text'),
		array('name'=>'weixin_keyword_allow_length',	'title'=>'搜索关键字最大长度',	'type'=>'text',		'description'=>'一个汉字算两个字节，一个英文单词算两个字节，空格不算，搜索多个关键字可以用空格分开！'),
		array('name'=>'weixin_count',					'title'=>'返回结果最大条数',	'type'=>'text',		'description'=>'微信接口最多支持返回10个'), 
		array('name'=>'weixin_welcome',					'title'=>'欢迎语',			'type'=>'textarea'),
		array('name'=>'weixin_keyword_too_long',		'title'=>'超过最大长度提示语',	'type'=>'textarea',	'description'=>'设置超过最大长度提示语，留空则不回复！'),
		array('name'=>'weixin_not_found',				'title'=>'搜索结果为空提示语',	'type'=>'textarea',	'description'=>'可以使用 [keyword] 代替相关的搜索关键字，留空则不回复！'),
		array('name'=>'weixin_voice',					'title'=>'语音回复',			'type'=>'textarea',	'description'=>'设置语言的默认回复文本，留空则不回复！'),
	);

	foreach ($weixin_robot_basic_settings_fields as $field) {
		add_settings_field( 
			$field['name'],
			$field['title'],		
			'weixin_robot_basic_settings_field_callback',	
			'weixin-robot-basic', 
			'weixin-robot-basic-section',	
			$field
		);
	}
	/* end of 基本设置 */ 
}

function weixin_robot_basic_validate( $weixin_robot_basic ) {
	$current = get_option( 'weixin-robot-basic' );

	if ( !is_numeric( $weixin_robot_basic['weixin_keyword_allow_length'] ) ){
		$weixin_robot_basic['weixin_keyword_allow_length'] = $current['weixin_keyword_allow_length'];
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '搜索关键字最大长度必须为数字。' );
	}
	if ( !is_numeric( $weixin_robot_basic['weixin_count'] ) ){
		$weixin_robot_basic['weixin_count'] = $current['weixin_count'];
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '返回结果最大条数必须为数字。' );
	}elseif($weixin_robot_basic['weixin_count'] > 10){
		$weixin_robot_basic['weixin_count'] = 10;
		add_settings_error( 'weixin-robot-basic', 'invalid-int', '返回结果最大条数不能超过10。' );
	}

	return $weixin_robot_basic;
}

function weixin_robot_basic_settings_field_callback($args) {
	$weixin_robot_basic = weixin_robot_get_basic_option();
	$value = $weixin_robot_basic[$args['name']];

	if($args['type'] == 'text'){
		echo '<input type="text" name="weixin-robot-basic['.$args['name'].']" value="'.$value.'" class="regular-text" />';
	}elseif($args['type'] == 'textarea'){
		echo '<textarea name="weixin-robot-basic['.$args['name'].']" rows="6" cols="50" class="regular-text code">'.$value.'</textarea>';
	}
	if(isset($args['description'])) echo '<p class="description">'.$args['description'].'</p>';
}

add_filter('weixin_token',					'wpjam_basic_filter');
add_filter('weixin_default',				'wpjam_basic_filter');
add_filter('weixin_welcome',				'wpjam_basic_filter');
add_filter('weixin_voice',					'wpjam_basic_filter');
add_filter('weixin_keyword_allow_length',	'wpjam_basic_filter');
add_filter('weixin_keyword_too_long',		'wpjam_basic_filter');
add_filter('weixin_count',					'wpjam_basic_filter');
add_filter('weixin_not_found',				'wpjam_basic_filter');

function wpjam_basic_filter($original){
	$weixin_robot_basic = weixin_robot_get_basic_option();

	global $wp_current_filter;

	//最后一个才是当前的 filter
	$wpjam_current_filter = $wp_current_filter[count($wp_current_filter)-1];

	if(isset($weixin_robot_basic[$wpjam_current_filter])){
		if($weixin_robot_basic[$wpjam_current_filter ]){
			return $weixin_robot_basic[$wpjam_current_filter];
		}
	}else{
		return $original;
	}
}