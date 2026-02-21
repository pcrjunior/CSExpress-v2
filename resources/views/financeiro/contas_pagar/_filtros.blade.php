<!-- resources/views/financeiro/contas_pagar/_filtros.blade.php -->
@component('financeiro.components.section-card', ['title' => 'Filtros', 'icon' => 'filter'])
    @component('financeiro.components.filter-form', [
        'route' => route('financeiro.filtrar-contas-pagar'), 
        'clearRoute' => route('financeiro.contas-pagar')
    ])
    @include('financeiro.components.date-range-filter')
        
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="">Todos</option>
                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="entregador_id" class="form-label">Motorista</label>
            <select class="form-select" id="entregador_id" name="entregador_id">
                <option value="">Todos</option>
                @foreach(App\Models\Entregador::all() as $entregador)
                    <option value="{{ $entregador->id }}" {{ request('entregador_id') == $entregador->id ? 'selected' : '' }}>
                        {{ $entregador->nome }}
                    </option>
                @endforeach
            </select>
        </div>
    @endcomponent
@endcomponent

