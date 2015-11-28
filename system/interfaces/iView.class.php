<?php
/**
 * Maes Jerome
 * iView.php, created at Nov 13, 2015
 *
 */
namespace system\interfaces;

interface iView{
	
	public function __construct(\DOMDocument $domdoc, array $fields_properties, array $field_name_list, $model);
	
	public function build($values, array $options);
}