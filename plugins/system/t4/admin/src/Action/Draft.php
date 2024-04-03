<?php
namespace T4Admin\Action;


class Draft {
	public static function doSave () {
		$key = \T4Admin\Draft::store();
		return ["ok" => 1, "key" => $key];
	}
}