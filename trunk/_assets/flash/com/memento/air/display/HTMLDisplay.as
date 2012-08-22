

package com.memento.air.display {



	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.display.MovieClip;
	import flash.geom.Rectangle;
	import flash.utils.Timer;

	import flash.html.HTMLLoader;



	public class HTMLDisplay extends Sprite {



		public static var ScrollBgr:Class;
		public static var ScrollArr:Class;
		public static var ScrollBtn:Class;
		public static var ScrollGpr:Class;

		private var _scrDifV:Number;
		private var _scrDifH:Number;
		private var _jumpGapV:Number;
		private var _jumpGapH:Number;

		private var _scrH:Boolean = false;
		private var _scrV:Boolean = false;

		// maybe we want to stop the timer,
		// before it is instantiated
		private var _scrTimer:Timer = new Timer(30);

		private var _uArr:MovieClip;
		private var _dArr:MovieClip;
		private var _bgrV:MovieClip;
		private var _btnV:MovieClip;
		private var _vScroll:MovieClip;

		private var _lArr:MovieClip;
		private var _rArr:MovieClip;
		private var _bgrH:MovieClip;
		private var _btnH:MovieClip;
		private var _hScroll:MovieClip;

		private var _htmlLoader:HTMLLoader;

		//private var notfy:Function = MovieClip(parent).notify;



		public function HTMLDisplay(widthNum:Number = 0, heightNum:Number = 0):void {

			_htmlLoader                         = new HTMLLoader();
			_htmlLoader.paintsDefaultBackground = false;
			_htmlLoader.textEncodingFallback     = "ISO-8859-2";

			_htmlLoader.addEventListener(Event.HTML_BOUNDS_CHANGE, boundsChange);
			_htmlLoader.addEventListener(Event.SCROLL,             scrollContent);
			//_htmlLoade.addEventListener(Event.HTML_RENDER, htmlRender);
			//_htmlLoader.addEventListener(Event.LOCATION_CHANGE, locationChange);
			//_htmlLoader.addEventListener(Event.HTML_DOM_INITIALIZE, domInit);
			//_htmlLoader.addEventListener(Event.COMPLETE, loadComplete);
/*
			_htmlLoader.addEventListener(MouseEvent.MOUSE_WHEEL,
				function (event:MouseEvent):void {
					MovieClip(parent).notify(_htmlLoader.scrollV.toString());
				}
			);
*/
			addChild(_htmlLoader);
		}



		private function boundsChange(event:Event):void {

			setSize(_htmlLoader.width, _htmlLoader.height);
		}
/*
		private function htmlRender(event:Event):void {

			setSize(_htmlLoader.width, _htmlLoader.height);
		}

		private function locationChange(event:Event):void {

			//
		}

		private function domInit(event:Event):void {

			//
		}

		private function loadComplete(event:Event):void {

			//setSize(_mask.width, _mask.height);
		}
*/


		public function setPlace(horizontal:Number, vertical:Number):void {
			x = horizontal;
			y = vertical;
		}

		public function setSize(widthNum:Number, heightNum:Number):void {

			if (widthNum < 2880 && widthNum >= 0) {
				_htmlLoader.width = widthNum;
			}
			else if (widthNum < 0) {
				_htmlLoader.width = 0;
			}
			else {
				_htmlLoader.width = 2880;
			}

			if (heightNum < 2880 && heightNum >= 0) {
				_htmlLoader.height = heightNum;
			}
			else if (heightNum < 0) {
				_htmlLoader.width = 0;
			}
			else {
				_htmlLoader.height = 2880;
			}

			createScrolls();
		}



		private function createScrolls():void {

			_scrH = true;
			_scrV = true;

			var ref:MovieClip = new ScrollArr();

			while (numChildren>1) removeChildAt(1);

			if (	(_htmlLoader.height > _htmlLoader.contentHeight) ||
					(_htmlLoader.height < 3*ref.height) ||
					(_htmlLoader.width < ref.width) ) {

				_scrV = false;
				//removeEventListener(MouseEvent.MOUSE_WHEEL, scrollContent);
			}

			if (	(_htmlLoader.width > _htmlLoader.contentWidth) ||
					(_htmlLoader.width < 3*ref.width)  ||
					(_htmlLoader.height  < ref.height) ) {

				_scrH = false;
			}

			if (_scrV && _scrH) {

				initVerticalSroll(true);
				initHorizontalSroll(true);
			}
			else if (_scrV) {

				initVerticalSroll();
			}
			else if (_scrH) {

				initHorizontalSroll(true);
			}
		}



		// display scrollbars
		//
		private function initVerticalSroll(bothScroll:Boolean = false):void {

			_uArr            = new ScrollArr();

			_uArr.addEventListener(MouseEvent.MOUSE_DOWN, scrollUp);

			_dArr            = new ScrollArr();
			_dArr.scaleY     = -1;
			_dArr.y          = _htmlLoader.height - (bothScroll ? _dArr.height : 0);

			_dArr.addEventListener(MouseEvent.MOUSE_DOWN, scrollDown);

			_bgrV            = new ScrollBgr();
			_bgrV.height     = _htmlLoader.height - (bothScroll ? (3*_uArr.height) : (2*_uArr.height));
			_bgrV.y          = _uArr.height;

			_btnV            = new ScrollBtn();
			_btnV.height     = _htmlLoader.height * _bgrV.height /_htmlLoader.contentHeight;



			var thePlace:Number = _bgrV.height * _htmlLoader.scrollV / _htmlLoader.contentHeight;
			_btnV.y             = _uArr.height + ( (thePlace>0) ? (thePlace) : (0) );



			_btnV.addEventListener(MouseEvent.MOUSE_DOWN, scrollDragV);

			_vScroll   = new MovieClip();
			_vScroll.x = _htmlLoader.width - _uArr.width;
			_vScroll.addChild(_bgrV);
			_vScroll.addChild(_uArr);
			_vScroll.addChild(_dArr);
			_vScroll.addChild(_btnV);

			_scrDifV  = _htmlLoader.contentHeight - _htmlLoader.height;
			_jumpGapV = _btnV.height / 10;

			super.addChild(_vScroll);
		}

		private function initHorizontalSroll(bothScroll:Boolean = false):void {

			_lArr            = new ScrollArr();
			_lArr.rotation   = -90;
			// are height and width relative to stage or to object???
			// if (BUG) try {changing 'em}
			_lArr.y          = _lArr.height; //_lArr.width

			_lArr.addEventListener(MouseEvent.MOUSE_DOWN, scrollLeft);

			_rArr            = new ScrollArr();
			_rArr.rotation   = 90;
			_rArr.x          = _htmlLoader.width - (bothScroll ? _rArr.width : 0);

			_rArr.addEventListener(MouseEvent.MOUSE_DOWN, scrollRight);

			_bgrH            = new ScrollBgr();
			_bgrH.x          = _lArr.height;
			_bgrH.y          = _bgrH.width;
			_bgrH.height     = _htmlLoader.width - (bothScroll ? (3*_lArr.height) : (2*_lArr.height));
			_bgrH.rotation   = -90;

			_btnH            = new ScrollBtn();
			_btnH.height      = _htmlLoader.width* _bgrH.width / _htmlLoader.contentWidth;
			_btnH.rotation   = -90;

			var thePlace:Number = _bgrH.width * _htmlLoader.scrollH / _htmlLoader.contentWidth;
			_btnH.x             = _lArr.width + ( (thePlace>0) ? (thePlace) : (0) );
			_btnH.y             = _btnH.height;

			_btnH.addEventListener(MouseEvent.MOUSE_DOWN, scrollDragH);

			_hScroll   = new MovieClip();
			_hScroll.y = _htmlLoader.height - _lArr.height;
			_hScroll.addChild(_bgrH);
			_hScroll.addChild(_lArr);
			_hScroll.addChild(_rArr);
			_hScroll.addChild(_btnH);

			_scrDifH  = _htmlLoader.contentWidth - _htmlLoader.width;
			_jumpGapH = _btnH.width / 10;

			super.addChild(_hScroll);
		}



		

		// mouse wheel for vertical scrollbar
		//
		private function scrollContent(event:Event):void {

			if (_scrV) {

				var thePlace:Number = _bgrV.height * _htmlLoader.scrollV / _htmlLoader.contentHeight;
				_btnV.y             = _uArr.height + ( (thePlace>0) ? (thePlace) : (0) );
			}

			if (_scrH) {

				thePlace            = _bgrH.width * _htmlLoader.scrollH / _htmlLoader.contentWidth;
				_btnH.x             = _lArr.width + ( (thePlace>0) ? (thePlace) : (0) );
			}
		}



		// scroll movers
		//
		private function scrollUp(event:Event):void {

			stage.addEventListener(MouseEvent.MOUSE_UP, stopUp);
			stage.addEventListener(Event.MOUSE_LEAVE,   stopUp);

			doUp();

			_scrTimer = new Timer(100);
			_scrTimer.addEventListener(TimerEvent.TIMER, doUp);
			_scrTimer.start();
		}

		private function doUp(event:* = null):void {

			if (_btnV.y > _uArr.height + _jumpGapV) {

				_btnV.y -= _jumpGapV;
			}
			else {

				_btnV.y = _uArr.height;
				_scrTimer.stop();
			}

			placeContentY();
		}

		private function stopUp(event:* = null):void {

			_scrTimer.stop();
			placeContentY();

			stage.removeEventListener(MouseEvent.MOUSE_UP, stopUp);
			stage.removeEventListener(Event.MOUSE_LEAVE,   stopUp);
		}



		private function scrollDown(event:Event):void {

			stage.addEventListener(MouseEvent.MOUSE_UP, stopDown);
			stage.addEventListener(Event.MOUSE_LEAVE,   stopDown);

			doDown();

			_scrTimer = new Timer(100);
			_scrTimer.addEventListener(TimerEvent.TIMER, doDown);
			_scrTimer.start();
		}

		private function doDown(event:* = null):void {

			if (_btnV.y < _dArr.y - _dArr.height - _btnV.height - _jumpGapV) {

				_btnV.y += _jumpGapV;
			}
			else {

				_btnV.y = _dArr.y - _dArr.height - _btnV.height;
				_scrTimer.stop();
			}

			placeContentY();
		}

		private function stopDown(event:* = null):void {

			_scrTimer.stop();
			placeContentY();

			stage.removeEventListener(MouseEvent.MOUSE_UP, stopDown);
			stage.removeEventListener(Event.MOUSE_LEAVE,   stopDown);
		}



		private function scrollLeft(event:Event):void {

			stage.addEventListener(MouseEvent.MOUSE_UP, stopLeft);
			stage.addEventListener(Event.MOUSE_LEAVE,   stopLeft);

			doLeft();

			_scrTimer = new Timer(100);
			_scrTimer.addEventListener(TimerEvent.TIMER, doLeft);
			_scrTimer.start();
		}

		private function doLeft(event:* = null):void {

			if (_btnH.x > _lArr.width + _jumpGapH) {

				_btnH.x   -= _jumpGapV;
			}
			else {

				_btnH.x    = _lArr.width;
				_scrTimer.stop();
			}

			placeContentX();
		}

		private function stopLeft(event:* = null):void {

			_scrTimer.stop();
			placeContentX();

			stage.removeEventListener(MouseEvent.MOUSE_UP, stopLeft);
			stage.removeEventListener(Event.MOUSE_LEAVE,   stopLeft);
		}



		private function scrollRight(event:Event):void {

			stage.addEventListener(MouseEvent.MOUSE_UP, stopRight);
			stage.addEventListener(Event.MOUSE_LEAVE,   stopRight);

			doRight();

			_scrTimer = new Timer(100);
			_scrTimer.addEventListener(TimerEvent.TIMER, doRight);
			_scrTimer.start();
		}

		private function doRight(event:* = null):void {

			if (_btnH.x < _rArr.x - _rArr.width - _btnH.width - _jumpGapV) {

				_btnH.x += _jumpGapV;
			}
			else {

				_btnH.x = _rArr.x - _rArr.width - _btnH.width;
				_scrTimer.stop();
			}

			placeContentX();
		}

		private function stopRight(event:* = null):void {

			_scrTimer.stop();
			placeContentX();

			stage.removeEventListener(MouseEvent.MOUSE_UP, stopRight);
			stage.removeEventListener(Event.MOUSE_LEAVE,   stopRight);
		}



		private function scrollDragV(event:Event):void {

			stage.addEventListener(MouseEvent.MOUSE_UP, stopDragV);
			stage.addEventListener(Event.MOUSE_LEAVE,   stopDragV);

			_scrTimer = new Timer(30);
			_scrTimer.addEventListener(TimerEvent.TIMER, placeContentY);
			_scrTimer.start();

			_btnV.startDrag(false, new Rectangle(_bgrV.x, _bgrV.y, 0, _bgrV.height - _btnV.height));
		}

		private function stopDragV(event:* = null) {

			stage.removeEventListener(MouseEvent.MOUSE_UP, stopDragV);
			stage.removeEventListener(Event.MOUSE_LEAVE,   stopDragV);

			_scrTimer.stop();

			_btnV.stopDrag();
			placeContentY();
		}



		private function scrollDragH(event:Event):void {

			stage.addEventListener(MouseEvent.MOUSE_UP, stopDragH);
			stage.addEventListener(Event.MOUSE_LEAVE,   stopDragH);

			_scrTimer = new Timer(30);
			_scrTimer.addEventListener(TimerEvent.TIMER, placeContentX);
			_scrTimer.start();

			_btnH.startDrag(false, new Rectangle(_bgrH.x, _bgrH.y, _bgrH.width - _btnH.width, 0));
		}

		private function stopDragH(event:* = null) {

			stage.removeEventListener(MouseEvent.MOUSE_UP, stopDragH);
			stage.removeEventListener(Event.MOUSE_LEAVE,   stopDragH);

			_scrTimer.stop();

			_btnH.stopDrag();
			placeContentY();
		}



		private function placeContentY(event:* = null):void {

			_htmlLoader.scrollV = -(_bgrV.y - _btnV.y) / (_bgrV.height-_btnV.height) * _scrDifV;
		}

		private function placeContentX(event:* = null):void {

			_htmlLoader.scrollH = -(_bgrH.x - _btnH.x) / (_bgrH.width - _btnH.width) * _scrDifH;
		}



		public function get htmlLoader():HTMLLoader {
			return _htmlLoader;
		}
	}
}
