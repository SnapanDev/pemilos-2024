<?php

namespace App\Http\Controllers;

use App\Events\UserVoteEvent;
use App\Models\Candidate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VoterController extends Controller
{
    public function __construct()
    {
        $this->logger = Log::build([
            "driver" => "single",
            "path" => storage_path('logs/app.log'),
        ]);
    }

    public function home(): View
    {
        return view('voter.home');
    }

    public function vote(): RedirectResponse|View
    {
        $canVote = Cache::remember('canVote', 5, fn() => Setting::query()->where('attribute', 'vote')->first());

        if ($canVote?->value !== 'on') {
            if ($canVote?->value === 'off') {
                return view('voter.cannot-vote');
            } elseif ($canVote?->value === 'finish') {
                return view('voter.vote-finish');
            }

            return view('voter.cannot-vote');
        }

        if (sizeof(auth()->user()->votes) > 0) {
            return redirect()->to('/logout');
        }

        $labels = Candidate::query()
            ->select('id', 'name', 'label', 'number', 'image')
            ->orderBy('number')
            ->orderBy('label', 'desc')
            ->get()
            ->groupBy('label');

        return view('voter.vote', compact('labels'));
    }

    public function submit(Request $request): RedirectResponse
    {
        abort_if(
            in_array(auth()->user()->role_id, [User::ADMIN, User::SUPER_ADMIN]), 403
        );

        $canVote = Cache::remember('canVote', 5, fn() => Setting::query()->where('attribute', 'vote')->first());

        if ($canVote?->value !== 'on') {
            return redirect()->route('vote');
        }

        $vote = $request->validate([
            'osis' => ['required', 'numeric'],
            'mpk' => ['required', 'numeric']
        ]);

        $osis = Candidate::query()->where('id', $vote['osis'])->first();
        $mpk = Candidate::query()->where('id', $vote['mpk'])->first();

        if ($osis->label !== 'OSIS' || $mpk->label !== 'MPK') {
            return back()->withErrors(['general' => 'Pilihan anda tidak cocok dengan label']);
        }

        auth()->user()->votes()->createMany([
            [
                'candidate_id' => $vote['osis'],
                'label' => 'OSIS'
            ],
            [
                'candidate_id' => $vote['mpk'],
                'label' => 'MPK'
            ]
        ]);

        event(new UserVoteEvent());

        $this->logger->info("User Vote", [
            "id" => Auth::user()->uuid,
            "voted_osis" => $osis->name,
            "voted_mpk" => $mpk->name,
        ]);

        return redirect()->route('logout');
    }

    public function logout(): View|RedirectResponse
    {
        abort_if(in_array(auth()->user()->role_id, [User::ADMIN, User::SUPER_ADMIN]), 403);

        if (sizeof(auth()->user()->votes) === 0) {
            return redirect()->route('vote');
        }

        return view('voter.logout');
    }

    public function liveVotes(string $label): View
    {
        if ($label !== "mpk" && $label !== "osis") {
            throw new NotFoundHttpException();
        }

        $candidates = Candidate::query()
            ->select('name', 'label', 'number')
            ->withCount('votes')
            ->orderBy('number')
            ->get();

        $osis = $candidates->where('label', 'OSIS');
        $mpk = $candidates->where('label', 'MPK');

        return view('chart', compact('osis', 'mpk', 'label'));
    }
}
