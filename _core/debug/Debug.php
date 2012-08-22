<?php

	class Debug
	{
		protected static $_instance;
		protected static $log_path;

		/*
			SINGLETON APPROACH
		*/
		private function __construct()
		{}

		private function __clone()
		{}

		public static function getInstance($log_path)
		{
			if (self::$_instance === NULL)
				self::$_instance = new self();

			self::$log_path = $log_path;

			return self::$_instance;
		}

		/*
			DEBUG FUNCTIONS
		*/
		public function print_r_html()
		{
			$HTML = "<div><p><b>DEBUG</b>";
			$args = func_get_args();
			foreach ($args as $key=>$arg)
			{
				$s = print_r($arg, true);
				$s = htmlentities($s, ENT_COMPAT, "utf-8");
				$s = str_replace("\r",	"",						$s);
				$s = str_replace("\n",	"<br/>",				$s);
				$s = str_replace(" ",	"&nbsp;",				$s);
				$s = str_replace("\t",	"&nbsp;&nbsp;&nbsp;",	$s);

				$HTML .= "<hr/>".$key.": ".$s;
			}

			$HTML .= "<hr/></p></div>";
			print $HTML;
		}

		public function print_str_html()
		{
			$HTML = "<div><p><b>DEBUG</b>\n";
			$args = func_get_args();
			foreach ($args as $key=>$arg)
			{
				$s = print_r($arg, true);
				$s = htmlentities($s, ENT_COMPAT, "utf-8");
				$s = str_replace("\r",	"",						$s);
				$s = str_replace("\n",	"<br/>",				$s);
				$s = str_replace(" ",	"&nbsp;",				$s);
				$s = str_replace("\t",	"&nbsp;&nbsp;&nbsp;",	$s);

				$HTML .= "<hr/>".$key.": ".$s;
			}

			$HTML .= "<hr/></p></div>";
			return $HTML;
		}

		public function filog()
		{
			$dump = 'Logged on: '.date(DATE_RFC822)."\r\n".print_r(func_get_args(), true)."\r\n";
			file_put_contents(self::$log_path, $dump, FILE_APPEND);
		}
	}

?>