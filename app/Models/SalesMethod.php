<?php

namespace App\Models;

use App\Models\Base\SalesMethod as BaseSalesMethod;
use OwenIt\Auditing\Contracts\Auditable;

class SalesMethod extends BaseSalesMethod implements Auditable
{
    use \OwenIt\Auditing\Auditable;

	protected $fillable = [
		'name'
	];
}
