
package com.memento.flash.display.design {

	import flash.display.Sprite;
	import flash.filters.BitmapFilter;
	import flash.filters.BevelFilter;
	import flash.filters.GlowFilter;
	import flash.filters.BitmapFilterQuality;
	import flash.filters.BitmapFilterType;

	public class BGR extends Sprite {

		public function BGR(width:Number, height:Number):void {

			super();

			with (graphics) {
				lineStyle(0, 0, 0);
				beginFill(0, 0);
				drawRect(0, 0, width, height);
				endFill();
				beginFill(0xEEEEEE);
				drawRoundRect(5, 5, width-10, height-10, 5);
				endFill();
			}

			var bevel:BitmapFilter = getBevelFilter();
			var glow:BitmapFilter  = getGlowFilter();

			filters = [bevel, glow];
		}

		public function setSize(width:Number, height:Number):void {

			with (graphics) {
				clear();
				lineStyle(0, 0, 0);
				beginFill(0, 0);
				drawRect(0, 0, width, height);
				endFill();
				beginFill(0xEEEEEE);
				drawRoundRect(5, 5, width-10, height-10, 5);
				endFill();
			}
		}

		private function getBevelFilter():BitmapFilter {

			var distance:Number       = 2;
			var angleInDegrees:Number = 45;
			var highlightColor:Number = 0xFFFFFF;
			var highlightAlpha:Number = 0.8;
			var shadowColor:Number    = 0x000000;
			var shadowAlpha:Number    = 0.8;
			var blurX:Number          = 2;
			var blurY:Number          = 2;
			var strength:Number       = 1;
			var quality:Number        = BitmapFilterQuality.LOW;
			var type:String           = BitmapFilterType.INNER;
			var knockout:Boolean      = false;

			return new BevelFilter(	distance,
									angleInDegrees,
									highlightColor,
									highlightAlpha,
									shadowColor,
									shadowAlpha,
									blurX,
									blurY,
									strength,
									quality,
									type,
									knockout );
		}

		private function getGlowFilter():BitmapFilter {

			var color:Number     = 0x000000;
			var alpha:Number     = 1;
			var blurX:Number     = 5;
			var blurY:Number     = 5;
			var strength:Number  = 0.8;
			var inner:Boolean    = false;
			var knockout:Boolean = false;
			var quality:Number   = BitmapFilterQuality.LOW;

			return new GlowFilter(	color,
									alpha,
									blurX,
									blurY,
									strength,
									quality,
									inner,
									knockout);
		}
	}
}