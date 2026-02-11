<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\SlideShow;
use App\Models\UiCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * UiDesignController
 */
class UiDesignController extends Controller
{
    // Slide Show
    public function index()
    {
        $slideshows = SlideShow::latest()->get(); // Or paginate if needed

        return view('ui.slideshow', compact('slideshows'));
    }

    // Create Sliders Image UI     
    /**
     * create
     *
     * @return void
     */
    public function create()
    {
        $langs = Language::langs(); // shows "English" / "Arabic"

        return view('ui.create', compact('langs'));
    }
    // Create Sliders Image Logic     
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'image' => 'required',
            'lang' => 'sometimes',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->route('design.show')->with('error', $messages->first());
        } else {
            $data =  $validator->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('slides', 'public');
                $data['image'] = $path;
            }
            $newSildeShow = SlideShow::create($data);

            if ($newSildeShow) {
                return redirect()->route('design.show')->with('success', 'SlidShow  Successfully Created');
            }
        }
    }
    // Edit Sliders Image UI     
    /**
     * edit
     *
     * @param  mixed $slideshow
     * @return void
     */
    public function edit(slideshow $slideshow)
    {
        return view('ui.edit', compact('slideshow'));
    }
    // Edit Sliders Image Logic    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $slideshow
     * @return void
     */
    public function update(Request $request, SlideShow $slideshow)
    {
        // Validate inputs
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            '       lang' => 'nullable|string|in:ar,en',
            'imageInput' => 'nullable|image|max:2048',
        ]);
        // Update basic fields
        $slideshow->name = $validated['name'];
        // Update image if provided
        if ($request->hasFile('imageInput')) {
            $path = $request->file('imageInput')->store('slides', 'public');
            $slideshow->image = $path;
        }
        $slideshow->save();
        return redirect()->route('design.show')->with('success', 'Slide updated successfully!');
    }
    // Delete Slide Show     
    /**
     * destroy
     *
     * @param  mixed $slideshow
     * @return void
     */
    public function destroy(SlideShow $slideshow)
    {
        // Delete the image file from storage
        if ($slideshow->image && Storage::disk('public')->exists($slideshow->image)) {
            Storage::disk('public')->delete($slideshow->image);
        }

        // Delete the slideshow record from database
        if ($slideshow->delete()) {
            return redirect()->back()->with('success', 'Slide deleted successfully!');
        }

        return redirect()->back()->with('error', 'Failed to delete slide.');
    }

    // Categories UI Show
        
    /**
     * categoryShow
     *
     * @return void
     */
    public function categoryShow(){
            $categoriesUi = UiCategory::orderBy('id')->get();
            return view('ui.category.index',compact('categoriesUi'));
    }


    public function setting(ProductServiceCategory $category ,Request $request){
        $categories = ProductServiceCategory::orderBy('id')->get();
        $categories = $categories->pluck('name','id');
        return view('ui.category.create',compact('categories'));
    }
        public function categoryUiedit(UiCategory $category ){
                 $categories = ProductServiceCategory::orderBy('id')->get();
        $categories = $categories->pluck('name','id');
        return view('ui.category.edit',compact('category','categories'));
    }


    public function categoryStore (Request $request){
              $validator = $request->validate([
            'cat_id'  => 'required',
            'is_enabled'  => 'sometimes',
            'section' => 'required',
        ]);
        $validator['is_enabled'] = $validator['is_enabled']  ?? '0' ;

            $newUiCategiry  = UiCategory::create($validator);
                if ($newUiCategiry) {
            return redirect()->back()->with('success', 'Ctegiry Ui Created successfully!');
        }

        return redirect()->back()->with('error', 'Failed to Create  Category Ui.');
    }
    public function categoryUiUpdate (UiCategory $category,Request $request){
         
            $data = $request->only('cat_id','section','is_enabled');
            $data['is_enabled'] = $data['is_enabled']  ?? '0' ;

            $updateCategoryUi  = $category->update($data);
                if ($updateCategoryUi) {
            return redirect()->back()->with('success', 'Category Ui Created successfully!');
        }

        return redirect()->back()->with('error', 'Failed to Create  Category Ui.');
    }

    public function delete(UiCategory $category){
                if ($category->delete()) {
                    return redirect()->back()->with('success','category Successfully Deleted');
                }else{
                    return redirect()->back()->with('failed','category Successfully Deleted');

                }

    }
}
