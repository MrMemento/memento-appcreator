


package com.memento.air.net {

	// need to import ServicemonitorShim
	// component to Library

	import air.net.URLMonitor;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.HTTPStatusEvent;
	import flash.events.IOErrorEvent;
	import flash.events.StatusEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;



	public class ConnectionMonitor extends EventDispatcher {

		public static const CONNECTION_AVAILABLE:String     = "connectionAvailable";
		public static const CONNECTION_NOT_AVAILABLE:String = "connectionNotAvailable";
		private var _monitor:URLMonitor;



		public function ConnectionMonitor(urlToCheck:String):void {

			if (urlToCheck != "") {

				var headRequest:URLRequest = new URLRequest();
				headRequest.method = "HEAD";
				headRequest.url = urlToCheck;
	
				_monitor = new URLMonitor(headRequest);
	
				_monitor.addEventListener(StatusEvent.STATUS, statusChanged);
				_monitor.start();
			}
		}

		// check the results and alert
		// the user if no connection is found

		private function statusChanged(status:StatusEvent):void {

			if (status.code == "Service.unavailable") {

				// no internet connection
				//
				dispatchEvent(new Event(CONNECTION_NOT_AVAILABLE));
			}
			else {

				// got internet connection
				//
				dispatchEvent(new Event(CONNECTION_AVAILABLE));
			}
		}
	}
}


