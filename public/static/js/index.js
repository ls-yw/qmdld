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