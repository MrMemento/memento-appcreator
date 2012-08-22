


package com.memento.flash.display {



	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.text.AntiAliasType;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.events.MouseEvent;



	public class Calendar extends MovieClip {



		public static var DATE_CHANGE:String = "DateHasChanged";

		private var _monthsOfYear:Array = new Array("Január",
													"Február",
													"Március",
													"Április",
													"Május",
													"Június",
													"Júlis",
													"Augusztus",
													"Szeptember",
													"Október",
													"November",
													"December");
        private var _daysOfWeek:Array = new Array("Vasárnap",
													"Hétfő",
													"Kedd",
													"Szerda",
													"Csütörtök",
													"Péntek",
													"Szombat");
        private var _daysOfMonths:Array = new Array(31,
													28,
													31,
													30,
													31,
													30,
													31,
													31,
													30,
													31,
													30,
													31);

		private var _showDate:Date;



		public function Calendar(dateToShow:Date = null):void {

			while(numChildren>0) removeChild(getChildAt(0));
			graphics.clear();

			if (dateToShow) {

				showDate(dateToShow);
			} else {

				showDate(new Date());
			}
		}

		public function showDate(dateToShow:Date):void {

			while (numChildren>0) removeChild(getChildAt(0));
			graphics.clear();

			_showDate = dateToShow;

			publishHeader();
			publishDays();
		}

		private function publishHeader() {

			with (graphics) {
				lineStyle(0, 0, 0);
				beginFill(0xbbd5e4);
				drawRoundRect(0, 45, 150, 20, 6);
				endFill();
			}

			// texts
			//
			var format:TextFormat = new TextFormat();
			format.size           = 45;
			format.color          = 0xe8e8e8;
			format.font           = "Skia";

			var year:TextField = new TextField();
			year.mouseEnabled  = false;
			year.embedFonts    = true;
			year.antiAliasType = AntiAliasType.NORMAL;
			year.autoSize      = TextFieldAutoSize.LEFT;
			year.text          = _showDate.getFullYear().toString();
			year.setTextFormat(format);
			year.x             = (150 - year.width) / 2;
			addChild(year);

			format.color = 0xFFFFFF;
			format.size  = 14;

			var month:TextField = new TextField();
			month.mouseEnabled  = false;
			month.embedFonts    = true;
			month.antiAliasType = AntiAliasType.NORMAL;
			month.autoSize      = TextFieldAutoSize.LEFT;
			month.text          = _monthsOfYear[_showDate.getMonth()];
			month.setTextFormat(format);
			month.x             = (150 - month.width) / 2;
			month.y             = 45;
			addChild(month);

			var stepLeft:Sprite = new Sprite();
			stepLeft.x          = 5;
			stepLeft.y          = 45;
			stepLeft.buttonMode = true;
			with (stepLeft.graphics) {
				lineStyle(0, 0, 0);
				beginFill(0, 0);
				drawRect(0, 0, 20, 20);
				endFill();
				moveTo(15, 5);
				beginFill(0xFFFFFF, 1);
				lineTo(15, 15);
				lineTo(5,  10);
				lineTo(15, 5);
				endFill();
			}
			stepLeft.addEventListener(MouseEvent.CLICK, stepMonthMinus);
			addChild(stepLeft);

			var stepRight:Sprite = new Sprite();
			stepRight.x          = 125;
			stepRight.y          = 45;
			stepRight.buttonMode = true;
			with (stepRight.graphics) {
				lineStyle(0, 0, 0);
				beginFill(0, 0);
				drawRect(0, 0, 20, 20);
				endFill();
				moveTo(5, 5);
				beginFill(0xFFFFFF, 1);
				lineTo(5, 15);
				lineTo(15,  10);
				lineTo(5, 5);
				endFill();
			}
			stepRight.addEventListener(MouseEvent.CLICK, stepMonthPlus);
			addChild(stepRight);
		}

		private function publishDays():void {

			var dayContainer:MovieClip = new MovieClip();
			dayContainer.x             = 5;
			dayContainer.y             = 65;

			var daysNo:uint = (_showDate.getFullYear()%4 == 0 && _showDate.getFullYear()%100 != 0 && _showDate.getMonth() == 1) ? (29) : (_daysOfMonths[_showDate.getMonth()]);

			var tmpDate:Date   = new Date(_showDate.getFullYear(), _showDate.getMonth(), 1);
			var startDay:uint = tmpDate.getDay() as uint;
			(startDay == 0) ? (startDay = 6) : (startDay--);

			var row:uint = 0;

			for (var i:uint=1; i<daysNo+1; i++) {

				var thisDay:MovieClip = new MovieClip();
				thisDay.x             = startDay*20;
				thisDay.y             = row*20;
				with (thisDay.graphics) {
					lineStyle(0, 0, 0);
					beginFill(0, 0);
					drawRect(0, 0, 20, 20);
					endFill();
				}

				var format:TextFormat = new TextFormat();
				format.size           = 12;
				format.font           = "Skia";

				if (i == _showDate.getDate()) {
					format.color = 0x990000;
					with (thisDay.graphics) {
						lineStyle(1, 0x666666, 1);
						beginFill(0, 0);
						drawCircle(11, 10, 9);
						endFill();
					}
				}
				else {
					format.color = 0x666666;
					thisDay.buttonMode    = true;
					thisDay.addEventListener(MouseEvent.CLICK, dayClick);
				}

				var dayText:TextField = new TextField();
				dayText.mouseEnabled  = false;
				dayText.embedFonts    = true;
				dayText.antiAliasType = AntiAliasType.ADVANCED;
				dayText.autoSize      = TextFieldAutoSize.LEFT;
				dayText.text          = i.toString();
				dayText.setTextFormat(format);
				dayText.x             = (thisDay.width - dayText.width) / 2;
				thisDay.addChild(dayText);

				dayContainer.addChild(thisDay);

				startDay++;
				if (startDay >= 7) {
					startDay = 0;
					row++;
				}
			}

			addChild(dayContainer);
		}

		private function stepMonthMinus(event:MouseEvent):void {

			var year:int   = _showDate.getFullYear();
			var month:uint = _showDate.getMonth();
			var day:uint   = _showDate.getDate();

			if (month == 0) {
				month = 11;
				year--;
			}
			else {
				month--;
			}

			var tmpDate:Date = new Date(year, month, day);
			showDate(tmpDate);
			dispatchEvent(new CustomEvent(DATE_CHANGE, tmpDate));
		}

		private function stepMonthPlus(event:MouseEvent):void {

			var year:int   = _showDate.getFullYear();
			var month:uint = _showDate.getMonth();
			var day:uint   = _showDate.getDate();

			if (month == 11) {
				month = 0;
				year++;
			}
			else {
				month++;
			}

			var tmpDate:Date = new Date(year, month, day);
			showDate(tmpDate);
			dispatchEvent(new CustomEvent(DATE_CHANGE, tmpDate));
		}

		private function dayClick(event:MouseEvent):void {

			var target:TextField = event.currentTarget.getChildAt(0) as TextField;

			var year:int   = _showDate.getFullYear();
			var month:uint = _showDate.getMonth();
			var day:uint   = parseInt(target.text);

			var tmpDate:Date = new Date(year, month, day);
			showDate(tmpDate);
			dispatchEvent(new CustomEvent(DATE_CHANGE, tmpDate));
		}
	}
}