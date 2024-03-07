<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use PictaStudio\VenditioCore\Traits\HasDataToValidate;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use HasDataToValidate;
    use ValidatesRequests;
}
