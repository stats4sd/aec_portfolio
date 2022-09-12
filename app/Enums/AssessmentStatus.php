<?php

namespace App\Enums;

enum AssessmentStatus: string
{
    case NotStarted = "Not Started";
    case RedlinesIncomplete = "Redlines Incomplete";
    case RedlinesComplete = "Redlines Complete";
    case InProgress = "Assessment In Progress";
    case Complete = "Assessment complete";
}
