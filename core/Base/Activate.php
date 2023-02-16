<?php
/**
 * @package  CVGT_Locations
 */
namespace Porta_Places\Base;

class Activate
{
	public static function activate() {
		flush_rewrite_rules();
	}
}