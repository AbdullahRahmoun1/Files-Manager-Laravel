<?php
namespace App\Enums;
use App\Traits\EnumFunctions;
enum GroupFileStatusEnum :string{
    use EnumFunctions;
    case PENDING ='pending';
    case ACCEPTED ='accepted';
    case REJECTED ='rejected';
}
