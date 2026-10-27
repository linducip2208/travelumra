<?php

use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProgrammaticSeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/paket-umrah', [MarketingController::class, 'packages'])->name('packages');
Route::get('/paket-umrah/{slug}', [MarketingController::class, 'packageDetail'])->name('package.detail');
Route::get('/booking/{slug}', [\App\Http\Controllers\BookingController::class, 'create'])->name('booking.create')->middleware('throttle:30,1');
Route::post('/booking/{slug}', [\App\Http\Controllers\BookingController::class, 'store'])->name('booking.store')->middleware('throttle:10,1');
Route::get('/booking/{bookingNumber}/success', [\App\Http\Controllers\BookingController::class, 'success'])->name('booking.success');
Route::get('/payment/{invoice}/pay', [\App\Http\Controllers\BookingController::class, 'pay'])->name('booking.pay');
Route::get('/payment/{invoice}/finish', [\App\Http\Controllers\BookingController::class, 'finish'])->name('payment.finish')->middleware('throttle:30,1');
Route::get('/payment/{invoice}/simulator', [\App\Http\Controllers\BookingController::class, 'simulator'])->name('payment.simulator')->middleware('throttle:30,1');
Route::post('/payment/{invoice}/simulator-confirm', [\App\Http\Controllers\BookingController::class, 'simulatorConfirm'])->name('payment.simulator.confirm')->middleware('throttle:10,1');
Route::post('/payment/{invoice}/callback', [\App\Http\Controllers\BookingController::class, 'callback'])->name('payment.callback')->middleware('throttle:60,1');
Route::get('/tentang-kami', [MarketingController::class, 'about'])->name('about');
Route::get('/kontak', [MarketingController::class, 'contact'])->name('contact');
Route::post('/kontak', [MarketingController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/docs', [\App\Http\Controllers\DocsController::class, 'index'])->name('docs');

// CMS Routes
Route::get('/blog', [MarketingController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [MarketingController::class, 'blogShow'])->name('blog.show');
Route::get('/faq', [MarketingController::class, 'faq'])->name('faq');
Route::get('/halaman/{slug}', [MarketingController::class, 'page'])->name('page.show');

// PDF download routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/download/laporan-penjualan', [\App\Http\Controllers\ReportDownloadController::class, 'salesPdf'])->name('report.sales.pdf');
    Route::get('/download/laporan-keuangan', [\App\Http\Controllers\ReportDownloadController::class, 'financePdf'])->name('report.finance.pdf');
    Route::get('/download/invoice/{invoice}', [\App\Http\Controllers\ReportDownloadController::class, 'invoicePdf'])->name('invoice.pdf');
    Route::get('/download/manifest/{cluster}', [\App\Http\Controllers\ReportDownloadController::class, 'manifestPdf'])->name('manifest.pdf');
});

Route::prefix('s')->group(function () {
    Route::get('/paket-umrah-{bulan}-{tahun}', [ProgrammaticSeoController::class, 'packageByMonth']);
    Route::get('/best-umrah-packages-{tahun}', [ProgrammaticSeoController::class, 'bestPackages']);
    Route::get('/umrah-{kategori}', [ProgrammaticSeoController::class, 'packageByCategory']);
    Route::get('/umrah-dari-{kota}', [ProgrammaticSeoController::class, 'packageByCity']);
    Route::get('/compare/{slugA}-vs-{slugB}', [ProgrammaticSeoController::class, 'compare']);
    Route::get('/alternatives-to-{slug}', [ProgrammaticSeoController::class, 'alternatives']);
    Route::get('/travel-umrah-murah', [ProgrammaticSeoController::class, 'budgetUmrah']);
    Route::get('/haji-plus-{tahun}', [ProgrammaticSeoController::class, 'hajiPlusYear']);
    Route::get('/umrah-{maskapai}', [ProgrammaticSeoController::class, 'byAirline'])->where('maskapai', '.*');
});

Route::get('/sitemap/sitemap.xml', [ProgrammaticSeoController::class, 'sitemap']);
Route::get('/robots.txt', [ProgrammaticSeoController::class, 'robots']);

Route::prefix('portal')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Portal\AuthController::class, 'showLogin'])->name('portal.login');
    Route::post('/login', [\App\Http\Controllers\Portal\AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/logout', [\App\Http\Controllers\Portal\AuthController::class, 'logout'])->name('portal.logout');

    Route::middleware('portal.auth')->group(function () {
        Route::get('/', [\App\Http\Controllers\Portal\DashboardController::class, 'index'])->name('portal.dashboard');
        Route::get('/bookings', [\App\Http\Controllers\Portal\BookingController::class, 'index'])->name('portal.bookings');
        Route::get('/bookings/{booking}', [\App\Http\Controllers\Portal\BookingController::class, 'show'])->name('portal.bookings.show');
        Route::get('/invoices', [\App\Http\Controllers\Portal\InvoiceController::class, 'index'])->name('portal.invoices');
        Route::get('/invoices/{invoice}', [\App\Http\Controllers\Portal\InvoiceController::class, 'show'])->name('portal.invoices.show');
        Route::post('/invoices/{invoice}/upload-payment', [\App\Http\Controllers\Portal\InvoiceController::class, 'uploadPayment'])->name('portal.invoices.upload-payment');
        Route::get('/files/payments/{payment}', [\App\Http\Controllers\SecureFileController::class, 'portalPayment'])->name('portal.files.payment');
    });
});

require base_path('routes/pair-routes.php');
