<?php

namespace App\Enums;

enum GeographicalReach: string
{
    case Global = "Global level";
    case MultiCountry = "Multi-country level";
    case Country = "Country level";

}
