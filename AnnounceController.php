<?php

namespace App\Http\Controllers;

use App\Models\Announce;
use App\Models\AnnounceCategory;
use App\Models\AdministrativeUnit;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AnnounceController extends Controller
{
    public function index()
    {
        $announces = Announce::where('createdByUserId', auth()->id())->paginate(10);
        return view('announces.index', compact('announces'));
    }


    public function create()
    {
        return view('announces.create');
    }

    public function show($unitId, $announceId)
    {
        $announce = Announce::where('id', $announceId)
            ->whereHas('category.administrativeUnit', function ($query) use ($unitId) {
                $query->where('id', $unitId);
            })
            ->firstOrFail();
        return view('announces.show', compact('announce'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'announceText' => 'required',
            'category_id' => 'required|exists:announce_categories,id',
            'announceImageURL' => 'nullable|file|image|max:10240',
        ]);

        $announce = new Announce([
            'announceText' => $request->input('announceText'),
            'announcesCategoryId' => $request->input('category_id'),
            'createdByUserId' => auth()->id(),
            'updateDate' => now(),
            'createDate' => now(),
        ]);

        if ($request->hasFile('announceImageURL') && $request->file('announceImageURL')->isValid()) {
            $path = $request->announceImageURL->store('public/announces');
            $announce->announceImageURL = Storage::url($path);
        }

        $announce->save();

        $adminUnitId = auth()->user()->administrativeUnitId;
        $users = User::whereHas('loginApplications.loginApprovals', function ($query) use ($adminUnitId) {
            $query->where('administrativeUnitId', $adminUnitId)
                ->where('applicationStatus', 1);
        })->get();

        foreach ($users as $user) {
            $administrativeUnitName = AdministrativeUnit::where('id', auth()->user()->administrativeUnitId)->value('name');
            $notificationData = [
                "message" => "A fost publicată un anunț nou către unitate administrativă din {$administrativeUnitName}",
                "upload_announce_id" => $announce->id,
            ];
            $notification = new Notification;
            $notification->user_id = auth()->user()->id;
            $notification->data = $notificationData;
            $notification->type = "App\Notifications\NewAnnounceUploaded";
            $notification->notifiable_id = $user->id;
            $notification->notifiable_type = "App\Models\User";
            $notification->read_at = null;
            $notification->save();
        }

        return redirect()->route('announces.index')->with('success', 'New announce has been published successfully!');
    }

    public function edit(Announce $announce)
    {
        $unitId = $announce->category->administrativeUnit->id;
        return view('announces.edit', compact('announce', 'unitId'));
    }


    public function update(Request $request, Announce $announce)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:announce_categories,id',
            'announceText' => 'required',
            'announceImageURL' => 'nullable|file|image|max:10240',
        ]);

        $announce->update([
            'announceCategoryId' => $validated['category_id'],
            'announceText' => $validated['announceText'],
            'updateDate' => now(),
            'deleteDate' => null,
            'createdByUserId' => auth()->id(),
            'announceImageURL' => $announce->announceImageURL,
        ]);

        if ($request->hasFile('announceImageURL')) {
            $imagePath = $request->file('announceImageURL')->store('public/announce_images');
            $announce->announceImageURL = Storage::url($imagePath);
            $announce->save();
        }

        return redirect()->route('announces.index')->with('success', 'Announcement updated successfully!');
    }

    public function destroy($id)
    {
        $announce = Announce::find($id);
        if ($announce) {
            $announce->delete();
            return redirect()->route('announces.index')->with('success', 'Announcement deleted successfully');
        }
        return back()->with('error', 'Announcement not found');
    }

    public function indexByUnit($unitId)
    {
        $announces = Announce::whereHas('category.administrativeUnit', function ($query) use ($unitId) {
            $query->where('id', $unitId);
        })->latest()->paginate(10);

        $adminUnit = AdministrativeUnit::find($unitId);
        $adminUnitName = $adminUnit ? $adminUnit->name : 'Unknown Unit';
        return view('administrative-unit.dashboard', compact('unitId', 'announces', 'adminUnitName'));
    }

    public function categoryindex()
    {
        $unitId = auth()->user()->administrativeUnitId;
        $categories = AnnounceCategory::whereHas('administrativeUnit', function ($query) use ($unitId) {
            $query->where('id', $unitId);
        })->paginate(15);
        return view('announces.categoryindex', compact('categories', 'unitId'));
    }

    public function createCategory()
    {
        return view('announces.categoryCreate');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'categoryName' => 'required|string|max:50',
        ]);

        $category = new AnnounceCategory([
            'announceCategoryName' => $request->input('categoryName'),
            'administrativeUnitId' => auth()->user()->administrativeUnitId,
        ]);
        $category->save();

        return redirect()->route('announces.create')->with('success', 'New announce category has been created successfully!');
    }

    public function editCategory(AnnounceCategory $category)
    {
        return view('announces.categoryEdit', compact('category'));
    }

    public function updateCategory(Request $request, AnnounceCategory $category)
    {
        $validated = $request->validate([
            'categoryName' => 'required|string|max:50',
        ]);

        $category->update([
            'announceCategoryName' => $validated['categoryName'],
        ]);

        return redirect()->route('announces.categoryindex')->with('success', 'Announcement category updated successfully!');
    }

    public function destroyCategory(AnnounceCategory $category)
    {
        $category->delete();
        return redirect()->route('announces.categoryindex')->with('success', 'Announcement category deleted successfully');
    }
}
