﻿package com.memento.flash.display{	import flash.display.DisplayObject;	import flash.display.MovieClip;	import flash.events.Event;	import flash.events.MouseEvent;	import flash.events.KeyboardEvent;	import flash.events.TimerEvent;	import flash.utils.Timer;	import com.memento.flash.display.ScrollableClip;	import com.memento.flash.process.PlayList;	import com.memento.flash.events.CustomEvent;	public class PlayListScroller extends ScrollableClip {		public static const ITEM_INVOKE:String = "ItemInvocationOccured";		private var _hitClip:MovieClip		private var _draggedItem:int;		private var _playList:PlayList;		private var _PlayListElement:Class;		private var _listDeleteEnabled:Boolean;		private var _doubleClickTimer:Timer;		private var _lastClickedIndex:uint;		public function PlayListScroller(playList:PlayList, playListElement:Class, scrBgr:Class, scrArr:Class, scrBtn:Class, scrGpr:Class):void {			// ScrollableClip needs static init classes			// to use as the scrollbar			//			ScrollableClip.ScrollBgr = scrBgr;			ScrollableClip.ScrollArr = scrArr;			ScrollableClip.ScrollBtn = scrBtn;			// ScrollableClip			//			super();			_playList          = playList;			_PlayListElement   = playListElement;			_draggedItem       = -1;			_listDeleteEnabled = true;			_doubleClickTimer = new Timer(300, 1);			addEventListener(KeyboardEvent.KEY_DOWN, keyHandler);			addEventListener(Event.ADDED,            added);		}		private function added(event:Event):void {			_hitClip = new MovieClip();			_hitClip.addEventListener(MouseEvent.MOUSE_UP, maskUpMouse);			parent.addChildAt(_hitClip, parent.getChildIndex(this));			removeEventListener(Event.ADDED, added);		}				// mouse functions		private function overMouse(event:MouseEvent):void {			var target:MovieClip = event.currentTarget as MovieClip;			target.selectionStatus = ( target.selected ? "selected" : "mouseover" );			target.init();		}		private function outMouse(event:MouseEvent):void {			var target:MovieClip = event.currentTarget as MovieClip;			target.selectionStatus = ( target.selected ? "selected" : "deselected" );			target.init();		}		private function downMouse(event:MouseEvent):void {			var target:MovieClip = event.currentTarget as MovieClip;			var index:uint = getChildIndex(target);			_draggedItem = getChildIndex(target);			// select with mouseDown, deselect with mouseUp			//			if (!target.selected) {				target.justClicked = target.selected = _playList.list[index].selected = true;				target.selectionStatus = "selected";				target.init();			}		}		private function upMouse(event:MouseEvent):void {			var target:MovieClip = event.currentTarget as MovieClip;			var index:uint = getChildIndex(target);			if (_draggedItem != index && _draggedItem != -1) {				// mouse_up only				//				var moveArr:Array = new Array();				for (var i:uint=0; i<numChildren; i++) {					var newTarget:MovieClip = getChildAt(i) as MovieClip;					newTarget.justClicked = false;					if (newTarget.selected) moveArr.push(i);				}				_playList.moveItems(moveArr, index-_draggedItem);				renderPlayList(0, content.y);			}			else {				// click				//				if (target.selected && !target.justClicked) {					target.justClicked = target.selected = _playList.list[index].selected = false;					target.selectionStatus = "mouseover";				}				else if (target.justClicked) {					target.justClicked = false;				}				// double click				//				if (_doubleClickTimer.running) {					if (_lastClickedIndex == index) {						var prevSelected:MovieClip = getChildAt(_playList.selectionIndex) as MovieClip;						prevSelected.actual = false;						prevSelected.init();						_playList.selectItem(index);						target.actual = true;						dispatchEvent(new CustomEvent(ITEM_INVOKE, _playList.list[index]));					}					_doubleClickTimer.stop();				}				else {					_doubleClickTimer.start();				}				_lastClickedIndex = index;				target.init();			}			_draggedItem = -1;		}		private function maskUpMouse(event:MouseEvent):void {			var newTarget:MovieClip;			var i:uint;			if (_draggedItem != -1) {				// mouse_up only				//				var moveArr:Array = new Array();				for (i=0; i<numChildren; i++) {					newTarget             = getChildAt(i) as MovieClip;					newTarget.justClicked = false;					if (newTarget.selected) {						moveArr.push(i);					}				}				_playList.moveItems(moveArr, _playList.list.length-1);				renderPlayList(0, 0);				_draggedItem = -1;			}		}		public function renderPlayList(startX:Number=0, startY:Number=0):void {			var where:Number = 0;			while (numChildren>0)				removeChildAt(0);			for (var i=0; i<_playList.list.length; i++) {				var item:MovieClip      = new _PlayListElement() as MovieClip;				item.selected           = _playList.list[i].selected;				item.selectionStatus    = item.selected ? 'selected' : 'deselected';				item.actual             = (i == _playList.selectionIndex) ? true : false;				item.titleText          = _playList.list[i].videodata.title;				item.y                  = where;				item.justClicked        = false;				item.doubleClickEnabled = true;				item.init();				item.addEventListener(MouseEvent.MOUSE_OVER, overMouse);				item.addEventListener(MouseEvent.MOUSE_OUT,  outMouse);				item.addEventListener(MouseEvent.MOUSE_DOWN, downMouse);				item.addEventListener(MouseEvent.MOUSE_UP,   upMouse);				addChild(item);				where += item.height;			}			with (_hitClip.graphics) {				clear();				beginFill(0, 0);				drawRect(mask.x, mask.y, mask.width, mask.height);				endFill();			}			// check scroll			//			verticalScroll = true;			setSkin(startX, startY);		}		private function keyHandler(event:KeyboardEvent):void {			if (event.charCode == 127 && numChildren>0 && _listDeleteEnabled) {				var delArr:Array = new Array();				for (var i:uint=0; i<numChildren; i++) {					if ((getChildAt(i) as MovieClip).selected) delArr.push(i);				}				_playList.removeItems(delArr);				if (mask != null) {					if ((content.height - delArr.length*getChildAt(0).height) > mask.height) {							if (mask.height-content.y >= content.height - delArr.length * getChildAt(0).height) {								renderPlayList(0, mask.height - (content.height - delArr.length * getChildAt(0).height));						}						else {								renderPlayList(0, content.y);						}					}					else {							renderPlayList();					}				}			}		}		public function disable():void {			mouseEnabled  = false;			mouseChildren = false;		}		public function enable():void {			mouseEnabled  = true;			mouseChildren = true;		}		public function set deletable(able:Boolean):void {			_listDeleteEnabled = able;		}		public function get deletable():Boolean {			return _listDeleteEnabled;		}	}}