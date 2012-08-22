
package com.memento.flash.display {

	import flash.display.Sprite;
	import flash.geom.Matrix;
	import flash.display.GradientType;

	public dynamic class AlphaGrad extends Sprite {

		public function AlphaGrad(myWidth:Number, myHeight:Number, color:Number = 0):void {

			super();

			var gradMatrix:Matrix = new Matrix();
			gradMatrix.createGradientBox(myWidth, myHeight, Math.PI/2);

			with (graphics) {
				beginGradientFill(GradientType.LINEAR, [color, color], [1, 0], [0x00, 0xFF], gradMatrix);
				drawRect(0, 0, myWidth, myHeight);
				endFill();
			}

			cacheAsBitmap = true;
		}
	}
}
