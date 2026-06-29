@extends('layouts.app')

@section('title', 'Organization Structure')

@section('content')

    <div class="wrapper">
        <div class="page-content">
            <div class="container-xxl">
                @foreach($levels as $level => $teams)
                    <div class="mb-5">
                        <h4 class="text-center">
                            Level {{ $level }}
                        </h4>

                        <div class="row justify-content-center">
                            @foreach($teams as $team)
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <H4 class="text-primary fw-semibold">{{ $team->name }}</H4>
                                        </div>

                                        <div class="card-body">
                                            @php
                                                $leaders = $team->members->where('member_role', 'Leader');
                                                $members = $team->members->where('member_role', 'Member');
                                            @endphp

                                            <div class="mb-3">
                                                <small class="text-muted">Leader</small>

                                                @forelse($leaders as $leader)
                                                    <div>
                                                        👤 {{ $leader->employee->full_name }}
                                                    </div>
                                                @empty
                                                    <div class="text-muted">
                                                        -
                                                    </div>
                                                @endforelse
                                            </div>

                                            <hr>

                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    Anggota Team
                                                </small>

                                                @forelse($members as $member)
                                                    <div>
                                                        • {{ $member->employee->full_name }}

                                                        @if($member->employee?->position)
                                                            <small class="text-muted">
                                                                ({{ $member->employee->position->name }})
                                                            </small>
                                                        @endif
                                                    </div>
                                                @empty

                                                    <div class="text-muted">
                                                        -
                                                    </div>

                                                @endforelse

                                            </div>

                                            <hr>

                                            <div class="d-flex justify-content-between mt-2">
                                                <span>Total Anggota</span>
                                                <strong>
                                                    {{ $team->members->count() }}
                                                </strong>
                                            </div>

                                        </div>


                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>


@endsection