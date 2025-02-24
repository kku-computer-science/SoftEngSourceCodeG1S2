<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ResearchGroup;
use Illuminate\Http\Request;
use App\Models\Fund;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ResearchGroupController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:groups-list|groups-create|groups-edit|groups-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:groups-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:groups-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:groups-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $researchGroups = ResearchGroup::with('user')->get();
        return view('research_groups.index', compact('researchGroups'));
    }

    public function create()
    {
        $users = User::role(['teacher', 'student'])->get();
        $funds = Fund::get();
        return view('research_groups.create', compact('users', 'funds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name_th' => 'required',
            'group_name_en' => 'required',
            'head' => 'required',
        ]);

        $input = $request->all();

        if ($request->group_image) {
            $input['group_image'] = time() . '.' . $request->group_image->extension();
            $request->group_image->move(public_path('img'), $input['group_image']);
        }

        $researchGroup = ResearchGroup::create($input);
        $head = $request->head;

        // เพิ่มหัวหน้ากลุ่ม (role = 1)
        $researchGroup->user()->attach($head, ['role' => 1]);

        // เพิ่มสมาชิกกลุ่มที่เหลือ (role = 2)
        if ($request->moreFields) {
            foreach ($request->moreFields as $value) {
                if (!empty($value['userid'])) {
                    $researchGroup->user()->attach($value['userid'], ['role' => 2]);
                }
            }
        }

        return redirect()->route('researchGroups.index')->with('success', 'Research group created successfully.');
    }

    public function show(ResearchGroup $researchGroup)
    {
        return view('research_groups.show', compact('researchGroup'));
    }

    public function edit(ResearchGroup $researchGroup)
    {
        $this->authorize('update', $researchGroup);
        $researchGroup = ResearchGroup::with('user')->where('id', $researchGroup->id)->first();
        $users = User::get();
        return view('research_groups.edit', compact('researchGroup', 'users'));
    }

    public function update(Request $request, ResearchGroup $researchGroup)
    {
        $request->validate([
            'group_name_th' => 'required',
            'group_name_en' => 'required',
        ]);

        $input = $request->all();

        if ($request->group_image) {
            $input['group_image'] = time() . '.' . $request->group_image->extension();
            $request->group_image->move(public_path('img'), $input['group_image']);
        }

        $researchGroup->update($input);

        // เช็กว่าเป็น Admin หรือไม่
        if (auth()->user()->hasRole('Admin')) {
            $head = $request->head;
            $researchGroup->user()->wherePivot('role', 1)->detach(); // ลบหัวหน้าคนเก่า
            $researchGroup->user()->attach([$head => ['role' => 1]]); // เพิ่มหัวหน้าคนใหม่
        }

        // อัปเดตสมาชิกกลุ่ม (role = 2)
        $researchGroup->user()->wherePivot('role', 2)->detach();
        if ($request->moreFields) {
            foreach ($request->moreFields as $value) {
                if (!empty($value['userid'])) {
                    $researchGroup->user()->attach($value['userid'], ['role' => 2]);
                }
            }
        }

        return redirect()->route('researchGroups.index')->with('success', 'Research group updated successfully.');
    }

    public function destroy(ResearchGroup $researchGroup)
    {
        $this->authorize('delete', $researchGroup);
        $researchGroup->delete();
        return redirect()->route('researchGroups.index')->with('success', 'Research group deleted successfully.');
    }
}
