

package com.memento.flash.animation {



	import flash.display.DisplayObject;
	import flash.events.EventDispatcher;
	import flash.events.Event;



	public class EventFader extends EventDispatcher {



		public static const FADED_IN:String  = 'fadedIn';
		public static const FADED_OUT:String = 'fadedOut';

		public static var ALPHA_STEP:Number = 0.1;



		public function EventFader():void {
		}



		public static function fadeIn(target:DisplayObject):void {

			target.removeEventListener(Event.ENTER_FRAME, EventFader.enterFrameFadeOut);
			target.removeEventListener(Event.ENTER_FRAME, EventFader.enterFrameFadeIn);
			target.addEventListener(Event.ENTER_FRAME,    EventFader.enterFrameFadeIn);
		}

		public static function fadeOut(target:DisplayObject):void {

			target.removeEventListener(Event.ENTER_FRAME, EventFader.enterFrameFadeOut);
			target.removeEventListener(Event.ENTER_FRAME, EventFader.enterFrameFadeIn);
			target.addEventListener(Event.ENTER_FRAME,    EventFader.enterFrameFadeOut);
		}



		public static function enterFrameFadeIn(event:Event):void {

			if (event.currentTarget.alpha <= 1 - ALPHA_STEP) {

				event.currentTarget.alpha += ALPHA_STEP;
			}
			else {

				event.currentTarget.alpha = 1;
				event.currentTarget.removeEventListener(Event.ENTER_FRAME, enterFrameFadeIn);
				event.currentTarget.dispatchEvent(new Event(FADED_IN));
			}
		}

		public static function enterFrameFadeOut(event:Event):void {

			if (event.currentTarget.alpha >= ALPHA_STEP) {

				event.currentTarget.alpha -= ALPHA_STEP;
			}
			else {

				event.currentTarget.alpha = 0;
				event.currentTarget.removeEventListener(Event.ENTER_FRAME, enterFrameFadeOut);
				event.currentTarget.dispatchEvent(new Event(FADED_OUT));
			}
		}
	}
}