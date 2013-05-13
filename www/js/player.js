Player = {};

/**
 * @private
 * @type {Playlist}
 */
Player.nextPlaylist = undefined;

/**
 * @type {Playlist}
 * @private
 */
Player.currentPlaylist = undefined;

/**
 * @private
 * @type {PlaylistItem}
 */
Player.lastItem = undefined;

/**
 * @private
 * @type {PlaylistItem}
 */
Player.currentItem = undefined;

/**
 * @private
 * @type {PlaylistItem}
 */
Player.nextItem = undefined;

/**
 * @public
 * @param playlist {Playlist}
 */
Player.setPlaylist = function (playlist) {
	Player.nextPlaylist = playlist;
};

/**
 * @public
 */
Player.play = function () {
	if (Player.nextPlaylist) {
		Player.currentPlaylist = Player.nextPlaylist;
		Player.nextPlaylist = undefined;
	}
	Player.playNextItem();
};

/**
 * @public
 */
Player.playNextItem = function () {

	Player.lastItem = Player.currentItem;
	/*
	 * Do any reporting that's necessary
	 */
	if (Player.lastItem) {
		Player.lastItem.destroy();
	}

	Player.currentItem = Player.currentPlaylist.nextPlaylistItem();
	Player.currentItem.play();

};

$().ready(function() {

	Canvas.render();

	var playlist = new Playlist();

	// Segment 1 - IDs
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/7_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/46_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/51_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/45_1.mov'));
	playlist.addSegment(segment);

	// Segment 2 - ASAP
	segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/613_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/601_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/599_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/437_1.mov'));
	playlist.addSegment(segment);

	// Segment 3 - SJP COE
	segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/24_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/26_1.mov'));
	playlist.addSegment(segment);

	// Segment 4 - FOX Health
	segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/601_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/575_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/574_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/559_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/558_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/557_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/556_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/11_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/8_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/12_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/13_1.mov'));
	playlist.addSegment(segment);

	// Segment 5 - AOL
	segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/578_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/579_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/479_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/489_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/581_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/482_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/583_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/585_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/587_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/589_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/598_1.mov'));
	playlist.addSegment(segment);

	// Segment 6 - Movie Trailer
	segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/362_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/363_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/361_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/606_1.mov'));
	playlist.addSegment(segment);

	// Segment 7 - SJP TV
	segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/53_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/5_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/47_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/4_1.mov'));
	playlist.addSegment(segment);

	// Segment 8 - IDs
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/498_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/497_1.mov'));
	playlist.addSegment(segment);

	// Segment 9 - Ad
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/576_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/577_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/580_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/591_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/592_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/593_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/594_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/595_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/596_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/597_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/44_1.mov'));
	playlist.addSegment(segment);

	// Segment 10 - Music
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/566_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/567_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/568_1.mov'));
	playlist.addSegment(segment);

	// Segment 11 - SJP Nearby
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/539_1.mov')); // 3001 - Schoenherr
//	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/546_1.mov')); // 3003 - Providence Family Health Center
//	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/547_1.mov')); // 3004 - Garfield
	playlist.addSegment(segment);

	// Segment 12 - Fox Info
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/618_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/617_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/572_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/555_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/554_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/125_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/146_1.mov'));
	playlist.addSegment(segment);

	// Segment 13 - DYK
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/563_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/564_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/565_1.mov'));
	playlist.addSegment(segment);

	// Segment 14 - IDs
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/234_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/4_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/23_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/51_1.mov'));
	playlist.addSegment(segment);

	// Segment 15 - SJP TV
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/358_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/359_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/360_1.mov'));
	playlist.addSegment(segment);

	// Segment 16 - AOL
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/514_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/405_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/16_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/25_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/29_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/31_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/40_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/104_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/105_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/110_1.mov'));
	playlist.addSegment(segment);

	// Segment 17 - Health Tips
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/41_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/42_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/81_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/101_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/219_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/220_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/221_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/223_1.mov'));
	playlist.addSegment(segment);

	// Segment 18 - Fox Job Shop
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/602_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/603_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/604_1.mov'));
	playlist.addSegment(segment);

	// Segment 19 - SJP COE
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/571_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/570_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/569_1.mov'));
	playlist.addSegment(segment);

	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/695_1.mov'));
        playlist.addSegment(segment);

	// Segment 20 - IDs
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/562_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/561_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/560_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/573_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/615_1.mov'));
	playlist.addSegment(segment);

	// Segment 21 - Movie
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/33_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/30_1.mov'));
	playlist.addSegment(segment);

	// Segment 22 - Fox Credit
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/6_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/22_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/43_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/48_1.mov'));
	playlist.addSegment(segment);

	// Segment 23 - SJP TV
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/582_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/481_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/490_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/478_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/485_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/487_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/584_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/586_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/588_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/590_1.mov'));
	playlist.addSegment(segment);

	// Segment 23 - SJP TV
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/59_1.mov'));
	playlist.addSegment(segment);

	// Segment 23 - SJP TV
	var segment = new Segment();
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/361_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/360_1.mov'));
	segment.addItem(PlaylistUtil.fullscreenQuicktimePlaylistItem('/assets/videos/605_1.mov'));
	playlist.addSegment(segment);

	Player.setPlaylist(playlist);
	Player.play();

});

PlayerConfig = {};
