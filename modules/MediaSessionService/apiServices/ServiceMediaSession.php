<?php
/**
* This service supports sending parsing player config for the purpose of ad stitching delivery
*/
require_once( dirname( __FILE__ ) . '/../../KalturaSupport/KalturaCommon.php' );

class ServiceMediaSession extends BaseStreamService{
	var $resultObject = null;
	
	var $errorVideoEntries = array(
		'georestricted' => '1_vibqimym',
		'nosources' => '1_g18we0u3'
	);
	
	function run(){
		global $wgEnableScriptDebug;
		// Create the hander 
		$sessionSource = $this->getSessionSource();
		
		if( !$sessionSource ){
			// TODO redirect to download stream ( per-user-agent stream selection ) 
			exit();
		}
		// We only support application/vnd.apple.mpegurl right now: 
		if( $sessionSource['type'] != 'application/vnd.apple.mpegurl' ){
			// TODO redirect to download stream ( per-user-agent stream selection )
			exit();
		}
		$this->setStreamUrl( $sessionSource['src'] );
		$streamHandler = $this->getStreamHandler();
		// send header and StreamList output:
		header( 'Content-Type: application/x-mpegurl');
		echo $streamHandler->getManifest();
	}
	
	function getSessionSource(){
		// create new session 
		$kSources = new KalturaSources();
		$sources = $kSources->getSources();
		// for now we only support 'application/vnd.apple.mpegurl' type
		foreach( $sources as $source){
			// technically there are iPadNew and iPhoneNew ( two Adaptive sets ) 
			// We may want to consolidate now that bugs around Adaptive are not as common in iOS
			if( $source['type'] == 'application/vnd.apple.mpegurl' ){
				return $source;
			}
		}
		// error out if no
		return array();
	}
	
}