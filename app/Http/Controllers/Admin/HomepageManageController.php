<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Banner;
use App\Models\HeaderTicker;
use App\Traits\LogsActivity;
use App\Models\NativeIngredient;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomepageManageController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of homepage slider components and promotional banners.
     */
    public function index()
    {
        $sliders = Slider::orderBy('sort_order')->get();
        $banners = Banner::orderBy('id')->get();
        $tickers = HeaderTicker::orderBy('sort_order')->get();
        $nativeIngredients = NativeIngredient::orderBy('sort_order')->get();
        $coupons = \App\Models\Coupon::where('is_active', true)->get();
        return view('admin.homepage.index', compact('sliders', 'banners', 'tickers', 'nativeIngredients', 'coupons'));
    }

    /**
     * Update Lucky Wheel settings
     */
    public function promoUpdate(Request $request)
    {
        $request->validate([
            'lucky_wheel_enabled' => 'nullable|string',
            'lucky_wheel_delay' => 'required|integer|min:0',
            'lucky_wheel_coupons' => 'required|array',
            'lucky_wheel_winner' => 'required|string',
        ]);

        Setting::set('lucky_wheel_enabled', $request->has('lucky_wheel_enabled') ? 'true' : 'false', 'boolean', 'homepage');
        Setting::set('lucky_wheel_delay', $request->input('lucky_wheel_delay'), 'string', 'homepage');
        Setting::set('lucky_wheel_coupons', $request->input('lucky_wheel_coupons'), 'json', 'homepage');
        Setting::set('lucky_wheel_winner', $request->input('lucky_wheel_winner'), 'string', 'homepage');

        $this->logActivity('Updated Spin the Wheel promotion settings.');

        return redirect()->route('admin.homepage.index', ['#promo-content'])->with('success', 'Spin the Wheel settings updated successfully.');
    }

    /**
     * Update Homepage Section Image settings
     */
    public function sectionImageUpdate(Request $request)
    {
        $request->validate([
            'home_section_image_pc' => 'required|string|max:1000',
            'home_section_image_mobile' => 'nullable|string|max:1000',
            'home_section_image_link' => 'nullable|string|max:500',
        ]);

        Setting::set('home_section_image_pc', $request->input('home_section_image_pc'), 'string', 'homepage');
        Setting::set('home_section_image_mobile', $request->input('home_section_image_mobile'), 'string', 'homepage');
        Setting::set('home_section_image_link', $request->input('home_section_image_link'), 'string', 'homepage');

        $this->logActivity('Updated Homepage Section Image settings.');

        return redirect()->route('admin.homepage.index', ['#section-image-content'])->with('success', 'Homepage Section Image updated successfully.');
    }

    /**
     * Store a newly created slider slide in database.
     */
    public function sliderStore(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string|max:1000',
            'mobile_image_path' => 'nullable|string|max:1000',
            'button_url' => 'nullable|string|max:500',
        ]);

        $slider = Slider::create([
            'title' => 'Slider Banner ' . (Slider::max('id') + 1),
            'subtitle' => null,
            'image_path' => $request->input('image_path'),
            'mobile_image_path' => $request->input('mobile_image_path'),
            'button_text' => null,
            'button_url' => $request->input('button_url'),
            'sort_order' => Slider::max('sort_order') + 1,
            'is_active' => true,
        ]);

        $this->logActivity('Uploaded home slider banner slide ID: ' . $slider->id);

        return redirect()->route('admin.homepage.index')->with('success', 'Banner slide added successfully.');
    }

    /**
     * Update the specified slider banner slide in database.
     */
    public function sliderUpdate(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $request->validate([
            'image_path' => 'required|string|max:1000',
            'mobile_image_path' => 'nullable|string|max:1000',
            'button_url' => 'nullable|string|max:500',
        ]);

        $slider->update([
            'image_path' => $request->input('image_path'),
            'mobile_image_path' => $request->input('mobile_image_path'),
            'button_url' => $request->input('button_url'),
        ]);

        $this->logActivity('Updated home slider banner slide ID: ' . $id);

        return redirect()->route('admin.homepage.index')->with('success', 'Banner slide updated successfully.');
    }

    /**
     * Handle bulk AJAX drag & drop reordering of slider slides.
     */
    public function sliderReorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:sliders,id',
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            Slider::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->logActivity('Updated home slider slides sorting order.');

        return response()->json([
            'status' => 'success',
            'message' => 'Slider sorting order updated successfully.'
        ]);
    }

    /**
     * Delete the specified slide from database and physical disk.
     */
    public function sliderDelete($id)
    {
        $slider = Slider::findOrFail($id);

        // Delete physical file if exists locally
        if ($slider->image_path && !str_starts_with($slider->image_path, 'http')) {
            $absolutePath = public_path($slider->image_path);
            if (File::exists($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        $slider->delete();

        $this->logActivity('Deleted home slider slide ID: ' . $id);

        return redirect()->route('admin.homepage.index')->with('success', 'Banner slide deleted successfully.');
    }

    /**
     * Store a newly created top header ticker point in database.
     */
    public function tickerStore(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'icon_class' => 'required|string|max:255',
        ]);

        $ticker = HeaderTicker::create([
            'text' => $request->input('text'),
            'icon_class' => $request->input('icon_class'),
            'sort_order' => HeaderTicker::max('sort_order') + 1,
            'is_active' => true,
        ]);

        $this->logActivity('Created top header ticker: ' . $ticker->text);

        return redirect()->route('admin.homepage.index')->with('success', 'Ticker point added successfully.');
    }

    /**
     * Handle bulk AJAX drag & drop reordering of top header tickers.
     */
    public function tickerReorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:header_tickers,id',
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            HeaderTicker::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->logActivity('Updated top header tickers sorting order.');

        return response()->json([
            'status' => 'success',
            'message' => 'Tickers sorting order updated successfully.'
        ]);
    }

    /**
     * Delete the specified ticker point from database.
     */
    public function tickerDelete($id)
    {
        $ticker = HeaderTicker::findOrFail($id);
        $ticker->delete();

        $this->logActivity('Deleted top header ticker ID: ' . $id);

        return redirect()->route('admin.homepage.index')->with('success', 'Ticker point deleted successfully.');
    }

    /**
     * Store a newly created native ingredient.
     */
    public function nativeIngredientStore(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
        ]);

        $ingredient = NativeIngredient::create([
            'title' => $request->input('title'),
            'image_path' => $request->input('image_path'),
            'link' => $request->input('link'),
            'sort_order' => NativeIngredient::max('sort_order') + 1,
            'is_active' => true,
        ]);

        $this->logActivity('Added Native Ingredient ID: ' . $ingredient->id);

        return redirect()->route('admin.homepage.index')->with('success', 'Native ingredient added successfully.');
    }

    /**
     * Update the specified native ingredient.
     */
    public function nativeIngredientUpdate(Request $request, $id)
    {
        $ingredient = NativeIngredient::findOrFail($id);

        $request->validate([
            'image_path' => 'required|string|max:1000',
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
        ]);

        $ingredient->update([
            'title' => $request->input('title'),
            'image_path' => $request->input('image_path'),
            'link' => $request->input('link'),
        ]);

        $this->logActivity('Updated Native Ingredient ID: ' . $id);

        return redirect()->route('admin.homepage.index')->with('success', 'Native ingredient updated successfully.');
    }

    /**
     * Handle bulk AJAX drag & drop reordering of native ingredients.
     */
    public function nativeIngredientReorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:native_ingredients,id',
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            NativeIngredient::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->logActivity('Updated Native Ingredients sorting order.');

        return response()->json([
            'status' => 'success',
            'message' => 'Native Ingredients sorting order updated successfully.'
        ]);
    }

    /**
     * Delete the specified native ingredient.
     */
    public function nativeIngredientDelete($id)
    {
        $ingredient = NativeIngredient::findOrFail($id);

        // Delete physical file if exists locally
        if ($ingredient->image_path && !str_starts_with($ingredient->image_path, 'http')) {
            $absolutePath = public_path($ingredient->image_path);
            if (File::exists($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        $ingredient->delete();

        $this->logActivity('Deleted Native Ingredient ID: ' . $id);

        return redirect()->route('admin.homepage.index')->with('success', 'Native ingredient deleted successfully.');
    }
}
