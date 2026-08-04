<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\GalleryAlbumController as AdminGalleryAlbumController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\NewsPostController as AdminNewsPostController;
use App\Http\Controllers\Admin\PropertyController as AdminPropertyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$legacyListMode = static fn (?string $legacyView): ?string => match ((string) $legacyView) {
    '1' => 'block',
    '2' => 'table',
    '3' => 'map',
    default => null,
};

$legacyTypeMap = [
    'kvartira' => 1,
    'dom' => 2,
    'komnata' => 3,
    'zemelnyj-uchastok' => 4,
    'mnogokvartirnyj-dom' => 5,
    'novostrojki' => 5,
    'gostinica' => 6,
    'gostinicy' => 6,
    'garazh' => 8,
    'kommerciya' => 9,
    'kommercheskaja-nedvizhimost' => 9,
];

$legacyPageSearchMap = [
    'poisk-na-karte' => ['ls' => 'map'],
    'prodazha' => ['apType' => 2],
    'novostrojki' => ['apType' => 2, 'objType' => 5],
    'arenda' => ['apType' => 1],
    'gostinicy' => ['objType' => 6],
];

$redirectToSearch = static function (Request $request, array $extra = []) {
    $parameters = $request->query();

    foreach ($extra as $key => $value) {
        if ($value !== null) {
            $parameters[$key] = $value;
        }
    }

    return redirect()->route('search', $parameters, 301);
};

$redirectCityType = static function (Request $request, int $cityId, string $type, ?int $page = null) use ($legacyTypeMap, $redirectToSearch) {
    $objType = $legacyTypeMap[$type] ?? null;
    abort_if($objType === null, 404);

    $extra = [
        'city' => [$cityId],
        'objType' => $objType,
    ];

    if ($page !== null && $page > 1) {
        $extra['page'] = $page;
    }

    return $redirectToSearch($request, $extra);
};

Route::get('/', HomeController::class)->name('home');
Route::get('/index-{page}', fn () => redirect()->route('home', [], 301))->whereNumber('page');
Route::get('/index-{page}.htm', fn () => redirect()->route('home', [], 301))->whereNumber('page');
Route::permanentRedirect('/index.htm', '/');

Route::get('/search', SearchController::class)->name('search');
Route::get('/search.html', fn (Request $request) => $redirectToSearch($request));
Route::get('/search-{legacy}', fn (Request $request) => $redirectToSearch($request));
Route::get('/search-{legacy}.html', fn (Request $request) => $redirectToSearch($request));

