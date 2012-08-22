package com.memento.flash.process {

	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.geom.Matrix;
	import flash.geom.ColorTransform;
	import flash.display.MovieClip;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Graphics;
	import flash.display.Stage;
	import flash.display.Shape;
	import flash.filters.DisplacementMapFilter;

	public class TrapezoidTransform extends MovieClip {

		public var point:Point;
		public var target:MovieClip = new MovieClip();
		public var bitmap:BitmapData;
		public var debugMe:Boolean;

		public function TrapezoidTransform(mc:MovieClip, debug:Boolean=false) {

			debugMe = debug;

			traceBug("- constructor start");

			target = mc;
			point  = new Point(0, 0);
			bitmap = new BitmapData(10, 10, false, 0x800000);

			traceBug("constructor end");
		}

		private function traceBug(str:String) {
			if (debugMe) trace(str);
		}

		public function setHorizontalTrapezoid(newX:Number, newY:Number, trapeze:Object) {

			var flipped:Boolean    = false;
			var leftSided:Boolean  = false;
			var newWidth:Number    = trapeze.width;
			var heightLeft:Number  = trapeze.leftHeight;
			var heightRight:Number = trapeze.rightHeight;

			if (heightLeft < heightRight) {
				leftSided = true;
			}

			if (newWidth < 0) {
				flipped  = true;
				newWidth = -newWidth;
			}

			target.x      = newX;
			target.y      = (flipped) ? (newY - (heightRight-heightLeft)/2) : (newY);
			target.width  = newWidth;
			target.height = (leftSided) ? (heightRight) : (heightLeft);

			if (flipped) {
				target.scaleX = -target.scaleX;
			}

			var myHeight:Number = Math.abs(heightLeft - heightRight);

			this.recreateHorizontalBmp(newWidth+2, heightLeft+2, heightRight+2, ((flipped)?(!leftSided):(leftSided)));

			var myFilter   = new DisplacementMapFilter(bitmap, point, 0, 1, 0, myHeight, "color", 0, 0);
			target.filters = new Array(myFilter);
		}

		private function recreateHorizontalBmp(newWidth:Number, heightLeft:Number, heightRight:Number, reverse:Boolean) {

			traceBug("- bitmap creation start");

			var maxHeight:Number = Math.max(heightLeft, heightRight);
			var minHeight:Number = Math.min(heightLeft, heightRight);
			var difHeight:Number = maxHeight - minHeight;

			bitmap.dispose();
			bitmap = new BitmapData(newWidth, maxHeight, false, 0xFF0000);

			bitmap.fillRect(new Rectangle(0, 0, newWidth, bitmap.height/2),0);

			var tempClip:MovieClip = new MovieClip();
			tempClip.x = tempClip.y = -100;

			var matrix = new Matrix();

			matrix.createGradientBox(newWidth, minHeight, 1.570796E+000, 0, difHeight/2);

			traceBug("  - graphics start");

			var child1:Shape = new Shape();

			child1.graphics.beginGradientFill("linear", [128, 16711808], [100, 100], [0, 255], matrix, "pad");
			child1.graphics.moveTo(0,        difHeight/2);
			child1.graphics.lineTo(newWidth, difHeight/2);
			child1.graphics.lineTo(newWidth, difHeight/2 + minHeight);
			child1.graphics.lineTo(0,        difHeight/2 + minHeight);
			child1.graphics.lineTo(0,        difHeight/2);
			child1.graphics.endFill();

			tempClip.addChild(child1);

			matrix.createGradientBox(newWidth, maxHeight, 0, 0, 0);

			var child2:Shape = new Shape();

			if (!reverse) {
				child2.graphics.beginGradientFill("linear", [8388608, 8388608], [100, 0], [0, 255], matrix, "pad");
			}
			else {
				child2.graphics.beginGradientFill("linear", [8388608, 8388608], [0, 100], [0, 255] ,matrix, "pad");
			}
			child2.graphics.moveTo(0,        0);
			child2.graphics.lineTo(newWidth, 0);
			child2.graphics.lineTo(newWidth, maxHeight);
			child2.graphics.lineTo(0,        maxHeight);
			child2.graphics.lineTo(0,        0);
			child2.graphics.endFill();

			tempClip.addChild(child2);

			traceBug("  graphics end");

			bitmap.draw(tempClip, new Matrix(), new ColorTransform(), "normal", bitmap.rect, true);

			traceBug("bitmap creation end");

			if(debugMe) {

				trace("- adding map");

				if (this.stage != null) {

					this.stage.addChild(tempClip);
					trace("map added");
				}
				else {
					trace("map could not be added, no stage property of target mc");
				}
			}
		}
	}
}