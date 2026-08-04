<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Page;
use App\Services\SeoService;
use App\Services\DelhiveryService;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use LogsActivity;

    protected $delhiveryService;
    protected $seoService;

    public function __construct(DelhiveryService $delhiveryService, SeoService $seoService)
    {
        $this->delhiveryService = $delhiveryService;
        $this->seoService = $seoService;
    }

    /**
     * About Us Page.
     */
    public function about()
    {
        $seo = $this->seoService->generateTags([
            'title' => 'Our Farm Story',
            'description' => 'Learn how RohidaFarm maintains 100% purity using traditional organic processes.'
        ]);
        return view('frontend.pages.about', compact('seo'));
    }

    /**
     * Contact Us Page.
     */
    public function contact()
    {
        $seo = $this->seoService->generateTags([
            'title' => 'Contact Us',
            'description' => 'Get in touch with the RohidaFarm support team.'
        ]);
        return view('frontend.pages.contact', compact('seo'));
    }

    /**
     * Submit Contact Form.
     */
    public function contactSubmit(ContactRequest $request)
    {
        $payload = $request->validated();
        self::logActivity('contact_inquiry', "Received contact message from {$payload['email']}", $payload);
        return back()->with('success', 'Thank you for reaching out! Our wellness experts will contact you shortly.');
    }

    /**
     * FAQs Page.
     */
    public function faq()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
        $seo = $this->seoService->generateTags([
            'title' => 'Frequently Asked Questions',
            'description' => 'Clarify your doubts on Bilona Cow Ghee, ordering processes, and shipping fees.'
        ]);
        return view('frontend.pages.faq', compact('faqs', 'seo'));
    }

    /**
     * Track Order Form & Details.
     */
    public function trackOrder(Request $request)
    {
        $orderNo = $request->query('order_number');
        $order = null;
        $timeline = [];

        if ($orderNo) {
            $order = Order::with('shipment')->where('order_number', $orderNo)->first();
            if ($order) {
                $timeline = $this->delhiveryService->getTrackingTimeline($order);
            } else {
                session()->now('error', 'No orders found matching this order number.');
            }
        }

        $seo = $this->seoService->generateTags([
            'title' => 'Track Your Shipment',
            'description' => 'Check the delivery status of your order in real time.'
        ]);

        return view('frontend.pages.track-order', compact('order', 'timeline', 'seo'));
    }

    /**
     * Load a dynamic policy page from DB by slug.
     * Shared helper used by all 4 policy page methods.
     */
    protected function loadPolicyPage(string $slug, string $defaultTitle, string $defaultDescription): \Illuminate\View\View
    {
        // firstOrCreate ensures the page row always exists
        $page = Page::firstOrCreate(
            ['slug' => $slug],
            [
                'title'     => $defaultTitle,
                'content'   => '',
                'is_active' => true,
            ]
        );

        $seo = $this->seoService->generateTags([
            'title'       => $page->meta_title       ?: $defaultTitle,
            'description' => $page->meta_description ?: $defaultDescription,
            'keywords'    => $page->keywords          ?: '',
        ]);

        return view('frontend.pages.policy-page', compact('page', 'seo'));
    }

    /**
     * Privacy Policy Page — dynamic from DB.
     */
    public function privacyPolicy()
    {
        return $this->loadPolicyPage(
            'privacy-policy',
            'Privacy Policy — RohidaFarm',
            'Read our Privacy Policy to understand how RohidaFarm collects and uses your data.'
        );
    }

    /**
     * Refund & Return Policy Page — dynamic from DB.
     */
    public function refundPolicy()
    {
        return $this->loadPolicyPage(
            'refund-policy',
            'Refund & Return Policy — RohidaFarm',
            'Read our refund and return policy before placing an order.'
        );
    }

    /**
     * Shipping Policy Page — dynamic from DB.
     */
    public function shippingPolicy()
    {
        return $this->loadPolicyPage(
            'shipping-policy',
            'Shipping Policy — RohidaFarm',
            'Understand our delivery timelines, shipping charges and logistics partners.'
        );
    }

    /**
     * Terms & Conditions Page — dynamic from DB.
     */
    public function termsConditions()
    {
        return $this->loadPolicyPage(
            'terms-conditions',
            'Terms & Conditions — RohidaFarm',
            'Review the terms and conditions that govern your use of RohidaFarm services.'
        );
    }
}
