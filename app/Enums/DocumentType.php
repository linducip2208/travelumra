<?php

namespace App\Enums;

enum DocumentType: string
{
    case KTP = 'ktp';
    case KK = 'kk';
    case Passport = 'passport';
    case Photo = 'photo';
    case Vaccine = 'vaccine';
    case MarriageBook = 'marriage_book';
    case BirthCert = 'birth_cert';
    case BankAccount = 'bank_account';
}
