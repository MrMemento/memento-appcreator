
package com.memento.flash.display {

	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.DisplayObject;
	import flash.display.Bitmap;
	import flash.events.Event;
	import flash.display.Loader;
	import flash.net.URLRequest;
	import flash.system.LoaderContext;

	import fl.transitions.Tween;
	import fl.transitions.easing.Regular;

	import com.memento.flash.display.AlphaGrad;

	public dynamic class ReflectionPic extends MovieClip {

		private var _pic:Loader;
		private var _reflection:Loader;
		private var _desiredWidth:Number;
		private var _desiredHeight:Number;
		private var _picTween:Tween;
		private var _reflectionTween:Tween;

		public function ReflectionPic(url:String, myWidth:Number, myHeight:Number, reflect:Boolean=true, reflectionHeight:Number=20, slide:Boolean = true):void {

			super();

			_desiredWidth  = myWidth;
			_desiredHeight = myHeight;

			_pic = new Loader();
			_pic.contentLoaderInfo.addEventListener(Event.COMPLETE,
				function (event:Event):void {
					var target:DisplayObject = event.currentTarget.content;
					(target as Bitmap).smoothing = true;
		
					if (target.width > _desiredWidth) {
						target.width = _desiredWidth;
						target.scaleY = target.scaleX;
					}
					if (target.height > _desiredHeight) {
						target.height = _desiredHeight;
						target.scaleX = target.scaleY;
					}
		
					target.x = (_desiredWidth -  target.width)  /2;
					target.y = (_desiredHeight - target.height) /2;
				}
			);
			_pic.load(new URLRequest(url), new LoaderContext(true));
			addChild(_pic);


			var picMask:Sprite = new Sprite();
			with (picMask.graphics) {
				beginFill(0);
				drawRect(0, 0, _desiredWidth, _desiredHeight);
				endFill();
			}

			_pic.mask = picMask;

			if (slide) _pic.y = _desiredHeight;

			addChild(_pic);
			addChild(picMask);

			hitArea = picMask;

			if (reflect) {

				_reflection               = new Loader();
				_reflection.mouseEnabled  = false;
				_reflection.mouseChildren = false;
				_reflection.contentLoaderInfo.addEventListener(Event.INIT,
					function (event:Event):void {
						var target:DisplayObject = event.currentTarget.content;
						(target as Bitmap).smoothing = true;
						if (target.width > _desiredWidth) {
							target.width  = _desiredWidth;
							target.scaleY = target.scaleX;
						}
						if (target.height > _desiredHeight) {
							target.height = _desiredHeight;
							target.scaleX = target.scaleY;
						}
						target.x = (_desiredWidth -  target.width)  /2;
						target.y = (_desiredHeight - target.height) /2;
					}
				);
				_reflection.load(new URLRequest(url), new LoaderContext(true));
				_reflection.scaleY        = -1;
				_reflection.y             = slide ? (_desiredHeight + 2) : (_desiredHeight*2 + 2);
				_reflection.alpha         = 0.4;
				_reflection.cacheAsBitmap = true;

				var reflectionMask:Sprite    = new AlphaGrad(_desiredWidth, reflectionHeight);
				reflectionMask.mouseEnabled  = false;
				reflectionMask.mouseChildren = false;
				reflectionMask.cacheAsBitmap = true;
				reflectionMask.y             = height + 2;
				_reflection.mask              = reflectionMask;

				addChild(_reflection);
				addChild(reflectionMask);
			}
		}

		public function startAnim():void {

			_picTween = new Tween(_pic, 'y', Regular.easeOut, _pic.y, 0, 0.5, true);
			if (_reflection) _reflectionTween = new Tween(_reflection, 'y', Regular.easeOut, _reflection.y, (_desiredHeight*2 + 2), 0.5, true);
		}

		public function resetAnim():void {

			_pic.y = _desiredHeight;
			if (_reflection) _reflection.y = _desiredHeight + 2;
		}

		public function get percentage():Number {

			return (_pic.contentLoaderInfo.bytesTotal != 0) ? (_pic.contentLoaderInfo.bytesLoaded / _pic.contentLoaderInfo.bytesTotal) : (0);
		}
	}
}