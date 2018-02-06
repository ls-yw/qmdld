/**
 * 
 */
$(function(){
	shop();
	heroLilianGoods();
	init();
	$('#index').on('click', '.editToken', function(){
		$('#index .h5token').attr('contenteditable', true);
		$('#index .h5token').focus();
		$(this).text('保存');
		$(this).addClass('saveEditToken');
	});
	$('#index').on('click', '.saveEditToken', function(){
		var h5token = $('#index .h5token').text();
		$.post("/index/index/updateToken",{h5token:h5token},function(result){
			if(result.code == 0){
				$('#index .h5token').removeAttr('contenteditable');
				$('#index .saveEditToken').text('修改');
				$('#index .saveEditToken').removeClass('saveEditToken');
				getUserInfo();
			}else{
				alert(result.msg)
			}
		},'json')
	});
	
	$('#index').on('click', '.submitConfig',function(){
		$.post("/index/user/saveConfig",$('#configForm').serializeArray(),function(result){
			if(result.code == 0){
				getConfig();
			}else{
				alert(result.msg)
			}
		},'json');
	});
	
	$('.view_fac_cave').on('click',function(){
		dialog({
			id:'fac_cave',
			width:'1024px',
			title: '帮派洞穴',
			content: '加载中...',
			okValue: '确定',
			ok: true,
		}).show();
		updateCave();
	});
	$('#main').on('click', '.cave-fight', function(){
		var autoStatus = $('#caveFightAutoStatus').prop('checked') ? 1 : 0;
		var id = $(this).data('id');
		var sep = $(this).data('sep');
		$.post("/dld/faction/caveFight",{autoStatus:autoStatus,id:id,sep:sep},function(res){
			updateCave();
			dialog({
				width:'200px',
				title: '帮派洞穴挑战结果',
				content: res.msg,
				okValue: '确定',
				ok: true,
			}).show();
		},'json');
	});
	$('#main').on('click', '.cave-refresh', function(){
		updateCave();
	});
});

function init()
{
	$.post("/index/login/islogin",function(result){
		if(result.code == 0){
			getUserInfo();
			$('#index .loginForm').hide();
			$('#index .userInfo').show();
			//加载日志
			window.setInterval('getLog()',3000);
			getConfig();
		}else{
			$('#index .loginForm').show();
			$('#index .userInfo').hide();
		}
	},'json');
}
function getConfig()
{
	$.post("/index/user/getConfig",function(result){
		if(result.code == 0){
			var shops = new Array('pvp_shop', 'servant_shop', 'qualifying_shop', 'lilian_hero_ordinary_goods');
			var selects = new Array('lilian_ordinary_type', 'lilian_hero_ordinary_type');
			for(i in result.data){
				if($.inArray(i, selects) >= 0){
					$('#configForm .'+i).val(result.data[i]);
				}else if($.inArray(i, shops) >= 0 && result.data[i] != ''){
					good_ids =result.data[i].split(","); //字符分割 
					for (j=0;j<good_ids.length ;j++ )
					{
						$('#'+i).find('option[value="'+good_ids[j]+'"]').attr('selected', 'selected');
					} 
					$('#'+i).trigger("chosen:updated");
				}else{
					if(result.data[i] == 1){
						$('#configForm .'+i).prop('checked', true);
					}else if(result.data[i] == 0){
						$('#configForm .'+i).prop('checked', false);
					}
				}
			}
		}else{
			alert(result.msg)
		}
	},'json');
}

function shop()
{
	$.post("/index/user/shop",function(result){
		if(result.code == 0){
			var shops = result.data;
			for(mark in shops){
				var cont = '';
				for(good_id in shops[mark]){
					cont += '<option value="'+good_id+'">'+shops[mark][good_id]+'</option>';
				}
				$('#'+mark+'_shop').html(cont);
			}
			$('.chosen').trigger("chosen:updated");
		}else{
			alert(result.msg);
		}
	},'json');
}
function heroLilianGoods()
{
	$.post("/index/user/lilianGoods",{'dup':10001},function(result){
		if(result.code == 0){
			var goods = result.data;
			var cont = '';
			for(mark in goods){
				cont += '<option value="'+goods[mark]['good_id']+'">'+goods[mark]['name']+'</option>';
			}
			$('#lilian_hero_ordinary_goods').html(cont);
			$('.chosen').trigger("chosen:updated");
		}else{
			alert(result.msg);
		}
	},'json');
}

function updateCave(){
	dialog({id: 'fac_cave'}).content('加载中...');
	$.post("/dld/faction/getCaveInfo",function(res){
		if(res.code == 0){
			var html = '<div class="row">';
			for(var i in res.data.ordinary){
				html += '<div class="col-md-2">';
				html += '<ul class="list-unstyled '+(res.data.ordinary[i].status == 0 ? 'gray' : (res.data.ordinary[i].status == 2 ? '' : 'text-success'))+'">';
				html += '<li>'+res.data.ordinary[i].name+'</li>';
				html += '<li class="red">'+res.data.ordinary[i].hp+'</li>';
				html += '<li>'+res.data.ordinary[i].des_stauts+'</li>';
				html += '<li>'+res.data.ordinary[i].des_weak+'</li>';
				html += '<li>'+(res.data.ordinary[i].status == 0 ? '已击败' : (res.data.ordinary[i].status == 2 ? '未开启' : (res.data.canFight ? '<button type="button" class="cave-fight" data-sep="'+res.data.sep+'" data-id="'+res.data.ordinary[i].id+'">挑战</button>' : '无次数')))+'</li>';
				html += '</ul>';
				html += '</div>';
			}
			html += '</div>';
			html += '<hr>';
			html += '<div class="row">';
			for(var j in res.data.boss){
				html += '<div class="col-md-3">';
				html += '<ul class="list-unstyled '+(res.data.boss[j].status == 0 ? 'gray' : (res.data.boss[j].status == 2 ? '' : 'text-success'))+'">';
				html += '<li>'+res.data.boss[j].name+'</li>';
				html += '<li class="red">'+res.data.boss[j].hp+'</li>';
				html += '<li>'+res.data.boss[j].des_stauts+'</li>';
				html += '<li>'+res.data.boss[j].des_weak+'</li>';
				html += '<li>'+(res.data.boss[j].status == 0 ? '已击败' : (res.data.boss[j].status == 2 ? '未开启' : (res.data.canFight ? '<button type="button" class="cave-fight" data-sep="'+res.data.sep+'" data-id="'+res.data.boss[j].id+'">挑战</button>' : '无次数')))+'</li>';
				html += '</ul>';
				html += '</div>';
			}
			html += '</div>';
			html += '<div class="row"><div class="col-md-12"><label class="checkbox-inline"><input type="checkbox" id="caveFightAutoStatus" checked="checked" class="caveFightAutoStatus" value="1">自动嗑药</label> <button type="button" class="cave-refresh">刷新</button></div></div>';
		}else{
			var html = res.msg;
		}
		dialog({id: 'fac_cave'}).content(html);
	},'json');
}

function exchangeCode(){
	dialog({
		id:'fac_cave',
		width:'300px',
		title: '兑换',
		content: '<div>兑换码：<input type="text" value="" id="duihuan_code"/></div>',
		okValue: '确定',
		ok: function(){
			$.post("/dld/other/code",{'code':$('#duihuan_code').val()},function(res){
				if(res.code == 0){
					return true;
				}else{
					alert('兑换失败');
					return false;
				}
			},'json');
		},
		cancel:true,
	}).show();
}