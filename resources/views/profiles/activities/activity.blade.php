 <div class="panel panel-default">
    <div class="panel-heading">
        <div class="level">
            <span class="flex">
                {{ $heading }}
            </span>
            {{-- <span>{{ $activity->subject->created_at->diffF/rHumans() }}</span> --}}
        </div>
    </div>
    <div class="panel-body">
        {{ $body }}
    </div>
</div>