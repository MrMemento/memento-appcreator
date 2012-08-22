package com.memento.flash.process {
	
	import flash.display.BitmapData;
	import flash.events.*;
	import flash.utils.*;
	
	public class GetPixelArray extends EventDispatcher {
		
		private var _bmpSource:BitmapData;
		private var _arrPixels:Array;
		private var _strPixels:String;
		private var _numPixTotal:Number;
		private var _numPixProg:Number;
		private var _numProgPerc:Number;
		private var _numIntDelay:Number;
		
		// Constructor
		public function GetPixelArray(set_bmpSource:BitmapData) {
			super(this);
			_bmpSource = set_bmpSource;
			_numPixTotal = new Number(_bmpSource.width * _bmpSource.height);
			_numPixProg = new Number(0);
			_numProgPerc = new Number(0);
		}
		
		// Get progress percent complete
		public function get percent():Number {
			return _numProgPerc;
		}
		
		// Get pixel array
		public function get data():Array {
			return _arrPixels;
		}
		
		// Get pixel array in a string format
		public function get dataString():String {
			return _strPixels;
		}
		
		// Called to start processing bitmap
		public function process():void {
			_arrPixels = new Array();
			_strPixels = new String("");
			// Delay the parsing so that the app does not freeze up
			_numIntDelay = setInterval(getPixelRow, 5);
		}
		
		// Process pixel info from bitmapData object, one row at a time, store non-white pixels in array
		private function getPixelRow():void {
			var numPixel:uint = new uint(0);
			var strPixel:String = new String("");
			_arrPixels.push(new Array());
			var h:Number = _arrPixels.length - 1;
			// Parse bitmap data
			for (var w:Number = 0; w < _bmpSource.width; w++) {
				numPixel = _bmpSource.getPixel(w, h);
				// Ignore white pixels, store colored
				strPixel = (numPixel == 0xFFFFFF) ? "" : numPixel.toString(16);
				_strPixels += strPixel;
				_arrPixels[h].push(strPixel);
				_numPixProg++;
				if (w < _bmpSource.width - 1) {
					_strPixels += ",";
				}
			}
			// Report progress one row at a time
			_numProgPerc = Math.round((_numPixProg / _numPixTotal) * 100);										
			dispatchEvent(new Event("progress"));
			// Done processing
			if (_numPixProg == _numPixTotal) {
				clearInterval(_numIntDelay);
				dispatchEvent(new Event("complete"));
			} else {
				_strPixels += "|";
			}
		}
	}
}