<?php
interface jfRouteMatcherInterface {
	public function match($path);
	public function assemble($data = array(), $reset = false, $encode = false);
}