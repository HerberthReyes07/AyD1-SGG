<div class="card">
    <div class="card-body">
        @if ($trainerHistory->isEmpty())
            <p class="text-secondary text-center mb-0 py-3">
                <i class="bi bi-inbox d-block mb-2" style="font-size: 2.5rem;"></i>
                Todavía no has tenido ningún entrenador asignado.
            </p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Entrenador</th>
                            <th>Período</th>
                            <th>Objetivo</th>
                            <th>Motivo de reasignación</th>
                            <th>Calificación</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainerHistory as $assignment)
                            <tr class="{{ $assignment->end_date === null ? 'table-primary' : '' }}">
                                <td>
                                    {{ $assignment->trainer->user->first_name }}
                                    {{ $assignment->trainer->user->last_name }}
                                    @if ($assignment->end_date === null)
                                        <span class="badge bg-primary ms-1">Actual</span>
                                    @endif
                                </td>
                                <td class="small">
                                    {{ $assignment->assignment_date->format('d/m/Y') }} —
                                    {{ $assignment->end_date?->format('d/m/Y') ?? 'presente' }}
                                </td>
                                <td>{{ $assignment->goal ?? '—' }}</td>
                                <td class="small text-secondary">{{ $assignment->reassignment_reason ?? '—' }}</td>
                                <td>
                                    @if ($assignment->trainerRating)
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $assignment->trainerRating->rating ? '-fill text-warning' : ' text-secondary' }}"></i>
                                        @endfor
                                    @else
                                        <span class="text-secondary small">Sin calificar</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal" data-bs-target="#rate-trainer-{{ $assignment->id }}">
                                        <i class="bi bi-star me-1"></i>{{ $assignment->trainerRating ? 'Editar Calificación' : 'Calificar' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Modales de calificación, uno por asignación --}}
@foreach ($trainerHistory as $assignment)
    <x-modal :name="'rate-trainer-' . $assignment->id" max-width="md">
        <form method="POST" action="{{ route('trainer-assignments.rate', $assignment) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    Calificar a {{ $assignment->trainer->user->first_name }} {{ $assignment->trainer->user->last_name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label d-block">Calificación</label>
                    <div class="star-rating" data-assignment-id="{{ $assignment->id }}">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star star-icon" data-value="{{ $i }}" style="font-size: 1.5rem; cursor: pointer;"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" class="rating-input" value="{{ $assignment->trainerRating?->rating }}" required>
                </div>
                <div class="mb-3">
                    <label for="comment-{{ $assignment->id }}" class="form-label">Comentario (opcional)</label>
                    <textarea class="form-control" id="comment-{{ $assignment->id }}" name="comment"
                        rows="3">{{ $assignment->trainerRating?->comment }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            </div>
        </form>
    </x-modal>
@endforeach

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.star-rating').forEach(function (container) {
            const stars = container.querySelectorAll('.star-icon');
            const input = container.parentElement.querySelector('.rating-input');
            const initialValue = parseInt(input.value) || 0;

            function paintStars(value) {
                stars.forEach(star => {
                    const starValue = parseInt(star.dataset.value);
                    star.classList.toggle('bi-star-fill', starValue <= value);
                    star.classList.toggle('bi-star', starValue > value);
                    star.classList.toggle('text-warning', starValue <= value);
                });
            }

            paintStars(initialValue);

            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const value = parseInt(this.dataset.value);
                    input.value = value;
                    paintStars(value);
                });
            });
        });
    });
</script>
@endpush
