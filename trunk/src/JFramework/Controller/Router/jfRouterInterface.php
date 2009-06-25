<?php

interface jfRouterInterface {
	
	public function route($request);
	public function assemble($userParams, $name = null, $reset = false, $encode = true);
}

