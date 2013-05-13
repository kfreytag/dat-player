Canvas = {};

/**
 * @private
 * @type {Object}
 */
Canvas.container = undefined;

/**
 * @public
 */
Canvas.render = function () {
	this.id = 'canvas';
	this.container = $('<div/>');
	$(this.container).attr('id', this.id).width(Screen.getWidth()).height(Screen.getHeight());
	$(this.container).css('position', 'absolute').css('top', '0px').css('left', '0px');
	$('body').prepend(this.container);
};

/**
 * @public
 * @return {String}
 */
Canvas.getId = function () {
	return this.id;
};

/**
 * @public
 * @param width {number}
 */
Canvas.setWidth = function (width) {
	$(this.container).width(width);
};

/**
 * @public
 * @return {number}
 */
Canvas.getWidth = function () {
	return $(this.container).width();
};

/**
 * @public
 * @param height
 */
Canvas.setHeight = function (height) {
	$(this.container).height(height);
};

/**
 * @public
 * @return {number}
 */
Canvas.getHeight = function () {
	return $(this.container).height();
};