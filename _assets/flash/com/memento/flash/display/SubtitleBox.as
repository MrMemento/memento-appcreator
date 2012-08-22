
package com.flash.display {

	import flash.display.Sprite;

	import flash.text.AntiAliasType;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFieldType;
	import flash.text.TextFormat;
	import flash.text.TextFormatAlign;

	import flash.filters.BitmapFilter;
	import flash.filters.GlowFilter;

	import flash.events.Event;

	public class SubtitleBox extends Sprite {

		public static const VISIBLE:String   = "SubtitleVisible";
		public static const INVISIBLE:String = "SubtitleInvisible";

		private var _subtitle:TextField;
		private var _gap:Number;
		private var _status:String;

		public function SubtitleBox(subWidth:Number = 400, subHeight:Number = 35, subGap:Number = 5):void {

			_gap = subGap;

			var format:TextFormat = new TextFormat();
			format.size           = 13;
			format.color          = 0xFFFFFF;
			format.font           = "Arial";
			format.align          = TextFormatAlign.CENTER;

			_subtitle                   = new TextField();
			_subtitle.mouseEnabled      = false;
			_subtitle.antiAliasType     = AntiAliasType.NORMAL;
			_subtitle.multiline         = true;
			_subtitle.wordWrap          = true;
			_subtitle.defaultTextFormat = format;
			_subtitle.x                 = _gap;
			_subtitle.y                 = _gap;

			_subtitle.filters = [
				new GlowFilter(0, 1, 2, 2)
			];

			addChild(_subtitle);

			setSize(subWidth, subHeight);

			alpha = 0;
			_status = INVISIBLE;
		}

		public function setSize(subWidth:Number, subHeight:Number = 35):void {

			with (graphics) {

				clear();
				lineStyle(0, 0, 0);
				beginFill(0, 0.4);
				drawRect(0, 0, subWidth, subHeight);
				endFill();
			}

			_subtitle.width  = subWidth  - 2*_gap;
			_subtitle.height = subHeight - 2*_gap;
		}

		public function set gap(subGap:Number):void {

			_subtitle.x      = _gap;
			_subtitle.y      = _gap;
			_subtitle.width  = width  - 2*_gap;
			_subtitle.height = height - 2*_gap;
		}

		public function set text(str:String):void {
			_subtitle.text = str;
		}

		private function fadePlus(event:Event):void {
			if (alpha < 1) {
				alpha += 0.2;
			}
			else {
				alpha = 1;
				removeEventListener(Event.ENTER_FRAME, fadePlus);
			}
		}

		private function fadeMinus(event:Event):void {
			if (alpha > 0) {
				alpha -= 0.2;
			}
			else {
				alpha = 0;
				removeEventListener(Event.ENTER_FRAME, fadeMinus);
			}
		}

		public function get status():String {
			return _status;
		}

		public function fadeIn():void {
			removeEventListener(Event.ENTER_FRAME, fadeMinus);
			addEventListener(Event.ENTER_FRAME,    fadePlus);
			_status = VISIBLE;
		}

		public function fadeOut():void {
			removeEventListener(Event.ENTER_FRAME, fadePlus);
			addEventListener(Event.ENTER_FRAME,    fadeMinus);
			_status = INVISIBLE;
		}
	}
}