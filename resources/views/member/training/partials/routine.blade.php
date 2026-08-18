@if ($routines->isEmpty())
<div class="card">
    <div class="card-body text-center py-4">
        <i class="bi bi-inbox d-block mb-2 text-secondary" style="font-size: 2.5rem;"></i>
        <p class="text-secondary mb-0">Tu entrenador todavía no te ha asignado ninguna rutina.</p>
    </div>
</div>
@else
{{-- Leyenda de rutinas --}}
<div class="d-flex flex-wrap gap-3 mb-3">
    @foreach ($routines as $routine)
    <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle bg-{{ $routine->color }}" style="width: 0.75rem; height: 0.75rem;"></span>
        <span class="small">{{ $routine->name }}</span>
    </div>
    @endforeach
</div>

{{-- Calendario semanal --}}
<div class="row g-2">
    @foreach ([
    'monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles',
    'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo',
    ] as $dayValue => $dayLabel)
    <div class="col-6 col-md-3 col-lg">
        <div class="card h-100">
            <div class="card-header text-center small fw-semibold py-2">
                {{ $dayLabel }}
            </div>
            <div class="card-body p-2 d-flex flex-column gap-2">
                @forelse ($exercisesByDay[$dayValue] as $item)
                <button type="button" class="btn btn-{{ $item['routine']->color }} btn-sm text-start"
                    data-bs-toggle="modal" data-bs-target="#exercise-detail-{{ $item['exercise']->id }}">
                    <div class="fw-semibold text-truncate" style="font-size: 0.75rem;">
                        {{ $item['routine']->name }}
                    </div>
                    <div class="text-truncate" style="font-size: 0.75rem;">
                        {{ $item['exercise']->exercise_name }}
                    </div>
                </button>
                @empty
                <p class="text-secondary text-center small mb-0 mt-2">—</p>
                @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Modales de detalle, uno por ejercicio --}}
@foreach ($routines as $routine)
@foreach ($routine->routineExercises as $exercise)
<x-modal :name="'exercise-detail-' . $exercise->id" max-width="sm">
    <div class="modal-header">
        <h5 class="modal-title">{{ $exercise->exercise_name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p class="mb-2">
            <span class="badge bg-{{ $routine->color }}">{{ $routine->name }}</span>
        </p>
        @if ($routine->description)
        <p class="text-secondary small">{{ $routine->description }}</p>
        @endif
        <ul class="list-unstyled mb-0">
            <li><strong>Series:</strong> {{ $exercise->sets }}</li>
            <li><strong>Repeticiones:</strong> {{ $exercise->reps }}</li>
            <li><strong>Día sugerido:</strong> {{ $exercise->suggested_day->label() }}</li>
        </ul>
    </div>
</x-modal>
@endforeach
@endforeach
@endif
