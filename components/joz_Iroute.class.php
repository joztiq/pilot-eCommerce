<?php
/**
 * interface defining routes
 * @author Daniel Maison
 * @author Markus Gerdau
 */
interface joz_Iroute
{
	/**
	 * function to parse the route and map controller and action.
	 * classes are to set values in the frontController object.
	 * @param joz_frontController $fc
	 */
	public function parse(joz_frontController &$fc);
}