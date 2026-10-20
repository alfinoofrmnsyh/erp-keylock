<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Aboutpage;
use App\Models\Category;
use App\Models\CmsContact;
use App\Models\Gallery;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    /**
     * Unified CMS Hub
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'categories');
        $validTabs = ['categories', 'products', 'galleries', 'partners', 'testimonials', 'about', 'contact'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'categories';
        }

        return $this->renderView($tab);
    }

    public function categories()
    {
        return $this->renderView('categories');
    }

    public function products()
    {
        return $this->renderView('products');
    }

    public function galleries()
    {
        return $this->renderView('galleries');
    }

    public function partners()
    {
        return $this->renderView('partners');
    }

    public function testimonials()
    {
        return $this->renderView('testimonials');
    }

    public function about()
    {
        return $this->renderView('about');
    }

    public function contact()
    {
        return $this->renderView('contact');
    }

    private function checkAdminAccess(): void
    {
        $user = auth()->user();
        abort_if(!$user || !$user->isAdmin(), 403, 'Akses Ditolak: Hanya Administrator yang berhak mengelola konten CMS.');
    }

    private function renderView(string $tab)
    {
        $this->checkAdminAccess();

        $categories = Category::withCount(['products', 'galleries'])->orderBy('id', 'desc')->get();
        $products = Product::with('category')->orderBy('id', 'desc')->get();
        $galleries = Gallery::with('category')->orderBy('id', 'desc')->get();
        $partners = Partner::orderBy('id', 'desc')->get();
        $testimonials = Testimonial::orderBy('id', 'desc')->get();
        $aboutpage = Aboutpage::first();
        $cmsContact = CmsContact::first();

        // Initial About page template if empty
        if (!$aboutpage) {
            $aboutpage = Aboutpage::create([
                'data' => [
                    'content' => [
                        'id' => [
                            'badge' => 'SEKILAS PERUSAHAAN',
                            'title' => 'Solusi Logistik & Material Handling Terpercaya',
                            'stats_units_value' => '500+',
                            'stats_units_label' => 'TOTAL UNIT ALAT',
                            'stats_cities_value' => '1000+',
                            'stats_cities_label' => 'PROYEK SELESAI',
                            'stats_clients_value' => '500+',
                            'stats_clients_label' => 'KLIEN MITRA',
                            'vision_eyebrow' => 'MASA DEPAN',
                            'vision_title' => 'Visi Kami',
                            'vision_text' => 'Menjadi perusahaan distributor dan penyedia solusi logistik industri terpercaya, profesional, dan berdaya saing tinggi.',
                            'mission_eyebrow' => 'KOMITMEN KAMI',
                            'mission_title' => 'Misi Kami',
                            'description_1' => 'PT Key Lock Indonesia merupakan perusahaan distributor yang menyediakan produk industrial berkualitas.',
                            'description_2' => 'Kami berfokus pada penyediaan palet plastik, rak penyimpanan, forklift, truck scale, perkakas industri.',
                            'mission_list' => [
                                ['id' => 1, 'text' => 'Menyediakan produk berkualitas dan sesuai kebutuhan pelanggan.'],
                                ['id' => 2, 'text' => 'Memberikan pelayanan cepat dan tepat.'],
                                ['id' => 3, 'text' => 'Membangun hubungan kerja sama jangka panjang yang saling menguntungkan.'],
                            ]
                        ]
                    ]
                ]
            ]);
        }

        // Initial Contact record if empty
        if (!$cmsContact) {
            $cmsContact = CmsContact::create([
                'contact1' => '6289699392924',
                'contact2' => '6285173013525',
                'wechat_qr' => null,
            ]);
        }

        $stats = [
            'total_categories' => $categories->count(),
            'total_products' => $products->count(),
            'trending_products' => $products->where('trending', 1)->count(),
            'total_galleries' => $galleries->count(),
            'active_galleries' => $galleries->where('status', 1)->count(),
            'total_partners' => $partners->count(),
            'total_testimonials' => $testimonials->count(),
        ];

        return view('erp.cms', compact(
            'tab',
            'categories',
            'products',
            'galleries',
            'partners',
            'testimonials',
            'aboutpage',
            'cmsContact',
            'stats'
        ));
    }

    // =========================================================================
    // CATEGORIES CRUD
    // =========================================================================
    public function storeCategory(Request $request)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'name_id' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'name_zh' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name_id']);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name' => [
                'id' => $validated['name_id'],
                'en' => $validated['name_en'] ?: $validated['name_id'],
                'zh-Hans' => $validated['name_zh'] ?: $validated['name_id'],
            ],
            'slug' => $slug,
            'image' => $imagePath,
        ]);

        return redirect()->route('erp.cms.categories')->with('success', "Kategori '{$category->display_name}' berhasil ditambahkan!");
    }

    public function updateCategory(Request $request, $id)
    {
        $this->checkAdminAccess();

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name_id' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'name_zh' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name' => [
                'id' => $validated['name_id'],
                'en' => $validated['name_en'] ?: $validated['name_id'],
                'zh-Hans' => $validated['name_zh'] ?: $validated['name_id'],
            ],
            'slug' => Str::slug($validated['slug']),
            'image' => $imagePath,
        ]);

        return redirect()->route('erp.cms.categories')->with('success', "Kategori '{$category->display_name}' berhasil diperbarui!");
    }

    public function deleteCategory($id)
    {
        $this->checkAdminAccess();

        $category = Category::findOrFail($id);
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        $name = $category->display_name;
        $category->delete();

        return redirect()->route('erp.cms.categories')->with('success', "Kategori '{$name}' berhasil dihapus!");
    }

    // =========================================================================
    // PRODUCTS CRUD
    // =========================================================================
    public function storeProduct(Request $request)
    {
        $this->checkAdminAccess();

        $titleId = $request->input('title_id') ?: ($request->input('title_en') ?: $request->input('title_zh'));
        $titleEn = $request->input('title_en') ?: $titleId;
        $titleZh = $request->input('title_zh') ?: $titleId;

        $request->merge([
            'title_id' => $titleId,
            'title_en' => $titleEn,
            'title_zh' => $titleZh,
        ]);

        $validated = $request->validate([
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_zh' => 'nullable|string|max:255',
            'category_slug' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'trending' => 'nullable|boolean',
            'desc_id' => 'nullable|string',
            'desc_en' => 'nullable|string',
            'desc_zh' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title_id']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $descId = $validated['desc_id'] ?? ($validated['desc_en'] ?? ($validated['desc_zh'] ?? ''));
        $descEn = $validated['desc_en'] ?? $descId;
        $descZh = $validated['desc_zh'] ?? $descId;

        $product = Product::create([
            'document_id' => (string) Str::uuid(),
            'locale' => 'id',
            'title' => [
                'id' => $validated['title_id'],
                'en' => $validated['title_en'] ?: $validated['title_id'],
                'zh-Hans' => $validated['title_zh'] ?: $validated['title_id'],
            ],
            'slug' => $slug,
            'category_slug' => $validated['category_slug'],
            'trending' => $request->boolean('trending'),
            'desc' => [
                'id' => $descId ?: ($descEn ?: $descZh),
                'en' => $descEn ?: ($descId ?: $descZh),
                'zh-Hans' => $descZh ?: ($descId ?: $descEn),
            ],
            'image' => $imagePath,
        ]);

        return redirect()->route('erp.cms.products')->with('success', "Produk '{$product->display_title}' berhasil ditambahkan!");
    }

    public function updateProduct(Request $request, $id)
    {
        $this->checkAdminAccess();

        $product = Product::findOrFail($id);

        $titleId = $request->input('title_id') ?: ($request->input('title_en') ?: $request->input('title_zh'));
        $titleEn = $request->input('title_en') ?: $titleId;
        $titleZh = $request->input('title_zh') ?: $titleId;

        $request->merge([
            'title_id' => $titleId,
            'title_en' => $titleEn,
            'title_zh' => $titleZh,
        ]);

        $validated = $request->validate([
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_zh' => 'nullable|string|max:255',
            'category_slug' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'trending' => 'nullable|boolean',
            'desc_id' => 'nullable|string',
            'desc_en' => 'nullable|string',
            'desc_zh' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $descId = $validated['desc_id'] ?? ($validated['desc_en'] ?? ($validated['desc_zh'] ?? ''));
        $descEn = $validated['desc_en'] ?? $descId;
        $descZh = $validated['desc_zh'] ?? $descId;

        $product->update([
            'title' => [
                'id' => $validated['title_id'],
                'en' => $validated['title_en'] ?: $validated['title_id'],
                'zh-Hans' => $validated['title_zh'] ?: $validated['title_id'],
            ],
            'slug' => Str::slug($validated['slug']),
            'category_slug' => $validated['category_slug'],
            'trending' => $request->boolean('trending'),
            'desc' => [
                'id' => $descId ?: ($descEn ?: $descZh),
                'en' => $descEn ?: ($descId ?: $descZh),
                'zh-Hans' => $descZh ?: ($descId ?: $descEn),
            ],
            'image' => $imagePath,
        ]);

        return redirect()->route('erp.cms.products')->with('success', "Produk '{$product->display_title}' berhasil diperbarui!");
    }

    public function deleteProduct($id)
    {
        $this->checkAdminAccess();

        $product = Product::findOrFail($id);
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $name = $product->display_title;
        $product->delete();

        return redirect()->route('erp.cms.products')->with('success', "Produk '{$name}' berhasil dihapus!");
    }

    // =========================================================================
    // PARTNERS CRUD
    // =========================================================================
    public function storePartner(Request $request)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|file|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $imagePath = $request->file('image')->store('partners', 'public');

        $partner = Partner::create([
            'name' => $validated['name'],
            'image_url' => $imagePath,
        ]);

        return redirect()->route('erp.cms.partners')->with('success', "Mitra '{$partner->name}' berhasil ditambahkan!");
    }

    public function updatePartner(Request $request, $id)
    {
        $this->checkAdminAccess();

        $partner = Partner::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $imagePath = $partner->image_url;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('partners', 'public');
        }

        $partner->update([
            'name' => $validated['name'],
            'image_url' => $imagePath,
        ]);

        return redirect()->route('erp.cms.partners')->with('success', "Mitra '{$partner->name}' berhasil diperbarui!");
    }

    public function deletePartner($id)
    {
        $this->checkAdminAccess();

        $partner = Partner::findOrFail($id);
        if ($partner->image_url && Storage::disk('public')->exists($partner->image_url)) {
            Storage::disk('public')->delete($partner->image_url);
        }
        $name = $partner->name;
        $partner->delete();

        return redirect()->route('erp.cms.partners')->with('success', "Mitra '{$name}' berhasil dihapus!");
    }

    // =========================================================================
    // GALLERIES CRUD
    // =========================================================================
    public function storeGallery(Request $request)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_zh' => 'nullable|string|max:255',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_zh' => 'nullable|string',
            'image' => 'required|file|mimes:jpeg,jpg,png,webp|max:6144',
            'status' => 'nullable|boolean',
        ]);

        $imagePath = $request->file('image')->store('galleries', 'public');

        $gallery = Gallery::create([
            'category_id' => $validated['category_id'],
            'image_url' => $imagePath,
            'title' => [
                'id' => $validated['title_id'],
                'en' => $validated['title_en'] ?: $validated['title_id'],
                'zh-Hans' => $validated['title_zh'] ?: $validated['title_id'],
            ],
            'description' => [
                'id' => $validated['description_id'] ?? '',
                'en' => $validated['description_en'] ?? ($validated['description_id'] ?? ''),
                'zh-Hans' => $validated['description_zh'] ?? ($validated['description_id'] ?? ''),
            ],
            'status' => $request->has('status') ? $request->boolean('status') : true,
        ]);

        return redirect()->route('erp.cms.galleries')->with('success', "Foto galeri '{$gallery->display_title}' berhasil ditambahkan!");
    }

    public function updateGallery(Request $request, $id)
    {
        $this->checkAdminAccess();

        $gallery = Gallery::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_zh' => 'nullable|string|max:255',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_zh' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:6144',
            'status' => 'nullable|boolean',
        ]);

        $imagePath = $gallery->image_url;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('galleries', 'public');
        }

        $gallery->update([
            'category_id' => $validated['category_id'],
            'image_url' => $imagePath,
            'title' => [
                'id' => $validated['title_id'],
                'en' => $validated['title_en'] ?: $validated['title_id'],
                'zh-Hans' => $validated['title_zh'] ?: $validated['title_id'],
            ],
            'description' => [
                'id' => $validated['description_id'] ?? '',
                'en' => $validated['description_en'] ?? ($validated['description_id'] ?? ''),
                'zh-Hans' => $validated['description_zh'] ?? ($validated['description_id'] ?? ''),
            ],
            'status' => $request->has('status') ? $request->boolean('status') : $gallery->status,
        ]);

        return redirect()->route('erp.cms.galleries')->with('success', "Foto galeri '{$gallery->display_title}' berhasil diperbarui!");
    }

    public function toggleGalleryStatus($id)
    {
        $this->checkAdminAccess();

        $gallery = Gallery::findOrFail($id);
        $gallery->status = !$gallery->status;
        $gallery->save();

        return response()->json([
            'success' => true,
            'status' => $gallery->status,
            'message' => 'Status tayang galeri berhasil diubah!',
        ]);
    }

    public function deleteGallery($id)
    {
        $this->checkAdminAccess();

        $gallery = Gallery::findOrFail($id);
        if ($gallery->image_url && Storage::disk('public')->exists($gallery->image_url)) {
            Storage::disk('public')->delete($gallery->image_url);
        }
        $title = $gallery->display_title;
        $gallery->delete();

        return redirect()->route('erp.cms.galleries')->with('success', "Foto galeri '{$title}' berhasil dihapus!");
    }

    // =========================================================================
    // TESTIMONIALS CRUD
    // =========================================================================
    public function storeTestimonial(Request $request)
    {
        $this->checkAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'message_id' => 'required|string',
            'message_en' => 'nullable|string',
            'message_zh' => 'nullable|string',
        ]);

        $testimonial = Testimonial::create([
            'name' => $validated['name'],
            'role' => $validated['role'] ?? '',
            'rating' => $validated['rating'],
            'message' => [
                'id' => $validated['message_id'],
                'en' => $validated['message_en'] ?: $validated['message_id'],
                'zh-Hans' => $validated['message_zh'] ?: $validated['message_id'],
            ],
        ]);

        return redirect()->route('erp.cms.testimonials')->with('success', "Testimoni dari '{$testimonial->name}' berhasil ditambahkan!");
    }

    public function updateTestimonial(Request $request, $id)
    {
        $this->checkAdminAccess();

        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'message_id' => 'required|string',
            'message_en' => 'nullable|string',
            'message_zh' => 'nullable|string',
        ]);

        $testimonial->update([
            'name' => $validated['name'],
            'role' => $validated['role'] ?? '',
            'rating' => $validated['rating'],
            'message' => [
                'id' => $validated['message_id'],
                'en' => $validated['message_en'] ?: $validated['message_id'],
                'zh-Hans' => $validated['message_zh'] ?: $validated['message_id'],
            ],
        ]);

        return redirect()->route('erp.cms.testimonials')->with('success', "Testimoni dari '{$testimonial->name}' berhasil diperbarui!");
    }

    public function deleteTestimonial($id)
    {
        $this->checkAdminAccess();

        $testimonial = Testimonial::findOrFail($id);
        $name = $testimonial->name;
        $testimonial->delete();

        return redirect()->route('erp.cms.testimonials')->with('success', "Testimoni dari '{$name}' berhasil dihapus!");
    }

    // =========================================================================
    // ABOUT PAGE UPDATE
    // =========================================================================
    public function updateAboutPage(Request $request)
    {
        $this->checkAdminAccess();

        $about = Aboutpage::firstOrCreate(['id' => 1]);
        $currentData = $about->data ?? [];

        $validated = $request->validate([
            'badge_id' => 'required|string|max:255',
            'title_id' => 'required|string|max:255',
            'description_1_id' => 'required|string',
            'description_2_id' => 'required|string',
            'stats_units_value' => 'required|string|max:50',
            'stats_units_label' => 'required|string|max:100',
            'stats_cities_value' => 'required|string|max:50',
            'stats_cities_label' => 'required|string|max:100',
            'stats_clients_value' => 'required|string|max:50',
            'stats_clients_label' => 'required|string|max:100',
            'vision_title' => 'required|string|max:255',
            'vision_text' => 'required|string',
            'mission_title' => 'required|string|max:255',
            'mission_item_1' => 'nullable|string',
            'mission_item_2' => 'nullable|string',
            'mission_item_3' => 'nullable|string',
        ]);

        $missionList = [];
        if (!empty($validated['mission_item_1'])) $missionList[] = ['id' => 1, 'text' => $validated['mission_item_1']];
        if (!empty($validated['mission_item_2'])) $missionList[] = ['id' => 2, 'text' => $validated['mission_item_2']];
        if (!empty($validated['mission_item_3'])) $missionList[] = ['id' => 3, 'text' => $validated['mission_item_3']];

        $currentData['content']['id'] = [
            'badge' => $validated['badge_id'],
            'title' => $validated['title_id'],
            'stats_units_value' => $validated['stats_units_value'],
            'stats_units_label' => $validated['stats_units_label'],
            'stats_cities_value' => $validated['stats_cities_value'],
            'stats_cities_label' => $validated['stats_cities_label'],
            'stats_clients_value' => $validated['stats_clients_value'],
            'stats_clients_label' => $validated['stats_clients_label'],
            'vision_eyebrow' => 'MASA DEPAN',
            'vision_title' => $validated['vision_title'],
            'vision_text' => $validated['vision_text'],
            'mission_eyebrow' => 'KOMITMEN KAMI',
            'mission_title' => $validated['mission_title'],
            'description_1' => $validated['description_1_id'],
            'description_2' => $validated['description_2_id'],
            'mission_list' => $missionList,
        ];

        // Mirror to en and zh-Hans if requested or keep existing
        if (!isset($currentData['content']['en'])) {
            $currentData['content']['en'] = $currentData['content']['id'];
        }
        if (!isset($currentData['content']['zh-Hans'])) {
            $currentData['content']['zh-Hans'] = $currentData['content']['id'];
        }

        $about->data = $currentData;
        $about->save();

        return redirect()->route('erp.cms.about')->with('success', 'Konten profil perusahaan berhasil diperbarui!');
    }

    // =========================================================================
    // CONTACT & QR UPDATE
    // =========================================================================
    public function updateContact(Request $request)
    {
        $this->checkAdminAccess();

        $contact = CmsContact::firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'contact1' => 'required|string|max:50',
            'contact2' => 'nullable|string|max:50',
            'wechat_qr' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $qrPath = $contact->wechat_qr;
        if ($request->hasFile('wechat_qr')) {
            if ($qrPath && Storage::disk('public')->exists($qrPath)) {
                Storage::disk('public')->delete($qrPath);
            }
            $qrPath = $request->file('wechat_qr')->store('wechat-qr', 'public');
        }

        $contact->update([
            'contact1' => preg_replace('/[^0-9]/', '', $validated['contact1']),
            'contact2' => !empty($validated['contact2']) ? preg_replace('/[^0-9]/', '', $validated['contact2']) : null,
            'wechat_qr' => $qrPath,
        ]);

        return redirect()->route('erp.cms.contact')->with('success', 'Kontak WhatsApp dan QR WeChat berhasil diperbarui!');
    }
}
