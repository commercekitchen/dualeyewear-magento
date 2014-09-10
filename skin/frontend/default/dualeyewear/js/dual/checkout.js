var Cachii = (function($){
	return {
		_excludeParent: '#checkout-step-payment',

		init: function(){
			$(document).ready(function(){
				document.observe('login:setMethod', Cachii.listen);
				document.observe('checkout:next-step', Cachii.listen);
				Cachii.listen();
			});
		},

		listen: function(){
			$('input').off().blur(Cachii.saveField);
			$('input[type="radio"]').off().click(Cachii.saveField);
			$('select').off().change(Cachii.saveField);
			$('#shipping\\:same_as_billing').change(Cachii.saveAll);
			Cachii.loadForm();
		},

		loadForm: function(){
			var $cookie = $.cookie('_opc');
			$cookie = $cookie != undefined ? $.parseJSON($cookie) : {};
			$('input, select').each(function(){
				var id = $(this).attr('id');
				if($cookie[id] != undefined){
					if($(this).attr('type') == 'radio'){
						$('input[name="billing[use_for_shipping]"]').prop('checked', false);
						$(this).prop('checked', true);
					}else{
						$(this).val($cookie[id]);
					}
				}
			});
		},

		saveAll: function(){
			setTimeout(function(){
				$('input, select').each(Cachii.saveField);
			}, 500);
		},

		saveField: function(){
			var $field = $(this);
			var $cookie = $.cookie('_opc');
			$cookie = $cookie != undefined ? $.parseJSON($cookie) : {};
			if($field.attr('type') != 'password' && $field.closest(Cachii._excludeParent).length == 0){
				if($field.attr('type') == 'radio'){
					var group = $field.attr('name');
					$('input[name="' + group + '"]').each(function(){
						delete $cookie[$(this).attr('id')];
					});
					$cookie[$field.attr('id')] = 1;
				}else{
					$cookie[$field.attr('id')] = $field.val();
				}
			}
			$.cookie('_opc', JSON.stringify($cookie));
		}
	};
})(jQuery);

Cachii.init();