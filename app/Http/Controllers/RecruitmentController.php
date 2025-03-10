<?php

namespace App\Http\Controllers;

use App\Models\Recruitment;
use App\Models\RecruitmentPosition;
use App\Models\RecruitmentQualification;
use App\Models\ResearchGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RecruitmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        Log::info('Showing Recruitment Announcements');

        // ดึงเฉพาะ recruitment ที่อยู่ในกลุ่มวิจัยของ user ที่เป็น headproject
        $recruitments = Recruitment::whereHas('researchGroup.user', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                ->where('work_of_research_groups.role', 1);
        })->with(['researchGroup', 'position', 'qualifications'])->get();

        return view('recruitment.index', compact('recruitments'));
    }


    public function create()
    {
        $user = auth()->user();

        if (!$user->hasRole('headproject')) {
            Log::warning("User $user->id attempted to create a recruitment announcement without 'headproject' role.");
            return redirect()->route('recruitment.index')->with('error', 'Only headproject role can create recruitment announcements.');
        }

        Log::info('Creating Recruitment Announcement');

        // ดึงเฉพาะกลุ่มวิจัยที่ผู้ใช้เป็นหัวหน้า (role = 1)
        $researchGroups = ResearchGroup::whereHas('user', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                  ->where('work_of_research_groups.role', 1);
        })->get();

        $positions = RecruitmentPosition::all();

        return view('recruitment.create', compact('researchGroups', 'positions'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitment.index')->with('error', 'Only headproject role can create recruitment announcements.');
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
            'apply_channel_th' => 'required',
            'apply_channel_en' => 'required',
        ]);

        if ($this->isDuplicateQualificationInRequest($request)) {
            return redirect()->back()->with('error', 'Duplicate qualifications found.');
        }

        $recruitment = Recruitment::create($request->all());
        $this->addQualificationsToRecruitment($recruitment, $request);

        return redirect()->route('recruitment.index')->with('success', 'Recruitment announcement created successfully.');
    }

    public function edit(Recruitment $recruitment)
    {
        $user = auth()->user();

        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitment.index')->with('error', 'Only headproject role can edit recruitment announcements.');
        }

        Log::info('Editing Recruitment Announcement');

        // ดึงเฉพาะกลุ่มวิจัยที่ผู้ใช้เป็นหัวหน้า (role = 1)
        $researchGroups = ResearchGroup::whereHas('user', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                  ->where('work_of_research_groups.role', 1);
        })->get();

        $positions = RecruitmentPosition::all();
        $recruitment->load(['qualifications']);

        return view('recruitment.edit', compact('recruitment', 'researchGroups', 'positions'));
    }

    public function update(Request $request, Recruitment $recruitment)
    {
        $user = auth()->user();

        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitment.index')->with('error', 'Only headproject role can update recruitment announcements.');
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
            'apply_channel_th' => 'required',
            'apply_channel_en' => 'required',
        ]);

        if ($this->isDuplicateQualificationInRequest($request)) {
            return redirect()->back()->with('error', 'Duplicate qualifications found.');
        }

        Log::info('Updating Recruitment Announcement: ' . json_encode($request->all()));

        $recruitment->update($request->all());
        $recruitment->qualifications()->delete();
        $this->addQualificationsToRecruitment($recruitment, $request);

        return redirect()->route('recruitment.index')->with('success', 'Recruitment announcement updated successfully.');
    }

    public function destroy(Recruitment $recruitment)
    {
        $user = auth()->user();

        if (!$user->hasRole('headproject')) {
            return redirect()->route('recruitment.index')->with('error', 'Only headproject role can delete recruitment announcements.');
        }

        $recruitment->delete();
        return redirect()->route('recruitment.index')->with('success', 'Recruitment announcement deleted successfully.');
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
                    $recruitment->qualifications()->create([
                        'text_th' => $value['text_th'],
                        'text_en' => $value['text_en']
                    ]);
                }
            }
        }
    }
}
