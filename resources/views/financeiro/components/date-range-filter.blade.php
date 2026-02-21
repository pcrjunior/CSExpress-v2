<div class="col-md-{{ $colSize ?? '3' }}">
    <label for="data_inicio" class="form-label">Data Início</label>
    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
</div>
<div class="col-md-{{ $colSize ?? '3' }}">
    <label for="data_fim" class="form-label">Data Fim</label>
    <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ request('data_fim') }}">
</div>