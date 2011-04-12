<?php
interface joz_sessionHandler{
	/*
	 * Singleton pattern enforced
	 */
	public static function getInstance();
	
	public function save();
	
	
}