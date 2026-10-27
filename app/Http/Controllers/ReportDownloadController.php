<?php

namespace App\Http\Controllers;

use App\Models\Cluster;
use App\Models\Invoice;
use App\Services\ReportPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportDownloadController extends Controller
{
    public function salesPdf(Request $request, ReportPdfService $pdfService)
    {
        $filters = [
            'date_from' => $request->get('date_from', now()->startOfMonth()->toDateString()),
            'date_to' => $request->get('date_to', now()->endOfMonth()->toDateString()),
        ];

        $path = $pdfService->generateSalesReport($filters);
        return Response::download($path)->deleteFileAfterSend();
    }

    public function financePdf(Request $request, ReportPdfService $pdfService)
    {
        $filters = [
            'date_from' => $request->get('date_from', now()->startOfMonth()->toDateString()),
            'date_to' => $request->get('date_to', now()->endOfMonth()->toDateString()),
        ];

        $path = $pdfService->generateFinanceReport($filters);
        return Response::download($path)->deleteFileAfterSend();
    }

    public function invoicePdf(Invoice $invoice, ReportPdfService $pdfService)
    {
        $path = $pdfService->generateInvoice($invoice);
        return Response::download($path)->deleteFileAfterSend();
    }

    public function manifestPdf(Cluster $cluster, ReportPdfService $pdfService)
    {
        $path = $pdfService->generateManifest($cluster);
        return Response::download($path)->deleteFileAfterSend();
    }
}
