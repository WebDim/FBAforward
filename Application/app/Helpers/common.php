<?php
/**
 * Created by PhpStorm.
 * User: webdimensions-1
 * Date: 2/12/16
 * Time: 5:33 PM
 */

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
//use Stringy\Stringy;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Eloquent\Model;
use App\Models\LogEvent;
//use App\Jobs\Job;
use \yajra\Datatables\Html\Builder;

function add_to_path($path)
{
    $lib_path = app_path($path);
    $include_path = get_include_path();
    if (!str_contains($include_path, $lib_path)) {
        set_include_path($include_path . PATH_SEPARATOR . $lib_path);
    }
}