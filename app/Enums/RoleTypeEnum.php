<?php
namespace App\Enums;
use App\Traits\EnumFunctions;
enum RoleTypeEnum :string{
    use EnumFunctions;
    case USER ='user';
    case ADMIN ='super';
}
