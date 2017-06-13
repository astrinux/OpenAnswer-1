$(document).ready(function(){

	$("#jplayer").jPlayer({
		ready: function(){
			$(this).jPlayer("setMedia", {
				m4v: "http://pixelbin.s3.amazonaws.com/resources/video_placeholders/video.m4v",
				ogv: "http://pixelbin.s3.amazonaws.com/resources/video_placeholders/video.ogg",
				poster: "http://pixelbin.s3.amazonaws.com/resources/video_placeholders/image.png"
			});
		},
		size: {
		    width: "500px",
		    height: "281px"
		},
		volume: '0.5',
		backgroundColor: "#000000",
		swfPath: "./js/Jplayer.swf",
		cssSelectorAncestor: ".player-container",
		supplied: "m4v,ogv",
		solution: "html, flash",
		currentTime: '.jp-current-time'
	});

	$('.jp-progress-container').hover(function(){
		var current_time = $(this).find('.jp-current-time');
		current_time.stop().show().animate({opacity: 1}, 300);
	}, function(){
		var current_time = $(this).find('.jp-current-time');
		current_time.stop().animate({opacity: 0}, 150, function(){ jQuery(this).hide(); });
	});
	
});