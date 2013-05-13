/**
 * @constructor
 */
function Playlist() {

	/**
	 * @private
	 * @type {Array, <Segment>}
	 */
	this.segments = [];

	/**
	 * @private
	 * @type {Number}
	 */
	this.pointer = 0;

	/**
	 * @public
	 * @param segment {Segment}
	 */
	this.addSegment = function (segment) {
		this.segments.push(segment);
	};

	/**
	 * @public
	 * @return {PlaylistItem}
	 */
	this.nextPlaylistItem = function () {

		if (this.segments.length > 0) {
			if (this.pointer == this.segments.length) {
				this.pointer = 0;
			}
			var currentSegment = this.segments[this.pointer++];
			return currentSegment.next();
		} else {
			return null;
		}
	};

}

function Segment() {

	/**
	 * @private
	 * @type {Array, <PlaylistItem>}
	 */
	this.playlistItems = [];

	/**
	 * @private
	 * @type {Number}
	 */
	this.pointer = 0;

	/**
	 * @public
	 * @param item {PlaylistItem}
	 */
	this.addItem = function (item) {
		this.playlistItems.push(item);
	};

	/**
	 * @public
	 * @return {PlaylistItem}
	 */
	this.next = function () {

		if (this.playlistItems.length > 0) {
			if (this.pointer == this.playlistItems.length) {
				this.pointer = 0;
			}
			return this.playlistItems[this.pointer++];
		} else {
			return null;
		}
	};

}

/**
 * @param layout <Layout>
 * @constructor
 */
function PlaylistItem(layout) {

	/**
	 * @private
	 * @type {Layout}
	 **/
	this.layout = layout;

	/**
	 * @public
	 * @return {Layout}
	 */
	this.getLayout = function () {
		return this.layout;
	};

	/**
	 * @public
	 */
	this.render = function () {
		this.layout.render();
	};

	/**
	 * @public
	 */
	this.destroy = function () {
		this.layout.destroy();
	};

	/**
	 * @public
	 */
	this.play = function () {
		this.layout.play();
	};

}


PlaylistUtil = {};

PlaylistUtil.fullscreenQuicktimePlaylistItem = function (url) {
	var asset = new QuicktimeVideoAsset(url);
	var zone = new QuicktimeVideoZone(asset);
	var layout = new FullscreenLayout(zone);
	return new PlaylistItem(layout);
};