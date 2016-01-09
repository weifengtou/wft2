// 正则验证rule
var email_rule = /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/;
var username_rule = /^[a-zA-Z0-9_\x7f-\xff]{3,20}$/;
var telphone_rule =  /(^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$)|(^0\d{2,3}-?\d{7,8})/;
var password_rule = /^[a-zA-Z0-9]{6,20}$/;
var special_character_rule = RegExp(/[(\ )(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\-)(\_)(\+)(\=)(\[)(\])(\{)(\})(\|)(\\)(\;)(\:)(\')(\")(\,)(\.)(\/)(\<)(\>)(\?)(\)]+/);
var number_rule = /^\d+$/;

//jquery 监听元素大小改变
(function($,h,c){var a=$([]),e=$.resize=$.extend($.resize,{}),i,k="setTimeout",j="resize",d=j+"-special-event",b="delay",f="throttleWindow";e[b]=250;e[f]=true;$.event.special[j]={setup:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.add(l);$.data(this,d,{w:l.width(),h:l.height()});if(a.length===1){g()}},teardown:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.not(l);l.removeData(d);if(!a.length){clearTimeout(i)}},add:function(l){if(!e[f]&&this[k]){return false}var n;function m(s,o,p){var q=$(this),r=$.data(this,d);r.w=o!==c?o:q.width();r.h=p!==c?p:q.height();n.apply(this,arguments)}if($.isFunction(l)){n=l;return m}else{n=l.handler;l.handler=m}}};function g(){i=h[k](function(){a.each(function(){var n=$(this),m=n.width(),l=n.height(),o=$.data(this,d);if(m!==o.w||l!==o.h){n.trigger(j,[o.w=m,o.h=l])}});g()},e[b])}})(jQuery,this);

//靠底部对齐
jQuery(document).ready(function($) {
	on_bottom();
	$(".on_bottom").resize(function () {
		on_bottom();
	});
	$(".on_bottom").parent().resize(function () {
		on_bottom();
	});
});
function on_bottom () {
	$(".on_bottom").each(function (e) {
		$(this).css({
			"margin-top":0
		});
		//自身高度
		var h1 = $(this).height();
		//父元素高度
		var h2 = $(this).parent().height();
		var _h = h2 - h1;
		$(this).css({
			"margin-top":_h
		});
	})
}

var DataTableLanguageCN = {
    "sProcessing": "处理中...",
    "sLengthMenu": "显示 _MENU_ 项结果",
    "sZeroRecords": "没有匹配结果",
    "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
    "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
    "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
    "sInfoPostFix": "",
    "sSearch": "搜索:",
    "sUrl": "",
    "sEmptyTable": "表中数据为空",
    "sLoadingRecords": "载入中...",
    "sInfoThousands": ",",
    "oPaginate": {
        "sFirst": "首页",
        "sPrevious": "上页",
        "sNext": "下页",
        "sLast": "末页"
    },
    "oAria": {
        "sSortAscending": ": 以升序排列此列",
        "sSortDescending": ": 以降序排列此列"
    }
};