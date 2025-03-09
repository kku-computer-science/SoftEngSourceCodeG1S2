<?php

namespace App\Http\Controllers;

use App\Models\Recruitment;
use App\Models\RecruitmentPosition;
use App\Models\RecruitmentQualification;
use App\Models\ResearchGroup;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RecruitmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:recruitment-list|recruitment-create|recruitment-edit|recruitment-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:recruitment-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:recruitment-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:recruitment-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $user = auth()->user();
        Log::info('Showing Recruitment Announcements');

        if ($user->hasRole('admin')) {
            $recruitments = Recruitment::with(['researchGroup', 'position', 'qualifications'])->get();
        } else {
            $userId = $user->id;
            $recruitments = Recruitment::whereHas('researchGroup', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->with(['researchGroup', 'position', 'qualifications'])->get();
        }

        return view('recruitments.index', compact('recruitments'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('headproject')) {
            Log::warning("User $user->id attempted to create a recruitment announcement without 'headproject' role.");
            return redirect()->route('recruitments.index')->with('error', 'Only headproject role can create recruitment announcements.');
        }

        Log::info('Creating Recruitment Announcement');
        $researchGroups = ResearchGroup::where('user_id', $user->id)->get();
        $positions = RecruitmentPosition::all();

        return response()->view('recruitments.create', compact('researchGroups', 'positions'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitments.index')->with('error', 'Only headproject role can create recruitment announcements.');
        }

        $request->validate([
            'research_group_id' => 'required',
            'title_th' => 'required',
            'title_en' => 'required',
            'position_id' => 'required',
            'job_description_th' => 'required',
            'job_description_en' => 'required',
            'place_th' => 'required',
            'place_en' => 'required',
            'salary' => 'required|numeric',
            'apply_channel' => 'required',
        ]);

        if ($this->isDuplicateQualificationInRequest($request)) {
            return redirect()->back()->with('error', 'Duplicate qualifications found.');
        }

        $input = $request->all();
        $recruitment = Recruitment::create($input);
        $this->addQualificationsToRecruitment($recruitment, $request);

        return redirect()->route('recruitments.index')->with('success', 'Recruitment announcement created successfully.');
    }

    public function show(Recruitment $recruitment)
    {
        $recruitment->load(['researchGroup', 'position', 'qualifications']);
        return view('recruitments.show', compact('recruitment'));
    }

    public function edit(Recruitment $recruitment)
    {
        $user = auth()->user();
        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitments.index')->with('error', 'Only headproject role can edit recruitment announcements.');
        }

        $researchGroups = ResearchGroup::where('user_id', $user->id)->get();
        $positions = RecruitmentPosition::all();
        $recruitment->load(['qualifications']);

        return view('recruitments.edit', compact('recruitment', 'researchGroups', 'positions'));
    }

    public function update(Request $request, Recruitment $recruitment)
    {
        $user = auth()->user();
        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitments.index')->with('error', 'Only headproject role can update recruitment announcements.');
        }

        $request->validate([
            'research_group_id' => 'required',
            'title_th' => 'required',
            'title_en' => 'required',
            'position_id' => 'required',
            'job_description_th' => 'required',
            'job_description_en' => 'required',
            'place_th' => 'required',
            'place_en' => 'required',
            'salary' => 'required|numeric',
            'apply_channel' => 'required',
        ]);

        if ($this->isDuplicateQualificationInRequest($request)) {
            return redirect()->back()->with('error', 'Duplicate qualifications found.');
        }

        Log::info('Updating Recruitment Announcement : ' . json_encode($request->all()));

        $input = $request->all();
        $recruitment->update($input);

        $recruitment->qualifications()->detach();
        $this->addQualificationsToRecruitment($recruitment, $request);

        return redirect()->route('recruitments.index')->with('success', 'Recruitment announcement updated successfully.');
    }

    public function destroy(Recruitment $recruitment)
    {
        $user = auth()->user();
        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitments.index')->with('error', 'Only headproject role can delete recruitment announcements.');
        }

        $recruitment->delete();
        return redirect()->route('recruitments.index')->with('success', 'Recruitment announcement deleted successfully.');
    }

    private function isDuplicateQualificationInRequest(Request $request)
    {
        $qualifications = [];
        if ($request->qualifications == null) {
            return false;
        }
        foreach ($request->qualifications as $value) {
            if ($value['text_th'] != null || $value['text_en'] != null) {
                if (in_array($value['text_th'], $qualifications) || in_array($value['text_en'], $qualifications)) {
                    return true;
                }
                $qualifications[] = $value['text_th'];
                $qualifications[] = $value['text_en'];
            }
        }
        return false;
    }

    private function addQualificationsToRecruitment($recruitment, $request)
    {
        if ($request->qualifications) {
            foreach ($request->qualifications as $value) {
                if ($value['text_th'] != null || $value['text_en'] != null) {
                    $recruitment->qualifications()->attach([
                        'text_th' => $value['text_th'],
                        'text_en' => $value['text_en']
                    ]);
                }
            }
        }
    }
}
