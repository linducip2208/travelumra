<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case DpPaid = 'dp_paid';
    case Confirmed = 'confirmed';
    case DocumentsComplete = 'documents_complete';
    case VisaProcessing = 'visa_processing';
    case VisaApproved = 'visa_approved';
    case VisaRejected = 'visa_rejected';
    case ReadyToDepart = 'ready_to_depart';
    case Departed = 'departed';
    case Returned = 'returned';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
}
