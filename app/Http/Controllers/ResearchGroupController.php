<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Customer;
use App\Models\ResearchGroup;
use Illuminate\Http\Request;
use App\Models\Fund;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ResearchGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('permission:groups-list|groups-create|groups-edit|groups-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:groups-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:groups-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:groups-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $user = auth()->user();

        \Log::info('Showing Research Groups');

        if ($user->hasRole('admin')) {
            $researchGroups = ResearchGroup::with(['user', 'author'])->get();
        } else {
            $userId = $user->id;
            $researchGroups = ResearchGroup::whereHas('user', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })->with(['user', 'author'])->get();
        }

        return view('research_groups.index', compact('researchGroups'));
    }

    // Create new Research Group Form
    public function create()
    {
        $user = auth()->user();
        if (!$user->can('create', ResearchGroup::class)) {
            \Log::warning("User $user->id tried to create a research group without permission");
            return redirect()->route('researchGroups.index')->with('error', 'You do not have permission to create a research group');
        }
        \Log::info('Creating Research Group');
        $users = User::role(['teacher', 'student'])->get();
        $funds = Fund::get();
        $authors = Author::get();
        return response()->view('research_groups.create', compact('users', 'funds', 'authors'));
    }
    
    // Create new Research Group Trigger
    public function store(Request $request)
    {
        $request->validate([
            'group_name_th' => 'required',
            'group_name_en' => 'required',
            'head' => 'required',
            //'group_image' => 'required|mimes:png,jpg,jpeg|max:2048',
        ]);
        if ($this->isDuplicateUserInRequest($request)) {
            return redirect()->back()->with('error', 'Duplicate user in request');
        }
        $input = $request->all();
        $researchGroup = ResearchGroup::create($input);
        $input['group_image'] = $this->setImage($request, $researchGroup);
        $researchGroup->update($input);
        $this->addUsersToResearchGroup($researchGroup, $request);
        return redirect()->route('researchGroups.index')->with('success', 'research group created successfully.');
    }

    public function show(ResearchGroup $researchGroup)
    {
        $researchGroup->load(['user', 'author']);
        return view('research_groups.show', compact('researchGroup'));
    }


    // Edit Research Group Form
    public function edit(ResearchGroup $researchGroup)
    {
        $researchGroup = ResearchGroup::find($researchGroup->id);
        $this->authorize('update', $researchGroup);
        $researchGroup = ResearchGroup::with(['user','author'])->where('id', $researchGroup->id)->first();
        $users = User::role(["teacher", "student"])->get();
        $authors = Author::get();
        return view('research_groups.edit', compact('researchGroup', 'users', 'authors'));
    }

    // Edit Research Group Trigger
    public function update(Request $request, ResearchGroup $researchGroup)
    {
        $request->validate([
            'group_name_th' => 'required',
            'group_name_en' => 'required',
        ]);
        if ($this->isDuplicateUserInRequest($request) || $this->isDuplicateAuthorInRequest($request)) {
            return redirect()->back()->with('error', 'Duplicate user in request');
        }
        \Log::info('Updating Research Group : ' . json_encode($request->all()));
        $input = $request->all();
        $input['group_image'] = $this->setImage($request, $researchGroup);
        $researchGroup->update($input);
        $researchGroup->user()->detach();
        $this->addUsersToResearchGroup($researchGroup, $request);
        $researchGroup->author()->detach();
        $this->addAuthorsToResearchGroup($researchGroup, $request);
        return redirect()->route('researchGroups.index')
            ->with('success', 'researchGroups updated successfully');
    }

    // Delete Research Group
    public function destroy(ResearchGroup $researchGroup)
    {
        $this->authorize('delete', $researchGroup);
        $researchGroup->delete();
        return redirect()->route('researchGroups.index')
            ->with('success', 'researchGroups deleted successfully');
    }

    private function isDuplicateUserInRequest(Request $request){
        $users = [];
        if ($request->moreFields['users'] == null) {
            return false;
        }
        foreach ($request->moreFields['users'] as $value) {
            if ($value['userid'] != null) {
                if (in_array($value['userid'], $users) || $value['userid'] == $request->head) {
                    return true;
                }
                $users[] = $value['userid'];
            }
        }
        return false;
    }
    private function isDuplicateAuthorInRequest(Request $request){
        $authors = [];
        if ($request->authors == null) {
            return false;
        }
        foreach ($request->authors as $value) {
            if ($value['userid'] != null) {
                if (in_array($value['userid'], $authors) || $value['userid'] == $request->head) {
                    return true;
                }
                $authors[] = $value['userid'];
            }
        }
        return false;
    }

    
    private function setImage($request, $researchGroup){
        $fileName = '';
        if ($request->group_image) {
            if (!$this->isFileExtensionValid($request->group_image)) {
                return redirect()->back()->with('error', 'Invalid file type');
            }
            $fileName = 'RG'. $researchGroup->id . '.' . $request->group_image->extension();
            $request->group_image->move(public_path('img'), $fileName);
        }else{
            if ($researchGroup->group_image) {
                $fileName = $researchGroup->group_image;
            }else{
                $fileName = 'img.jpg';
            }
        }
        \Log::info('Image uploaded ' . $fileName);
        return $fileName;
        
    }
    
    private function isFileExtensionValid($file){
        $permitted_extensions = array('jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp');
        return in_array($file->extension(), $permitted_extensions);
    }
    private function addUsersToResearchGroup($researchGroup, $request){
        $head = $request->head;
        $researchGroup->user()->attach($head, ['role' => 1, 'permissions' => 1]);
        if ($request->moreFields['users']) {
            foreach ($request->moreFields['users'] as $value) {
                if ($value['userid'] != null) {
                    \Log::info('Adding user to research group : ' . json_encode($value));
                    $researchGroup->user()->attach($value['userid'], ['role' => $value['role'], 'permissions' => $value['permission']]);
                }
            }
        }
    }

    private function addAuthorsToResearchGroup($researchGroup, $request){
        if ($request->authors) {
            foreach ($request->authors as $value) {
                if ($value['userid'] != null) {
                    $researchGroup->author()->attach($value['userid']);
                }
            }
        }
    }
}


