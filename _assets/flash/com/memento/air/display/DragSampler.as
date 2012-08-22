package com.air.display {

	import flash.display.MovieClip;
	import flash.text.TextField;

	import flash.events.NativeDragEvent;
	import flash.desktop.NativeDragManager;
	import flash.desktop.ClipboardFormats;
	import flash.filesystem.File;

	import com.flash.events.CustomEvent;

	public class DragSampler extends MovieClip {

		public static const SAMPLER_DROP_ACCEPTED:String = new String("samplerDropAccepted");

		private var dropList:Object;
		private var acceptableExtensions:Array;
		private var acceptedFiles:Array;

		public function DragSampler(extensions:String = "*"):void {

			super();

			acceptableExtensions = extensions.split(",");

			this.addEventListener(NativeDragEvent.NATIVE_DRAG_ENTER, onDragEnter);
			this.addEventListener(NativeDragEvent.NATIVE_DRAG_DROP,  onDragDrop);
		}

		private function onDragEnter(event:NativeDragEvent):void {

			// the list of dragged items
			//
			dropList = event.clipboard.getData(ClipboardFormats.FILE_LIST_FORMAT);

			// search for acceptable extension
			//
			var file:File;
			var dirFiles:Array;
			var i:uint;

			if (acceptableExtensions[0] != "*") {

				// only searching for acceptable files
				// (one directory deep)
				//
				var found:Boolean = false;

				mainLoop: for each (file in dropList) {

					if (file.isDirectory) {

						dirFiles = file.getDirectoryListing();
						for each (file in dirFiles) {
							for (i=0; i<acceptableExtensions.length; i++) {
								if (acceptableExtensions[i] == file.extension) {
									found = true;
									break mainLoop;
								}
							}
						}
					}
					else {

						for (i=0; i<acceptableExtensions.length; i++) {
							if (acceptableExtensions[i] == file.extension) {
									found = true;
									break mainLoop;
								}
						}
					}
				}

				// if any dragged file's extension meets our needs, accept drop
				//
				if (found) NativeDragManager.acceptDragDrop(this);
			}
			else {

				// searching for any file objects
				// (also directories)
				//
				var anyFile:Boolean = false;
				for each (file in dropList) {
					anyFile = true;
					break;
				}

				// if any file object dragged, accept drop
				//
				if (anyFile) NativeDragManager.acceptDragDrop(this);
			}
		}

		private function onDragDrop(event:NativeDragEvent):void {

			// invoked only, if drop was accepted

			dropList = event.clipboard.getData(ClipboardFormats.FILE_LIST_FORMAT);

			// search for acceptable files
			//
			var file:File;
			var dirFiles:Array;
			var dirFileNum:uint = 0;
			var i:uint;
			acceptedFiles = new Array();

			if (acceptableExtensions[0] != "*") {

				// passing only files, wich
				// accept our recommendations
				//
				for each (file in dropList) {
					if (file.isDirectory) {
						dirFiles = file.getDirectoryListing();
						for each (file in dirFiles) {
							for (i=0; i<acceptableExtensions.length; i++) {
								if (acceptableExtensions[i] == file.extension) {
									acceptedFiles.push(file);
									dirFileNum++;
								}
							}
						}
					}
					else {
						for (i=0; i<acceptableExtensions.length; i++) {
							if (acceptableExtensions[i] == file.extension) {
									acceptedFiles.push(file);
								}
						}
					}
				}
			}
			else {

				// passing all file objects
				// (even directories)
				//
				for each (file in dropList) {
					acceptedFiles.push(file);
				}
			}

			acceptedFiles.directoryFiles = dirFileNum;

			// send an event with the accepted files array
			// (can't be empty)
			//
			this.dispatchEvent(new CustomEvent(SAMPLER_DROP_ACCEPTED, acceptedFiles));
		}
	}
}