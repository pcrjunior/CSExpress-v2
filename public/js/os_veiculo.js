// Função para carregar veículos do motorista selecionado
function carregarVeiculosMotorista(motoristaId) {
    if (motoristaId) {
        $('#veiculos').empty();
        // Substitui o placeholder na URL global definida na view
        var url = urlVeiculos.replace('MOTORISTA_ID_PLACEHOLDER', motoristaId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.veiculos) {
                    $.each(data.veiculos, function(index, veiculo) {
                        $('#veiculos').append(
                            $('<option></option>')
                                .val(veiculo.id)
                                .text(veiculo.placa + ' - ' + veiculo.fabricante + ' - ' + veiculo.modelo)
                        );
                    });
                } else if (data.error) {
                    alert(data.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar os veículos:", error);
                alert("Não foi possível carregar os veículos do motorista.");
            }
        });
    } else {
        $('#veiculos').append('<option value="">Selecione um motorista para carregar os veículos</option>');
    }
}
