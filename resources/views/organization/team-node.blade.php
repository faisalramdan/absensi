<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <strong>
            {{ $team->name }}
        </strong>
    </div>

    <div class="card-body">
        <small class="text-muted">
            {{ $team->company?->name }}
        </small>
        <hr>
        <strong>Leader</strong>
        @foreach($team->members->where('member_role', 'Leader') as $leader)
            <div>
                👤 {{ $leader->employee->full_name }}
            </div>
        @endforeach
        <hr>
        <strong>Member</strong>
        @foreach($team->members->where('member_role', 'Member') as $member)
            <div>
                • {{ $member->employee->full_name }}
            </div>
        @endforeach
    </div>
</div>

@if($team->children->count())
    <div class="row ms-5">
        @foreach($team->children as $child)
            <div class="col-lg-4">
                @include(
                    'organization.team-node',
                    [
                        'team' => $child
                    ]
                )
                                </div>
        @endforeach
            </div>
@endif