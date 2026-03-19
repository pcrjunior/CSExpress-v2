function CarregarDetalhesClientes(tipo, clienteId) {
    return new Promise((resolve, reject) => {
        if (!clienteId) {
            limparDetalhesClientes(tipo);
            resolve();
            return;
        }
        // Utiliza a variável global definida na view
        let url = urlClientesDados.replace('CLIENTE_ID_PLACEHOLDER', clienteId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.cliente) {
                    //$('#responsavel_' + tipo).val(data.cliente.responsavel);
                    //$('#telefone_' + tipo).val(data.cliente.telefone);
                    //$('#email_' + tipo).val(data.cliente.email);
                    $('#cep_' + tipo).val(data.cliente.cep);
                    $('#endereco_' + tipo).val(data.cliente.endereco);
                    $('#numero_' + tipo).val(data.cliente.numero);
                    $('#complemento_' + tipo).val(data.cliente.complemento);
                    $('#cidade_uf_' + tipo).val(data.cliente.cidade_uf);
                }
                resolve();
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar os dados do cliente:", error);
                alert("Não foi possível carregar os dados do cliente.");
                reject(error);
            }
        });
    });
}

// Função para limpar os detalhes dos clientes
function limparDetalhesClientes(tipo) {
    $(`#responsavel_${tipo}`).val('');
    $(`#telefone_${tipo}`).val('');
    $(`#email_${tipo}`).val('');
    $(`#cep_${tipo}`).val('');
    $(`#endereco_${tipo}`).val('');
    $(`#cidade_uf_${tipo}`).val('');
    $(`#busca_cliente_${tipo}`).val('');
    $(`#numero_${tipo}`).val('');
}

function adicionarCliente_Destino(cliente) {
    const select = $('#cliente_destino');

    // Remove opções vazias
    select.find('option[value=""]').remove();

    // Cria a nova opção com data-apelido
    const novaOpcao = $('<option>', {
        value: cliente.id,
        'data-apelido': cliente.apelido || '',
        text: `${cliente.nome} ${cliente.apelido ? `(${cliente.apelido})` : ''}`
    });

    // Adiciona ao início do select
    select.prepend(novaOpcao);

    // Seleciona a opção
    select.val(cliente.id);

    // Preenche os detalhes do cliente MANUALMENTE (já que é avulso)
    $('#responsavel_destino').val(cliente.nome || '');
    $('#telefone_destino').val(cliente.telefone || '');
    $('#email_destino').val(cliente.email || '');
    $('#cep_destino').val(cliente.cep || '00000000');
    $('#endereco_destino').val(cliente.endereco || '');
    $('#numero_destino').val(cliente.numero || '');
    $('#cidade_uf_destino').val(cliente.cidade_uf || '');

    // Atualiza as observações automaticamente
    if (typeof atualizarObservacoesAuto === 'function') {
        atualizarObservacoesAuto();
    }

    console.log('Cliente destino adicionado:', cliente);
}

// Função para limpar os detalhes dos clientes
function limparDetalhesClientesAvulso() {
    $('#nome_avulso_origem').val('');
    $('#telefone_avulso_origem').val('');
    $('#endereco_avulso_origem').val('');
    $('#numero_avulso_origem').val('');
    $('#nome_avulso_destino').val('');
    $('#telefone_avulso_destino').val('');
    $('#endereco_avulso_destino').val('');
    $('#numero_avulso_destino').val('');
}


function adicionarCliente_Origem(cliente) {
    const select = $('#cliente_origem');

    // Remove opções vazias
    select.find('option[value=""]').remove();

    // Cria a nova opção com data-apelido
    const novaOpcao = $('<option>', {
        value: cliente.id,
        'data-apelido': cliente.apelido || '',
        text: `${cliente.nome} ${cliente.apelido ? `(${cliente.apelido})` : ''}`
    });

    // Adiciona ao início do select
    select.prepend(novaOpcao);

    // Seleciona a opção
    select.val(cliente.id);

    // Preenche os detalhes do cliente MANUALMENTE (já que é avulso)
    $('#responsavel_origem').val(cliente.nome || '');
    $('#telefone_origem').val(cliente.telefone || '');
    $('#email_origem').val(cliente.email || '');
    $('#cep_origem').val(cliente.cep || '00000000');
    $('#endereco_origem').val(cliente.endereco || '');
    $('#numero_origem').val(cliente.numero || '');
    $('#cidade_uf_origem').val(cliente.cidade_uf || '');

    // Atualiza as observações automaticamente
    if (typeof atualizarObservacoesAuto === 'function') {
        atualizarObservacoesAuto();
    }

    console.log('Cliente destino adicionado:', cliente);
}
