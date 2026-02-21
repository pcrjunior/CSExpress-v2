// Função para atualizar o valor total dos ajudantes com base na tabela
function atualizarValorTotalAjudantes() {
    let valorTotalAjudantes = 0;
    let valor_repassado_aj = 0;

    $('#tabelaAjudantes tbody tr').not('.sem-registros').each(function() {
        const valorTextoAjudante = $(this).find('.valor-ajudante').text().replace(/\./g, '').replace(',', '.');
        const valorAjudante = parseFloat(valorTextoAjudante) || 0;
        valorTotalAjudantes += valorAjudante;

        const valorTextoRepasse = $(this).find('.valor_repassado_aj').text().replace(/\./g, '').replace(',', '.');
        const valorRepasse = parseFloat(valorTextoRepasse) || 0;
        valor_repassado_aj += valorRepasse;
    });


    $('#valor_ajudantes').val(valorTotalAjudantes.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
    $('#valor_repassado_ajudantes').val(valor_repassado_aj.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
}


// Função para carregar os ajudantes no modal
function carregarAjudantes() {
    const nomeMotorista = $('#motorista option:selected').text().trim();

    if (!nomeMotorista || $('#motorista').val() === '') {
        alert('Selecione um motorista antes de adicionar ajudantes.');
        return;
    }

    $.ajax({
        url: urlAjudantes,
        type: 'GET',
        dataType: 'json',
        data: { motorista_nome: nomeMotorista }, // ← Envia o nome do motorista
        success: function(response) {
            $('#ajudante').empty();
            $('#ajudante').append('<option value="">Selecione um ajudante</option>');
            $.each(response.ajudantes, function(index, ajudante) {
                $('#ajudante').append(
                    $('<option></option>')
                        .val(ajudante.id)
                        .text(ajudante.nome)
                        .attr('data-telefone', ajudante.telefone ?? '')

                );
            });
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar ajudantes:', error);
            alert('Não foi possível carregar os ajudantes.');
        }
    });
}


// Função para adicionar ajudante à tabela
function adicionarAjudanteTabela(id, nome, telefone, valor, valor_repassado_aj) {
    // Remover linha padrão de "sem registros"
    $('.sem-registros').remove();

    // Verifica duplicidade
    if ($(`#ajudante-row-${id}`).length > 0) {
        alert('Este ajudante já foi adicionado.');
        return;
    }

    const linha = `
        <tr id="ajudante-row-${id}" data-id="${id}">
            <td>${nome}</td>
            <td>${telefone}</td>
            <td class="valor-ajudante">${valor}</td>
            <td class="valor_repassado_aj">${valor_repassado_aj}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btn-remover-ajudante" data-id="${id}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#tabelaAjudantes tbody').append(linha);

    atualizarValorTotalAjudantes();
    calcularValorTotalServico();

    $('#btnAddAjudante').focus();
}

// Evento para remover ajudante da tabela (delegação)
$(document).on('click', '.btn-remover-ajudante', function(e) {
    e.preventDefault();

    $(this).closest('tr').remove();

    // Verifica se ainda há ajudantes
    if ($('#tabelaAjudantes tbody tr').length === 0) {
        $('#tabelaAjudantes tbody').html(`
            <tr class="sem-registros">
                <td colspan="5" class="text-center">Nenhum ajudante adicionado</td>
            </tr>
        `);
    }

    atualizarValorTotalAjudantes();
    calcularValorTotalServico();
});

// (Opcional) Resetar campos do modal sempre que for fechado
$('#modalAjudante').on('hidden.bs.modal', function () {
    $('#ajudante').val('');
    $('#valor_ajudante').val('');
    $('#valor_repassado_aj').val('');
});