Route::get('/contacts', [EmployeeController::class, 'index'])->name('contacts');
Route::get('/users/viewall', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/users/view', [EmployeeController::class, 'show'])->name('employees.show');

Route::get('/property/{slug}.html', fn (string $slug) => redirect()->route('properties.show', ['property' => $slug], 301));
Route::get('/property/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

Route::get('/news-{page}', fn (int $page) => redirect()->route('news.index', ['page' => $page], 301))->whereNumber('page');
Route::get('/news-{page}.html', fn (int $page) => redirect()->route('news.index', ['page' => $page], 301))->whereNumber('page');
Route::get('/news/{slug}.html', fn (string $slug) => redirect()->route('news.show', ['newsPost' => $slug], 301));
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{newsPost:slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/informaciya/fotogalereya', [GalleryController::class, 'index'])->name('gallery.index');
Route::redirect('/informaciya/fotogalereya.html', '/informaciya/fotogalereya', 301);
Route::redirect('/informaciya/foto', '/informaciya/fotogalereya', 301);
Route::redirect('/informaciya/foto.html', '/informaciya/fotogalereya', 301);

Route::middleware(['throttle:5,1'])->group(function (): void {
    Route::get('/contact-us', [InquiryController::class, 'contactForm'])->name('contact.form');
    Route::post('/contact-us', [InquiryController::class, 'storeContact'])->name('contact.store');
    Route::get('/callback', [InquiryController::class, 'callbackForm'])->name('callback.form');
    Route::post('/callback', [InquiryController::class, 'storeCallback'])->name('callback.store');
    Route::get('/booking/request', [InquiryController::class, 'bookingForm'])->name('booking.form');
    Route::post('/booking/request', [InquiryController::class, 'storeBooking'])->name('booking.store');
    Route::get('/apartments/sendEmail', [InquiryController::class, 'propertyMessageForm'])->name('property-message.form');
    Route::post('/apartments/sendEmail', [InquiryController::class, 'storePropertyMessage'])->name('property-message.store');
});

Route::redirect('/booking/request.html', '/booking/request', 301);
Route::redirect('/apartments/sendEmail.html', '/apartments/sendEmail', 301);
Route::redirect('/complain/add', '/contact-us', 301);
Route::redirect('/complain/add.html', '/contact-us', 301);
Route::redirect('/politika-konfidencialnosti', '/page/politika-konfidencialnosti', 301);
Route::redirect('/politika-konfidencialnosti.html', '/page/politika-konfidencialnosti', 301);
Route::redirect('/polzovatelskoe-soglashenie', '/page/polzovatelskoe-soglashenie', 301);
Route::redirect('/polzovatelskoe-soglashenie.html', '/page/polzovatelskoe-soglashenie', 301);

Route::get('/page/{slug}-{page}', function (Request $request, string $slug, int $page) use ($legacyPageSearchMap, $redirectToSearch) {
    if (isset($legacyPageSearchMap[$slug])) {
        return $redirectToSearch($request, array_merge($legacyPageSearchMap[$slug], ['page' => $page]));
    }

    return redirect()->route('pages.show', ['slug' => $slug], 301);
})->whereNumber('page');
Route::get('/page/{slug}-{page}.html', function (Request $request, string $slug, int $page) use ($legacyPageSearchMap, $redirectToSearch) {
    if (isset($legacyPageSearchMap[$slug])) {
        return $redirectToSearch($request, array_merge($legacyPageSearchMap[$slug], ['page' => $page]));
    }

    return redirect()->route('pages.show', ['slug' => $slug], 301);
})->whereNumber('page');
Route::get('/page/{slug}.html', fn (string $slug) => redirect()->route('pages.show', ['slug' => $slug], 301));
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::middleware(['throttle:6,1'])->group(function (): void {
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::get('/recover', [AuthController::class, 'recoverForm'])->name('recover');
Route::middleware(['throttle:3,1'])->group(function (): void {
    Route::post('/register', [AuthController::class, 'storeRegisterRequest'])->name('register.store');
    Route::post('/recover', [AuthController::class, 'storeRecoverRequest'])->name('recover.store');
});

Route::middleware('role:admin,employee')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/employee-mode', [DashboardController::class, 'setEmployeeMode'])->name('employee-mode');
    Route::resource('properties', AdminPropertyController::class)->except(['show']);
    Route::resource('employees', AdminEmployeeController::class)->except(['show']);

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
        Route::post('/mail-test', [DashboardController::class, 'sendMailTest'])->name('mail-test');
        Route::resource('news', AdminNewsPostController::class)->except(['show']);
        Route::resource('gallery', AdminGalleryAlbumController::class)->except(['show']);
    });
});

Route::get('/faq', [ContentController::class, 'faqIndex'])->name('faq');
Route::get('/faq/{slug}.html', fn (string $slug) => redirect()->route('faq.show', ['slug' => $slug], 301));
Route::get('/faq/{slug}', [ContentController::class, 'faqShow'])->name('faq.show');
Route::get('/review', [ContentController::class, 'review'])->name('review');
Route::get('/favorites', [ContentController::class, 'favorites'])->name('favorites');
Route::get('/articles-{page}', fn (int $page) => redirect()->route('articles.index', ['page' => $page], 301))->whereNumber('page');
Route::get('/articles-{page}.html', fn (int $page) => redirect()->route('articles.index', ['page' => $page], 301))->whereNumber('page');
Route::get('/articles', [ContentController::class, 'articlesIndex'])->name('articles.index');
Route::get('/articles/{slug}.html', fn (string $slug) => redirect()->route('articles.show', ['slug' => $slug], 301));
Route::get('/articles/{slug}', [ContentController::class, 'articlesShow'])->name('articles.show');

Route::get('/informaciya', [ContentController::class, 'information'])->name('information');
Route::redirect('/informaciya.html', '/informaciya', 301);
Route::get('/sitemap', [ContentController::class, 'sitemap'])->name('sitemap');
Route::get('/sitemap.xml', [ContentController::class, 'sitemapXml'])->name('sitemap.xml');
Route::redirect('/sitemap.html', '/sitemap', 301);

Route::get('/tuapse', [ContentController::class, 'tuapse'])->name('city.tuapse');
Route::get('/tuapse-{page}', fn (Request $request, int $page) => $redirectToSearch($request, ['city' => [9], 'page' => $page]))->whereNumber('page');
Route::get('/tuapse-{page}.html', fn (Request $request, int $page) => $redirectToSearch($request, ['city' => [9], 'page' => $page]))->whereNumber('page');
Route::get('/tuapse/{type}-{page}.html', fn (Request $request, string $type, int $page) => $redirectCityType($request, 9, $type, $page))->whereNumber('page');
Route::get('/tuapse/{type}-{page}', fn (Request $request, string $type, int $page) => $redirectCityType($request, 9, $type, $page))->whereNumber('page');
Route::get('/tuapse/{type}.html', fn (Request $request, string $type) => $redirectCityType($request, 9, $type));
Route::get('/tuapse/{type}', fn (Request $request, string $type) => $redirectCityType($request, 9, $type));

Route::get('/tuapsinskij-rajon', [ContentController::class, 'tuapsinskyDistrict'])->name('city.tuapsinsky');
Route::get('/tuapsinskij-rajon-{page}', fn (Request $request, int $page) => $redirectToSearch($request, ['city' => [10], 'page' => $page]))->whereNumber('page');
Route::get('/tuapsinskij-rajon-{page}.html', fn (Request $request, int $page) => $redirectToSearch($request, ['city' => [10], 'page' => $page]))->whereNumber('page');
Route::get('/tuapsinskij-rajon/{type}-{page}.html', fn (Request $request, string $type, int $page) => $redirectCityType($request, 10, $type, $page))->whereNumber('page');
Route::get('/tuapsinskij-rajon/{type}-{page}', fn (Request $request, string $type, int $page) => $redirectCityType($request, 10, $type, $page))->whereNumber('page');
Route::get('/tuapsinskij-rajon/{type}.html', fn (Request $request, string $type) => $redirectCityType($request, 10, $type));
Route::get('/tuapsinskij-rajon/{type}', fn (Request $request, string $type) => $redirectCityType($request, 10, $type));

Route::get('/guestad/add', [ContentController::class, 'guestAdd'])->name('guestad.add');

Route::get('/specialoffers-{view}.html', fn (Request $request, string $view) => $redirectToSearch($request, ['featured' => 1, 'ls' => $legacyListMode($view)]));
Route::get('/specialoffers-{view}', fn (Request $request, string $view) => $redirectToSearch($request, ['featured' => 1, 'ls' => $legacyListMode($view)]));
Route::get('/specialoffers.html', fn (Request $request) => $redirectToSearch($request, ['featured' => 1]));
Route::get('/specialoffers', fn (Request $request) => $redirectToSearch($request, ['featured' => 1]));

Route::get('/service-{serviceId}-{view}.html', fn (Request $request, string $serviceId, string $view) => $redirectToSearch($request, ['serviceId' => $serviceId, 'ls' => $legacyListMode($view)]));
Route::get('/service-{serviceId}-{view}', fn (Request $request, string $serviceId, string $view) => $redirectToSearch($request, ['serviceId' => $serviceId, 'ls' => $legacyListMode($view)]));
Route::get('/service-{serviceId}.html', fn (Request $request, string $serviceId) => $redirectToSearch($request, ['serviceId' => $serviceId]));
Route::get('/service-{serviceId}', fn (Request $request, string $serviceId) => $redirectToSearch($request, ['serviceId' => $serviceId]));

Route::redirect('/contacts.html', '/contacts', 301);
Route::redirect('/news.html', '/news', 301);
Route::redirect('/contact-us.html', '/contact-us', 301);
Route::redirect('/callback.html', '/callback', 301);
Route::redirect('/faq.html', '/faq', 301);
Route::redirect('/review.html', '/review', 301);
Route::redirect('/favorites.html', '/favorites', 301);
Route::redirect('/articles.html', '/articles', 301);
Route::redirect('/login.html', '/login', 301);
Route::redirect('/register.html', '/register', 301);
Route::redirect('/recover.html', '/recover', 301);
Route::redirect('/guestad/add.html', '/guestad/add', 301);
Route::redirect('/tuapse.html', '/tuapse', 301);
Route::redirect('/tuapsinskij-rajon.html', '/tuapsinskij-rajon', 301);
