<?php

namespace App\Http\Controllers;

use App\Services\MemberService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    protected MemberService $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    public function index()
    {
        $members = $this->memberService->getAllMembers();
        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'nullable|regex:/^[0-9+\-() ]+$/|max:20',
            'password' => 'required|string|min:6',
            'birth_date' => 'required|date|before:today',
        ]);

        $this->memberService->createMember($request->all());
        return redirect()->route('members.index')->with('success', 'Socio creado con éxito.');
    }

    public function edit(string $id)
    {
        $member = $this->memberService->getMemberById($id);
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'nullable|regex:/^[0-9+\-() ]+$/|max:20',
            'password' => 'nullable|string|min:6',
            'birth_date' => 'required|date|before:today',
            'is_active' => 'required|in:0,1',
        ]);

        $this->memberService->updateMember($id, $request->all());
        return redirect()->route('members.index')->with('success', 'Socio actualizado con éxito.');
    }

    public function show(string $id)
    {
        $member = $this->memberService->getMemberById($id);
        return view('admin.members.show', compact('member'));
    }

    public function activate(string $id)
    {
        $this->memberService->activateMember($id);
        return redirect()->route('members.index')->with('success', 'Socio activado con éxito.');
    }

    public function destroy(string $id)
    {
        $this->memberService->deleteMember($id);
        return redirect()->route('members.index')->with('success', 'Socio eliminado con éxito.');
    }
}
