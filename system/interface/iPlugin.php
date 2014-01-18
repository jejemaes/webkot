<?php


interface iPlugin{
	
	public function __construct(array $options);
	
	public function load();
	
}