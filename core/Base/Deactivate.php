<?php
/**
 * @package  CVGT_Locations
 */
namespace Porta_Places\Base;

class Deactivate
{
	public static function deactivate() {
		flush_rewrite_rules();
	}
}