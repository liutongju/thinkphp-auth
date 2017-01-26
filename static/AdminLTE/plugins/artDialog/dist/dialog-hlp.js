/**
 * 对artDialog进行简单封装，方便业务系统调用
 * @author Evan <tangzwgo@foxmail.com>
 * @since 2016-07-22
 */
var ArtDialogHlp = {
	/**
	 * 显示提示信息（类alert）
	 * @param title 标题
	 * @param content 提示内容
	 * @param callback 点确定按钮时的回调
	 */
	showInfo : function(title, content, callback){
		var d = dialog({
		    title: title,
		    content: content,
		    okValue: '确定',
            zIndex: 9999,
		    ok: function () {
		    	if (!!callback) {
					var cb = callback(d);
					return cb || cb == void 0;
				}
				return true;
		    },
		    cancel: false
		});
		d.showModal();
	},
	/**
	 * 显示确认对话框（类confirm）
	 * @param title 标题
	 * @param content 提示内容
	 * @param callback 点确定按钮时的回调
	 */
	showConfirm : function(title, content, callback, cancelCallback){
		var d = dialog({
		    title: title,
		    fixed : true,
		    content: content,
		    okValue: '确定',
            zIndex: 9999,
		    ok: function () {
		    	if (!!callback) {
					var cb = callback(d);
					return cb || cb == void 0;
				}
				return true;
		    },
		    cancelValue : '取消',
		    cancel: function () {
		    	if (!!cancelCallback) {
					var ccb = cancelCallback(d);
					return ccb || ccb == void 0;
				}
				return true;
		    }
		});
		d.showModal();
	},
};