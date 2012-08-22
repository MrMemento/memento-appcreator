
package com.flash.net {

	/* required FLASH classes
	 */

	import flash.display.Loader;
	import flash.errors.IllegalOperationError;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;

	public class SwfClassLoader extends EventDispatcher {

		public static var CLASS_LOADED:String = "classLoaded";
		public static var LOAD_ERROR:String   = "classLoadError";

		private var loader:Loader;
		private var swfLib:String;
		private var request:URLRequest;
		private var loadedClass:Class;

		public function SwfClassLoader() {

			loader = new Loader();
			loader.contentLoaderInfo.addEventListener(Event.COMPLETE,completeHandler);
			loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR,ioErrorHandler);
			loader.contentLoaderInfo.addEventListener(SecurityErrorEvent.SECURITY_ERROR,securityErrorHandler);
		}

		public function load(lib:String):void {
			swfLib = lib;
			request = new URLRequest(swfLib);
			var context:LoaderContext = new LoaderContext();
			context.applicationDomain = ApplicationDomain.currentDomain;
			loader.load(request, context);
		}

		public function getClass(className:String):Class {
			try {
				return loader.contentLoaderInfo.applicationDomain.getDefinition(className)  as  Class;
			} catch (e:Error) {
			}
			return null;
		}

		private function completeHandler(e:Event):void {
			dispatchEvent(new Event(CLASS_LOADED));
		}

		private function ioErrorHandler(e:Event):void {
			dispatchEvent(new Event(LOAD_ERROR));
		}

		private function securityErrorHandler(e:Event):void {
			dispatchEvent(new Event(LOAD_ERROR));
		}
	}
}
