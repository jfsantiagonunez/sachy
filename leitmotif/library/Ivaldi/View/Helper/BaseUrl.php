<?php

	class Ivaldi_View_Helper_BaseUrl {
		function baseUrl() {
			return substr($_SERVER['PHP_SELF'], 0, -9);
		}
	}
