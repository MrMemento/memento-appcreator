package com.memento.flash.net {

	/* required FLASH classes
	 */

	import flash.net.URLRequest;
	import flash.net.URLLoader;
	import flash.events.EventDispatcher;
	import flash.events.Event;

	/* required CUSTOM classes
	 */

	import com.memento.flash.events.CustomEvent;
	import com.memento.air.display.Message;

	/*
	 * parsing RSS data
	 *
	 * based on RSS 2.0 specification at
	 * http://cyber.law.harvard.edu/rss/rss.html
	 *
	 * should work with all valid RSS feeds validated by
	 * http://feedvalidator.org/
	 *
	 * url: the url of the feed to parse
	 *
	 * after the parsing "_rssContent" will contain all the data needed to work with
	 * check it's structure through RssParser.TRACE = true;
	 *
	 */

	public class RssParser extends EventDispatcher {

		/*
		 * variables ---------------------------------------------------------------------------------------------------------------
		 * -------------------------------------------------------------------------------------------------------------------------
		 */

		public static const PARSE_OVER:String = "ParseHasEnded";

		public var id:* = null;

		private const dTEXT:String = "Új tartalom az oldalon:";
		private var _rssContent:Array;
		private var _rssXML:XML;
		private var _myLoader:URLLoader;

		/*
		 * instantiation -----------------------------------------------------------------------------------------------------------
		 * -------------------------------------------------------------------------------------------------------------------------
		 */

		public function RssParser():void {

			// nothing here
		}

		public function parse(url:String):void {

			try {
				_myLoader = new URLLoader(new URLRequest(url));
				_myLoader.addEventListener(Event.COMPLETE, xmlLoaded);
			}
			catch (e:Error) {
				//Message.slideMessage(e.toString());
			}
		}


		/*
		 * convert xml data --------------------------------------------------------------------------------------------------------
		 * -------------------------------------------------------------------------------------------------------------------------
		 */

		private function xmlLoaded(evtObj:Event):void {

			_rssXML = XML(_myLoader.data);

			/* the source RSS data may or may not use a namespace to define its content
			 */

			if (_rssXML.namespace("") != undefined) {

				default xml namespace = _rssXML.namespace("");
			}

			/* build RSS container
			 */

			_rssContent              = new Array();
			_rssContent.buildDate    = test(_rssXML.channel.lastBuildDate); // RFC (2)822 format
			_rssContent.title        = test(_rssXML.channel.title);
			_rssContent.link         = test(_rssXML.channel.link);
			_rssContent.description  = test(_rssXML.channel.description);

			_rssContent.rssPic       = new Object();
			_rssContent.rssPic.url   = test(_rssXML.channel.image.url);
			_rssContent.rssPic.title = test(_rssXML.channel.image.title);
			_rssContent.rssPic.link  = test(_rssXML.channel.image.link);

			if (id != null) _rssContent.id = id;

			for each (var item:XML in _rssXML..item) {

				var container:Object     = new Object();
				container.title          = test(item.title);
				container.description    = test(item.description);
				container.link           = test(item.link);
				container.guid           = test(item.guid);
				container.date           = test(item.pubDate); // RFC (2)822 format
				container.enclosure      = new Object();
				container.enclosure.url  = test(item.enclosure.@url);
				container.enclosure.type = test(item.enclosure.@type); // MIME type
				//container.enclosure.size = test(item.enclosure.@length); // in bytes

				_rssContent.push(container);
			}

			dispatchEvent(new CustomEvent(PARSE_OVER, _rssContent));
		}

		private function test(target:*):String {

			if (target.toString()) {

				var toTest:String = target.toString()
				if (toTest.length > 2) {
					return(target.toString());
				}
			}
			else {
				return(dTEXT);
			}

			return "";
		}

		/*
		 * trace -------------------------------------------------------------------------------------------------------------------
		 * -------------------------------------------------------------------------------------------------------------------------
		 */

		public function get debug():String {

			/* just a simple trace, can be achieved via class specific variable - TRACE
			 */

			var debug:String;

			debug = ("------------------------------------------------------------------------------------------\n");
			for (var inst0:* in _rssContent) {
				debug += ("-> " + inst0.toString() + " :" + typeof _rssContent[inst0] + "\n");
				switch (typeof _rssContent[inst0]) {
					case "object":
						for (var inst1:* in _rssContent[inst0]) {
							debug += ("    -> " + inst1.toString() + " :" + typeof _rssContent[inst0][inst1] + "\n");
							switch (typeof _rssContent[inst0][inst1]) {
								case "object":
									for (var inst2:* in _rssContent[inst0][inst1]) {
										debug += ("        -> " + inst2.toString() + " :" + typeof _rssContent[inst0][inst1][inst2] + "\n             " + _rssContent[inst0][inst1][inst2].toString() + "\n");
									}
									break;
								default:
									debug += ("         " + _rssContent[inst0][inst1].toString() + "\n")
									break;
							}
						}
						break;
					default:
						debug += ("     " + _rssContent[inst0] + "\n");
						break;
				}
				debug += ("\n");
			}
			debug += ("------------------------------------------------------------------------------------------/n");

			return debug;
		}
	}
}