
package com.flash.net {



	import flash.events.EventDispatcher;
	import flash.events.ProgressEvent;
	import flash.events.SecurityErrorEvent;
	import flash.events.HTTPStatusEvent;
	import flash.events.IOErrorEvent;
	import flash.net.URLRequest;
	import flash.net.URLStream;

	import com.flash.events.CustomEvent;



	public class HttpTypeTester extends EventDispatcher {



		public static const FILE_EXISTS:String     = "FlvExists";
		public static const FILE_LOAD_ERROR:String = "FlvLoadError";
		public static const FILE_TYPE_ERROR:String = "FlvTypeError";

		//TODO: add more static codes to use

		public static const FLV_CODE:String = "FLV";
		public static const ZIP_CODE:String = "PK";

		private var          _code:String;

		private var          _stream:URLStream;
		private var          _testUrl:String;



		public function HttpTypeTester(codeToTestAgainst:String, urlToTest:String = null) {

			super();

			_stream = new URLStream();
			_code   = codeToTestAgainst;

			_stream.addEventListener(IOErrorEvent.IO_ERROR,             ioErrorHandler);
			_stream.addEventListener(ProgressEvent.PROGRESS,            progressHandler);
			_stream.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);

			if (urlToTest != null) testUrl(urlToTest);
		}



		public function testUrl(urlToTest:String):void {

			_testUrl = urlToTest;

			try {

				_stream.load(new URLRequest(urlToTest));
			}
			catch (error:Error) {

				dispatchEvent(new CustomEvent(FILE_LOAD_ERROR, _testUrl));
			}
		}



		private function progressHandler(event:ProgressEvent):void {

			if (_stream.bytesAvailable > 100) {

				if (_stream.readUTFBytes(3) == _code) {

					dispatchEvent(new CustomEvent(FILE_EXISTS, _testUrl));
				}
				else {

					dispatchEvent(new CustomEvent(FILE_TYPE_ERROR, _testUrl));
				}
			}

			_stream.close();
		}

		private function securityErrorHandler(event:SecurityErrorEvent):void {

			trace("security error: " + event);
			dispatchEvent(new CustomEvent(FILE_LOAD_ERROR, _testUrl));
		}

		private function ioErrorHandler(event:IOErrorEvent):void {

			trace("IO error: " + event.text.substr(7,4));
			dispatchEvent(new CustomEvent(FILE_LOAD_ERROR, _testUrl));
		}
	}
}