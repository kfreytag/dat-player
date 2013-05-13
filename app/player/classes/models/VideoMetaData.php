<?php
require_once('Model.php');
/**
 *
 * Generic Model Class for Video Meta Data 
 *
 */
class VideoMetaData extends Model{
	protected $id;
	protected $height;
	protected $width;
	protected $duration;
	protected $container;
	protected $video_codec;
	protected $video_bitrate;
	protected $framerate;
	protected $timebase_framerate;
	protected $audio_codec;
	protected $audio_bitrate;
	protected $sampling_rate;


	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getHeight(){
		return $this->height;
	}

	public function setHeight($height){
		$this->height = $height;
		return $this;
	}

	public function getWidth(){
		return $this->width;
	}

	public function setWidth($width){
		$this->width = $width;
		return $this;
	}

	public function getDuration(){
		return $this->duration;
	}

	public function setDuration($duration){
		$this->duration = $duration;
		return $this;
	}

	public function getContainer(){
		return $this->container;
	}

	public function setContainer($container){
		$this->container = $container;
		return $this;
	}

	public function getVideoCodec(){
		return $this->video_codec;
	}

	public function setVideoCodec($video_codec){
		$this->video_codec = $video_codec;
		return $this;
	}

	public function getVideoBitRate(){
		return $this->video_bitrate;
	}

	public function setVideoBitRate($video_bitrate){
		$this->video_bitrate = $video_bitrate;
		return $this;
	}

	public function getFramerate(){
		return $this->framerate;
	}

	public function setFrameRate($framerate){
		$this->framerate = $framerate;
		return $this;
	}

	public function getTimeBaseFrameRate(){
		return $this->timebase_framerate;
	}

	public function setTimeBaseFrameRate($timebase_framerate){
		$this->timebase_framerate = $timebase_framerate;
		return $this;
	}

	public function getAudioCodec(){
		return $this->audio_codec;
	}

	public function setAudioCodec($audio_codec){
		$this->audio_codec = $audio_codec;
		return $this;
	}

	public function getAudioBitRate(){
		return $this->audio_bitrate;
	}

	public function setAudioBitRate($audio_bitrate){
		$this->audio_bitrate = $audio_bitrate;
		return $this;
	}

	public function getSamplingRate(){
		return $this->sampling_rate;
	}

	public function setSamplingRate($sampling_rate){
		$this->sampling_rate = $sampling_rate;
		return $this;
	}
}



?>