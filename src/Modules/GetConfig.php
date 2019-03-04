<?php
// ConfigModuls

namespace App\Modules;

class GetConfig
{
	private $configFile = '';
	
	public function get($param = '')
	{
		return $this->configFile;	
		
	}
	
}