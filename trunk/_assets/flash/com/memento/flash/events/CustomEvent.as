


package com.memento.flash.events {

	/* required FLASH classes
	 */

	import flash.events.Event;

	/* custom event wich contains any data
	 */

	public class CustomEvent extends Event {

		public var data:*;

		public function CustomEvent(typeToUse:String, dataToWrite:*):void {

			super(typeToUse, true);
			this.data = dataToWrite;
		}

		override public function clone():Event {

			/*
			 * can't change return type
			 * be careful with forwarded and cloned events
			 * may result in implicit coercion
			 * anyway, it'll contain data
			 *
			 * ERROR  : var retek:CustomEvent = event.clone();
			 * instead: var retek:Event = event.clone();
			 * (event is an instance of CustomEvent)
			 *
			 */

			var toReturn:CustomEvent = new CustomEvent(this.type, data);
			return toReturn;
		}
	}
}


