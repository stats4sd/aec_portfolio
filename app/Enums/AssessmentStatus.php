<?php

namespace App\Enums;

enum AssessmentStatus: string
{
    case NotStarted = "Not Started";
    case InProgress = "In Progress";
    case Complete = "Complete";
    case Na = "Not Applicable";
}
