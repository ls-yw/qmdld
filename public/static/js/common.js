function checkLogin()
{
	$.post("/index/login/islogin",function(result){
		if(result.code == 0){
			return true;
		}else{
			return false;
		}
	},'json');
}
function getUserInfo()
{
	$.post("/dld/info/getinfo",function(result){
		if(result.code == 0){
			$('.userInfo .name').text(result.data.name);
			$('.userInfo .lvl').text(result.data.lvl);
			$('.userInfo .uid').text(result.data.uid);
			$('.userInfo .vip_lvl').text(result.data.vip_lvl);
			$('.userInfo .attack_power').text(result.data.attack_power);
			$('.userInfo .sex').text(result.data.sex == 1 ? '男' : '女');
			$('.userInfo .fac_id').text(result.data.fac_id);
			$('.userInfo .fac_name').text(result.data.fac_name);
			$('.userInfo .fac_cave').text(result.data.fac_cave);
			$('.userInfo .exp').text(result.data.exp+'/'+result.data.max_exp);
			$('.userInfo .vit').text(result.data.vit+'/100');
			$('.userInfo .headimgurl').attr('src',result.data.headimgurl);
			$('.userInfo .h5token').text(result.data.h5token);
			$('.userInfo .doubi_num').text(result.data.doubi_num);
			$('.userInfo .douyu_num').text(result.data.douyu_num);
			$('.userInfo .servant_cash').text(result.data.servant_cash);
			$('.userInfo .king_medal').text(result.data.king_medal);
			$('.userInfo .prestige').text(result.data.prestige);
			$('.userInfo .spirit').text(result.data.spirit);
			$('.userInfo .winpoint').text(result.data.winpoint);
			$('.userInfo .login_days').text(result.data.login_days);
			$('.userInfo .marry_status').text(result.data.marry_status == 3 ? '已婚' : '未婚');
			$('.userInfo .qualifying').text(result.data.qualifying);
			$('.userInfo .qualifying_num').text(result.data.qualifying_num);
			$('.userInfo .lilian').text(result.data.lilian);
			$('.userInfo .lilian_num').text(result.data.lilian_num);
			$('.userInfo .hero_lilian').text(result.data.hero_lilian);
			$('.userInfo .hero_lilian_num').text(result.data.hero_lilian_num);
			$('.userInfo .tower').text(result.data.tower);
			$('.userInfo .marry_hangup').text(result.data.marry_hangup);
			$('.userInfo .unlock_page').text(result.data.unlock_page);
			$('.userInfo .unlock_scene').text(result.data.unlock_scene);
			$('.userInfo .unlock_weapon').text(result.data.unlock_weapon);
			$('.userInfo .unlock_skill').text(result.data.unlock_skill);
			
			(result.data.unlock_page == '120/120') ? $('.unlockPage').hide() : $('.unlockPage').show();
			(result.data.unlock_scene == '17/17') ? $('.unlockScene').hide() : $('.unlockScene').show();
			if(result.data.unlock_weapon != ''){
				var unlock_weapon = result.data.unlock_weapon.split('/');
				(unlock_weapon[0] == unlock_weapon[1]) ? $('.unlockWeapon').hide() : $('.unlockWeapon').show();
			}
			if(result.data.unlock_skill != ''){
				var unlock_skill = result.data.unlock_skill.split('/');
				(unlock_skill[0] == unlock_skill[1]) ? $('.unlockSkill').hide() : $('.unlockSkill').show();
			}
		}else{
			alert(result.msg)
		}
	},'json');
}
function refreshUserInfo()
{
	$.post("/dld/info/updateinfo",function(result){
		if(result.code == 0){
			getUserInfo();
		}else{
			alert(result.msg)
		}
	},'json');
}
function goLogin()
{
	var userName = $('#user_name').val();
	if(userName == ''){
		alert('用户名不能为空');
		return false;
	}
	$.post("/index/login/login",{userName:userName},function(result){
		if(result.code == 0){
			getUserInfo();
			$('#index .loginForm').hide();
			$('#index .userInfo').show();
			//加载日志
			window.setInterval('getLog()',3000);
			getConfig();
		}else{
			alert(result.msg);
			return false;
		}
	},'json');
}
function logout()
{
	$.post("/index/login/logout",function(result){
		if(result.code == 0){
			$('#index .loginForm').show();
			$('#index .userInfo').hide();
		}else{
			alert(result.msg)
		}
	},'json');
}
function unlockPage()
{
	$.post("/dld/other/unlockPage",function(result){
		if(result.code == 0){
			getUserInfo();
		}else{
			alert(result.msg)
		}
	},'json');
}
function unlockScene()
{
	$.post("/dld/other/unlockScene",function(result){
		if(result.code == 0){
			getUserInfo();
		}else{
			alert(result.msg)
		}
	},'json');
}
function unlockWeapon()
{
	$.post("/dld/other/unlockWeapon",function(result){
		if(result.code == 0){
			getUserInfo();
		}else{
			alert(result.msg)
		}
	},'json');
}
function unlockSkill()
{
	$.post("/dld/other/unlockSkill",function(result){
		if(result.code == 0){
			getUserInfo();
		}else{
			alert(result.msg)
		}
	},'json');
}


if($.isFunction($('select.chosen').chosen)){
	initChosen();
}

function initChosen(){
	$('select.chosen').each(function(){
		var options = {};
		options.placeholder_text_multiple = $(this).attr('placeholder') ? $(this).attr('placeholder') : '请选择';
		options.no_results_text = '查找不到结果为';
		options.width = '100%';
		
		if(typeof($(this).attr('max')) !== 'undefined'){
			options.max_selected_options = $(this).attr('max');
		}
		
		$(this).chosen(options);
	});
}








function getLog()
{
	$.post("/index/log/current",function(result){
		if(result.code == 0){
			$('#index .logs').html(result.data);
		}else{
			$('#index .logs').html('日志获取失败');
		}
	},'json');
}