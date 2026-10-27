<?php

namespace App\Enums;

enum VisaStatus: string
{
    case NotSubmitted = 'not_submitted';
    case Submitted = 'submitted';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Issued = 'issued';
    case Expired = 'expired';
}
