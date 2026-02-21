@auth
<div>
    <!-- Let all your things have their places; let each part of your business have its time. - Benjamin Franklin -->


<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="gerenciamentoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    Gerenciamento
  </a>
  <ul class="dropdown-menu" aria-labelledby="gerenciamentoDropdown">
    <li><a class="dropdown-item" href="{{ route('usuarios.index') }}">Usuário</a></li>
    <li><a class="dropdown-item" href="{{ route('empresas.index') }}">Empresas</a></li>
    <!--<li><a class="dropdown-item" href="{{ route('filiais.index') }}">Filiais</a></li>-->
    <li><a class="dropdown-item" href="{{ route('entregadores.index') }}">Colaborador</a></li>
    <li><a class="dropdown-item" href="{{ route('veiculos.index') }}">Veículos</a></li>
    <li><a class="dropdown-item" href="{{ route('clientes.index') }}">Clientes</a></li>
  </ul>
</li>

</div>
@endauth

