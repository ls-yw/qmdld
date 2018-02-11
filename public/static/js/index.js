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
			id:'dialog',
			width:'1024px',
			title: '帮派洞穴',
			content: '加载中...',
			okValue: '刷新',
			ok: function(){
				updateCave();
				return false;
			},
			cancelValue:'关闭',
			cancel:true,
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
});

function init()
{
	$.post("/index/login/islogin",function(result){
		if(result.code == 0){
			getUserInfo();
			$('#index .loginForm').hide();
			$('#index .userInfo').show();
			//加载日志
			getLog();
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
	dialog({id: 'dialog'}).content('加载中...');
	$.post("/dld/faction/getCaveInfo",function(res){
		if(res.code == 0){
			var html = '<div class="row">';
			for(var k in res.data.attrStatus){
				html += '<div class="col-md-2 '+(res.data.attrStatus[k].has == 1 ? 'text-success' : 'gray')+'">'+res.data.attrStatus[k].name+'：'+res.data.attrStatus[k].lv+(res.data.attrStatus[k].has == 1 && res.data.attrStatus[k].goods_num > 0 ? '' : ' <button type="button" class="addStatus" data-fun="updateCave" data-id="'+res.data.attrStatus[k].shop_id+'">开启</button>')+'</div>';
			}
			html += '</div>';
			
			html += '<div class="row" style="margin-top:5px;">';
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
		}else{
			var html = res.msg;
		}
		dialog({id: 'dialog'}).content(html);
	},'json');
}

function exchangeCode(){
	dialog({
		id:'dialog',
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
	$('#duihuan_code').focus();
}
/************************ 千层塔 ****************************/
$('.view_tower').on('click',function(){
	dialog({
		id:'dialog',
		width:'800px',
		title: '千层塔',
		content: '加载中...',
		okValue: '刷新',
		ok: function(){
			updateTower();
			return false;
		},
		cancelValue:'关闭',
		cancel:true,
	}).show();
	updateTower();
});
$('body').on('click', '.addStatus', function(){
	var id  = $(this).data('id');
	var fun = $(this).data('fun');
	$.post("/dld/other/addStatus",{id:id},function(res){
		if(res.code == 0){
			window[fun].call();
		}else{
			alert(res.msg);
		}
	},'json');
});
$('body').on('click', '.tower-fight', function(){
	var index  = $(this).data('index');
	var floor  = $(this).data('floor');
	$.post("/dld/tower/fight",{index:index,floor:floor},function(res){
		if(res.code == 0){
			updateTower();
		}else{
			alert(res.msg);
			return false;
		}
	},'json');
});
$('body').on('click', '.status-f', function(){
	$(this).select();
});
$('body').on('click', '.saveAttrDian', function(){
	var max  = $(this).data('max');
	var sum = 0;
	var attr = {};
	$('.status-f').each(function(){
		attr[$(this).data('key')] = $(this).data('id')+':'+$(this).val();
		sum = sum + parseInt($(this).val());
	});
	if(sum > max){
		alert('属性点已超出'+(sum-max)+'点');
		return false;
	}
	$.post("/dld/tower/saveAttr",attr,function(res){
		if(res.code == 0){
			updateTower();
			alert('保存成功');
		}else{
			alert(res.msg);
		}
	},'json');
});
function updateTower()
{
	dialog({id: 'dialog'}).content('加载中...');
	$.post("/dld/tower/getInfo",function(res){
		if(res.code == 0){
			var html = '<div class="row">';
			html += '<div class="col-md-2">强命：'+res.data.status[300030].num+' <input type="text" style="width:30px;" class="status-f" data-key="qm" data-id="'+res.data.status[300030].id+'" value="'+res.data.status[300030].num+'"/></div>';
			html += '<div class="col-md-2">英勇：'+res.data.status[300031].num+' <input type="text" style="width:30px;" class="status-f" data-key="yy" data-id="'+res.data.status[300031].id+'" value="'+res.data.status[300031].num+'"/></div>';
			html += '<div class="col-md-2">坚固：'+res.data.status[300032].num+' <input type="text" style="width:30px;" class="status-f" data-key="jg" data-id="'+res.data.status[300032].id+'" value="'+res.data.status[300032].num+'"/></div>';
			html += '<div class="col-md-2">急速：'+res.data.status[300033].num+' <input type="text" style="width:30px;" class="status-f" data-key="js" data-id="'+res.data.status[300033].id+'" value="'+res.data.status[300033].num+'"/></div>';
			html += '<div class="col-md-2 '+(res.data.status[0] > 0 ? 'green' : 'gray')+'">剩余：'+res.data.status[0]+' <button type="button" class="saveAttrDian" data-max="'+res.data.maxStatus+'">确定</button></div>';
			html += '<div class="col-md-2">复活次数：'+res.data.revive+'</div>';
			html += '</div>';
			html += '<div class="row" style="margin-top:5px;">';
			for(var k in res.data.attrStatus){
				html += '<div class="col-md-2 '+(res.data.attrStatus[k].has == 1 ? 'text-success' : 'gray')+'">'+res.data.attrStatus[k].name+'：'+res.data.attrStatus[k].lv+(res.data.attrStatus[k].has == 1 && res.data.attrStatus[k].goods_num > 0 ? '' : ' <button type="button" class="addStatus" data-fun="updateTower" data-id="'+res.data.attrStatus[k].shop_id+'">开启</button>')+'</div>';
			}
			html += '</div>';
			html += '<hr>';
			html += '<div class="row">';
			for(var i in res.data.monster){
				html += '<div class="col-md-4">';
				html += '<ul class="list-unstyled '+(res.data.monster[i].status == 0 ? 'text-success' : 'gray')+'">';
				html += '<li>'+res.data.monster[i].name+'</li>';
				html += '<li>等级：'+res.data.monster[i].level+'</li>';
				for(var j in res.data.monster[i].buffList){
					html += '<li class="red">'+res.data.monster[i].buffList[j].name+'：'+res.data.monster[i].buffList[j].desc+'</li>';
				}
				html += '<li>'+(res.data.monster[i].status != 0 ? '已击败' : '<button type="button" class="tower-fight" data-index="'+res.data.monster[i].index+'" data-floor="'+res.data.floor+'">挑战</button>')+'</li>';
				html += '</ul>';
				html += '</div>';
			}
			html += '</div>';
			
		}else{
			var html = res.msg;
		}
		dialog({id: 'dialog'}).content(html);
	},'json');
}