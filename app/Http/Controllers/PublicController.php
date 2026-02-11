<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicController extends Controller
{
    /**
     * All ferry routes data - matching carferry.in
     */
    private $routes = [
        'dabhol-dhopave' => [
            'slug' => 'dabhol-dhopave',
            'name' => 'Dabhol – Dhopave',
            'tagline' => 'Maharashtra\'s First Ferry Service Since 2003',
            'description' => 'Ferry service connecting Dabhol and Dhopave since 2003 - Maharashtra\'s first ferry boat service.',
            'image' => 'dabhol-dhopave.jpg',
            'timetable' => 'dabhol-dhopave.jpg',
            'ratecard' => 'dabhol.jpg',
            'paragraphs' => [
                'The Dabhol-Dhopave ferry service is the very first site which was started on 21.10.2003 and has been constantly working at all times and in all seasons since its first day.',
                'This ferry connects Dabhol (near Dapoli) and Dhopave (near Guhagar) in Maharashtra\'s Ratnagiri district. The service provides a crucial link between these two coastal destinations, reducing travel time significantly compared to road-only routes.',
                'The ferry service was started by Suvarnadurga Shipping & Marine Services Pvt. Ltd. as the first Ferry Boat Service in Maharashtra, eliminating the need for expensive and time-consuming highway travel.',
            ],
            'additional_info' => 'Nearby attractions include temples, forts, and agricultural institutions. The route is popular among tourists visiting the Konkan coast and offers a scenic journey across the waters.',
            'contacts' => [
                'Dabhol Office' => ['02348-248900', '9767248900'],
                'Dhopave' => ['7709250800'],
            ],
        ],
        'jaigad-tawsal' => [
            'slug' => 'jaigad-tawsal',
            'name' => 'Jaigad – Tawsal',
            'tagline' => 'Easy & Better Transportation to Ratnagiri',
            'description' => 'Ferry service for easy transportation from Guhaghar to Ratnagiri region.',
            'image' => 'jaigad-tawsal.jpg',
            'timetable' => 'jaigad-tawsal.jpg',
            'ratecard' => 'jaigad.jpg',
            'paragraphs' => [
                'This Ferry service was started for the easy & better transportation from Guhaghar to the Ratnagiri region. The service makes these areas easily accessible from Ratnagiri (Kolhapur region) as well as from Pune and Mumbai.',
                'The ferry was established by Suvarnadurga Shipping & Marine Services to enable transportation from Guhaghar to Ratnagiri, subsequently boosting tourism and commerce in the region.',
                'The route has become popular among both tourists and locals, providing a convenient and scenic alternative to road travel.',
            ],
            'additional_info' => 'Nearby tourist destinations include Ganpati Pule, Thiba Palace, Bhate beach, and Pawas. The service benefits fishing operations and economic accessibility to Ratnagiri district.',
            'contacts' => [
                'Jaigad' => ['02354-242500', '8550999884'],
                'Tawsal' => ['8550999880'],
            ],
        ],
        'dighi-agardande' => [
            'slug' => 'dighi-agardande',
            'name' => 'Dighi – Agardande',
            'tagline' => 'Direct NH-17 Connectivity',
            'description' => 'Ferry connecting Dighi and Agardande with direct access to National Highway 17.',
            'image' => 'dighi-agardande.jpg',
            'timetable' => null,
            'ratecard' => null,
            'paragraphs' => [
                'The Dighi-Agardande ferry service connects two coastal locations in Maharashtra, facilitating tourism and commercial fishing by providing direct access to National Highway 17.',
                'This route enables easy access to destinations like Murud-Janjeera, Kashid beach, and Alibaug. It also supports the local fishing industry by providing market access for commercially valuable fish species including Pomfret, Rawas, and Prawns.',
                'Previously, local fishermen lacked viable markets; the ferry service now enables distribution to Mumbai and other destinations through improved transportation connectivity.',
            ],
            'additional_info' => 'The route represents infrastructure development addressing historical limitations. Tourism development is focused on nearby attractions and beaches.',
            'contacts' => [
                'Dighi' => ['9156546700'],
                'Agardande' => ['8550999887'],
            ],
        ],
        'veshvi-bagmandale' => [
            'slug' => 'veshvi-bagmandale',
            'name' => 'Veshvi – Bagmandale',
            'tagline' => 'Quick Journey from Raigad to Ratnagiri',
            'description' => 'Ferry service operating since 2007, making Raigad to Ratnagiri journey quick and easy.',
            'image' => 'veshvi-bagmandale.jpg',
            'timetable' => null,
            'ratecard' => null,
            'paragraphs' => [
                'Operating since 2007, the Veshvi-Bagmandale ferry made the journey from Raigad to Ratnagiri very easy and quick, eliminating lengthy travel via Mandangad.',
                'This ferry service connects to nearby attractions including Harihareshwar, Kelshi\'s Mahalaxmi Temple, and Suvarnadurga Fort. It suggests multi-destination itineraries combining the ferry with other Konkan region beaches and historical sites.',
                'The service connects to nearby ferry options including the Rohini-Agardanda route as an alternative for travelers.',
            ],
            'additional_info' => 'Popular destinations accessible via this route include Harihareshwar beach, various temples, and the historic Suvarnadurga Fort.',
            'contacts' => [
                'Veshvi Office' => ['02350-223300'],
                'Bagmandale' => ['9322819161'],
            ],
        ],
        'vasai-bhayander' => [
            'slug' => 'vasai-bhayander',
            'name' => 'Vasai – Bhayander',
            'tagline' => 'RORO Service Under Sagarmala Project',
            'description' => 'Newest RORO ferry service connecting Vasai and Bhayander under the Sagarmala Project.',
            'image' => 'vasai-bhayander.jpg',
            'timetable' => null,
            'ratecard' => null,
            'paragraphs' => [
                'This is Suvarnadurga Shipping\'s seventh ferry route, connecting Vasai and Bhayander in Maharashtra. The company recently began operations on this route, which operates under the Sagarmala Project with provisional authorization from the Maharashtra Maritime Board.',
                'The service is a RORO (Roll-on/Roll-off) operation, following the company\'s 21+ years of experience with similar services. Vasai has historical significance with Vasai Fort and Portuguese cultural heritage, with potential for tourism development.',
                'Bhayander is positioned as a major marketplace with good connectivity, experiencing rapid growth. Schedules may vary based on tide levels, with separate timetables for weekdays and weekends.',
            ],
            'additional_info' => 'The route has potential for fruit trading expansion and tourism development due to its proximity to historical sites.',
            'contacts' => [
                'Contact 1' => ['8624063900'],
                'Contact 2' => ['8600314710'],
            ],
        ],
        'virar-saphale' => [
            'slug' => 'virar-saphale',
            'name' => 'Virar – Saphale (Jalsar)',
            'tagline' => 'Connecting Virar and Saphale',
            'description' => 'Ferry service connecting Virar and Saphale, also known as Jalsar.',
            'image' => null,
            'timetable' => null,
            'ratecard' => null,
            'paragraphs' => [
                'The Virar-Saphale ferry service, also known as Jalsar, connects these two locations providing an alternative to road travel.',
                'This route is part of Suvarnadurga Shipping\'s network of ferry services across the Maharashtra coast, providing reliable transportation for both passengers and vehicles.',
                'The service operates regularly and has become an important link for commuters and tourists in the region.',
            ],
            'additional_info' => 'Contact us for current schedules and fares.',
            'contacts' => [
                'General Enquiry' => ['+91 9422431371'],
            ],
        ],
        'ambet-mahpral' => [
            'slug' => 'ambet-mahpral',
            'name' => 'Ambet – Mahpral',
            'tagline' => 'Connecting Coastal Communities',
            'description' => 'Ferry service connecting Ambet and Mahpral coastal communities.',
            'image' => 'ambet-mahpral.jpg',
            'timetable' => null,
            'ratecard' => null,
            'paragraphs' => [
                'The Ambet-Mahpral ferry service connects these coastal communities, providing reliable ferry services for passengers and vehicles.',
                'This route is part of Suvarnadurga Shipping\'s extensive network across the Konkan coast, supporting local communities and promoting regional connectivity.',
                'The ferry service has become an important transportation link, reducing travel time and providing a scenic journey for passengers.',
            ],
            'additional_info' => 'The route serves local communities and tourists exploring the Konkan coast.',
            'contacts' => [
                'General Enquiry' => ['+91 9422431371'],
            ],
        ],
    ];

    /**
     * Display the homepage
     */
    public function home()
    {
        return Inertia::render('Public/Welcome', [
            'routes' => array_values($this->routes),
        ]);
    }

    /**
     * Display the about page
     */
    public function about()
    {
        return Inertia::render('Public/About');
    }

    /**
     * Display the contact page
     */
    public function contact()
    {
        return Inertia::render('Public/Contact');
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        // Here you would typically:
        // 1. Save to database
        // 2. Send email notification
        // 3. Send auto-reply to user

        // For now, just redirect with success message
        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    /**
     * Display a ferry route page
     */
    public function route($slug)
    {
        if (!isset($this->routes[$slug])) {
            abort(404, 'Route not found');
        }

        $route = $this->routes[$slug];
        $otherRoutes = array_values($this->routes);

        return Inertia::render('Public/RouteDetail', [
            'route' => $route,
            'otherRoutes' => $otherRoutes,
        ]);
    }

    /**
     * Get all routes (for API or other uses)
     */
    public function getAllRoutes()
    {
        return $this->routes;
    }
}
